<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tocItems = isset($tocItems) && is_array($tocItems) ? $tocItems : [];
$tocHeaderText = isset($tocHeaderText) ? (string) $tocHeaderText : 'Inhaltsverzeichnis';

if (empty($tocItems)) {
    return;
}
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
        <ul role="list">
            <?php foreach ($tocItems as $item): ?>
            <li class="<?php echo (($item['level'] ?? 0) === 3) ? 'toc-h3' : ''; ?>">
                <a href="#<?php echo htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES); ?>">
                    <?php echo phinit_escape_text($item['text'] ?? ''); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</details>
