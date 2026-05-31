<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Liefert die auswählbaren Beitragsoptionen für Post-Picker im Theme-Customizer.
 *
 * @return list<array{id:int,title:string,status_label:string}>
 */
function phinit_get_customizer_post_picker_rows(): array
{
    static $postRows = null;
    $uiLocale = (string) ($GLOBALS['phinit_customizer_editor_locale'] ?? 'de');
    $isEnglish = strtolower($uiLocale) === 'en';

    if (is_array($postRows)) {
        return $postRows;
    }

    $postRows = [];

    try {
        $db = \CMS\Database::instance();
        $currentDateTime = date('Y-m-d H:i:s');
        $rows = $db->get_results(
            "SELECT id, title, status, published_at, created_at
             FROM {$db->getPrefix()}posts
             WHERE status IN ('published', 'private', 'draft')
             ORDER BY
                 CASE status
                     WHEN 'published' THEN 0
                     WHEN 'private' THEN 1
                     WHEN 'draft' THEN 2
                     ELSE 9
                 END ASC,
                 COALESCE(published_at, created_at) DESC,
                 id DESC
             LIMIT 500"
        ) ?: [];

        foreach ($rows as $row) {
            $post = (array) $row;
            $status = (string) ($post['status'] ?? 'draft');
            $publishedAt = trim((string) ($post['published_at'] ?? ''));
            $statusLabel = $isEnglish ? 'Draft' : 'Entwurf';

            if ($status === 'private') {
                $statusLabel = $isEnglish ? 'Private' : 'Privat';
            } elseif ($status === 'published') {
                $statusLabel = ($publishedAt !== '' && $publishedAt > $currentDateTime)
                    ? ($isEnglish ? 'Scheduled' : 'Geplant')
                    : ($isEnglish ? 'Published' : 'Veröffentlicht');
            }

            $title = trim((string) ($post['title'] ?? ''));
            if ($title === '') {
                $title = ($isEnglish ? 'Post #' : 'Beitrag #') . (int) ($post['id'] ?? 0);
            }

            $postRows[] = [
                'id' => (int) ($post['id'] ?? 0),
                'title' => $title,
                'status_label' => $statusLabel,
            ];
        }
    } catch (\Throwable) {
        $postRows = [];
    }

    return $postRows;
}

/**
 * Führt eine gespeicherte Widget-Reihenfolge mit der Standardreihenfolge zusammen,
 * ohne neu eingeführte Widgets stumpf ans Ende zu hängen.
 *
 * @param list<string> $configuredOrder
 * @param list<string> $defaultOrder
 * @return list<string>
 */
function phinit_merge_widget_order(array $configuredOrder, array $defaultOrder): array
{
    $normalizedOrder = array_values(array_unique(array_filter(array_map(
        static fn(mixed $item): string => is_string($item) ? strtolower(trim($item)) : '',
        $configuredOrder
    ), static fn(string $item): bool => $item !== '' && in_array($item, $defaultOrder, true))));

    foreach ($defaultOrder as $index => $widgetKey) {
        if (in_array($widgetKey, $normalizedOrder, true)) {
            continue;
        }

        $insertBefore = null;

        for ($nextIndex = $index + 1, $defaultCount = count($defaultOrder); $nextIndex < $defaultCount; $nextIndex++) {
            $candidateKey = $defaultOrder[$nextIndex];
            $candidatePosition = array_search($candidateKey, $normalizedOrder, true);

            if ($candidatePosition !== false) {
                $insertBefore = (int) $candidatePosition;
                break;
            }
        }

        if ($insertBefore === null) {
            $normalizedOrder[] = $widgetKey;
            continue;
        }

        array_splice($normalizedOrder, $insertBefore, 0, [$widgetKey]);
    }

    return $normalizedOrder;
}

/**
 * Rendert ein einzelnes Formularfeld mit Tabler-CSS-Klassen.
 *
 * @param array<string, mixed> $field
 */
function phinit_render_field(string $tab, string $fieldKey, array $field, mixed $value): void
{
    $uiLocale = strtolower((string) ($GLOBALS['phinit_customizer_editor_locale'] ?? 'de'));
    $isEnglish = $uiLocale === 'en';
    $id = 'f_' . $tab . '_' . $fieldKey;
    $name = $tab . '_' . $fieldKey;
    $idAttr = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
    $nameAttr = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $currentValue = (string) $value;
    $description = (string) ($field['description'] ?? '');
    ?>
    <div class="mb-3">
        <?php if (($field['type'] ?? 'text') === 'checkbox'): ?>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                       id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>" value="1"
                       <?php echo ($currentValue !== '' && $currentValue !== '0') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="<?php echo $idAttr; ?>">
                    <?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?>
                </label>
            </div>

        <?php elseif (($field['type'] ?? 'text') === 'color'): ?>
            <?php
            $defaultColor = (string) ($field['default'] ?? '#000000');
            $pickerValue = preg_match('/^#[0-9a-fA-F]{6}$/', $currentValue) === 1 ? $currentValue : $defaultColor;
            $pickerValue = preg_match('/^#[0-9a-fA-F]{6}$/', $pickerValue) === 1 ? $pickerValue : '#000000';
            $currentValue = preg_match('/^#[0-9a-fA-F]{6}$/', $currentValue) === 1 ? $currentValue : $pickerValue;
            ?>
            <label class="form-label"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <div class="input-group phinit-customizer__color-group">
                <input type="color"
                      id="<?php echo $idAttr; ?>"
                       value="<?php echo htmlspecialchars($pickerValue, ENT_QUOTES); ?>"
                       class="form-control form-control-color phinit-customizer__color-input"
                      data-color-picker
                      data-sync-target-text="<?php echo htmlspecialchars($id . '_txt', ENT_QUOTES); ?>"
                      data-sync-target-hidden="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>">
                <input type="text"
                      id="<?php echo htmlspecialchars($id . '_txt', ENT_QUOTES, 'UTF-8'); ?>"
                       value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                       class="form-control font-monospace phinit-customizer__color-text"
                      data-color-text
                      data-sync-target-picker="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>"
                      data-sync-target-hidden="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>">
                  <input type="hidden" name="<?php echo $nameAttr; ?>"
                      id="<?php echo $nameAttr; ?>"
                       value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>">
            </div>

        <?php elseif (($field['type'] ?? 'text') === 'select'): ?>
                 <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
                 <select id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>" class="form-select">
                <?php foreach (($field['options'] ?? []) as $optionValue => $optionLabel): ?>
                <option value="<?php echo htmlspecialchars((string) $optionValue, ENT_QUOTES); ?>"
                    <?php echo $currentValue === (string) $optionValue ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars((string) $optionLabel, ENT_QUOTES); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php elseif (($field['type'] ?? 'text') === 'textarea'): ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <textarea id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>"
                      class="form-control" rows="<?php echo (int) ($field['rows'] ?? 3); ?>"><?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?></textarea>

        <?php elseif (($field['type'] ?? 'text') === 'menu_tree'): ?>
            <?php
            $menuSlugMap = function_exists('phinit_get_customizer_menu_field_slug_map')
                ? phinit_get_customizer_menu_field_slug_map($uiLocale)
                : [];
            $targetMenuSlug = (string) ($menuSlugMap[$fieldKey] ?? '');
            $menuAriaLabel = $isEnglish ? 'Menu item list' : 'Menüeintragsliste';
            ?>
            <div class="phinit-menu-editor"
                 data-menu-editor
                 data-menu-editor-id="<?php echo $idAttr; ?>"
                 data-menu-aria-label="<?php echo htmlspecialchars($menuAriaLabel, ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-level-label="<?php echo htmlspecialchars($isEnglish ? 'Level' : 'Ebene', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-empty-label="<?php echo htmlspecialchars($isEnglish ? 'No menu items yet.' : 'Noch keine Menüeinträge vorhanden.', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-add-root-label="<?php echo htmlspecialchars($isEnglish ? 'Add item' : 'Eintrag hinzufügen', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-add-child-label="<?php echo htmlspecialchars($isEnglish ? 'Add child' : 'Unterpunkt hinzufügen', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-add-after-label="<?php echo htmlspecialchars($isEnglish ? 'Add below' : 'Darunter hinzufügen', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-remove-label="<?php echo htmlspecialchars($isEnglish ? 'Remove item' : 'Eintrag entfernen', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-up-label="<?php echo htmlspecialchars($isEnglish ? 'Move up' : 'Nach oben', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-down-label="<?php echo htmlspecialchars($isEnglish ? 'Move down' : 'Nach unten', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-indent-label="<?php echo htmlspecialchars($isEnglish ? 'Move deeper' : 'Eine Ebene tiefer', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-outdent-label="<?php echo htmlspecialchars($isEnglish ? 'Move higher' : 'Eine Ebene höher', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-reorder-group-label="<?php echo htmlspecialchars($isEnglish ? 'Menu item actions' : 'Aktionen für Menüeintrag', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-label-field="<?php echo htmlspecialchars($isEnglish ? 'Label' : 'Bezeichnung', ENT_QUOTES, 'UTF-8'); ?>"
                 data-menu-url-field="<?php echo htmlspecialchars($isEnglish ? 'URL' : 'URL', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="phinit-menu-editor__header">
                    <label class="form-label mb-0" for="<?php echo $idAttr; ?>_list"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            data-menu-action="add-root"
                            aria-label="<?php echo htmlspecialchars($isEnglish ? 'Add top-level menu item' : 'Menüeintrag auf oberster Ebene hinzufügen', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($isEnglish ? '+ Add item' : '+ Eintrag hinzufügen', ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                </div>
                <?php if ($targetMenuSlug !== ''): ?>
                    <p class="phinit-menu-editor__target mb-2">
                        <span><?php echo htmlspecialchars($isEnglish ? 'Target slug' : 'Ziel-Slug', ENT_QUOTES, 'UTF-8'); ?>:</span>
                        <code><?php echo htmlspecialchars($targetMenuSlug, ENT_QUOTES, 'UTF-8'); ?></code>
                    </p>
                <?php endif; ?>
                <div id="<?php echo $idAttr; ?>_list"
                     class="phinit-menu-editor__list"
                     data-menu-tree
                     role="tree"
                     tabindex="0"
                     aria-label="<?php echo htmlspecialchars($menuAriaLabel, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <p class="form-text phinit-menu-editor__hint" id="<?php echo $idAttr; ?>_hint">
                    <?php echo htmlspecialchars(
                        $isEnglish
                            ? 'Use buttons to add, nest, remove, and reorder items. Shortcut: Alt + Arrow keys on focused row.'
                            : 'Mit den Buttons kannst du Einträge hinzufügen, verschachteln, löschen und sortieren. Shortcut: Alt + Pfeiltasten auf fokussierter Zeile.',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </p>
            </div>
            <textarea id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>"
                      class="visually-hidden"
                      rows="<?php echo (int) ($field['rows'] ?? 8); ?>"
                      spellcheck="false"
                      autocapitalize="off"
                      aria-hidden="true"
                      tabindex="-1"
                      data-menu-source
                      hidden><?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?></textarea>

        <?php elseif (($field['type'] ?? 'text') === 'widget_order'): ?>
            <?php
            $widgetOptions = is_array($field['options'] ?? null) ? $field['options'] : [];
            $configuredOrder = array_values(array_filter(array_map(
                static fn(string $item): string => strtolower(trim($item)),
                preg_split('/[\r\n,;|]+/', $currentValue, -1, PREG_SPLIT_NO_EMPTY) ?: []
            ), static fn(string $item): bool => array_key_exists($item, $widgetOptions)));
            $defaultOrder = array_values(array_filter(array_map(
                static fn(string $item): string => strtolower(trim($item)),
                preg_split('/[\r\n,;|]+/', (string) ($field['default'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: []
            ), static fn(string $item): bool => array_key_exists($item, $widgetOptions)));
            if ($defaultOrder === []) {
                $defaultOrder = array_keys($widgetOptions);
            }
            $configuredOrder = phinit_merge_widget_order($configuredOrder, $defaultOrder);
            $currentValue = implode("\n", $configuredOrder);
            ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <div class="phinit-widget-order" data-widget-order-control>
                <input type="hidden"
                       id="<?php echo $idAttr; ?>"
                       name="<?php echo $nameAttr; ?>"
                       value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                       data-widget-order-input>
                <div class="phinit-widget-order__list" data-widget-order-list>
                    <?php foreach ($configuredOrder as $widgetKey): ?>
                    <div class="phinit-widget-order__item" data-widget-order-item data-widget-key="<?php echo htmlspecialchars($widgetKey, ENT_QUOTES); ?>">
                        <span class="phinit-widget-order__handle" aria-hidden="true">↕</span>
                        <span class="phinit-widget-order__label"><?php echo htmlspecialchars((string) ($widgetOptions[$widgetKey] ?? $widgetKey), ENT_QUOTES); ?></span>
                        <div class="phinit-widget-order__actions" aria-label="<?php echo htmlspecialchars($isEnglish ? 'Change order' : 'Reihenfolge ändern', ENT_QUOTES); ?>">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-widget-order-action="up" aria-label="<?php echo htmlspecialchars($isEnglish ? 'Move up' : 'Nach oben', ENT_QUOTES); ?>">↑</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-widget-order-action="down" aria-label="<?php echo htmlspecialchars($isEnglish ? 'Move down' : 'Nach unten', ENT_QUOTES); ?>">↓</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif (($field['type'] ?? 'text') === 'url'): ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="text" id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                   class="form-control"
                   inputmode="url"
                   placeholder="<?php echo htmlspecialchars($isEnglish ? 'https://example.com, contact, /contact, en/contact' : 'https://example.com, kontakt, /kontakt, en/contact', ENT_QUOTES); ?>">

        <?php elseif (($field['type'] ?? 'text') === 'number'): ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="number" id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                   class="form-control phinit-customizer__number-input"
                   step="<?php echo htmlspecialchars((string) ($field['step'] ?? 'any'), ENT_QUOTES); ?>"
                   <?php echo isset($field['min']) ? 'min="' . htmlspecialchars((string) $field['min'], ENT_QUOTES) . '"' : ''; ?>
                   <?php echo isset($field['max']) ? 'max="' . htmlspecialchars((string) $field['max'], ENT_QUOTES) . '"' : ''; ?>>

        <?php elseif (($field['type'] ?? 'text') === 'post_picker'): ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <?php $postRows = phinit_get_customizer_post_picker_rows(); ?>
            <select id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>" class="form-select">
                <option value=""><?php echo htmlspecialchars($isEnglish ? '— No post —' : '— Kein Beitrag —', ENT_QUOTES); ?></option>
                <?php foreach ($postRows as $postRow): ?>
                <option value="<?php echo (int) ($postRow['id'] ?? 0); ?>"
                    <?php echo $currentValue === (string) ($postRow['id'] ?? '') ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars((string) ($postRow['title'] ?? ''), ENT_QUOTES); ?>
                    <?php echo htmlspecialchars(' [' . (string) ($postRow['status_label'] ?? '') . ']', ENT_QUOTES); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php else: ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="text" id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>" class="form-control">
        <?php endif; ?>

        <?php if ($description !== ''): ?>
            <div class="form-text"><?php echo htmlspecialchars($description, ENT_QUOTES); ?></div>
        <?php endif; ?>
    </div>
    <?php
}
