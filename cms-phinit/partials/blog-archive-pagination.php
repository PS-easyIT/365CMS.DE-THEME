<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$blogPage = isset($blogPage) ? (int) $blogPage : 1;
$blogPages = isset($blogPages) ? (int) $blogPages : 1;

if ($blogPages <= 1) {
    return;
}
?>
<nav class="pagination pagination--spaced" aria-label="Archiv-Seitennavigation">
    <?php if ($blogPage > 1): ?>
    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $blogPage - 1; ?>"
       class="page-link" aria-label="Vorherige Seite">← Zurück</a>
    <?php endif; ?>

    <?php for ($page = 1; $page <= $blogPages; $page++): ?>
        <?php if ($page === 1 || $page === $blogPages || abs($page - $blogPage) <= 2): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $page; ?>"
           class="page-link <?php echo $page === $blogPage ? 'active' : ''; ?>"
           <?php echo $page === $blogPage ? 'aria-current="page"' : ''; ?>>
            <?php echo $page; ?>
        </a>
        <?php elseif (abs($page - $blogPage) === 3): ?>
        <span class="page-link dots" aria-hidden="true">…</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($blogPage < $blogPages): ?>
    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $blogPage + 1; ?>"
       class="page-link" aria-label="Nächste Seite">Weiter →</a>
    <?php endif; ?>
</nav>
