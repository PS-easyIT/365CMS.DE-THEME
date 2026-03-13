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

<div class="page-body">
    <div class="container-xl">

        <?php if ($alertMsg !== null): ?>
        <div class="alert alert-<?php echo htmlspecialchars($alertType); ?> alert-dismissible" role="alert">
            <?php echo $alertMsg; ?>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <form method="POST" id="customizer-form"
              action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
            <input type="hidden" name="action" value="save_theme_options">
            <input type="hidden" name="active_section" id="active_section_input" value="<?php echo htmlspecialchars($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <div class="row g-3">

                <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-sidebar.php'; ?>

                <!-- ── Rechte Spalte: Tab-Inhalt ── -->
                <div class="col-12 col-md-9 col-lg-10">

                    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-action-bar.php'; ?>

                    <!-- Tab-Inhalte -->
                    <?php
                    $currentGroups = $tabGroups[$activeTab] ?? [];
                    $tabSections   = $config[$activeTab]['sections'] ?? [];
                    // Für advanced: größere Textareas
                    if ($activeTab === 'advanced') {
                        foreach (['custom_css', 'custom_head_code', 'custom_footer_code'] as $_fk) {
                            if (isset($tabSections[$_fk])) { $tabSections[$_fk]['rows'] = 8; }
                        }
                    }
                    ?>

                    <?php if ($activeTab === 'colors'): ?>
                        <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-color-presets.php'; ?>
                    <?php endif; ?>

                    <div class="row row-cards">
                    <?php foreach ($currentGroups as $groupTitle => $fieldKeys): ?>
                        <div class="col-12 col-xl-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo htmlspecialchars($groupTitle); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($fieldKeys as $fk):
                                        if (!isset($tabSections[$fk])) { continue; }
                                        $f   = $tabSections[$fk];
                                        $val = $customizer->get($activeTab, $fk, $f['default'] ?? '');
                                        phinit_render_field($activeTab, $fk, $f, $val);
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                    <!-- Hinweis: Menü-Einträge über Menü-Editor (nur bei header-Tab) -->
                    <?php if ($activeTab === 'header'): ?>
                        <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-header-menu-note.php'; ?>
                    <?php endif; ?>

                </div><!-- /.col (rechts) -->
            </div><!-- /.row -->
        </form>

    </div>
</div>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-preview-drawer.php'; ?>

<?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-scripts.php'; ?>
