<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
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
    $currentValue = (string) $value;
    $description = (string) ($field['description'] ?? '');
    ?>
    <div class="mb-3">
        <?php if (($field['type'] ?? 'text') === 'checkbox'): ?>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                       id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="1"
                       <?php echo ($currentValue !== '' && $currentValue !== '0') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="<?php echo $id; ?>">
                    <?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?>
                </label>
            </div>

        <?php elseif (($field['type'] ?? 'text') === 'color'): ?>
            <label class="form-label"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <div class="input-group phinit-customizer__color-group">
                <input type="color"
                       id="<?php echo $id; ?>"
                       value="<?php echo htmlspecialchars($currentValue ?: '#000000', ENT_QUOTES); ?>"
                       class="form-control form-control-color phinit-customizer__color-input"
                       oninput="syncColor('<?php echo $id; ?>','<?php echo $id; ?>_txt','<?php echo $name; ?>')">
                <input type="text"
                       id="<?php echo $id; ?>_txt"
                       value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                       class="form-control font-monospace phinit-customizer__color-text"
                       oninput="syncColorTxt('<?php echo $id; ?>','<?php echo $id; ?>_txt','<?php echo $name; ?>')">
                <input type="hidden" name="<?php echo $name; ?>"
                       id="<?php echo $name; ?>"
                       value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>">
            </div>

        <?php elseif (($field['type'] ?? 'text') === 'select'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="form-select">
                <?php foreach (($field['options'] ?? []) as $optionValue => $optionLabel): ?>
                <option value="<?php echo htmlspecialchars((string) $optionValue, ENT_QUOTES); ?>"
                    <?php echo $currentValue === (string) $optionValue ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars((string) $optionLabel, ENT_QUOTES); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php elseif (($field['type'] ?? 'text') === 'textarea'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <textarea id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                      class="form-control" rows="<?php echo (int) ($field['rows'] ?? 3); ?>"><?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?></textarea>

        <?php elseif (($field['type'] ?? 'text') === 'url'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="url" id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                   class="form-control"
                   inputmode="url"
                   placeholder="https://example.com/ oder /interner-pfad">

        <?php elseif (($field['type'] ?? 'text') === 'number'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="number" id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>"
                   class="form-control phinit-customizer__number-input"
                   step="<?php echo htmlspecialchars((string) ($field['step'] ?? 'any'), ENT_QUOTES); ?>"
                   <?php echo isset($field['min']) ? 'min="' . htmlspecialchars((string) $field['min'], ENT_QUOTES) . '"' : ''; ?>
                   <?php echo isset($field['max']) ? 'max="' . htmlspecialchars((string) $field['max'], ENT_QUOTES) . '"' : ''; ?>>

        <?php elseif (($field['type'] ?? 'text') === 'post_picker'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <?php
            $postRows = [];
            try {
                $_db = \CMS\Database::instance();
                $postRows = array_map(
                    static fn($row) => (array) $row,
                    $_db->get_results("SELECT id, title FROM {$_db->getPrefix()}posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 300") ?: []
                );
            } catch (\Throwable $_e) {
                $postRows = [];
            }
            ?>
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="form-select">
                <option value="">— Kein Beitrag —</option>
                <?php foreach ($postRows as $postRow): ?>
                <option value="<?php echo (int) ($postRow['id'] ?? 0); ?>"
                    <?php echo $currentValue === (string) ($postRow['id'] ?? '') ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars((string) ($postRow['title'] ?? ''), ENT_QUOTES); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php else: ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES); ?></label>
            <input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                   value="<?php echo htmlspecialchars($currentValue, ENT_QUOTES); ?>" class="form-control">
        <?php endif; ?>

        <?php if ($description !== ''): ?>
            <div class="form-text"><?php echo htmlspecialchars($description, ENT_QUOTES); ?></div>
        <?php endif; ?>
    </div>
    <?php
}
