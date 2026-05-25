<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$_pg_toc = isset($_pg_toc) && is_array($_pg_toc) ? $_pg_toc : [];
$pageTocClass = isset($pageTocClass) ? (string) $pageTocClass : '';
$pageTocTree = function_exists('phinit_build_toc_tree') ? phinit_build_toc_tree($_pg_toc) : [];

if ($pageTocTree === []) {
    return;
}
?>
<?php
$renderPageTocTree = static function (array $nodes, bool $nested = false) use (&$renderPageTocTree): void {
    if ($nodes === []) {
        return;
    }
    ?>
    <ul class="page-toc__list<?php echo $nested ? ' page-toc__list--nested' : ''; ?>" role="list">
        <?php foreach ($nodes as $node): ?>
        <?php $level = max(1, (int) ($node['level'] ?? 2)); ?>
        <li class="page-toc__item page-toc__item--level-<?php echo $level; ?>">
            <a href="#<?php echo htmlspecialchars((string) ($node['id'] ?? ''), ENT_QUOTES); ?>" class="page-toc__link"><?php echo htmlspecialchars((string) ($node['text'] ?? ''), ENT_QUOTES); ?></a>
            <?php if (!empty($node['children']) && is_array($node['children'])): ?>
                <?php $renderPageTocTree($node['children'], true); ?>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
};
?>
<details class="toc-box page-toc page-toc--inline<?php echo htmlspecialchars($pageTocClass, ENT_QUOTES); ?>" data-inline-toc<?php echo $pageTocClass === '' ? ' data-anim' : ''; ?>>
    <summary class="page-toc__summary">
        <span class="page-toc__summary-main">
            <span class="page-toc__summary-icon" aria-hidden="true">&#x1F4CB;</span>
            <span class="page-toc__summary-copy">
                <span class="page-toc__eyebrow">Schnellnavigation</span>
                <span class="page-toc__summary-text">Inhaltsverzeichnis</span>
            </span>
        </span>
        <span class="page-toc__summary-meta">
            <span class="page-toc__count"><?php echo (int) count($_pg_toc); ?> Punkte</span>
            <span class="page-toc__hint" aria-hidden="true"></span>
            <span class="page-toc__chevron" aria-hidden="true">▾</span>
        </span>
    </summary>
    <nav class="page-toc__body" aria-label="Inhaltsverzeichnis der Seite">
        <?php $renderPageTocTree($pageTocTree); ?>
    </nav>
</details>
