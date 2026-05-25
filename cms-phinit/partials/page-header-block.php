<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$page = isset($page) && is_array($page) ? $page : [];
$_pg_showHero = isset($_pg_showHero) ? (bool) $_pg_showHero : true;
$_pg_showDate = isset($_pg_showDate) ? (bool) $_pg_showDate : true;
$favoriteControl = isset($favoriteControl) && is_array($favoriteControl) ? $favoriteControl : [];
$pageHeroImage = function_exists('phinit_normalize_public_media_url')
    ? phinit_normalize_public_media_url((string) ($page['featured_image'] ?? ''), false, defined('SITE_URL') ? SITE_URL : '')
    : (string) ($page['featured_image'] ?? '');
$hasHeroImage = $pageHeroImage !== '' && $_pg_showHero;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$createdAt = trim((string) ($page['created_at'] ?? ''));
$updatedAt = trim((string) ($page['updated_at'] ?? ''));
$displayDate = $updatedAt !== '' ? $updatedAt : $createdAt;
$dateTimestamp = $displayDate !== '' ? strtotime($displayDate) : false;
$dateLabel = $updatedAt !== '' ? 'Aktualisiert' : 'Veröffentlicht';
$dateIcon = $updatedAt !== '' ? '🔄' : '📅';
$pageAuthorId = (int) ($page['author_id'] ?? 0);
$pageAuthorName = trim((string) ($page['author_name'] ?? ($page['author_display_name'] ?? '')));

if ($pageAuthorName === '' && $pageAuthorId > 0) {
    try {
        $db = \CMS\Database::instance();
        $authorRow = $db->get_row(
            "SELECT COALESCE(NULLIF(display_name, ''), NULLIF(username, ''), '') AS author_name FROM {$db->prefix()}users WHERE id = ? LIMIT 1",
            [$pageAuthorId]
        );
        if ($authorRow !== null) {
            $pageAuthorName = trim((string) ($authorRow->author_name ?? ''));
        }
    } catch (\Throwable) {
        $pageAuthorName = '';
    }
}

$pageAuthorUrl = $pageAuthorId > 0
    ? (function_exists('phinit_localized_href') ? phinit_localized_href('/author/user-' . $pageAuthorId, $currentLocale, defined('SITE_URL') ? SITE_URL : '') : rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/') . '/author/user-' . $pageAuthorId)
    : '';
$pageReadingTime = 0;
if (function_exists('phinit_reading_time') && function_exists('phinit_customizer_bool') && phinit_customizer_bool('posts', 'show_reading_time', true)) {
    $pageReadingTimeSource = isset($safePageContent) ? (string) $safePageContent : (string) ($page['content'] ?? '');
    if (trim(strip_tags($pageReadingTimeSource)) !== '') {
        $pageReadingTime = phinit_reading_time($pageReadingTimeSource);
    }
}
$showPageMeta = ($_pg_showDate && $dateTimestamp !== false) || $pageAuthorName !== '' || $pageReadingTime > 0;
?>
<div class="page-header-block<?php echo $hasHeroImage ? ' page-header-block--with-image' : ''; ?>" data-anim>
    <?php echo phinit_render_favorite_button($favoriteControl); ?>
    <?php if ($hasHeroImage): ?>
    <img class="page-hero-img"
         src="<?php echo htmlspecialchars($pageHeroImage, ENT_QUOTES); ?>"
         alt="<?php echo htmlspecialchars((string) ($page['title'] ?? ''), ENT_QUOTES); ?>"
            <?php echo phinit_image_loading_attributes(true); ?>
            <?php echo phinit_image_dimension_attributes($pageHeroImage); ?>>
    <?php endif; ?>
    <div class="page-header-body">
        <h1><?php echo htmlspecialchars((string) ($page['title'] ?? ''), ENT_QUOTES); ?></h1>
        <?php if ($showPageMeta): ?>
        <div class="page-meta" aria-label="Seiten-Metainformationen">
            <?php if ($_pg_showDate && $dateTimestamp !== false): ?>
            <span class="page-meta__item page-meta__item--date">
                <span class="page-meta__icon" aria-hidden="true"><?php echo htmlspecialchars($dateIcon, ENT_QUOTES); ?></span>
                <span class="page-meta__label"><?php echo htmlspecialchars($dateLabel, ENT_QUOTES); ?></span>
                <strong>
                    <time datetime="<?php echo htmlspecialchars($displayDate, ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars(phinit_format_date($displayDate, 'numeric', $currentLocale), ENT_QUOTES); ?>
                    </time>
                </strong>
            </span>
            <?php endif; ?>
            <?php if ($pageAuthorName !== ''): ?>
            <span class="page-meta__item page-meta__item--author">
                <span class="page-meta__icon" aria-hidden="true">👤</span>
                <span class="page-meta__label">Autor</span>
                <strong>
                <?php if ($pageAuthorUrl !== ''): ?>
                <a href="<?php echo htmlspecialchars($pageAuthorUrl, ENT_QUOTES); ?>" class="page-meta__link"><?php echo phinit_escape_text($pageAuthorName); ?></a>
                <?php else: ?>
                <?php echo phinit_escape_text($pageAuthorName); ?>
                <?php endif; ?>
                </strong>
            </span>
            <?php endif; ?>
            <?php if ($pageReadingTime > 0): ?>
            <span class="page-meta__item reading-time-badge" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $pageReadingTime], $currentLocale), ENT_QUOTES); ?>">
                <?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $pageReadingTime], $currentLocale), ENT_QUOTES); ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
