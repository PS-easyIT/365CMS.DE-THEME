<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Security;
use CMS\Services\ThemeCustomizer;

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

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return [
            'alertMsg' => $alertMsg,
            'alertType' => $alertType,
            'activeTab' => $activeTab,
        ];
    }

    $postAction = (string) ($_POST['action'] ?? '');

    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'phinit_customizer')) {
        return [
            'alertMsg' => 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.',
            'alertType' => 'danger',
            'activeTab' => $activeTab,
        ];
    }

    if ($postAction === 'reset_theme_tab') {
        $resetTab = (string) ($_POST['active_section'] ?? $activeTab);
        if (!isset($config[$resetTab])) {
            $resetTab = $activeTab;
        }

        $ok = true;
        foreach ($config[$resetTab]['sections'] as $fieldKey => $fieldConfig) {
            $defaultValue = $fieldConfig['default'] ?? '';
            if (is_bool($defaultValue)) {
                $defaultValue = $defaultValue ? '1' : '0';
            }
            if (!$customizer->set($resetTab, (string) $fieldKey, (string) $defaultValue)) {
                $ok = false;
            }
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
        $saveTab = (string) ($_POST['active_section'] ?? $activeTab);
        if (!isset($config[$saveTab])) {
            $saveTab = $activeTab;
        }

        $ok = true;
        foreach ($config[$saveTab]['sections'] as $fieldKey => $fieldConfig) {
            $fieldName = $saveTab . '_' . $fieldKey;
            $fieldType = (string) ($fieldConfig['type'] ?? 'text');

            if ($fieldType === 'checkbox') {
                $value = isset($_POST[$fieldName]) ? '1' : '0';
            } elseif ($fieldType === 'textarea' && !in_array($saveTab, ['advanced'], true)) {
                $value = strip_tags((string) ($_POST[$fieldName] ?? ''));
            } else {
                $value = (string) ($_POST[$fieldName] ?? '');
            }

            if (!$customizer->set($saveTab, (string) $fieldKey, $value)) {
                $ok = false;
            }
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
        if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && (($file['size'] ?? 0) < 524288)) {
            $tmpName = (string) ($file['tmp_name'] ?? '');
            $raw = $tmpName !== '' ? file_get_contents($tmpName) : false;
            $data = json_decode($raw ?: '', true);
            $importOk = is_array($data) && $customizer->import($data);

            return [
                'alertMsg' => $importOk
                    ? 'Einstellungen erfolgreich importiert.'
                    : 'Import fehlgeschlagen – ungültige JSON-Datei.',
                'alertType' => $importOk ? 'success' : 'danger',
                'activeTab' => $activeTab,
            ];
        }

        return [
            'alertMsg' => 'Datei-Upload fehlgeschlagen oder Datei zu groß (&gt;512 KB).',
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
