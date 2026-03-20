<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tocItems = isset($tocItems) && is_array($tocItems) ? $tocItems : [];
$tocHeaderText = isset($tocHeaderText) ? (string) $tocHeaderText : 'Inhaltsverzeichnis';

$tocTree = function_exists('phinit_build_toc_tree') ? phinit_build_toc_tree($tocItems) : [];

if (empty($tocTree)) {
    return;
}
?>
<?php
$renderInlineTocTree = static function (array $nodes, bool $nested = false) use (&$renderInlineTocTree): void {
    if ($nodes === []) {
        return;
    }
    ?>
    <ul class="toc-inline__list<?php echo $nested ? ' toc-inline__list--nested' : ''; ?>" role="list">
        <?php foreach ($nodes as $node): ?>
        <?php $level = max(1, (int) ($node['level'] ?? 2)); ?>
        <li class="toc-inline__item toc-inline__item--level-<?php echo $level; ?>">
            <a class="toc-inline__link" href="#<?php echo htmlspecialchars((string) ($node['id'] ?? ''), ENT_QUOTES); ?>">
                <?php echo phinit_escape_text($node['text'] ?? ''); ?>
            </a>
            <?php if (!empty($node['children']) && is_array($node['children'])): ?>
                <?php $renderInlineTocTree($node['children'], true); ?>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
};
?>
<details class="toc-inline" data-inline-toc data-anim data-anim-delay="1">
    <summary class="toc-inline__toggle">
        <span class="toc-inline__toggle-main">
            <span class="toc-inline__toggle-icon" aria-hidden="true">📋</span>
            <span class="toc-inline__toggle-text"><?php echo htmlspecialchars($tocHeaderText, ENT_QUOTES); ?></span>
        </span>
        <span class="toc-inline__toggle-meta">
            <span class="toc-inline__count"><?php echo (int) count($tocItems); ?> Punkte</span>
            <span class="toc-inline__chevron" aria-hidden="true">▾</span>
        </span>
    </summary>
    <nav class="toc-inline__body" aria-label="Inhaltsverzeichnis des Artikels">
        <?php $renderInlineTocTree($tocTree); ?>
    </nav>
</details>
