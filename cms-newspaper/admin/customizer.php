<?php
/**
 * CMS Newspaper Theme – Customizer (Admin)
 *
 * Admin UI for all groups defined in theme.json → customization.
 * Loaded by CMS/admin/theme-editor.php (standalone or embedInAdminLayout).
 *
 * @package CmsNewspaper_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Auth;
use CMS\Security;
use CMS\Services\ThemeCustomizer;

$embedInAdminLayout = !empty($embedInAdminLayout);

$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$attr = static function (string $value): string {
    if (function_exists('esc_attr')) {
        return esc_attr($value);
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};

$sanitizeKey = static function (string $value): string {
    if (function_exists('sanitize_key')) {
        return sanitize_key($value);
    }
    return strtolower(preg_replace('/[^a-z0-9_\-]/', '', $value) ?? '');
};

function newspaper_customizer_normalize_color(mixed $value, string $fallback = '#000000'): string
{
    $candidate = trim((string) $value);
    return preg_match('/^#[0-9a-f]{6}$/i', $candidate) === 1 ? strtolower($candidate) : $fallback;
}

function newspaper_customizer_normalize_number(
    mixed $value,
    int|float $default = 0,
    ?float $min = null,
    ?float $max = null
): string {
    $number = is_numeric($value) ? (float) $value : (float) $default;
    if ($min !== null) {
        $number = max($min, $number);
    }
    if ($max !== null) {
        $number = min($max, $number);
    }

    if (abs($number - round($number)) < 0.0001) {
        return (string) (int) round($number);
    }

    return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
}

function newspaper_customizer_normalize_url(mixed $value, bool $allowRelative = true): string
{
    $candidate = trim((string) $value);
    if ($candidate === '') {
        return '';
    }

    if ($allowRelative && str_starts_with($candidate, '/')) {
        return $candidate;
    }

    if (function_exists('esc_url_raw')) {
        return esc_url_raw($candidate);
    }

    return filter_var($candidate, FILTER_SANITIZE_URL) ?: '';
}

function newspaper_customizer_sanitize_textarea(string $value): string
{
    if (function_exists('sanitize_textarea_field')) {
        return sanitize_textarea_field($value);
    }

    return trim(strip_tags($value));
}

function newspaper_customizer_sanitize_text(string $value): string
{
    if (function_exists('sanitize_text_field')) {
        return sanitize_text_field($value);
    }

    return trim(strip_tags($value));
}

function newspaper_customizer_normalize_options(mixed $options): array
{
    if (!is_array($options)) {
        return [];
    }

    $normalized = [];
    foreach ($options as $optionKey => $optionValue) {
        if (is_array($optionValue)) {
            $value = (string) ($optionValue['value'] ?? $optionKey);
            $label = (string) ($optionValue['label'] ?? $value);
            $normalized[$value] = $label;
            continue;
        }

        if (is_int($optionKey)) {
            $value = (string) $optionValue;
            $normalized[$value] = $value;
            continue;
        }

        $normalized[(string) $optionKey] = (string) $optionValue;
    }

    return $normalized;
}

function newspaper_customizer_normalize_field(array $fieldConfig): array
{
    $normalized = $fieldConfig;
    $type = (string) ($normalized['type'] ?? 'text');

    $normalized['type'] = match ($type) {
        'toggle' => 'checkbox',
        'image'  => 'image_upload',
        default  => $type,
    };

    if (isset($normalized['options'])) {
        $normalized['options'] = newspaper_customizer_normalize_options($normalized['options']);
    }

    return $normalized;
}

function newspaper_customizer_normalize_value(string $tab, string $fieldKey, array $fieldConfig, mixed $value): string
{
    $type = (string) ($fieldConfig['type'] ?? 'text');
    $default = $fieldConfig['default'] ?? '';
    $min = isset($fieldConfig['min']) && is_numeric($fieldConfig['min']) ? (float) $fieldConfig['min'] : null;
    $max = isset($fieldConfig['max']) && is_numeric($fieldConfig['max']) ? (float) $fieldConfig['max'] : null;

    return match ($type) {
        'checkbox' => !empty($value) ? '1' : '0',
        'color' => newspaper_customizer_normalize_color($value, is_string($default) ? $default : '#000000'),
        'number' => newspaper_customizer_normalize_number(
            $value,
            is_numeric($default) ? (float) $default : 0.0,
            $min,
            $max
        ),
        'select' => array_key_exists((string) $value, $fieldConfig['options'] ?? [])
            ? (string) $value
            : (string) $default,
        'textarea' => match (true) {
            $tab === 'advanced' && $fieldKey === 'custom_css' =>
                trim((string) preg_replace('/<\/?style[^>]*>/i', '', (string) $value)),
            default => newspaper_customizer_sanitize_textarea((string) $value),
        },
        'image_upload' => newspaper_customizer_normalize_url($value),
        default => match (true) {
            $fieldKey === 'logo_url',
            str_ends_with($fieldKey, '_url') => newspaper_customizer_normalize_url($value),
            default => newspaper_customizer_sanitize_text((string) $value),
        },
    };
}

/**
 * @return array<string, array{title: string, sections: array<string, array<string, mixed>>}>
 */
function newspaper_build_customizer_config(): array
{
    $customization = [];

    try {
        $opts = ThemeCustomizer::instance()->getCustomizationOptions();
        if (is_array($opts) && $opts !== []) {
            $customization = $opts;
        }
    } catch (\Throwable) {
        $customization = [];
    }

    if ($customization === []) {
        $jsonFile = dirname(__DIR__) . '/theme.json';
        if (is_file($jsonFile)) {
            $decoded = json_decode((string) file_get_contents($jsonFile), true);
            if (is_array($decoded['customization'] ?? null)) {
                $customization = $decoded['customization'];
            }
        }
    }

    $config = [];
    foreach ($customization as $categoryKey => $categoryConfig) {
        if (!is_array($categoryConfig)) {
            continue;
        }

        $settings = $categoryConfig['settings'] ?? null;
        if (!is_array($settings) || $settings === []) {
            continue;
        }

        $sections = [];
        foreach ($settings as $settingKey => $settingConfig) {
            if (!is_array($settingConfig)) {
                continue;
            }
            $sections[(string) $settingKey] = newspaper_customizer_normalize_field($settingConfig);
        }

        if ($sections === []) {
            continue;
        }

        $label = trim((string) ($categoryConfig['label'] ?? $categoryConfig['title'] ?? $categoryKey));
        $config[(string) $categoryKey] = [
            'title'    => $label !== '' ? $label : (string) $categoryKey,
            'sections' => $sections,
        ];
    }

    return $config;
}

function newspaper_customizer_verify_csrf(string $token, string $action = 'theme_customizer'): bool
{
    if (function_exists('cms_admin_section_shell_was_csrf_verified')
        && cms_admin_section_shell_was_csrf_verified($action)
    ) {
        return true;
    }

    return Security::instance()->verifyToken($token, $action);
}

/**
 * @param array<string, mixed> $field
 */
function newspaper_customizer_render_field(
    string $activeTab,
    string $fieldKey,
    array $field,
    mixed $val,
    callable $esc,
    callable $attr
): void {
    $inputId     = "field_{$activeTab}_{$fieldKey}";
    $textInputId = $inputId . '_text';
    $inputName   = "{$activeTab}_{$fieldKey}";
    $type        = (string) ($field['type'] ?? 'text');
    ?>
    <div class="form-group">
        <label for="<?php echo $attr($inputId); ?>" class="form-label">
            <?php echo $esc($field['label'] ?? $fieldKey); ?>
        </label>

        <?php if ($type === 'textarea') : ?>
            <textarea id="<?php echo $attr($inputId); ?>" name="<?php echo $attr($inputName); ?>"
                      class="form-control" rows="4"><?php echo $esc((string) $val); ?></textarea>

        <?php elseif ($type === 'checkbox') : ?>
            <?php $checked = filter_var($val, FILTER_VALIDATE_BOOLEAN); ?>
            <div class="customizer-checkbox-row">
                <input type="checkbox" id="<?php echo $attr($inputId); ?>"
                       name="<?php echo $attr($inputName); ?>" value="1"
                    <?php echo $checked ? 'checked' : ''; ?>>
                <label for="<?php echo $attr($inputId); ?>" class="customizer-checkbox-label">Aktivieren</label>
            </div>

        <?php elseif ($type === 'select') : ?>
            <select id="<?php echo $attr($inputId); ?>" name="<?php echo $attr($inputName); ?>" class="form-control">
                <?php foreach (($field['options'] ?? []) as $optVal => $optLabel) : ?>
                    <option value="<?php echo $esc((string) $optVal); ?>"
                        <?php echo (string) $val === (string) $optVal ? 'selected' : ''; ?>>
                        <?php echo $esc($optLabel); ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($type === 'image_upload') : ?>
            <?php $previewUrl = trim((string) $val); ?>
            <div class="customizer-control-row-stack" data-customizer-logo-group>
                <div class="customizer-logo-preview" data-customizer-logo-preview>
                    <?php if ($previewUrl !== '') : ?>
                        <img src="<?php echo $esc($previewUrl); ?>" alt="" class="customizer-logo-preview-image">
                    <?php else : ?>
                        <span class="customizer-logo-preview-placeholder">🖼️ Noch kein Bild ausgewählt</span>
                    <?php endif; ?>
                </div>
                <div class="customizer-control-row-inline">
                    <label class="customizer-upload-button">
                        📁 Bild hochladen
                        <input type="file" name="logo_upload_file" accept="image/*"
                               class="customizer-file-input" data-customizer-logo-upload>
                    </label>
                    <span class="customizer-upload-hint">oder URL eingeben:</span>
                </div>
                <input type="text" id="<?php echo $attr($inputId); ?>" name="<?php echo $attr($inputName); ?>"
                       value="<?php echo $attr($previewUrl); ?>" class="form-control"
                       placeholder="https://… oder /pfad/zum/bild" data-customizer-logo-url>
            </div>

        <?php elseif ($type === 'color') : ?>
            <div class="customizer-control-row">
                <input type="color" id="<?php echo $attr($inputId); ?>" name="<?php echo $attr($inputName); ?>"
                       value="<?php echo $esc((string) $val); ?>"
                       class="customizer-color-picker"
                       data-customizer-color-picker
                       data-sync-text="<?php echo $attr($textInputId); ?>">
                <input type="text" id="<?php echo $attr($textInputId); ?>"
                       value="<?php echo $esc((string) $val); ?>"
                       class="form-control customizer-color-text"
                       data-customizer-color-text
                       data-sync-picker="<?php echo $attr($inputId); ?>">
            </div>

        <?php elseif ($type === 'number') : ?>
            <input type="number" id="<?php echo $attr($inputId); ?>" name="<?php echo $attr($inputName); ?>"
                   value="<?php echo $esc((string) $val); ?>"
                   class="form-control customizer-number-input"
                <?php if (isset($field['min'])) : ?> min="<?php echo $esc((string) $field['min']); ?>"<?php endif; ?>
                <?php if (isset($field['max'])) : ?> max="<?php echo $esc((string) $field['max']); ?>"<?php endif; ?>
                <?php if (isset($field['step'])) : ?> step="<?php echo $esc((string) $field['step']); ?>"<?php endif; ?>>

        <?php else : ?>
            <input type="<?php echo $esc($type === 'url' ? 'url' : 'text'); ?>"
                   id="<?php echo $attr($inputId); ?>" name="<?php echo $attr($inputName); ?>"
                   value="<?php echo $esc((string) $val); ?>"
                   class="form-control">
        <?php endif; ?>

        <?php if (!empty($field['description'])) : ?>
            <small class="form-text"><?php echo $esc($field['description']); ?></small>
        <?php endif; ?>
    </div>
    <?php
}

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

// Admin sidebar helpers
$sidebarPaths = [
    (defined('ABSPATH') ? rtrim(ABSPATH, '/\\') : '') . '/admin/partials/admin-menu.php',
    dirname(__DIR__, 3) . '/CMS/admin/partials/admin-menu.php',
    dirname(__DIR__, 2) . '/admin/partials/admin-menu.php',
];
foreach ($sidebarPaths as $path) {
    if ($path !== '' && is_file($path)) {
        require_once $path;
        break;
    }
}
if (!function_exists('renderAdminSidebar')) {
    function renderAdminSidebar(string $slug): void
    {
        echo '<!-- Sidebar fallback: ' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . ' -->';
    }
}
if (!function_exists('renderAdminSidebarStyles')) {
    function renderAdminSidebarStyles(): void {}
}

$config = newspaper_build_customizer_config();

$customizer = ThemeCustomizer::instance();
if (class_exists('\CMS\ThemeManager')) {
    try {
        $customizer->setTheme(\CMS\ThemeManager::instance()->getActiveThemeSlug());
    } catch (\Throwable) {
        $customizer->setTheme('cms-newspaper');
    }
}

$defaultTab = array_key_first($config) ?: 'colors';
$activeTab  = $sanitizeKey((string) ($_GET['tab'] ?? $defaultTab));
if (!isset($config[$activeTab])) {
    $activeTab = $defaultTab;
}

$success = null;
$error   = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'reset_theme_tab') {
    if (!newspaper_customizer_verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        $resetTab = $sanitizeKey((string) ($_POST['active_section'] ?? $activeTab));
        if (!isset($config[$resetTab])) {
            $resetTab = $activeTab;
        }
        $resetFailed = false;
        foreach ($config[$resetTab]['sections'] as $fieldKey => $fieldConfig) {
            $default = $fieldConfig['default'] ?? '';
            if (is_bool($default)) {
                $default = $default ? '1' : '0';
            }
            if (!$customizer->set($resetTab, $fieldKey, (string) $default)) {
                $resetFailed = true;
            }
        }
        if ($resetFailed) {
            $error = 'Einstellungen konnten nicht zurückgesetzt werden.';
        } else {
            $success = 'Einstellungen für „' . htmlspecialchars($config[$resetTab]['title']) . '“ auf Standardwerte zurückgesetzt.';
        }
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'save_theme_options') {
    if (!newspaper_customizer_verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        if (!empty($_FILES['logo_upload_file']['tmp_name'])) {
            $allowedExts  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowedMimes = [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
                'gif'  => ['image/gif'],
                'webp' => ['image/webp'],
            ];
            $fileExt = strtolower(pathinfo((string) $_FILES['logo_upload_file']['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExts, true)) {
                $error = 'Ungültiges Dateiformat. Erlaubt: JPG, PNG, GIF, WebP.';
            } elseif (!is_uploaded_file((string) $_FILES['logo_upload_file']['tmp_name'])) {
                $error = 'Ungültiger Upload erkannt.';
            } elseif (((int) ($_FILES['logo_upload_file']['size'] ?? 0)) > (2 * 1024 * 1024)) {
                $error = 'Logo-Datei ist zu groß (max. 2 MB).';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = $finfo !== false
                    ? (string) finfo_file($finfo, (string) $_FILES['logo_upload_file']['tmp_name'])
                    : '';
                if ($finfo !== false) {
                    finfo_close($finfo);
                }
                if (!in_array($mime, $allowedMimes[$fileExt] ?? [], true)) {
                    $error = 'Ungültiger Dateityp. Bitte JPG, PNG, GIF oder WebP hochladen.';
                }
            }

            if (!$error && defined('UPLOAD_PATH') && defined('UPLOAD_URL')) {
                $uploadDir = rtrim((string) UPLOAD_PATH, '/\\') . '/theme-logos';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName = 'logo-' . time() . '.' . $fileExt;
                $destPath    = $uploadDir . '/' . $newFileName;
                if (move_uploaded_file((string) $_FILES['logo_upload_file']['tmp_name'], $destPath)) {
                    $customizer->set('header', 'logo_url', rtrim((string) UPLOAD_URL, '/') . '/theme-logos/' . $newFileName);
                } else {
                    $error = 'Logo-Upload fehlgeschlagen (Schreibrechte prüfen).';
                }
            }
        }

        if (!$error) {
            $saveTab = $sanitizeKey((string) ($_POST['active_section'] ?? $activeTab));
            if (!isset($config[$saveTab])) {
                $saveTab = $activeTab;
            }
            $saveFailed = false;
            foreach ($config[$saveTab]['sections'] as $fieldKey => $fieldConfig) {
                $inputName = "{$saveTab}_{$fieldKey}";
                $type      = (string) ($fieldConfig['type'] ?? 'text');

                if ($saveTab === 'header' && $fieldKey === 'logo_url') {
                    $postVal = (string) ($_POST[$inputName] ?? '');
                    if ($postVal !== '') {
                        if (!$customizer->set(
                            $saveTab,
                            $fieldKey,
                            newspaper_customizer_normalize_value($saveTab, $fieldKey, $fieldConfig, $postVal)
                        )) {
                            $saveFailed = true;
                        }
                    }
                    continue;
                }

                if ($type === 'checkbox') {
                    $value = isset($_POST[$inputName]) ? '1' : '0';
                } else {
                    $value = $_POST[$inputName] ?? '';
                }

                $normalized = newspaper_customizer_normalize_value($saveTab, $fieldKey, $fieldConfig, $value);
                if (!$customizer->set($saveTab, $fieldKey, $normalized)) {
                    $saveFailed = true;
                }
            }

            if ($saveFailed) {
                $error = 'Einstellungen konnten nicht gespeichert werden.';
            } else {
                $success = 'Einstellungen für „' . htmlspecialchars($config[$saveTab]['title']) . '“ gespeichert.';
            }
        }
    }
}

$csrfToken = Security::instance()->generateToken('theme_customizer');

$coreMainCssUrl = function_exists('cms_asset_url')
    ? cms_asset_url('css/main.css')
    : SITE_URL . '/assets/css/main.css';
$coreAdminCssUrl = function_exists('cms_asset_url')
    ? cms_asset_url('css/admin.css')
    : SITE_URL . '/assets/css/admin.css';
$coreAdminJsUrl = function_exists('cms_asset_url')
    ? cms_asset_url('js/admin.js')
    : SITE_URL . '/assets/js/admin.js';

$themeUrl = class_exists('\CMS\ThemeManager')
    ? rtrim((string) \CMS\ThemeManager::instance()->getThemeUrl('cms-newspaper'), '/')
    : rtrim(SITE_URL, '/') . '/themes/cms-newspaper';

$customizerCssFile = dirname(__DIR__) . '/css/customizer-admin.css';
$customizerJsFile  = dirname(__DIR__) . '/js/customizer-admin.js';
$customizerCssUrl  = is_file($customizerCssFile)
    ? $themeUrl . '/css/customizer-admin.css?v=' . rawurlencode((string) filemtime($customizerCssFile))
    : '';
$customizerJsUrl = is_file($customizerJsFile)
    ? $themeUrl . '/js/customizer-admin.js?v=' . rawurlencode((string) filemtime($customizerJsFile))
    : '';

$siteName = defined('SITE_NAME') ? (string) SITE_NAME : 'CMS Newspaper';

if (!$embedInAdminLayout) :
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Customizer – <?php echo $esc($siteName); ?></title>
    <link rel="stylesheet" href="<?php echo $esc($coreMainCssUrl); ?>">
    <link rel="stylesheet" href="<?php echo $esc($coreAdminCssUrl); ?>">
    <?php if ($customizerCssUrl !== '') : ?>
        <link rel="stylesheet" href="<?php echo $esc($customizerCssUrl); ?>">
    <?php endif; ?>
    <?php renderAdminSidebarStyles(); ?>
</head>
<body class="admin-body">
    <?php renderAdminSidebar('theme-customizer'); ?>
<?php else : ?>
    <?php if ($customizerCssUrl !== '') : ?>
        <link rel="stylesheet" href="<?php echo $esc($customizerCssUrl); ?>">
    <?php endif; ?>
<?php endif; ?>

    <div class="admin-content">
        <div class="admin-page-header">
            <div>
                <h2>🗞️ Theme Customizer</h2>
                <p>CMS Newspaper – Farben, Typografie, Layout, Header, Footer, Hero und Sektionen.</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo $esc(SITE_URL); ?>/" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">🌐 Seite ansehen</a>
            </div>
        </div>

        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error) : ?>
            <div class="alert alert-error"><?php echo $esc($error); ?></div>
        <?php endif; ?>

        <?php if ($config === []) : ?>
            <div class="alert alert-error">Keine Customizer-Gruppen in theme.json gefunden.</div>
        <?php else : ?>
        <form id="customizer-form" method="POST" action="?tab=<?php echo rawurlencode($activeTab); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_theme_options">
            <input type="hidden" name="active_section" value="<?php echo $attr($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $attr($csrfToken); ?>">

            <div class="customizer-layout">
                <nav class="customizer-nav" aria-label="Customizer-Bereiche">
                    <?php foreach ($config as $key => $tab) : ?>
                        <a href="?tab=<?php echo rawurlencode((string) $key); ?>"
                           class="<?php echo $activeTab === $key ? 'active' : ''; ?>">
                            <?php echo $esc($tab['title']); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="customizer-content">
                    <?php if (isset($config[$activeTab])) :
                        $currentSection = $config[$activeTab];
                        ?>
                    <div class="admin-card">
                        <h3><?php echo $esc($currentSection['title']); ?></h3>
                        <?php foreach ($currentSection['sections'] as $fieldKey => $field) :
                            $val = $customizer->get($activeTab, $fieldKey, $field['default'] ?? '');
                            newspaper_customizer_render_field($activeTab, $fieldKey, $field, $val, $esc, $attr);
                        endforeach; ?>
                    </div>

                    <div class="admin-card customizer-sticky-card">
                        <div class="form-actions customizer-form-actions">
                            <button type="submit" class="btn btn-primary">💾 Einstellungen speichern</button>
                            <button type="button" class="btn btn-secondary"
                                    data-customizer-reset-open
                                    title="Tab auf Standardwerte zurücksetzen">
                                ↺ Auf Standardwerte zurücksetzen
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <form id="reset-form" method="POST" action="?tab=<?php echo rawurlencode($activeTab); ?>" class="customizer-hidden">
            <input type="hidden" name="action" value="reset_theme_tab">
            <input type="hidden" name="active_section" value="<?php echo $attr($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $attr($csrfToken); ?>">
        </form>
        <?php endif; ?>
    </div>

    <div id="confirm-reset-modal" class="modal customizer-reset-modal" hidden aria-hidden="true">
        <div class="modal-content customizer-reset-dialog">
            <div class="modal-header">
                <h3>⚠️ Einstellungen zurücksetzen?</h3>
                <button class="modal-close" type="button" data-customizer-reset-close>&times;</button>
            </div>
            <div class="modal-body">
                <p>Alle Einstellungen dieses Tabs werden auf die <strong>Standardwerte</strong> aus theme.json zurückgesetzt.</p>
                <p class="customizer-reset-note">Bereits gespeicherte Anpassungen gehen für diesen Bereich verloren.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-customizer-reset-close">Abbrechen</button>
                <button type="button" class="btn btn-danger" data-customizer-reset-confirm">↺ Zurücksetzen</button>
            </div>
        </div>
    </div>

<?php if (!$embedInAdminLayout) : ?>
    <script src="<?php echo $esc($coreAdminJsUrl); ?>"></script>
<?php endif; ?>
<?php if ($customizerJsUrl !== '') : ?>
    <script src="<?php echo $esc($customizerJsUrl); ?>"></script>
<?php endif; ?>
<?php if (!$embedInAdminLayout) : ?>
</body>
</html>
<?php endif; ?>
