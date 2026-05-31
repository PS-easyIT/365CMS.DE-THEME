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

$editorQueryArg = 'editor_lang=' . rawurlencode((string) ($editorUiLocale ?? 'de'));

$uiLocale = strtolower((string) ($editorUiLocale ?? 'de'));
$isEnglish = $uiLocale === 'en';

/**
 * @param array<string, array<string, mixed>> $config
 * @param array<string, list<string>> $fallbackNavGroups
 * @return array<string, array{label:string,tabs:list<string>}>
 */
$buildMenuGroups = static function (array $config, array $fallbackNavGroups) use ($isEnglish): array {
    $groupMap = [
        'content' => [
            'label' => $isEnglish ? 'Content & Text' : 'Inhalt & Text',
            'tabs' => [],
        ],
        'media' => [
            'label' => $isEnglish ? 'Media & Visuals' : 'Medien & Visuals',
            'tabs' => [],
        ],
        'layout' => [
            'label' => $isEnglish ? 'Layout & Blocks' : 'Layout & Blöcke',
            'tabs' => [],
        ],
        'settings' => [
            'label' => $isEnglish ? 'Settings & Meta' : 'Einstellungen & Meta',
            'tabs' => [],
        ],
        'actions' => [
            'label' => $isEnglish ? 'Publish & Actions' : 'Publish & Aktionen',
            'tabs' => [],
        ],
    ];

    $orderedTabs = [];
    foreach ($fallbackNavGroups as $tabs) {
        foreach ($tabs as $tabKey) {
            $orderedTabs[] = (string) $tabKey;
        }
    }
    if ($orderedTabs === []) {
        $orderedTabs = array_keys($config);
    }
    $orderedTabs = array_values(array_unique(array_filter(array_map('strval', $orderedTabs))));

    $contentTabs = [
        'homepage-layout',
        'homepage-list',
        'homepage-featured',
        'homepage-cards',
        'homepage-grid-feeds',
        'posts',
        'posts-sidebar',
        'pages',
        'memberdashboard',
    ];
    $mediaTabs = ['colors', 'typography', 'social'];
    $layoutTabs = ['layout', 'header', 'menus', 'footer', 'homepage-sidebar'];
    $settingsTabs = ['language', 'seo', 'performance', 'advanced'];

    foreach ($orderedTabs as $tabKey) {
        if (!isset($config[$tabKey])) {
            continue;
        }

        if (in_array($tabKey, $contentTabs, true)) {
            $groupMap['content']['tabs'][] = $tabKey;
            continue;
        }
        if (in_array($tabKey, $mediaTabs, true)) {
            $groupMap['media']['tabs'][] = $tabKey;
            continue;
        }
        if (in_array($tabKey, $layoutTabs, true)) {
            $groupMap['layout']['tabs'][] = $tabKey;
            continue;
        }
        if (in_array($tabKey, $settingsTabs, true)) {
            $groupMap['settings']['tabs'][] = $tabKey;
            continue;
        }
    }

    foreach (array_keys($config) as $tabKey) {
        $known = in_array($tabKey, $groupMap['content']['tabs'], true)
            || in_array($tabKey, $groupMap['media']['tabs'], true)
            || in_array($tabKey, $groupMap['layout']['tabs'], true)
            || in_array($tabKey, $groupMap['settings']['tabs'], true);
        if ($known) {
            continue;
        }
        $groupMap['actions']['tabs'][] = $tabKey;
    }

    return array_filter(
        $groupMap,
        static fn(array $group): bool => ($group['tabs'] ?? []) !== []
    );
};

$fallbackNavGroups = [];
foreach ($navGroups as $tabs) {
    if (!is_array($tabs)) {
        continue;
    }
    $fallbackNavGroups[] = array_values(array_filter(array_map('strval', $tabs)));
}
$menuGroups = $buildMenuGroups($config, $fallbackNavGroups);
?>
<div class="col-12 col-md-3 col-lg-2">
    <div class="card sticky-top phinit-customizer__sticky-card phinit-customizer__sidebar-nav-card">
        <div class="card-header py-2">
            <h4 class="card-title phinit-customizer__export-title mb-0">
                <?php echo htmlspecialchars($isEnglish ? 'Editor Areas' : 'Editor-Bereiche', ENT_QUOTES, 'UTF-8'); ?>
            </h4>
        </div>
        <div class="card-body p-0">
            <nav class="phinit-customizer__group-nav" aria-label="<?php echo htmlspecialchars($isEnglish ? 'Theme editor navigation' : 'Theme-Editor Navigation', ENT_QUOTES, 'UTF-8'); ?>">
                <?php $groupIndex = 0; ?>
                <?php foreach ($menuGroups as $groupKey => $groupData): ?>
                    <?php
                    $groupTabs = $groupData['tabs'] ?? [];
                    if ($groupTabs === []) {
                        continue;
                    }
                    $groupId = 'phinit-menu-group-' . $groupKey;
                    $groupLabelId = $groupId . '-label';
                    $isGroupActive = in_array($activeTab, $groupTabs, true);
                    ?>
                    <section class="phinit-customizer__menu-group">
                        <button
                            type="button"
                            class="phinit-customizer__menu-toggle"
                            id="<?php echo htmlspecialchars($groupLabelId, ENT_QUOTES, 'UTF-8'); ?>"
                            data-collapse-toggle
                            data-collapse-target="<?php echo htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8'); ?>"
                            aria-controls="<?php echo htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8'); ?>"
                            aria-expanded="<?php echo $isGroupActive || $groupIndex === 0 ? 'true' : 'false'; ?>"
                        >
                            <span class="phinit-customizer__menu-group-label"><?php echo htmlspecialchars((string) ($groupData['label'] ?? $groupKey), ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="phinit-customizer__menu-group-icon" aria-hidden="true">▾</span>
                        </button>
                        <div
                            id="<?php echo htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8'); ?>"
                            class="phinit-customizer__menu-body"
                            role="group"
                            aria-labelledby="<?php echo htmlspecialchars($groupLabelId, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo ($isGroupActive || $groupIndex === 0) ? '' : 'hidden'; ?>
                        >
                            <ul class="phinit-customizer__menu-list" role="list">
                                <?php foreach ($groupTabs as $tk): ?>
                                    <?php if (!isset($config[$tk])) {
                                        continue;
                                    } ?>
                                    <li>
                                        <a
                                            href="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . rawurlencode((string) $tk) . '&' . $editorQueryArg, ENT_QUOTES, 'UTF-8'); ?>"
                                            class="phinit-customizer__nav-link<?php echo $activeTab === $tk ? ' active' : ''; ?>"
                                            <?php if ($activeTab === $tk): ?>aria-current="page"<?php endif; ?>
                                        >
                                            <?php echo htmlspecialchars((string) ($config[$tk]['title'] ?? $tk), ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>
                    <?php $groupIndex++; ?>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>

    <div class="card mt-3 phinit-customizer__panel-card">
        <div class="card-header py-2">
            <h4 class="card-title phinit-customizer__export-title"><?php echo htmlspecialchars(phinit_t('theme_editor_export_import', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></h4>
        </div>
        <div class="card-body p-0">
            <section class="phinit-customizer__menu-group">
                <button
                    type="button"
                    class="phinit-customizer__menu-toggle"
                    id="phinit-tools-toggle"
                    data-collapse-toggle
                    data-collapse-target="phinit-tools-panel"
                    aria-controls="phinit-tools-panel"
                    aria-expanded="false"
                >
                    <span class="phinit-customizer__menu-group-label"><?php echo htmlspecialchars($isEnglish ? 'Import / Export tools' : 'Import-/Export-Werkzeuge', ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="phinit-customizer__menu-group-icon" aria-hidden="true">▾</span>
                </button>
                <div
                    id="phinit-tools-panel"
                    class="phinit-customizer__menu-body p-3"
                    role="group"
                    aria-labelledby="phinit-tools-toggle"
                    hidden
                    data-lazy-panel
                >
                    <form method="POST" action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . rawurlencode((string) $activeTab) . '&' . $editorQueryArg, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="export_settings">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                            ⬇️ <?php echo htmlspecialchars(phinit_t('theme_editor_export', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
                        </button>
                    </form>
                    <form method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . rawurlencode((string) $activeTab) . '&' . $editorQueryArg, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="import_settings">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="file" name="import_file" accept=".json" class="form-control form-control-sm mb-2">
                        <?php if ($activeTab === 'advanced'): ?>
                            <label class="form-check mb-2 small text-secondary">
                                <input class="form-check-input" type="checkbox" name="advanced_code_acknowledged" value="1">
                                <span class="form-check-label">
                                    <?php echo htmlspecialchars(phinit_t('theme_editor_advanced_import_ack', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
                                </span>
                            </label>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                            ⬆️ <?php echo htmlspecialchars(phinit_t('theme_editor_import', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
