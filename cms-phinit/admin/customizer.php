<?php
/**
 * CMS Phinit – Theme Customizer (Admin Fragment)
 *
 * Wird von CMS/admin/theme-editor.php als Fragment eingebunden:
 *   partials/header.php + partials/sidebar.php → customizer.php → partials/footer.php
 * Kein eigenes <!DOCTYPE html> / <head> / <body>!
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Auth;
use CMS\Security;
use CMS\Services\ThemeCustomizer;
use CMS\ThemeManager;

if (!defined('CMS_PHINIT_THEME_DIR')) {
    define('CMS_PHINIT_THEME_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (!defined('CMS_PHINIT_THEME_URL')) {
    define('CMS_PHINIT_THEME_URL', rtrim(ThemeManager::instance()->getThemeUrl(), '/') . '/');
}

if (!function_exists('phinit_input_string') || !function_exists('phinit_request_method')) {
    require_once CMS_PHINIT_THEME_DIR . 'includes/theme-template-helpers.php';
}

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

// ── 1. Konfigurations-Schema ─────────────────────────────────────────────────
$schema = require CMS_PHINIT_THEME_DIR . 'admin/customizer-schema.php';
$legacyConfig = $schema['config'] ?? [];

require_once CMS_PHINIT_THEME_DIR . 'admin/customizer-config-builder.php';
require_once CMS_PHINIT_THEME_DIR . 'admin/customizer-request-handler.php';
require_once CMS_PHINIT_THEME_DIR . 'admin/customizer-field-renderer.php';

// ── 2. Customizer-Instanz ────────────────────────────────────────────────────
$customizer = ThemeCustomizer::instance();
$customizer->setTheme('cms-phinit');

$requestedEditorLocale = strtolower(phinit_input_string($_GET, 'editor_lang', '', 10));
$configuredEditorLocale = strtolower(trim((string) $customizer->get('language', 'editor_ui_locale', 'auto')));
$editorUiLocale = in_array($requestedEditorLocale, ['de', 'en'], true) ? $requestedEditorLocale : $configuredEditorLocale;
if (!in_array($editorUiLocale, ['de', 'en'], true)) {
    $editorUiLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
}
if (!in_array($editorUiLocale, ['de', 'en'], true)) {
    $editorUiLocale = 'de';
}
$GLOBALS['phinit_customizer_editor_locale'] = $editorUiLocale;

$baseConfig = phinit_merge_customizer_config(
    phinit_build_customizer_base_config($customizer),
    phinit_strip_customizer_legacy_aliases(is_array($legacyConfig) ? $legacyConfig : [])
);

phinit_migrate_customizer_legacy_aliases($baseConfig, $customizer);

// cms-feed Kanal-Optionen dynamisch laden
$_feedOpts = ['0' => '— Kein Feed —'];
if (class_exists('CMS_Feed_Database')) {
    try {
        foreach (CMS_Feed_Database::instance()->get_channels(0) as $_ch) {
            if (!empty($_ch['is_active'])) {
                $_feedOpts[(string)$_ch['id']] = htmlspecialchars(
                    $_ch['name'] . (isset($_ch['category_name']) ? ' (' . $_ch['category_name'] . ')' : ''),
                    ENT_QUOTES
                );
            }
        }
    } catch (\Throwable $_e) {}
}
$baseConfig['homepage']['sections']['feed1_channel_id']['options'] = $_feedOpts;
$baseConfig['homepage']['sections']['feed2_channel_id']['options'] = $_feedOpts;

$config = $baseConfig;
$tabGroups = $schema['tabGroups'] ?? [];

foreach (($schema['tabViews'] ?? []) as $viewKey => $viewConfig) {
    $viewSections = [];
    $viewStorageTabs = [];

    foreach (($viewConfig['fields'] ?? []) as $fieldRef) {
        if (!is_array($fieldRef)) {
            continue;
        }

        $sourceTab = (string) ($fieldRef['tab'] ?? '');
        $fieldKey = (string) ($fieldRef['key'] ?? '');
        if ($sourceTab === '' || $fieldKey === '' || !isset($baseConfig[$sourceTab]['sections'][$fieldKey])) {
            continue;
        }

        $fieldConfig = $baseConfig[$sourceTab]['sections'][$fieldKey];
        $fieldConfig['storageTab'] = $sourceTab;
        $viewSections[$fieldKey] = $fieldConfig;
        $viewStorageTabs[$sourceTab] = true;
    }

    if ($viewSections === []) {
        continue;
    }

    $config[$viewKey] = [
        'title' => (string) ($viewConfig['title'] ?? $viewKey),
        'sections' => $viewSections,
        'storageTab' => count($viewStorageTabs) === 1 ? (string) array_key_first($viewStorageTabs) : $viewKey,
    ];
    $tabGroups[$viewKey] = $viewConfig['groups'] ?? [];
}

$customizerConfig = new CMS_Phinit_Customizer_Config_Snapshot($config, is_array($tabGroups) ? $tabGroups : []);
$config = $customizerConfig->categories;
$tabGroups = $customizerConfig->tabGroups;

if ($editorUiLocale !== 'en') {
    unset($config['menus'], $tabGroups['menus']);

    $filteredNavGroups = [];
    foreach (($schema['navGroups'] ?? []) as $groupLabel => $tabs) {
        if (!is_array($tabs)) {
            $filteredNavGroups[$groupLabel] = $tabs;
            continue;
        }

        $filteredTabs = array_values(array_filter(
            array_map('strval', $tabs),
            static fn(string $tab): bool => $tab !== 'menus'
        ));
        $filteredNavGroups[$groupLabel] = $filteredTabs;
    }
    $schema['navGroups'] = $filteredNavGroups;
}

if ($editorUiLocale === 'en') {
    $tabTitleMap = [
        'colors' => '🎨 Colors',
        'typography' => '🔤 Typography',
        'layout' => '📐 General layout',
        'language' => '🌐 Language',
        'header' => '🖥️ Header & navigation',
        'menus' => '🧭 Menus',
        'footer' => '🔻 Footer',
        'homepage-sidebar' => '📚 Homepage sidebar',
        'homepage-layout' => '🏠 Homepage · Structure',
        'homepage-list' => '📰 Homepage · Article list',
        'homepage-featured' => '📌 Homepage · Featured posts',
        'homepage-cards' => '🗂️ Homepage · Topic cards',
        'homepage-grid-feeds' => '🧱 Homepage · Grid & feeds',
        'posts' => '📝 Posts',
        'posts-sidebar' => '📝 Post sidebar',
        'pages' => '📄 Pages',
        'social' => '🌐 Social media',
        'advanced' => '🔧 Advanced',
        'seo' => '🔍 SEO',
        'performance' => '⚡ Performance',
        'memberdashboard' => '👤 Member dashboard',
    ];
    foreach ($tabTitleMap as $tabKey => $tabTitle) {
        if (isset($config[$tabKey])) {
            $config[$tabKey]['title'] = $tabTitle;
        }
    }

    $navGroups = $schema['navGroups'] ?? [];
    $translatedNavGroups = [];
    foreach ($navGroups as $groupLabel => $tabs) {
        if ($groupLabel === null) {
            $translatedNavGroups[$groupLabel] = $tabs;
            continue;
        }
        $label = (string) $groupLabel;
        $translatedNavGroups[
            match ($label) {
                '🎨 Design' => '🎨 Design',
                '📝 Inhalte' => '📝 Content',
                '⚙️ Sonstiges' => '⚙️ Other',
                default => $label,
            }
        ] = $tabs;
    }
    $schema['navGroups'] = $translatedNavGroups;
}

// Aktiver Tab
$activeTab = phinit_input_string($_GET, 'tab', 'colors', 80);
if (!$customizerConfig->hasTab($activeTab)) {
    $activeTab = 'colors';
}

// ── 3. POST-Handler ──────────────────────────────────────────────────────────
$postResult = phinit_handle_customizer_post($config, $customizer, $activeTab, (string) $editorUiLocale);
$alertMsg = $postResult['alertMsg'];
$alertType = $postResult['alertType'];
$activeTab = $postResult['activeTab'];

// CSRF-Token nach POST-Handling generieren (muss mit /admin/theme-editor Section-Shell synchron bleiben)
$csrfToken = Security::instance()->generateToken('admin_theme_editor');

// Nav-Gruppen für die Sidebar
$navGroups = $schema['navGroups'] ?? [];
?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-styles.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-page-header.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-page-body.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-preview-drawer.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-scripts.php'; ?>
