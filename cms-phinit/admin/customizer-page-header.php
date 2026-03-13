<?php
/**
 * Customizer page header partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Theme-Editor</div>
                <h2 class="page-title">🎨 Theme Customizer – CMS Phinit</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" id="preview-toggle-btn" class="btn btn-outline-secondary me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 7a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z"/>
                        <path d="M7 20l10 0"/><path d="M9 16l0 4"/><path d="M15 16l0 4"/>
                    </svg>
                    Live-Vorschau
                </button>
            </div>
        </div>
    </div>
</div>
