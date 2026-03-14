<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : '';
$queryTrimmed = isset($queryTrimmed) ? (string) $queryTrimmed : '';
$searchEmptyStateMode = isset($searchEmptyStateMode) ? (string) $searchEmptyStateMode : 'idle';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$searchUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/search', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/search';
$blogUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog';
?>
<?php if ($searchEmptyStateMode === 'query'): ?>
<section class="search-empty-state search-empty-state--query" data-anim data-anim-delay="2">
    <p class="search-empty-state__icon">🔍</p>
    <h2 class="search-empty-state__title"><?php echo htmlspecialchars(phinit_t('no_results', [], $currentLocale), ENT_QUOTES); ?></h2>
    <p class="search-empty-state__text">
        <?php echo htmlspecialchars(phinit_t('no_results_for', ['query' => $queryTrimmed], $currentLocale), ENT_QUOTES); ?>
    </p>
    <ul class="search-empty-state__tips">
        <li><?php echo htmlspecialchars(phinit_t('search_tip_1', [], $currentLocale), ENT_QUOTES); ?></li>
        <li><?php echo htmlspecialchars(phinit_t('search_tip_2', [], $currentLocale), ENT_QUOTES); ?></li>
        <li><?php echo htmlspecialchars(phinit_t('search_tip_3', [], $currentLocale), ENT_QUOTES); ?></li>
    </ul>
    <div class="search-empty-state__actions">
        <a href="<?php echo htmlspecialchars($searchUrl, ENT_QUOTES); ?>" class="btn btn-outline"><?php echo htmlspecialchars(phinit_t('new_search', [], $currentLocale), ENT_QUOTES); ?></a>
    </div>
</section>
<?php else: ?>
<section class="search-empty-state search-empty-state--idle" data-anim data-anim-delay="2">
    <p class="search-empty-state__icon">🔎</p>
    <h2 class="search-empty-state__title"><?php echo htmlspecialchars(phinit_t('search_intro_title', [], $currentLocale), ENT_QUOTES); ?></h2>
    <p class="search-empty-state__text"><?php echo htmlspecialchars(phinit_t('search_intro_text', [], $currentLocale), ENT_QUOTES); ?></p>
    <div class="search-empty-state__actions">
        <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>" class="btn btn-outline"><?php echo htmlspecialchars(phinit_t('go_to_blog', [], $currentLocale), ENT_QUOTES); ?></a>
    </div>
</section>
<?php endif; ?>
