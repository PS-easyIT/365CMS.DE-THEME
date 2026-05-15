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
            $statusLabel = 'Entwurf';

            if ($status === 'private') {
                $statusLabel = 'Privat';
            } elseif ($status === 'published') {
                $statusLabel = ($publishedAt !== '' && $publishedAt > $currentDateTime)
                    ? 'Geplant'
                    : 'Veröffentlicht';
            }

            $title = trim((string) ($post['title'] ?? ''));
            if ($title === '') {
                $title = 'Beitrag #' . (int) ($post['id'] ?? 0);
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
 * Rendert ein einzelnes Formularfeld mit Tabler-CSS-Klassen.
 *
 * @param array<string, mixed> $field
 */
function phinit_render_field(string $tab, string $fieldKey, array $field, mixed $value): void
{
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

        <?php elseif (($field['type'] ?? 'text') === 'widget_order'): ?>
            <?php
            $widgetOptions = is_array($field['options'] ?? null) ? $field['options'] : [];
            $configuredOrder = array_values(array_filter(array_map(
                static fn(string $item): string => strtolower(trim($item)),
                preg_split('/[\r\n,;|]+/', $currentValue, -1, PREG_SPLIT_NO_EMPTY) ?: []
            ), static fn(string $item): bool => array_key_exists($item, $widgetOptions)));
            $configuredOrder = array_values(array_unique(array_merge($configuredOrder, array_keys($widgetOptions))));
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
                        <div class="phinit-widget-order__actions" aria-label="Reihenfolge ändern">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-widget-order-action="up" aria-label="Nach oben">↑</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-widget-order-action="down" aria-label="Nach unten">↓</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif (($field['type'] ?? 'text') === 'url'): ?>
            <label class="form-label" for="<?php echo $idAttr; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="url" id="<?php echo $idAttr; ?>" name="<?php echo $nameAttr; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                   class="form-control"
                   inputmode="url"
                   placeholder="https://example.com/ oder /interner-pfad">

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
                <option value="">— Kein Beitrag —</option>
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
