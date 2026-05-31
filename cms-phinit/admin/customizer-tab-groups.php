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
<div class="row row-cards phinit-customizer__field-groups" role="region" aria-label="<?php echo htmlspecialchars((string) ($config[$activeTab]['title'] ?? $activeTab), ENT_QUOTES, 'UTF-8'); ?>">
<?php $groupIndex = 0; ?>
<?php foreach ($currentGroups as $groupTitle => $fieldKeys): ?>
    <?php
    $panelId = 'phinit-field-group-' . preg_replace('/[^a-z0-9_-]+/i', '-', strtolower((string) $activeTab . '-' . (string) $groupIndex));
    $toggleId = $panelId . '-toggle';
    $isInitiallyOpen = $groupIndex < 2;
    ?>
    <div class="col-12 col-xl-6">
        <div class="card h-100 phinit-customizer__group-card">
            <div class="card-header py-2">
                <button
                    type="button"
                    id="<?php echo htmlspecialchars($toggleId, ENT_QUOTES, 'UTF-8'); ?>"
                    class="phinit-customizer__group-toggle"
                    data-collapse-toggle
                    data-collapse-target="<?php echo htmlspecialchars($panelId, ENT_QUOTES, 'UTF-8'); ?>"
                    aria-controls="<?php echo htmlspecialchars($panelId, ENT_QUOTES, 'UTF-8'); ?>"
                    aria-expanded="<?php echo $isInitiallyOpen ? 'true' : 'false'; ?>"
                >
                    <span><?php echo htmlspecialchars((string) $groupTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="phinit-customizer__group-toggle-icon" aria-hidden="true">▾</span>
                </button>
            </div>
            <div
                id="<?php echo htmlspecialchars($panelId, ENT_QUOTES, 'UTF-8'); ?>"
                class="card-body phinit-customizer__group-panel"
                role="group"
                aria-labelledby="<?php echo htmlspecialchars($toggleId, ENT_QUOTES, 'UTF-8'); ?>"
                <?php echo $isInitiallyOpen ? '' : 'hidden data-lazy-panel'; ?>
            >
                <?php foreach ($fieldKeys as $fk):
                    if (!isset($tabSections[$fk])) {
                        continue;
                    }
                    $f = $tabSections[$fk];
                    $valueTab = (string) ($f['storageTab'] ?? $activeTab);
                    if (($f['type'] ?? 'text') === 'menu_tree' && function_exists('phinit_get_customizer_menu_field_value')) {
                        $val = phinit_get_customizer_menu_field_value(
                            (string) $fk,
                            (string) ($editorUiLocale ?? 'de'),
                            (string) ($f['default'] ?? '')
                        );
                    } else {
                        $val = phinit_customizer_value($valueTab, $fk, $f['default'] ?? '', (string) ($editorUiLocale ?? 'de'));
                    }
                    phinit_render_field($activeTab, $fk, $f, $val);
                endforeach; ?>
            </div>
        </div>
    </div>
    <?php $groupIndex++; ?>
<?php endforeach; ?>
</div>
