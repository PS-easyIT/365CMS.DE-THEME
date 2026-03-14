<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$blogQuery = isset($blogQuery) ? trim((string) $blogQuery) : '';
$blogTotal = isset($blogTotal) ? (int) $blogTotal : 0;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$homeUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/';
$blogUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog';
?>
<div class="blog-archive-bar" data-anim data-anim-delay="1">
    <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES); ?>"
       class="blog-archive-back">&#8592; <?php echo htmlspecialchars(phinit_t('home', [], $currentLocale), ENT_QUOTES); ?></a>
    <form class="blog-search-form" method="GET"
          action="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>">
        <input type="search" name="q"
               placeholder="<?php echo htmlspecialchars(phinit_t('search_posts_placeholder', [], $currentLocale), ENT_QUOTES); ?>"
               value="<?php echo htmlspecialchars($blogQuery, ENT_QUOTES); ?>">
        <button type="submit" aria-label="<?php echo htmlspecialchars(phinit_t('search_submit', [], $currentLocale), ENT_QUOTES); ?>">&#128269;</button>
    </form>
</div>

<?php if ($blogQuery !== ''): ?>
<p class="blog-search-hint">
    <?php echo htmlspecialchars(phinit_t('search_results_for', [], $currentLocale), ENT_QUOTES); ?> <strong>&bdquo;<?php echo htmlspecialchars($blogQuery, ENT_QUOTES); ?>&ldquo;</strong>
    &mdash; <?php echo htmlspecialchars(phinit_t('hits', ['count' => $blogTotal], $currentLocale), ENT_QUOTES); ?>
    &nbsp;<a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>">&#10005; <?php echo htmlspecialchars(phinit_t('reset', [], $currentLocale), ENT_QUOTES); ?></a>
</p>
<?php endif; ?>
