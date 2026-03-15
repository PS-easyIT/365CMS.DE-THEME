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

<?php if ($activeTab === 'advanced'): ?>
    <div class="card mb-3 border-warning-subtle bg-warning-lt">
        <div class="card-body">
            <h4 class="card-title mb-2">⚠️ Hinweis zu Custom-Code</h4>
            <p class="text-secondary mb-2">
                Die Felder für <strong>Custom Head Code</strong>, <strong>Custom Footer Code</strong> und <strong>Eigenes CSS</strong>
                werden bewusst als privilegierter Rohcode-Modus behandelt.
            </p>
            <p class="text-secondary mb-0">
                Bitte nur vertrauenswürdigen Code einfügen. Fehler oder fremder Script-Code wirken sich sofort auf das öffentliche Frontend aus.
            </p>
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" value="1" id="advanced_code_acknowledged"
                       name="advanced_code_acknowledged">
                <label class="form-check-label" for="advanced_code_acknowledged">
                    Ich bestätige, dass eigener Head-/Footer-Code und eigenes CSS ein privilegierter Live-Eingriff sind.
                </label>
            </div>
        </div>
    </div>
<?php endif; ?>
