<?php
/**
 * Customizer sidebar partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="col-12 col-md-3 col-lg-2">
    <div class="card sticky-top phinit-customizer__sticky-card">
        <div class="list-group list-group-flush">
            <?php foreach ($navGroups as $grpLabel => $tabs):
                if ($grpLabel !== null): ?>
                <div class="list-group-item py-1 px-3 phinit-customizer__nav-label">
                    <?php echo htmlspecialchars($grpLabel); ?>
                </div>
                <?php endif;
                foreach ($tabs as $tk):
                    if (!isset($config[$tk])) {
                        continue;
                    }
            ?>
                <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $tk); ?>"
                   class="list-group-item list-group-item-action py-2 px-3 phinit-customizer__nav-link<?php echo $activeTab === $tk ? ' active' : ''; ?>">
                    <?php echo htmlspecialchars($config[$tk]['title']); ?>
                </a>
            <?php endforeach; endforeach; ?>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header py-2">
            <h4 class="card-title phinit-customizer__export-title">Export / Import</h4>
        </div>
        <div class="card-body p-3">
            <form method="POST"
                  action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
                <input type="hidden" name="action" value="export_settings">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                    ⬇️ Exportieren
                </button>
            </form>
            <form method="POST" enctype="multipart/form-data"
                  action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
                <input type="hidden" name="action" value="import_settings">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <input type="file" name="import_file" accept=".json" class="form-control form-control-sm mb-2">
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    ⬆️ Importieren
                </button>
            </form>
        </div>
    </div>
</div>
