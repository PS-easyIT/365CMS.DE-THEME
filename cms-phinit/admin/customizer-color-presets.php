<?php
/**
 * Customizer color presets partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="card mb-3 phinit-customizer__preset-card" id="color-presets-card">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="phinit-customizer__preset-label">🎨 Schnell-Presets:</span>
            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--phinit" data-preset="phinit">Phinit (Standard)</button>
            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--bluesteel" data-preset="bluesteel">Blue Steel</button>
            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--greentech" data-preset="greentech">Green Tech</button>
            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--slate" data-preset="slate">Slate Dark</button>
            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--ruby" data-preset="ruby">Ruby Red</button>
            <small class="ms-auto phinit-customizer__preset-note">↑ Klick füllt Felder – danach Speichern nicht vergessen!</small>
        </div>
    </div>
</div>
