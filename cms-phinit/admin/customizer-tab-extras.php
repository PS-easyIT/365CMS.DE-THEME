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

$uiLocale = strtolower((string) ($editorUiLocale ?? 'de'));
$isEnglish = $uiLocale === 'en';
?>
<div class="card mb-3 border-primary-subtle">
    <div class="card-body">
        <h4 class="card-title mb-2">
            <?php echo htmlspecialchars($isEnglish ? '🌐 Language-specific theme values' : '🌐 Sprachgetrennte Theme-Werte', ENT_QUOTES, 'UTF-8'); ?>
        </h4>
        <p class="text-secondary mb-1">
            <?php echo htmlspecialchars($isEnglish
                ? 'DE and EN values are stored separately. Saving in this editor language only updates the current language.'
                : 'DE- und EN-Werte werden getrennt gespeichert. Speichern in dieser Editor-Sprache aktualisiert nur die aktive Sprache.', ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <p class="text-secondary mb-0">
            <?php echo htmlspecialchars($isEnglish
                ? 'Fallback: EN shows DE only while EN is still unchanged/default. As soon as EN is changed, EN is used.'
                : 'Fallback: EN zeigt DE nur solange EN unverändert/auf Default steht. Sobald EN geändert wurde, gilt EN.', ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>
</div>

<?php if ($activeTab === 'colors'): ?>
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-color-presets.php'; ?>
<?php endif; ?>

<?php if ($activeTab === 'header'): ?>
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-header-menu-note.php'; ?>
<?php endif; ?>

<?php if ($activeTab === 'menus'): ?>
    <?php
    $menuLocale = strtolower((string) ($editorUiLocale ?? 'de')) === 'en' ? 'en' : 'de';
    $menuTargets = $menuLocale === 'en'
        ? 'primary_en, quicklinks_en, footer-topics_en, footer-pages_en, footer_en'
        : 'primary, quicklinks, footer-topics, footer-pages, footer';
    ?>
    <div class="card mb-3 border-info-subtle">
        <div class="card-body">
            <h4 class="card-title mb-2">
                <?php echo htmlspecialchars($isEnglish ? '🧭 Locale-specific menu editing' : '🧭 Sprachspezifische Menübearbeitung', ENT_QUOTES, 'UTF-8'); ?>
            </h4>
            <p class="text-secondary mb-1">
                <?php echo htmlspecialchars($isEnglish
                    ? 'This tab edits only the menu set for the active editor language.'
                    : 'Dieser Tab bearbeitet nur den Menüsatz der aktiven Editor-Sprache.', ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p class="text-secondary mb-0">
                <?php echo htmlspecialchars($isEnglish
                    ? 'Current target slugs: '
                    : 'Aktuelle Ziel-Slugs: ', ENT_QUOTES, 'UTF-8'); ?>
                <code><?php echo htmlspecialchars($menuTargets, ENT_QUOTES, 'UTF-8'); ?></code>
            </p>
        </div>
    </div>
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
