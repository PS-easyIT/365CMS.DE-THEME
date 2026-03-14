<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$_pg_toc = isset($_pg_toc) && is_array($_pg_toc) ? $_pg_toc : [];
$pageTocClass = isset($pageTocClass) ? (string) $pageTocClass : '';
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
        <ol class="page-toc__list">
            <?php foreach ($_pg_toc as $_ti): ?>
            <li class="page-toc__item<?php echo (($_ti['level'] ?? 0) === 3) ? ' page-toc__item--nested' : ''; ?>">
                <a href="#<?php echo htmlspecialchars((string) ($_ti['id'] ?? ''), ENT_QUOTES); ?>" class="page-toc__link"><?php echo htmlspecialchars((string) ($_ti['text'] ?? ''), ENT_QUOTES); ?></a>
            </li>
            <?php endforeach; ?>
        </ol>
    </nav>
</details>
