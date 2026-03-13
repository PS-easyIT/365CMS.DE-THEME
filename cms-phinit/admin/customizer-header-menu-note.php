<?php
/**
 * Customizer header menu editor note partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="card mt-3 border-primary">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="text-primary phinit-customizer__menu-icon">📋</div>
        <div>
            <strong>Menü-Einträge</strong> (Hauptmenü, Quicklinks, Footer-Menüs) werden im
            <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/menu-editor'); ?>">Menü-Editor</a>
            verwaltet – hier nur Aussehen (Höhen, Farben, Sichtbarkeit).
        </div>
        <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/menu-editor'); ?>"
           class="btn btn-sm btn-primary ms-auto">Menü-Editor →</a>
    </div>
</div>
