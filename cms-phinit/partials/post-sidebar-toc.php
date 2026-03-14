<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tocItems = isset($tocItems) && is_array($tocItems) ? $tocItems : [];
$tocHeaderText = isset($tocHeaderText) ? (string) $tocHeaderText : '📋 Inhaltsverzeichnis';

if (empty($tocItems)) {
    return;
}
?>
<div class="toc">
    <div class="toc-title"><?php echo htmlspecialchars($tocHeaderText, ENT_QUOTES); ?></div>
    <ul class="toc-list" role="list">
        <?php foreach ($tocItems as $item): ?>
        <li class="<?php echo (($item['level'] ?? 0) === 3) ? 'toc-h3' : ''; ?>">
            <a href="#<?php echo htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES); ?>">
                <?php echo phinit_escape_text($item['text'] ?? ''); ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
