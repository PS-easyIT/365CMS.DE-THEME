<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$post = $post ?? null;
if (!is_array($post) && !is_object($post)) {
    return;
}

$card = function_exists('phinit_build_post_template_meta_items') ? phinit_build_post_template_meta_items($post) : ['title' => '', 'template_id' => '', 'items' => []];
$items = is_array($card['items'] ?? null) ? $card['items'] : [];
if ($items === []) {
    return;
}

$title = trim((string) ($card['title'] ?? 'Zusatzinfos'));
if ($title === '') {
    $title = 'Zusatzinfos';
}

$templateId = strtolower(trim((string) ($card['template_id'] ?? '')));
$templateClass = preg_replace('/[^a-z0-9_-]/', '', $templateId) ?? '';
$cardClasses = 'post-template-meta-card';
if ($templateClass !== '') {
    $cardClasses .= ' post-template-meta-card--' . $templateClass;
}

$renderUrlIcon = static function (string $key): string {
    if (str_contains($key, 'github')) {
        return 'gh';
    }

    if (str_contains($key, 'docs')) {
        return '↗';
    }

    return '🌐';
};
?>
<aside class="<?php echo htmlspecialchars($cardClasses, ENT_QUOTES); ?>" aria-label="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
    <div class="post-template-meta-card__title"><?php echo htmlspecialchars($title, ENT_QUOTES); ?></div>
    <dl class="post-template-meta-card__list">
        <?php foreach ($items as $item): ?>
            <?php
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));
            $type = trim((string) ($item['type'] ?? 'text'));
            $value = $item['value'] ?? '';
            $url = trim((string) ($item['url'] ?? ''));
            $key = trim((string) ($item['key'] ?? ''));
            $display = trim((string) ($item['display'] ?? ''));
            if ($label === '') {
                continue;
            }

            $itemClasses = ['post-template-meta-card__item'];
            if ($type === 'url' && $url !== '') {
                $itemClasses[] = 'post-template-meta-card__item--url';
            }
            if (is_array($value)) {
                $itemClasses[] = 'post-template-meta-card__item--array';
            }

            $itemClassAttr = implode(' ', $itemClasses);
            $renderAsIcon = $type === 'url' && $url !== '' && ($display === 'icon' || str_ends_with($key, '_url'));
            ?>
            <div class="<?php echo htmlspecialchars($itemClassAttr, ENT_QUOTES); ?>">
                <dt><?php echo htmlspecialchars($label, ENT_QUOTES); ?></dt>
                <dd>
                    <?php if ($type === 'url' && $url !== ''): ?>
                        <?php if ($renderAsIcon): ?>
                            <a class="post-template-meta-card__icon-link<?php echo str_contains($key, 'github') ? ' post-template-meta-card__icon-link--github' : ''; ?>" href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($label . ': ' . (string) $value, ENT_QUOTES); ?>">
                                <span class="post-template-meta-card__url-icon" aria-hidden="true"><?php echo htmlspecialchars($renderUrlIcon($key), ENT_QUOTES); ?></span>
                                <span class="visually-hidden"><?php echo htmlspecialchars($label, ENT_QUOTES); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?>
                            </a>
                        <?php endif; ?>
                    <?php elseif (is_array($value)): ?>
                        <ul class="post-template-meta-card__chips" role="list">
                            <?php foreach ($value as $entry): ?>
                                <?php $entry = trim((string) $entry); ?>
                                <?php if ($entry !== ''): ?>
                                    <li><?php echo htmlspecialchars($entry, ENT_QUOTES); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif (in_array((string) ($item['key'] ?? ''), ['version', 'tool', 'module', 'api_module'], true)): ?>
                        <code class="inline-code"><?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?></code>
                    <?php else: ?>
                        <?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?>
                    <?php endif; ?>
                </dd>
            </div>
        <?php endforeach; ?>
    </dl>
</aside>
