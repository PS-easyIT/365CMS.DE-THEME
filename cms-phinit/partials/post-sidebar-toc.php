<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tocItems = isset($tocItems) && is_array($tocItems) ? $tocItems : [];
$tocHeaderText = isset($tocHeaderText) ? (string) $tocHeaderText : '📋 Inhaltsverzeichnis';

$tocTree = function_exists('phinit_build_toc_tree') ? phinit_build_toc_tree($tocItems) : [];

if (empty($tocTree)) {
    return;
}
?>
<?php
$renderTocTree = static function (array $nodes, bool $nested = false) use (&$renderTocTree): void {
    if ($nodes === []) {
        return;
    }
    ?>
    <ul class="toc-list<?php echo $nested ? ' toc-list--nested' : ''; ?>" role="list">
        <?php foreach ($nodes as $node): ?>
        <?php $level = max(1, (int) ($node['level'] ?? 2)); ?>
        <li class="toc-item toc-item--level-<?php echo $level; ?>">
            <a class="toc-link" href="#<?php echo htmlspecialchars((string) ($node['id'] ?? ''), ENT_QUOTES); ?>">
                <?php echo phinit_escape_text($node['text'] ?? ''); ?>
            </a>
            <?php if (!empty($node['children']) && is_array($node['children'])): ?>
                <?php $renderTocTree($node['children'], true); ?>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
};
?>
<nav class="toc" aria-label="Inhaltsverzeichnis des Artikels">
    <div class="toc-title"><?php echo htmlspecialchars($tocHeaderText, ENT_QUOTES); ?></div>
    <?php $renderTocTree($tocTree); ?>
</nav>
