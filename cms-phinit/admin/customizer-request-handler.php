<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\AuditLogger;
use CMS\Auth;
use CMS\Database;
use CMS\Security;
use CMS\Services\ThemeCustomizer;

/**
 * @return list<string>
 */
function phinit_get_advanced_raw_code_fields(): array
{
    return ['custom_css', 'custom_head_code', 'custom_footer_code'];
}

/**
 * @return array<string, array<string, string>>
 */
function phinit_get_customizer_legacy_alias_map(): array
{
    return [
        'homepage' => [
            'featured_section_title' => 'article_list_label',
            'featured_posts_count' => 'article_list_count',
            'show_info_cards' => 'show_info_grid',
            'grid_section_title' => 'tile_grid_label',
            'grid_posts_per_page' => 'tile_grid_count',
        ],
    ];
}

/**
 * Entfernt migrierte Legacy-Alias-Felder aus der aktiven Customizer-Konfiguration,
 * damit Save/Reset/UI nur noch mit kanonischen theme.json-Keys arbeiten.
 *
 * @param array<string, array<string, mixed>> $config
 * @return array<string, array<string, mixed>>
 */
function phinit_strip_customizer_legacy_aliases(array $config): array
{
    foreach (phinit_get_customizer_legacy_alias_map() as $category => $aliases) {
        if (!isset($config[$category]['sections']) || !is_array($config[$category]['sections'])) {
            continue;
        }

        foreach (array_keys($aliases) as $legacyKey) {
            unset($config[$category]['sections'][$legacyKey]);
        }
    }

    return $config;
}

function phinit_normalize_customizer_alias_key(string $category, string $fieldKey): string
{
    $aliases = phinit_get_customizer_legacy_alias_map();
    if (!isset($aliases[$category][$fieldKey])) {
        return $fieldKey;
    }

    return $aliases[$category][$fieldKey];
}

/**
 * Migriert gespeicherte Legacy-Alias-Werte in die kanonischen theme.json-Keys.
 *
 * @param array<string, array<string, mixed>> $config
 */
function phinit_migrate_customizer_legacy_aliases(array $config, ThemeCustomizer $customizer): void
{
    $aliasMap = phinit_get_customizer_legacy_alias_map();
    if ($aliasMap === []) {
        return;
    }

    try {
        $db = Database::instance();
        $prefix = $db->getPrefix();

        foreach ($aliasMap as $category => $aliases) {
            if (!isset($config[$category]['sections']) || !is_array($config[$category]['sections']) || $aliases === []) {
                continue;
            }

            $queryKeys = array_values(array_unique(array_merge(array_keys($aliases), array_values($aliases))));
            $placeholders = implode(', ', array_fill(0, count($queryKeys), '?'));
            $params = array_merge([$customizer->getTheme(), $category], $queryKeys);

            $stmt = $db->execute(
                "SELECT setting_key, setting_value, user_id
                 FROM {$prefix}theme_customizations
                 WHERE theme_slug = ?
                   AND setting_category = ?
                   AND setting_key IN ({$placeholders})",
                $params
            );

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!is_array($rows) || $rows === []) {
                continue;
            }

            $byUser = [];
            foreach ($rows as $row) {
                $userBucket = array_key_exists('user_id', $row) && $row['user_id'] !== null
                    ? (string) (int) $row['user_id']
                    : 'global';
                $settingKey = (string) ($row['setting_key'] ?? '');
                if ($settingKey === '') {
                    continue;
                }

                $byUser[$userBucket][$settingKey] = [
                    'value' => (string) ($row['setting_value'] ?? ''),
                    'userId' => $userBucket === 'global' ? null : (int) $userBucket,
                ];
            }

            foreach ($byUser as $settingsByKey) {
                foreach ($aliases as $legacyKey => $canonicalKey) {
                    if (!isset($settingsByKey[$legacyKey])) {
                        continue;
                    }

                    $userId = $settingsByKey[$legacyKey]['userId'];
                    $canonicalExists = isset($settingsByKey[$canonicalKey]);

                    if (!$canonicalExists) {
                        $fieldConfig = $config[$category]['sections'][$canonicalKey] ?? null;
                        if (!is_array($fieldConfig)) {
                            continue;
                        }

                        $normalizedValue = phinit_normalize_customizer_post_value(
                            (string) ($fieldConfig['type'] ?? 'text'),
                            $settingsByKey[$legacyKey]['value'],
                            $fieldConfig
                        );

                        $customizer->set($category, $canonicalKey, $normalizedValue, $userId);
                    }

                    $customizer->reset($category, $legacyKey, $userId);
                }
            }
        }
    } catch (\Throwable $_e) {
        // Defensive best-effort migration: Frontend-Fallbacks bleiben erhalten.
    }
}

function phinit_is_advanced_raw_code_field(string $category, string $fieldKey): bool
{
    return $category === 'advanced' && in_array($fieldKey, phinit_get_advanced_raw_code_fields(), true);
}

function phinit_customizer_can_manage_advanced_code(): bool
{
    if (!class_exists(Auth::class)) {
        return false;
    }

    if (function_exists('current_user_can')) {
        if (current_user_can('themes.customize') || current_user_can('settings.system')) {
            return true;
        }
    }

    return Auth::instance()->isAdmin();
}

function phinit_customizer_has_advanced_code_acknowledgement(): bool
{
    return phinit_input_string($_POST, 'advanced_code_acknowledged', '', 1) === '1';
}

/**
 * @return array{empty: bool, length: int, sha256: string}
 */
function phinit_get_advanced_code_fingerprint(string $value): array
{
    return [
        'empty' => $value === '',
        'length' => strlen($value),
        'sha256' => substr(hash('sha256', $value), 0, 16),
    ];
}

/**
 * @param array<string, array{before: string, after: string}> $changes
 * @return array<string, array<string, mixed>>
 */
function phinit_summarize_advanced_code_changes(array $changes): array
{
    $summary = [];

    foreach ($changes as $fieldKey => $change) {
        $summary[$fieldKey] = [
            'before' => phinit_get_advanced_code_fingerprint($change['before']),
            'after' => phinit_get_advanced_code_fingerprint($change['after']),
        ];
    }

    return $summary;
}

/**
 * @param array<string, array{before: string, after: string}> $changes
 */
function phinit_log_advanced_code_event(string $action, ThemeCustomizer $customizer, array $changes, string $severity = 'warning'): void
{
    if (!class_exists(AuditLogger::class) || $changes === []) {
        return;
    }

    $fieldNames = implode(', ', array_keys($changes));
    $descriptions = [
        'save' => 'Advanced-Custom-Code aktualisiert: ' . $fieldNames,
        'import' => 'Advanced-Custom-Code aus Import übernommen: ' . $fieldNames,
        'reset' => 'Advanced-Custom-Code auf Standard zurückgesetzt: ' . $fieldNames,
        'denied' => 'Unzulässiger Advanced-Custom-Code-Versuch blockiert: ' . $fieldNames,
    ];

    AuditLogger::instance()->log(
        $action === 'denied' ? AuditLogger::CAT_SECURITY : AuditLogger::CAT_THEME,
        'theme.customizer.advanced_code.' . $action,
        $descriptions[$action] ?? ('Advanced-Custom-Code-Ereignis: ' . $fieldNames),
        'theme',
        null,
        [
            'theme' => $customizer->getTheme(),
            'fields' => array_keys($changes),
            'changes' => phinit_summarize_advanced_code_changes($changes),
        ],
        $severity
    );
}

/**
 * @param array<string, mixed> $fieldConfig
 */
function phinit_normalize_customizer_post_value(string $fieldType, mixed $rawValue, array $fieldConfig): string
{
    $default = $fieldConfig['default'] ?? '';

    if ($fieldType === 'checkbox') {
        return !empty($rawValue) ? '1' : '0';
    }

    if (is_array($rawValue) || is_object($rawValue)) {
        return (string) $default;
    }

    $value = is_string($rawValue) ? trim($rawValue) : trim((string) $rawValue);

    switch ($fieldType) {
        case 'color':
            if ($value === '') {
                return (string) $default;
            }

            return preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value) === 1
                ? $value
                : (string) $default;

        case 'number':
            if ($value === '' || !is_numeric($value)) {
                return (string) $default;
            }

            $number = (float) $value;
            $allowedNumberValues = $fieldConfig['allowed_values'] ?? null;
            if (is_array($allowedNumberValues) && $allowedNumberValues !== []) {
                $allowedValues = array_values(array_filter(
                    array_map(static fn(mixed $allowedValue): ?float => is_numeric((string) $allowedValue) ? (float) $allowedValue : null, $allowedNumberValues),
                    static fn(?float $allowedValue): bool => $allowedValue !== null
                ));

                if ($allowedValues !== []) {
                    $closest = $allowedValues[0];
                    foreach ($allowedValues as $allowedValue) {
                        if (abs($allowedValue - $number) < abs($closest - $number)) {
                            $closest = $allowedValue;
                        }
                    }

                    return (string) (int) round($closest);
                }
            }

            if (isset($fieldConfig['min']) && is_numeric((string) $fieldConfig['min'])) {
                $number = max((float) $fieldConfig['min'], $number);
            }
            if (isset($fieldConfig['max']) && is_numeric((string) $fieldConfig['max'])) {
                $number = min((float) $fieldConfig['max'], $number);
            }

            $step = isset($fieldConfig['step']) && is_numeric((string) $fieldConfig['step'])
                ? (float) $fieldConfig['step']
                : null;
            if ($step !== null && $step >= 1.0) {
                return (string) (int) round($number);
            }

            $formatted = rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
            return $formatted !== '' ? $formatted : (string) $default;

        case 'select':
            $options = $fieldConfig['options'] ?? [];
            if (!is_array($options) || $options === []) {
                return $value;
            }

            $allowedValues = [];
            foreach ($options as $optionKey => $optionValue) {
                if (is_array($optionValue)) {
                    $allowedValues[] = (string) ($optionValue['value'] ?? $optionKey);
                    continue;
                }

                $allowedValues[] = is_int($optionKey) ? (string) $optionValue : (string) $optionKey;
            }

            return in_array($value, $allowedValues, true) ? $value : (string) $default;

        case 'post_picker':
            return ctype_digit($value) ? $value : '';

        case 'widget_order':
            $options = $fieldConfig['options'] ?? [];
            if (!is_array($options) || $options === []) {
                return strip_tags((string) $rawValue);
            }

            $allowedValues = array_map('strval', array_keys($options));
            $tokens = array_values(array_filter(array_map(
                static fn(string $item): string => strtolower(trim($item)),
                preg_split('/[\r\n,;|]+/', (string) $rawValue, -1, PREG_SPLIT_NO_EMPTY) ?: []
            ), static fn(string $item): bool => in_array($item, $allowedValues, true)));
            $tokens = array_values(array_unique(array_merge($tokens, $allowedValues)));

            return implode("\n", $tokens);

        case 'url':
            if ($value === '') {
                return '';
            }

            if (str_starts_with($value, '/')) {
                return str_starts_with($value, '//') ? '' : $value;
            }

            $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
            if ($scheme === 'mailto') {
                $email = preg_replace('/^mailto:/i', '', $value) ?? '';
                return filter_var($email, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $email : (string) $default;
            }

            if (!in_array($scheme, ['http', 'https'], true)) {
                return (string) $default;
            }

            return filter_var($value, FILTER_VALIDATE_URL) ? $value : (string) $default;

        case 'textarea':
            return !empty($fieldConfig['allow_raw'])
                ? (string) $rawValue
                : strip_tags((string) $rawValue);

        default:
            return $value;
    }
}

/**
 * @param array<string, mixed> $config
 * @param array<string, mixed> $importData
 * @return array<string, array<string, string>>
 */
function phinit_normalize_customizer_import_data(array $config, array $importData, ThemeCustomizer $customizer): array
{
    $importTheme = $importData['theme'] ?? null;
    if (is_string($importTheme) && $importTheme !== '' && $importTheme !== $customizer->getTheme()) {
        return [];
    }

    $rawCustomizations = $importData['customizations'] ?? null;
    if (!is_array($rawCustomizations) || $rawCustomizations === []) {
        return [];
    }

    $normalized = [];

    foreach ($rawCustomizations as $categoryKey => $categoryValues) {
        $category = (string) $categoryKey;
        if (!isset($config[$category]['sections']) || !is_array($categoryValues)) {
            continue;
        }

        $categoryConfig = $config[$category]['sections'];
        if (!is_array($categoryConfig)) {
            continue;
        }

        foreach ($categoryValues as $fieldKey => $rawValue) {
            $sourceFieldKey = (string) $fieldKey;
            $normalizedFieldKey = $sourceFieldKey;
            if ($category === 'advanced' && $normalizedFieldKey === 'custom_header_code') {
                $normalizedFieldKey = 'custom_head_code';
            }

            $normalizedFieldKey = phinit_normalize_customizer_alias_key($category, $normalizedFieldKey);

            if ($normalizedFieldKey !== $sourceFieldKey && array_key_exists($normalizedFieldKey, $categoryValues)) {
                continue;
            }

            $fieldConfig = $categoryConfig[$normalizedFieldKey] ?? null;
            if (!is_array($fieldConfig)) {
                continue;
            }

            if ($category === 'advanced' && in_array($normalizedFieldKey, ['custom_css', 'custom_head_code', 'custom_footer_code'], true)) {
                $fieldConfig['allow_raw'] = true;
            }

            $fieldType = (string) ($fieldConfig['type'] ?? 'text');
            $normalized[$category][$normalizedFieldKey] = phinit_normalize_customizer_post_value($fieldType, $rawValue, $fieldConfig);
        }
    }

    return $normalized;
}

/**
 * @param array<string, array<string, string>> $normalizedSettings
 * @return array<string, array{before: string, after: string}>
 */
function phinit_collect_advanced_import_changes(array $normalizedSettings, ThemeCustomizer $customizer): array
{
    $changes = [];

    $advancedSettings = $normalizedSettings['advanced'] ?? null;
    if (!is_array($advancedSettings)) {
        return $changes;
    }

    foreach (phinit_get_advanced_raw_code_fields() as $fieldKey) {
        if (!array_key_exists($fieldKey, $advancedSettings)) {
            continue;
        }

        $before = (string) $customizer->get('advanced', $fieldKey, '');
        $after = (string) $advancedSettings[$fieldKey];
        if ($before === $after) {
            continue;
        }

        $changes[$fieldKey] = [
            'before' => $before,
            'after' => $after,
        ];
    }

    return $changes;
}

/**
 * @return list<string>
 */
function phinit_get_customizer_import_allowed_mime_types(): array
{
    return [
        'application/json',
        'text/json',
        'text/plain',
        'application/octet-stream',
    ];
}

/**
 * @return list<string>
 */
function phinit_get_customizer_import_allowed_top_level_keys(): array
{
    return ['theme', 'exported_at', 'customizations'];
}

function phinit_log_customizer_import_rejection(ThemeCustomizer $customizer, string $reason, array $metadata = []): void
{
    if (!class_exists(AuditLogger::class)) {
        return;
    }

    AuditLogger::instance()->log(
        AuditLogger::CAT_SECURITY,
        'theme.customizer.import.denied',
        'Customizer-Import blockiert: ' . $reason,
        'theme',
        null,
        array_merge([
            'theme' => $customizer->getTheme(),
            'reason' => $reason,
        ], $metadata),
        'warning'
    );
}

/**
 * @param mixed $file
 * @return array{ok: bool, message: string, raw?: string, originalName?: string, mime?: string}
 */
function phinit_validate_customizer_import_upload(mixed $file): array
{
    if (!is_array($file)) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – bitte eine gültige JSON-Datei auswählen.',
        ];
    }

    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        return [
            'ok' => false,
            'message' => 'Datei-Upload fehlgeschlagen oder Datei zu groß (&gt;512 KB).',
        ];
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size >= 524288) {
        return [
            'ok' => false,
            'message' => 'Datei-Upload fehlgeschlagen oder Datei zu groß (&gt;512 KB).',
        ];
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    $originalName = strtolower(trim((string) ($file['name'] ?? '')));
    if ($tmpName === '' || !is_file($tmpName) || $originalName === '' || !str_ends_with($originalName, '.json')) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – bitte eine gültige JSON-Datei auswählen.',
        ];
    }

    if (!is_uploaded_file($tmpName) && PHP_SAPI !== 'cli') {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Upload-Herkunft konnte nicht verifiziert werden.',
        ];
    }

    $mime = '';
    if (function_exists('finfo_open') && function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $detectedMime = finfo_file($finfo, $tmpName);
            finfo_close($finfo);
            $mime = is_string($detectedMime) ? strtolower(trim($detectedMime)) : '';
        }
    }

    if ($mime !== '' && !in_array($mime, phinit_get_customizer_import_allowed_mime_types(), true)) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – der Upload muss eine JSON-Datei sein.',
            'mime' => $mime,
        ];
    }

    $realPath = realpath($tmpName);
    if ($realPath === false || !is_file($realPath) || !is_readable($realPath)) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Upload-Datei konnte nicht verifiziert werden.',
            'mime' => $mime,
        ];
    }

    $stagingDir = rtrim(sys_get_temp_dir(), '\\/');
    $stagingRoot = realpath($stagingDir);
    if ($stagingRoot === false || !is_dir($stagingRoot) || !is_writable($stagingRoot)) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – das sichere Staging-Verzeichnis ist nicht verfügbar.',
            'mime' => $mime,
        ];
    }

    $tempPath = tempnam($stagingRoot, 'cms-phinit-customizer-');
    if (!is_string($tempPath) || $tempPath === '') {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Upload-Datei konnte nicht sicher zwischengespeichert werden.',
            'mime' => $mime,
        ];
    }

    $stagedPath = $tempPath . '.json';
    if (!@rename($tempPath, $stagedPath)) {
        @unlink($tempPath);
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Upload-Datei konnte nicht sicher vorbereitet werden.',
            'mime' => $mime,
        ];
    }

    $copied = PHP_SAPI !== 'cli'
        ? @move_uploaded_file($tmpName, $stagedPath)
        : @copy($realPath, $stagedPath);

    if (!$copied) {
        @unlink($stagedPath);
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Upload-Datei konnte nicht sicher übernommen werden.',
            'mime' => $mime,
        ];
    }

    $stagedRealPath = realpath($stagedPath);
    $normalizedStagingRoot = phinit_normalize_filesystem_path_for_compare($stagingRoot);
    $normalizedStagedPath = $stagedRealPath !== false
        ? phinit_normalize_filesystem_path_for_compare($stagedRealPath)
        : '';

    if ($normalizedStagedPath === '' || !str_starts_with($normalizedStagedPath, $normalizedStagingRoot . '/')) {
        if (is_file($stagedPath)) {
            @unlink($stagedPath);
        }

        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Staging-Datei liegt außerhalb des erlaubten Temp-Bereichs.',
            'mime' => $mime,
        ];
    }

    $stagedSize = filesize($stagedRealPath);
    if (!is_int($stagedSize) && !is_float($stagedSize)) {
        if (is_file($stagedPath)) {
            @unlink($stagedPath);
        }

        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Dateigröße konnte nicht ermittelt werden.',
            'mime' => $mime,
        ];
    }

    $bytes = (int) $stagedSize;
    if ($bytes <= 0 || $bytes >= 524288) {
        if (is_file($stagedPath)) {
            @unlink($stagedPath);
        }

        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die JSON-Datei ist leer oder zu groß.',
            'mime' => $mime,
        ];
    }

    $readHandle = @fopen($stagedRealPath, 'rb');
    try {
        $raw = is_resource($readHandle) ? stream_get_contents($readHandle, $bytes + 1) : false;
    } finally {
        if (is_resource($readHandle)) {
            fclose($readHandle);
        }
        if (is_file($stagedPath)) {
            @unlink($stagedPath);
        }
    }

    if (!is_string($raw) || $raw === '' || strlen($raw) > $bytes) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Upload-Datei konnte nicht sicher gelesen werden.',
            'mime' => $mime,
        ];
    }

    return [
        'ok' => true,
        'message' => '',
        'raw' => $raw,
        'originalName' => $originalName,
        'mime' => $mime,
    ];
}

function phinit_normalize_filesystem_path_for_compare(string $path): string
{
    $normalized = str_replace('\\', '/', trim($path));
    $normalized = rtrim($normalized, '/');

    if (DIRECTORY_SEPARATOR === '\\') {
        $normalized = strtolower($normalized);
    }

    return $normalized;
}

/**
 * @return array{ok: bool, message: string, data?: array<string, mixed>}
 */
function phinit_decode_customizer_import_payload(string $raw, ThemeCustomizer $customizer): array
{
    if (trim($raw) === '') {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die JSON-Datei ist leer.',
        ];
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die JSON-Datei ist syntaktisch ungültig.',
        ];
    }

    $unknownTopLevelKeys = array_values(array_diff(array_keys($data), phinit_get_customizer_import_allowed_top_level_keys()));
    if ($unknownTopLevelKeys !== []) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die JSON-Struktur enthält unbekannte Root-Keys.',
        ];
    }

    $theme = $data['theme'] ?? null;
    if ($theme !== null && (!is_string($theme) || trim($theme) === '' || trim($theme) !== $customizer->getTheme())) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – die Datei gehört nicht zu diesem Theme.',
        ];
    }

    $exportedAt = $data['exported_at'] ?? null;
    if ($exportedAt !== null && (!is_string($exportedAt) || trim($exportedAt) === '' || strtotime($exportedAt) === false)) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – das Exportdatum ist ungültig.',
        ];
    }

    if (!isset($data['customizations']) || !is_array($data['customizations']) || $data['customizations'] === []) {
        return [
            'ok' => false,
            'message' => 'Import fehlgeschlagen – es wurden keine kompatiblen Einstellungen gefunden.',
        ];
    }

    return [
        'ok' => true,
        'message' => '',
        'data' => $data,
    ];
}

function phinit_get_customizer_storage_tab(array $config, string $activeSection, string $fallbackTab): string
{
    if (isset($config[$activeSection]['storageTab']) && is_string($config[$activeSection]['storageTab']) && $config[$activeSection]['storageTab'] !== '') {
        return (string) $config[$activeSection]['storageTab'];
    }

    return $fallbackTab;
}

function phinit_get_customizer_posted_field_value(string $postedSection, string $storageSection, string $fieldKey, string $fieldType): mixed
{
    $postedFieldName = $postedSection . '_' . $fieldKey;
    $storageFieldName = $storageSection . '_' . $fieldKey;

    if ($fieldType === 'checkbox') {
        return isset($_POST[$postedFieldName]) || isset($_POST[$storageFieldName]) ? '1' : '0';
    }

    if (array_key_exists($postedFieldName, $_POST)) {
        $postedValue = $_POST[$postedFieldName];

        return is_scalar($postedValue) ? $postedValue : '';
    }

    if ($storageFieldName !== $postedFieldName && array_key_exists($storageFieldName, $_POST)) {
        $storageValue = $_POST[$storageFieldName];

        return is_scalar($storageValue) ? $storageValue : '';
    }

    return '';
}

function phinit_verify_customizer_csrf_token(mixed $token): bool
{
    $token = is_string($token) ? $token : (string) $token;
    if ($token === '') {
        return false;
    }

    if (function_exists('cms_admin_section_shell_was_csrf_verified')
        && cms_admin_section_shell_was_csrf_verified('admin_theme_editor')) {
        return true;
    }

    $security = Security::instance();

    if ($security->verifyPersistentToken($token, 'admin_theme_editor')) {
        return true;
    }

    return $security->verifyPersistentToken($token, 'phinit_customizer')
        || $security->verifyToken($token, 'phinit_customizer');
}

/**
 * Verarbeitet POST-Aktionen des Phinit-Customizers.
 *
 * @param array<string, mixed> $config
 * @return array{alertMsg: ?string, alertType: string, activeTab: string}
 */
function phinit_handle_customizer_post(array $config, ThemeCustomizer $customizer, string $activeTab): array
{
    $alertMsg = null;
    $alertType = 'success';

    if (phinit_request_method() !== 'POST') {
        return [
            'alertMsg' => $alertMsg,
            'alertType' => $alertType,
            'activeTab' => $activeTab,
        ];
    }

    $postAction = phinit_input_string($_POST, 'action', '', 60);

    if (!phinit_verify_customizer_csrf_token(phinit_input_string($_POST, 'csrf_token', '', 128))) {
        return [
            'alertMsg' => 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.',
            'alertType' => 'danger',
            'activeTab' => $activeTab,
        ];
    }

    if ($postAction === 'reset_theme_tab') {
        $resetTab = phinit_input_string($_POST, 'active_section', $activeTab, 80);
        if (!isset($config[$resetTab])) {
            $resetTab = $activeTab;
        }
        $resetStorageTab = phinit_get_customizer_storage_tab($config, $resetTab, $resetTab);

        $advancedChanges = [];
        if ($resetTab === 'advanced') {
            foreach (phinit_get_advanced_raw_code_fields() as $fieldKey) {
                $before = (string) $customizer->get('advanced', $fieldKey, '');
                $defaultValue = (string) (($config[$resetTab]['sections'][$fieldKey]['default'] ?? ''));
                if ($before === $defaultValue) {
                    continue;
                }

                $advancedChanges[$fieldKey] = [
                    'before' => $before,
                    'after' => $defaultValue,
                ];
            }
        }

        $ok = true;
        foreach ($config[$resetTab]['sections'] as $fieldKey => $fieldConfig) {
            $defaultValue = $fieldConfig['default'] ?? '';
            $storageTab = (string) ($fieldConfig['storageTab'] ?? $resetStorageTab);
            if (is_bool($defaultValue)) {
                $defaultValue = $defaultValue ? '1' : '0';
            }
            if (!$customizer->set($storageTab, (string) $fieldKey, (string) $defaultValue)) {
                $ok = false;
            }
        }

        if ($ok && $advancedChanges !== []) {
            phinit_log_advanced_code_event('reset', $customizer, $advancedChanges);
        }

        return [
            'alertMsg' => $ok
                ? 'Tab &bdquo;' . htmlspecialchars((string) ($config[$resetTab]['title'] ?? $resetTab), ENT_QUOTES) . '&ldquo; auf Standardwerte zurückgesetzt.'
                : 'Einige Felder konnten nicht zurückgesetzt werden.',
            'alertType' => $ok ? 'success' : 'danger',
            'activeTab' => $resetTab,
        ];
    }

    if ($postAction === 'save_theme_options') {
        $saveTab = phinit_input_string($_POST, 'active_section', $activeTab, 80);
        if (!isset($config[$saveTab])) {
            $saveTab = $activeTab;
        }
        $saveStorageTab = phinit_input_string($_POST, 'storage_section', phinit_get_customizer_storage_tab($config, $saveTab, $saveTab), 80);
        if ($saveStorageTab === '' || !isset($config[$saveStorageTab])) {
            $saveStorageTab = phinit_get_customizer_storage_tab($config, $saveTab, $saveTab);
        }

        $advancedChanges = [];
        $ok = true;
        foreach ($config[$saveTab]['sections'] as $fieldKey => $fieldConfig) {
            $fieldType = (string) ($fieldConfig['type'] ?? 'text');
            $storageTab = (string) ($fieldConfig['storageTab'] ?? $saveStorageTab);

            if (phinit_is_advanced_raw_code_field($saveTab, (string) $fieldKey)) {
                $fieldConfig['allow_raw'] = true;
            }

            $rawValue = phinit_get_customizer_posted_field_value($saveTab, $storageTab, (string) $fieldKey, $fieldType);
            $value = phinit_normalize_customizer_post_value($fieldType, $rawValue, is_array($fieldConfig) ? $fieldConfig : []);

            if (phinit_is_advanced_raw_code_field($saveTab, (string) $fieldKey)) {
                $before = (string) $customizer->get($storageTab, (string) $fieldKey, '');
                if ($before !== $value) {
                    $advancedChanges[(string) $fieldKey] = [
                        'before' => $before,
                        'after' => $value,
                    ];
                }
            }
        }

        if ($advancedChanges !== []) {
            if (!phinit_customizer_can_manage_advanced_code()) {
                phinit_log_advanced_code_event('denied', $customizer, $advancedChanges, 'warning');

                return [
                    'alertMsg' => 'Der privilegierte Custom-Code-Bereich darf nur mit passender Berechtigung geändert werden.',
                    'alertType' => 'danger',
                    'activeTab' => $saveTab,
                ];
            }

            if (!phinit_customizer_has_advanced_code_acknowledgement()) {
                phinit_log_advanced_code_event('denied', $customizer, $advancedChanges, 'warning');

                return [
                    'alertMsg' => 'Bitte bestätige vor dem Speichern, dass eigener Head-/Footer-Code bzw. CSS sofort live wirksam wird.',
                    'alertType' => 'danger',
                    'activeTab' => $saveTab,
                ];
            }
        }

        foreach ($config[$saveTab]['sections'] as $fieldKey => $fieldConfig) {
            $fieldType = (string) ($fieldConfig['type'] ?? 'text');
            $storageTab = (string) ($fieldConfig['storageTab'] ?? $saveStorageTab);

            if (phinit_is_advanced_raw_code_field($saveTab, (string) $fieldKey)) {
                $fieldConfig['allow_raw'] = true;
            }

            $rawValue = phinit_get_customizer_posted_field_value($saveTab, $storageTab, (string) $fieldKey, $fieldType);
            $value = phinit_normalize_customizer_post_value($fieldType, $rawValue, is_array($fieldConfig) ? $fieldConfig : []);

            if (!$customizer->set($storageTab, (string) $fieldKey, $value)) {
                $ok = false;
            }
        }

        if ($ok && $advancedChanges !== []) {
            phinit_log_advanced_code_event('save', $customizer, $advancedChanges);
        }

        return [
            'alertMsg' => $ok
                ? 'Einstellungen für &bdquo;' . htmlspecialchars((string) ($config[$saveTab]['title'] ?? $saveTab), ENT_QUOTES) . '&ldquo; gespeichert.'
                : 'Einige Einstellungen konnten nicht gespeichert werden.',
            'alertType' => $ok ? 'success' : 'danger',
            'activeTab' => $saveTab,
        ];
    }

    if ($postAction === 'export_settings') {
        $export = json_encode($customizer->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($export === false) {
            return [
                'alertMsg' => 'Export fehlgeschlagen.',
                'alertType' => 'danger',
                'activeTab' => $activeTab,
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="cms-phinit-customizer-' . date('Y-m-d') . '.json"');
        header('Content-Length: ' . strlen($export));
        echo $export;
        exit;
    }

    if ($postAction === 'import_settings') {
        $file = $_FILES['import_file'] ?? null;
        $uploadValidation = phinit_validate_customizer_import_upload($file);
        if (!($uploadValidation['ok'] ?? false)) {
            phinit_log_customizer_import_rejection($customizer, 'invalid_upload', [
                'mime' => (string) ($uploadValidation['mime'] ?? ''),
                'filename' => strtolower(trim((string) (($file['name'] ?? '') ?: ''))),
            ]);

            return [
                'alertMsg' => (string) ($uploadValidation['message'] ?? 'Import fehlgeschlagen.'),
                'alertType' => 'danger',
                'activeTab' => $activeTab,
            ];
        }

        $decodedImport = phinit_decode_customizer_import_payload((string) ($uploadValidation['raw'] ?? ''), $customizer);
        if (!($decodedImport['ok'] ?? false)) {
            phinit_log_customizer_import_rejection($customizer, 'invalid_payload', [
                'mime' => (string) ($uploadValidation['mime'] ?? ''),
                'filename' => (string) ($uploadValidation['originalName'] ?? ''),
            ]);

            return [
                'alertMsg' => (string) ($decodedImport['message'] ?? 'Import fehlgeschlagen.'),
                'alertType' => 'danger',
                'activeTab' => $activeTab,
            ];
        }

        $data = $decodedImport['data'] ?? [];
        if (is_array($data)) {
            $normalizedImport = is_array($data)
                ? phinit_normalize_customizer_import_data($config, $data, $customizer)
                : [];
            $advancedImportChanges = phinit_collect_advanced_import_changes($normalizedImport, $customizer);

            if ($advancedImportChanges !== []) {
                if (!phinit_customizer_can_manage_advanced_code()) {
                    phinit_log_advanced_code_event('denied', $customizer, $advancedImportChanges, 'warning');

                    return [
                        'alertMsg' => 'Der Import enthält privilegierten Custom-Code und wurde ohne passende Berechtigung blockiert.',
                        'alertType' => 'danger',
                        'activeTab' => $activeTab,
                    ];
                }

                if (!phinit_customizer_has_advanced_code_acknowledgement()) {
                    phinit_log_advanced_code_event('denied', $customizer, $advancedImportChanges, 'warning');

                    return [
                        'alertMsg' => 'Bitte bestätige vor dem Import, dass enthaltenes Custom-CSS bzw. Head-/Footer-Code sofort live wirksam wird.',
                        'alertType' => 'danger',
                        'activeTab' => $activeTab,
                    ];
                }
            }

            $importOk = $normalizedImport !== [] && $customizer->setMultiple($normalizedImport);

            if ($importOk && $advancedImportChanges !== []) {
                phinit_log_advanced_code_event('import', $customizer, $advancedImportChanges);
            }

            return [
                'alertMsg' => $importOk
                    ? 'Einstellungen erfolgreich importiert.'
                    : 'Import fehlgeschlagen – ungültige, leere oder nicht kompatible JSON-Datei.',
                'alertType' => $importOk ? 'success' : 'danger',
                'activeTab' => $activeTab,
            ];
        }

        return [
            'alertMsg' => 'Import fehlgeschlagen – ungültige, leere oder nicht kompatible JSON-Datei.',
            'alertType' => 'danger',
            'activeTab' => $activeTab,
        ];
    }

    return [
        'alertMsg' => $alertMsg,
        'alertType' => $alertType,
        'activeTab' => $activeTab,
    ];
}
