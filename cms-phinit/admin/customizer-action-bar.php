<?php
/**
 * Customizer action bar partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="card mb-3">
    <div class="card-body py-2 d-flex align-items-center gap-2 flex-wrap">
        <button type="submit" form="customizer-form" name="action" value="save_theme_options"
                class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                <circle cx="12" cy="15" r="2"/><polyline points="14 4 14 8 8 8 8 4"/>
            </svg>
            Speichern
        </button>
        <button type="submit" form="customizer-form" name="action" value="reset_theme_tab"
                class="btn btn-outline-secondary"
                onclick="return confirm('Alle Felder dieses Tabs auf Standardwerte zurücksetzen?');">
            ↩️ Tab zurücksetzen
        </button>
        <span id="unsaved-hint" class="ms-auto text-warning phinit-customizer__unsaved-hint">
            ⚠️ Ungespeicherte Änderungen
        </span>
        <span class="text-muted ms-auto phinit-customizer__shortcut-hint">Strg+S zum Speichern</span>
    </div>
</div>
