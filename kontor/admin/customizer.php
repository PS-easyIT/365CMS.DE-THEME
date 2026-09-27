<?php
/**
 * Kontor – Theme-Customizer (Admin)
 *
 * Rendert alle Gruppen aus theme.json → customization als Tabs und speichert die
 * Werte über den ThemeCustomizer-Service. Wird vom Theme-Editor (/admin/theme-editor)
 * in das Admin-Layout eingebettet; Sidebar, Admin-CSS/-JS und CSP-Runtime liefert der Core.
 *
 * @package Kontor_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Auth;
use CMS\Security;
use CMS\Services\ThemeCustomizer;

$embedInAdminLayout = !empty($embedInAdminLayout);
$esc = static fn(mixed $value): string => htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');

if (!function_exists('kontor_customizer_normalize_url')) {
    /** Erlaubt relative Pfade, Anker, http(s), mailto: und tel: – alles andere wird verworfen. */
    function kontor_customizer_normalize_url(mixed $value): string
    {
        $candidate = trim(is_scalar($value) ? (string) $value : '');
        if ($candidate === '' || preg_match('/[\x00-\x1F\x7F<>"]/', $candidate) === 1 || str_starts_with($candidate, '//')) {
            return '';
        }
        if (str_starts_with($candidate, '/') || str_starts_with($candidate, '#')) {
            return $candidate;
        }
        if (preg_match('/^(?:mailto:[^\s@]+@[^\s@]+|tel:\+?[0-9][0-9\s().\/-]{2,31})$/i', $candidate) === 1) {
            return $candidate;
        }
        $scheme = strtolower((string) parse_url($candidate, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) && filter_var($candidate, FILTER_VALIDATE_URL) !== false ? $candidate : '';
    }
}

if (!function_exists('kontor_customizer_normalize_field')) {
    /** @param array<string, mixed> $field */
    function kontor_customizer_normalize_field(array $field): array
    {
        $type = (string) ($field['type'] ?? 'text');
        $field['type'] = match ($type) {
            'toggle' => 'checkbox',
            'image', 'image_upload' => 'image',
            'url' => 'url',
            default => $type,
        };

        if (isset($field['options']) && is_array($field['options'])) {
            $options = [];
            foreach ($field['options'] as $key => $label) {
                if (is_array($label)) {
                    $value = (string) ($label['value'] ?? $key);
                    $options[$value] = (string) ($label['label'] ?? $value);
                } else {
                    $options[is_int($key) ? (string) $label : (string) $key] = (string) $label;
                }
            }
            $field['options'] = $options;
        }

        return $field;
    }
}

if (!function_exists('kontor_customizer_normalize_value')) {
    /** @param array<string, mixed> $field */
    function kontor_customizer_normalize_value(string $group, string $key, array $field, mixed $value): string
    {
        $type = (string) ($field['type'] ?? 'text');
        $default = $field['default'] ?? '';
        $raw = is_scalar($value) ? (string) $value : '';

        switch ($type) {
            case 'checkbox':
                return $raw !== '' && $raw !== '0' ? '1' : '0';

            case 'color':
                $candidate = strtolower(trim($raw));

                return preg_match('/^#[0-9a-f]{6}$/', $candidate) === 1 ? $candidate : (string) $default;

            case 'number':
                $number = is_numeric($raw) ? (float) $raw : (float) (is_numeric($default) ? $default : 0);
                if (isset($field['min']) && is_numeric($field['min'])) {
                    $number = max((float) $field['min'], $number);
                }
                if (isset($field['max']) && is_numeric($field['max'])) {
                    $number = min((float) $field['max'], $number);
                }

                return abs($number - round($number)) < 0.00001
                    ? (string) (int) round($number)
                    : rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');

            case 'select':
                $options = is_array($field['options'] ?? null) ? $field['options'] : [];

                return array_key_exists($raw, $options) ? $raw : (string) $default;

            case 'image':
            case 'url':
                return kontor_customizer_normalize_url($raw);

            case 'textarea':
                if ($group === 'advanced' && $key === 'custom_css') {
                    return trim((string) preg_replace('#<\s*/?\s*style\b[^>]*>#i', '', $raw));
                }

                return trim(strip_tags(str_replace("\r\n", "\n", $raw)));

            default:
                if (str_ends_with($key, '_url')) {
                    return kontor_customizer_normalize_url($raw);
                }

                return trim(strip_tags(preg_replace('/[\r\n]+/', ' ', $raw) ?? ''));
        }
    }
}

if (!function_exists('kontor_customizer_config')) {
    /** @return array<string, array{title:string, description:string, fields:array<string, array<string, mixed>>}> */
    function kontor_customizer_config(): array
    {
        $schema = [];
        try {
            $schema = ThemeCustomizer::instance()->getCustomizationOptions();
        } catch (\Throwable) {
            $schema = [];
        }

        if (!is_array($schema) || $schema === []) {
            $file = dirname(__DIR__) . '/theme.json';
            $decoded = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
            $schema = is_array($decoded['customization'] ?? null) ? $decoded['customization'] : [];
        }

        $config = [];
        foreach ($schema as $group => $definition) {
            if (!is_array($definition) || !is_array($definition['settings'] ?? null)) {
                continue;
            }
            $fields = [];
            foreach ($definition['settings'] as $key => $field) {
                if (is_array($field) && preg_match('/^[a-z0-9_]+$/', (string) $key) === 1) {
                    $fields[(string) $key] = kontor_customizer_normalize_field($field);
                }
            }
            if ($fields !== [] && preg_match('/^[a-z0-9_]+$/', (string) $group) === 1) {
                $config[(string) $group] = [
                    'title' => trim((string) ($definition['label'] ?? $definition['title'] ?? $group)),
                    'description' => trim((string) ($definition['description'] ?? '')),
                    'fields' => $fields,
                ];
            }
        }

        return $config;
    }
}

if (!function_exists('kontor_customizer_verify_csrf')) {
    function kontor_customizer_verify_csrf(string $token): bool
    {
        if (function_exists('cms_admin_section_shell_was_csrf_verified') && cms_admin_section_shell_was_csrf_verified('theme_customizer')) {
            return true;
        }

        return Security::instance()->verifyToken($token, 'theme_customizer');
    }
}

if (!function_exists('kontor_customizer_store_upload')) {
    /**
     * Speichert ein hochgeladenes Bild (JPG, PNG, GIF, WebP; max. 2 MB) unter uploads/theme-assets/.
     *
     * @param array<string, mixed> $file
     * @return array{url:string, error:string}
     */
    function kontor_customizer_store_upload(array $file): array
    {
        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['url' => '', 'error' => ''];
        }
        if ((int) ($file['error'] ?? 0) !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            return ['url' => '', 'error' => 'Der Upload ist fehlgeschlagen.'];
        }
        if ((int) ($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return ['url' => '', 'error' => 'Die Bilddatei ist zu groß (maximal 2 MB).'];
        }

        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
        $extension = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
        $mime = $finfo !== false ? (string) finfo_file($finfo, $tmp) : '';
        if ($finfo !== false) {
            finfo_close($finfo);
        }
        if (!isset($allowed[$extension]) || $allowed[$extension] !== $mime) {
            return ['url' => '', 'error' => 'Ungültiges Bildformat. Erlaubt sind JPG, PNG, GIF und WebP.'];
        }
        if (!defined('UPLOAD_PATH') || !defined('UPLOAD_URL')) {
            return ['url' => '', 'error' => 'Das Upload-Verzeichnis ist nicht konfiguriert.'];
        }

        $directory = rtrim((string) UPLOAD_PATH, '/\\') . '/theme-assets';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return ['url' => '', 'error' => 'Das Upload-Verzeichnis konnte nicht angelegt werden.'];
        }

        $name = 'kontor-' . bin2hex(random_bytes(6)) . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
        if (!move_uploaded_file($tmp, $directory . '/' . $name)) {
            return ['url' => '', 'error' => 'Die Datei konnte nicht gespeichert werden (Schreibrechte prüfen).'];
        }

        return ['url' => rtrim((string) UPLOAD_URL, '/') . '/theme-assets/' . $name, 'error' => ''];
    }
}

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

$config = kontor_customizer_config();
$customizer = ThemeCustomizer::instance();
try {
    $customizer->setTheme(\CMS\ThemeManager::instance()->getActiveThemeSlug());
} catch (\Throwable) {
    $customizer->setTheme('kontor');
}

$tabs = array_keys($config);
$activeTab = preg_replace('/[^a-z0-9_]/', '', strtolower((string) ($_GET['tab'] ?? ($tabs[0] ?? 'colors')))) ?? '';
if (!isset($config[$activeTab])) {
    $activeTab = (string) ($tabs[0] ?? '');
}

$notice = '';
$error = '';
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$action = (string) ($_POST['action'] ?? '');

if ($method === 'POST' && in_array($action, ['save_theme_options', 'reset_theme_tab'], true)) {
    $postedTab = preg_replace('/[^a-z0-9_]/', '', strtolower((string) ($_POST['active_section'] ?? ''))) ?? '';
    if (!kontor_customizer_verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
        $error = 'Sicherheitsprüfung fehlgeschlagen. Bitte die Seite neu laden und erneut speichern.';
    } elseif (!isset($config[$postedTab])) {
        $error = 'Unbekannter Einstellungsbereich.';
    } else {
        $activeTab = $postedTab;
        $failed = false;

        foreach ($config[$postedTab]['fields'] as $key => $field) {
            if ($action === 'reset_theme_tab') {
                $default = $field['default'] ?? '';
                $value = is_bool($default) ? ($default ? '1' : '0') : (is_scalar($default) ? (string) $default : '');
            } else {
                $input = 'f_' . $postedTab . '__' . $key;
                $value = kontor_customizer_normalize_value(
                    $postedTab,
                    $key,
                    $field,
                    ($field['type'] ?? '') === 'checkbox' ? (isset($_POST[$input]) ? '1' : '0') : ($_POST[$input] ?? '')
                );

                if (($field['type'] ?? '') === 'image' && isset($_FILES['u_' . $postedTab . '__' . $key]) && is_array($_FILES['u_' . $postedTab . '__' . $key])) {
                    $upload = kontor_customizer_store_upload($_FILES['u_' . $postedTab . '__' . $key]);
                    if ($upload['error'] !== '') {
                        $error = $upload['error'];
                        $failed = true;
                        continue;
                    }
                    if ($upload['url'] !== '') {
                        $value = $upload['url'];
                    }
                }
            }

            if (!$customizer->set($postedTab, $key, $value)) {
                $failed = true;
            }
        }

        if (!$failed) {
            $notice = $action === 'reset_theme_tab'
                ? 'Der Bereich „' . $config[$postedTab]['title'] . '“ wurde auf die Standardwerte zurückgesetzt.'
                : 'Die Einstellungen für „' . $config[$postedTab]['title'] . '“ wurden gespeichert.';
        } elseif ($error === '') {
            $error = 'Einige Einstellungen konnten nicht gespeichert werden.';
        }
    }
}

$csrfToken = Security::instance()->generateToken('theme_customizer');
try {
    $themeUrl = rtrim((string) \CMS\ThemeManager::instance()->getThemeUrl(), '/');
} catch (\Throwable) {
    $themeUrl = rtrim((string) SITE_URL, '/') . '/themes/kontor';
}
$assetVersion = static function (string $relative): string {
    $file = dirname(__DIR__) . '/' . $relative;

    return is_file($file) ? (string) filemtime($file) : '1';
};
$cssUrl = $themeUrl . '/css/customizer-admin.css?v=' . rawurlencode($assetVersion('css/customizer-admin.css'));
$jsUrl = $themeUrl . '/js/customizer-admin.js?v=' . rawurlencode($assetVersion('js/customizer-admin.js'));
$siteName = defined('SITE_NAME') ? (string) SITE_NAME : 'Kontor';

if (!$embedInAdminLayout) : ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Theme-Customizer – <?php echo $esc($siteName); ?></title>
    <?= function_exists('cms_csp_runtime_tags') ? cms_csp_runtime_tags() : '' ?>
    <link rel="stylesheet" href="<?php echo $esc(function_exists('cms_asset_url') ? cms_asset_url('css/admin.css') : SITE_URL . '/assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo $esc($cssUrl); ?>">
</head>
<body class="admin-body">
<?php else : ?>
<link rel="stylesheet" href="<?php echo $esc($cssUrl); ?>">
<?php endif; ?>

<div class="admin-content theme-customizer" data-theme-customizer>
    <div class="admin-page-header">
        <div>
            <h2>Kontor – Theme-Customizer</h2>
            <p>Farben, Typografie, Header, alle Startseiten-Sektionen, Kontaktdaten und Footer des Business-Themes anpassen.</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo $esc(rtrim((string) SITE_URL, '/') . '/'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Website ansehen</a>
        </div>
    </div>

    <?php if ($notice !== '') : ?>
        <div class="alert alert-success" role="status"><?php echo $esc($notice); ?></div>
    <?php endif; ?>
    <?php if ($error !== '') : ?>
        <div class="alert alert-error alert-danger" role="alert"><?php echo $esc($error); ?></div>
    <?php endif; ?>

    <?php if ($config === []) : ?>
        <div class="alert alert-error alert-danger">In der theme.json wurden keine Customizer-Gruppen gefunden.</div>
    <?php else :
        $section = $config[$activeTab];
        ?>
        <div class="customizer-layout">
            <nav class="customizer-nav" aria-label="Einstellungsbereiche">
                <?php foreach ($config as $group => $definition) : ?>
                    <a href="?tab=<?php echo rawurlencode($group); ?>"<?php echo $group === $activeTab ? ' class="active" aria-current="page"' : ''; ?>><?php echo $esc($definition['title']); ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="customizer-content">
                <form id="customizer-form" method="post" action="?tab=<?php echo rawurlencode($activeTab); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_theme_options">
                    <input type="hidden" name="active_section" value="<?php echo $esc($activeTab); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $esc($csrfToken); ?>">

                    <div class="admin-card customizer-card">
                        <h3><?php echo $esc($section['title']); ?></h3>
                        <?php if ($section['description'] !== '') : ?>
                            <p class="customizer-section-description"><?php echo $esc($section['description']); ?></p>
                        <?php endif; ?>

                        <?php foreach ($section['fields'] as $key => $field) :
                            $type = (string) ($field['type'] ?? 'text');
                            $id = 'fld-' . $activeTab . '-' . $key;
                            $name = 'f_' . $activeTab . '__' . $key;
                            $value = $customizer->get($activeTab, $key, $field['default'] ?? '');
                            $value = is_bool($value) ? ($value ? '1' : '0') : (is_scalar($value) ? (string) $value : '');
                            ?>
                            <div class="form-group customizer-field customizer-field--<?php echo $esc($type); ?>">
                                <?php if ($type === 'checkbox') : ?>
                                    <label class="customizer-switch" for="<?php echo $esc($id); ?>">
                                        <input type="checkbox" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>" value="1"<?php echo filter_var($value, FILTER_VALIDATE_BOOLEAN) ? ' checked' : ''; ?>>
                                        <span><?php echo $esc($field['label'] ?? $key); ?></span>
                                    </label>
                                <?php else : ?>
                                    <label class="form-label" for="<?php echo $esc($id); ?>"><?php echo $esc($field['label'] ?? $key); ?></label>

                                    <?php if ($type === 'textarea') : ?>
                                        <textarea class="form-control" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>" rows="<?php echo $key === 'custom_css' ? 10 : 4; ?>"<?php echo $key === 'custom_css' ? ' spellcheck="false"' : ''; ?>><?php echo $esc($value); ?></textarea>
                                    <?php elseif ($type === 'select') : ?>
                                        <select class="form-control form-select" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>">
                                            <?php foreach (($field['options'] ?? []) as $optionValue => $optionLabel) : ?>
                                                <option value="<?php echo $esc($optionValue); ?>"<?php echo (string) $optionValue === $value ? ' selected' : ''; ?>><?php echo $esc($optionLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($type === 'color') : ?>
                                        <div class="customizer-color">
                                            <input type="color" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>" value="<?php echo $esc(preg_match('/^#[0-9a-f]{6}$/i', $value) === 1 ? $value : '#000000'); ?>" data-customizer-color>
                                            <input type="text" class="form-control customizer-color__text" value="<?php echo $esc($value); ?>" aria-label="<?php echo $esc(($field['label'] ?? $key) . ' als Hex-Wert'); ?>" pattern="#[0-9a-fA-F]{6}" data-customizer-color-text="<?php echo $esc($id); ?>">
                                        </div>
                                    <?php elseif ($type === 'number') : ?>
                                        <input type="number" class="form-control customizer-number" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>" value="<?php echo $esc($value); ?>"<?php foreach (['min', 'max', 'step'] as $attr) : ?><?php echo isset($field[$attr]) && is_numeric($field[$attr]) ? ' ' . $attr . '="' . $esc($field[$attr]) . '"' : ''; ?><?php endforeach; ?>>
                                    <?php elseif ($type === 'image') : ?>
                                        <div class="customizer-image" data-customizer-image>
                                            <div class="customizer-image__preview" data-customizer-image-preview>
                                                <?php if ($value !== '') : ?>
                                                    <img src="<?php echo $esc($value); ?>" alt="Vorschau">
                                                <?php else : ?>
                                                    <span>Noch kein Bild gewählt</span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="text" class="form-control" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>" value="<?php echo $esc($value); ?>" placeholder="/uploads/… oder https://…" data-customizer-image-url>
                                            <label class="btn btn-secondary btn-sm customizer-image__upload">
                                                Bild hochladen
                                                <input type="file" name="<?php echo $esc('u_' . $activeTab . '__' . $key); ?>" accept="image/jpeg,image/png,image/gif,image/webp" data-customizer-image-file>
                                            </label>
                                        </div>
                                    <?php else : ?>
                                        <input type="<?php echo $type === 'url' ? 'url' : 'text'; ?>" class="form-control" id="<?php echo $esc($id); ?>" name="<?php echo $esc($name); ?>" value="<?php echo $esc($value); ?>">
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (!empty($field['description'])) : ?>
                                    <small class="form-text customizer-help"><?php echo $esc($field['description']); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="admin-card customizer-actions">
                        <button type="submit" class="btn btn-primary">Einstellungen speichern</button>
                        <button type="submit" class="btn btn-secondary" form="customizer-reset-form" data-customizer-reset>Bereich zurücksetzen</button>
                    </div>
                </form>

                <form id="customizer-reset-form" method="post" action="?tab=<?php echo rawurlencode($activeTab); ?>" hidden>
                    <input type="hidden" name="action" value="reset_theme_tab">
                    <input type="hidden" name="active_section" value="<?php echo $esc($activeTab); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $esc($csrfToken); ?>">
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?php echo $esc($jsUrl); ?>" defer></script>
<?php if (!$embedInAdminLayout) : ?>
</body>
</html>
<?php endif; ?>
