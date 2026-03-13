<?php
/**
 * Customizer tab-specific extras partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<?php if ($activeTab === 'colors'): ?>
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-color-presets.php'; ?>
<?php endif; ?>

<?php if ($activeTab === 'header'): ?>
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-header-menu-note.php'; ?>
<?php endif; ?>

<?php if ($activeTab === 'homepage'): ?>
    <div class="card mb-3 border-info-subtle">
        <div class="card-body">
            <h4 class="card-title mb-2">🏠 Hinweis zur Startseite</h4>
            <p class="text-secondary mb-2">
                Die Sidebar-Widgets der Startseite liegen jetzt im eigenen Bereich
                <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=homepage-sidebar', ENT_QUOTES); ?>">Startseiten-Sidebar</a>.
            </p>
            <p class="text-secondary mb-0">
                Die Karten-/Artikelbildhöhe findest du im Block <strong>„Kachel-Grid &amp; Bildhöhe“</strong>.
            </p>
        </div>
    </div>
<?php endif; ?>
