<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$post = $post ?? null;
if (!is_array($post) && !is_object($post)) {
    return;
}

$card = function_exists('phinit_build_post_template_meta_items') ? phinit_build_post_template_meta_items($post) : ['title' => '', 'items' => []];
$items = is_array($card['items'] ?? null) ? $card['items'] : [];
if ($items === []) {
    return;
}

$title = trim((string) ($card['title'] ?? 'Zusatzinfos'));
if ($title === '') {
    $title = 'Zusatzinfos';
}
?>
<aside class="post-template-meta-card" aria-label="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
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
            if ($label === '') {
                continue;
            }
            ?>
            <div class="post-template-meta-card__item">
                <dt><?php echo htmlspecialchars($label, ENT_QUOTES); ?></dt>
                <dd>
                    <?php if ($type === 'url' && $url !== ''): ?>
                        <a href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?>
                        </a>
                    <?php elseif (is_array($value)): ?>
                        <ul class="post-template-meta-card__chips" role="list">
                            <?php foreach ($value as $entry): ?>
                                <?php $entry = trim((string) $entry); ?>
                                <?php if ($entry !== ''): ?>
                                    <li><?php echo htmlspecialchars($entry, ENT_QUOTES); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif (in_array((string) ($item['key'] ?? ''), ['version', 'tool'], true)): ?>
                        <code class="inline-code"><?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?></code>
                    <?php else: ?>
                        <?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?>
                    <?php endif; ?>
                </dd>
            </div>
        <?php endforeach; ?>
    </dl>
</aside>
