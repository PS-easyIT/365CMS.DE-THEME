<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$blogQuery = isset($blogQuery) ? trim((string) $blogQuery) : '';
$blogTotal = isset($blogTotal) ? (int) $blogTotal : 0;
?>
<div class="blog-archive-bar" data-anim data-anim-delay="1">
    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/"
       class="blog-archive-back">&#8592; Startseite</a>
    <form class="blog-search-form" method="GET"
          action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog">
        <input type="search" name="q"
               placeholder="Beiträge durchsuchen&hellip;"
               value="<?php echo htmlspecialchars($blogQuery, ENT_QUOTES); ?>">
        <button type="submit" aria-label="Suchen">&#128269;</button>
    </form>
</div>

<?php if ($blogQuery !== ''): ?>
<p class="blog-search-hint">
    Suchergebnisse für <strong>&bdquo;<?php echo htmlspecialchars($blogQuery, ENT_QUOTES); ?>&ldquo;</strong>
    &mdash; <?php echo $blogTotal; ?> Treffer
    &nbsp;<a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog">&#10005; Zurücksetzen</a>
</p>
<?php endif; ?>
