<?php
/**
 * Customizer live preview drawer partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="px-drawer" aria-hidden="true"
     class="phinit-customizer__drawer">
    <div class="phinit-customizer__drawer-toolbar">
        <button id="px-close-btn" type="button"
          class="phinit-customizer__toolbar-btn"
                title="Schließen (Esc)">✕</button>
        <div class="phinit-customizer__device-switcher">
            <button type="button" class="px-dev-btn phinit-customizer__device-btn active" data-width="1280">🖥️ Desktop</button>
            <button type="button" class="px-dev-btn phinit-customizer__device-btn" data-width="768">📱 Tablet</button>
            <button type="button" class="px-dev-btn phinit-customizer__device-btn" data-width="375">📲 Mobil</button>
        </div>
        <button id="px-refresh-btn" type="button"
          class="phinit-customizer__toolbar-btn"
                title="Neu laden">⟳</button>
        <a href="<?php echo htmlspecialchars(SITE_URL); ?>/" target="_blank" rel="noopener noreferrer"
        class="phinit-customizer__toolbar-link"
           title="In neuem Tab öffnen">↗</a>
    </div>
    <div class="phinit-customizer__drawer-body">
        <iframe id="px-iframe" src=""
          class="phinit-customizer__iframe"
                title="Theme Live-Vorschau"></iframe>
    </div>
    <div id="px-label" class="phinit-customizer__drawer-label">
        Desktop (1280 px)
    </div>
</div>
