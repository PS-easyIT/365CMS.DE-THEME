<?php
/**
 * Partial: Artikel-Karte (article-card)
 *
 * Erwartete Variablen (via get_theme_part):
 *   @var array  $card         Post-Daten-Array (title, slug, featured_image,
 *                             category_name, excerpt, content, published_at, read_time)
 *   @var string $siteUrl      Base-URL der Site
 *   @var bool   $show_excerpt Excerpt anzeigen (default: true)
 *   @var bool   $show_meta    Meta-Zeile anzeigen (default: true)
 *   @var int    $exc_len      Maximale Excerpt-Länge (default: 180)
 *   @var bool   $show_cat     Kategorie in Meta anzeigen (default: true)
 *   @var bool   $show_date    Datum in Meta anzeigen (default: true)
 *   @var bool   $show_rt      Lesezeit in Meta anzeigen (default: true)
 *
 * Verwendung:
 *   get_theme_part('partials/post-card', [
 *       'card'    => $postArray,
 *       'siteUrl' => SITE_URL,
 *       ...
 *   ]);
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Variablen mit Default-Werten absichern
$card         = $card         ?? [];
$siteUrl      = $siteUrl      ?? (defined('SITE_URL') ? SITE_URL : '');
$show_excerpt = isset($show_excerpt) ? (bool)$show_excerpt : true;
$show_meta    = isset($show_meta)    ? (bool)$show_meta    : true;
$exc_len      = max(10, (int)($exc_len ?? 180));
$show_cat     = isset($show_cat)     ? (bool)$show_cat     : true;
$show_date    = isset($show_date)    ? (bool)$show_date    : true;
$show_rt      = isset($show_rt)      ? (bool)$show_rt      : true;
$above_the_fold_image = isset($above_the_fold_image) ? (bool) $above_the_fold_image : false;
$image_high_priority = isset($image_high_priority) ? (bool) $image_high_priority : true;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
if (is_array($card) && class_exists('CMS\\Services\\ContentLocalizationService')) {
    try {
        $card = \CMS\Services\ContentLocalizationService::getInstance()->localizePost($card, $currentLocale);
    } catch (\Throwable $_postCardLocalizationError) {
        // Fallback: Karte bleibt mit den bereits gelieferten Werten renderbar.
    }
}
$displayDate  = $card['published_at'] ?? ($card['created_at'] ?? null);
$permalinkService = class_exists('CMS\\Services\\PermalinkService') ? \CMS\Services\PermalinkService::getInstance() : null;
$permalink    = (string) ($card['permalink'] ?? '');
if ($permalink === '' && $permalinkService !== null) {
    $permalink = $permalinkService->buildPostUrl($card, $currentLocale);
}
$displaySlug = trim((string) ($card['slug'] ?? ''));
if ($displaySlug === '') {
    $displaySlug = trim((string) ($card['slug_en'] ?? ''));
}
if ($permalink === '') {
    $permalink = $siteUrl . '/blog/' . $displaySlug;
}

$displayTitle = trim((string) ($card['title'] ?? ''));
if ($displayTitle === '') {
    $displayTitle = trim((string) ($card['title_en'] ?? ''));
}

$displayExcerptSource = trim((string) ($card['excerpt'] ?? ''));
if ($displayExcerptSource === '') {
    $displayExcerptSource = trim((string) ($card['excerpt_en'] ?? ''));
}

$displayContentSource = trim((string) ($card['content'] ?? ''));
if ($displayContentSource === '') {
    $displayContentSource = trim((string) ($card['content_en'] ?? ''));
}

// Excerpt aufbereiten (Editor.js-JSON wird in Klartext gewandelt)
$_pc_excerpt = function_exists('phinit_excerpt_plain_text')
    ? phinit_excerpt_plain_text($displayExcerptSource)
    : strip_tags($displayExcerptSource);
if (empty(trim($_pc_excerpt)) && $displayContentSource !== '') {
    $_pc_excerpt = function_exists('phinit_excerpt_plain_text')
        ? phinit_excerpt_plain_text($displayContentSource)
        : strip_tags($displayContentSource);
}
$_pc_excerpt = mb_strimwidth($_pc_excerpt, 0, $exc_len, '…');

// Lesezeit berechnen
$_pc_rt = 0;
if ($show_rt && $show_meta) {
    $_pc_rt = !empty($card['read_time']) ? (int)$card['read_time'] : 0;
    if ($_pc_rt < 1 && $displayContentSource !== '') {
        $_pc_rt = function_exists('phinit_reading_time')
            ? phinit_reading_time($displayContentSource)
            : max(1, (int)round(str_word_count(strip_tags($displayContentSource)) / 220));
    }
}
?>
<article class="article-card">

    <div class="article-thumb">
        <?php if (!empty($card['featured_image'])): ?>
        <img src="<?php echo htmlspecialchars($card['featured_image'], ENT_QUOTES); ?>"
               alt="<?php echo phinit_escape_text($displayTitle); ?>"
                            <?php echo phinit_image_loading_attributes($above_the_fold_image, $image_high_priority); ?>
                             <?php echo phinit_image_dimension_attributes((string) ($card['featured_image'] ?? ''), 162, 215); ?>>
        <?php else: ?>
        <div class="article-thumb-placeholder" aria-hidden="true"><span>📄</span></div>
        <?php endif; ?>
        <?php if (!empty($card['category_name'])): ?>
        <span class="thumb-badge badge-teal"><?php echo phinit_escape_text($card['category_name'] ?? ''); ?></span>
        <?php endif; ?>
    </div>

    <div class="article-body">
        <h4>
            <a href="<?php echo htmlspecialchars($permalink, ENT_QUOTES); ?>">
                <?php echo phinit_escape_text($displayTitle !== '' ? $displayTitle : 'Ohne Titel'); ?>
            </a>
        </h4>
        <?php if ($show_excerpt && !empty(trim($_pc_excerpt))): ?>
        <p><?php echo htmlspecialchars($_pc_excerpt, ENT_QUOTES); ?></p>
        <?php endif; ?>
        <?php if ($show_meta): ?>
        <div class="article-meta">
            <?php if (($show_date && !empty($displayDate)) || ($show_rt && $_pc_rt > 0)): ?>
            <span class="article-meta__primary">
                <span class="article-meta__timing">
                    <?php if ($show_date && !empty($displayDate)): ?>
                    <span class="article-meta__date"><?php echo htmlspecialchars(phinit_format_date((string) $displayDate, 'long', $currentLocale), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <?php if ($show_rt && $_pc_rt > 0): ?>
                    <span class="read" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $_pc_rt], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $_pc_rt], $currentLocale), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </span>
            </span>
            <?php endif; ?>
        </div>
        <div class="article-footer">
            <?php if ($show_cat && !empty($card['category_name'])): ?>
            <span class="cat"><?php echo phinit_escape_text($card['category_name'] ?? ''); ?></span>
            <?php endif; ?>
            <a class="article-meta__more"
               href="<?php echo htmlspecialchars($permalink, ENT_QUOTES); ?>">
                <?php echo htmlspecialchars(phinit_t('continue_reading', [], $currentLocale), ENT_QUOTES); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>

</article>
