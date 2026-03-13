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

if (!defined('CMS_PHINIT_THEME_DIR')) {
    define('CMS_PHINIT_THEME_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

// ── 1. Konfigurations-Schema ─────────────────────────────────────────────────
$schema = require CMS_PHINIT_THEME_DIR . 'admin/customizer-schema.php';
$config = $schema['config'] ?? [];

require_once CMS_PHINIT_THEME_DIR . 'admin/customizer-request-handler.php';
require_once CMS_PHINIT_THEME_DIR . 'admin/customizer-field-renderer.php';

// ── 2. Customizer-Instanz ────────────────────────────────────────────────────
$customizer = ThemeCustomizer::instance();
$customizer->setTheme('cms-phinit');

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
$config['homepage']['sections']['feed1_channel_id']['options'] = $_feedOpts;
$config['homepage']['sections']['feed2_channel_id']['options'] = $_feedOpts;

// Aktiver Tab
$activeTab = $_GET['tab'] ?? 'colors';
if (!isset($config[$activeTab])) {
    $activeTab = 'colors';
}

// ── 3. POST-Handler ──────────────────────────────────────────────────────────
$postResult = phinit_handle_customizer_post($config, $customizer, $activeTab);
$alertMsg = $postResult['alertMsg'];
$alertType = $postResult['alertType'];
$activeTab = $postResult['activeTab'];

// CSRF-Token nach POST-Handling generieren (verhindert Token-Überschreibung)
$csrfToken = Security::instance()->generateToken('phinit_customizer');

// ── 5. Tab-Gruppen ───────────────────────────────────────────────────────────
$tabGroups = $schema['tabGroups'] ?? [];

// Nav-Gruppen für die Sidebar
$navGroups = $schema['navGroups'] ?? [];
?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-styles.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-page-header.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-page-body.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-preview-drawer.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-scripts.php'; ?>
