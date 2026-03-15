<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$blogPage = isset($blogPage) ? (int) $blogPage : 1;
$blogPages = isset($blogPages) ? (int) $blogPages : 1;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$blogBaseUrl = isset($blogBaseUrl) && trim((string) $blogBaseUrl) !== ''
    ? (string) $blogBaseUrl
    : (function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog');
$queryParams = isset($queryParams) && is_array($queryParams) ? $queryParams : [];
$pageParamName = isset($pageParamName) && trim((string) $pageParamName) !== '' ? (string) $pageParamName : 'page';

if ($blogPages <= 1) {
    return;
}

$buildPageUrl = static function (int $targetPage) use ($blogBaseUrl, $queryParams, $pageParamName): string {
    $params = $queryParams;

    if ($targetPage > 1) {
        $params[$pageParamName] = $targetPage;
    } else {
        unset($params[$pageParamName]);
    }

    $queryString = http_build_query($params);
    return $blogBaseUrl . ($queryString !== '' ? '?' . $queryString : '');
};
?>
<nav class="pagination pagination--spaced" aria-label="<?php echo htmlspecialchars(phinit_t('archive_navigation', [], $currentLocale), ENT_QUOTES); ?>">
    <?php if ($blogPage > 1): ?>
    <a href="<?php echo htmlspecialchars($buildPageUrl($blogPage - 1), ENT_QUOTES); ?>"
       class="page-link" aria-label="<?php echo htmlspecialchars(phinit_t('previous_page_aria', [], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('previous_page', [], $currentLocale), ENT_QUOTES); ?></a>
    <?php endif; ?>

    <?php for ($page = 1; $page <= $blogPages; $page++): ?>
        <?php if ($page === 1 || $page === $blogPages || abs($page - $blogPage) <= 2): ?>
        <a href="<?php echo htmlspecialchars($buildPageUrl($page), ENT_QUOTES); ?>"
           class="page-link <?php echo $page === $blogPage ? 'active' : ''; ?>"
           <?php echo $page === $blogPage ? 'aria-current="page"' : ''; ?>>
            <?php echo $page; ?>
        </a>
        <?php elseif (abs($page - $blogPage) === 3): ?>
        <span class="page-link dots" aria-hidden="true">…</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($blogPage < $blogPages): ?>
     <a href="<?php echo htmlspecialchars($buildPageUrl($blogPage + 1), ENT_QUOTES); ?>"
         class="page-link" aria-label="<?php echo htmlspecialchars(phinit_t('next_page_aria', [], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('next_page', [], $currentLocale), ENT_QUOTES); ?></a>
    <?php endif; ?>
</nav>
