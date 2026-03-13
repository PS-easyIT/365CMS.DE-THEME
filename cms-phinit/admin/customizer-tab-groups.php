<?php
/**
 * Customizer tab group cards partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="row row-cards">
<?php foreach ($currentGroups as $groupTitle => $fieldKeys): ?>
    <div class="col-12 col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title"><?php echo htmlspecialchars($groupTitle); ?></h4>
            </div>
            <div class="card-body">
                <?php foreach ($fieldKeys as $fk):
                    if (!isset($tabSections[$fk])) {
                        continue;
                    }
                    $f = $tabSections[$fk];
                    $valueTab = (string) ($f['storageTab'] ?? $activeTab);
                    $val = $customizer->get($valueTab, $fk, $f['default'] ?? '');
                    phinit_render_field($activeTab, $fk, $f, $val);
                endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
