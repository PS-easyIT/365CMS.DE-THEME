<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : '';
$typeIcons = isset($typeIcons) && is_array($typeIcons) ? $typeIcons : [];
$_searchExcerptLen = isset($_searchExcerptLen) ? (int) $_searchExcerptLen : 180;
$r = isset($r) && is_array($r) ? $r : [];
$rType = (string) ($r['_type'] ?? 'post');
$rTypeLabel = (string) ($r['_type_label'] ?? 'Beitrag');
$rTypeIcon = (string) ($typeIcons[$rType] ?? '📄');
$rTitle = trim((string) ($r['title'] ?? ''));
if ($rTitle === '') {
    $rTitle = trim((string) ($r['title_en'] ?? $r['name'] ?? 'Ohne Titel'));
}
$rExcerptSource = trim((string) ($r['excerpt'] ?? ''));
if ($rExcerptSource === '') {
    $rExcerptSource = trim((string) ($r['excerpt_en'] ?? ''));
}
if ($rExcerptSource === '') {
    $rExcerptSource = (string) ($r['meta_description'] ?? $r['description'] ?? $r['content'] ?? $r['content_en'] ?? '');
}
$rExcerpt = function_exists('phinit_excerpt_plain_text')
    ? phinit_excerpt_plain_text($rExcerptSource)
    : strip_tags($rExcerptSource);
$rSlug = trim((string) ($r['slug'] ?? ''));
if ($rSlug === '') {
    $rSlug = trim((string) ($r['slug_en'] ?? ''));
}

if ($rType === 'post') {
    $rUrl = function_exists('phinit_build_post_url')
        ? phinit_build_post_url($r, function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de')
        : ($siteUrl . '/blog/' . $rSlug);
} elseif ($rSlug !== '') {
    $rUrl = $siteUrl . '/' . $rSlug;
} else {
    $rUrl = '#';
}

$rPath = $rUrl !== '#' ? (string) (parse_url($rUrl, PHP_URL_PATH) ?: '/') : 'Nicht verlinkt';
?>
<article class="search-result-row search-result-row--<?php echo htmlspecialchars($rType, ENT_QUOTES); ?>">
    <div class="search-result-row__icon" aria-hidden="true"><?php echo htmlspecialchars($rTypeIcon, ENT_QUOTES); ?></div>
    <div class="search-result-body">
        <div class="search-result-topline">
            <span class="search-result-type search-result-type--<?php echo htmlspecialchars($rType, ENT_QUOTES); ?>"><?php echo htmlspecialchars($rTypeLabel, ENT_QUOTES); ?></span>
            <?php if (!empty($r['published_at'])): ?>
            <time class="search-result-date" datetime="<?php echo htmlspecialchars((string) $r['published_at'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(date('d.m.Y', strtotime((string) $r['published_at'])), ENT_QUOTES); ?></time>
            <?php endif; ?>
        </div>

        <h3 class="search-result-title">
            <a href="<?php echo htmlspecialchars($rUrl, ENT_QUOTES); ?>">
                <?php echo htmlspecialchars($rTitle, ENT_QUOTES); ?>
            </a>
        </h3>

        <?php if ($rExcerpt !== ''): ?>
        <p class="search-result-excerpt"><?php echo htmlspecialchars(mb_strimwidth($rExcerpt, 0, $_searchExcerptLen, '…'), ENT_QUOTES); ?></p>
        <?php endif; ?>

        <div class="search-result-footer">
            <span class="search-result-path"><?php echo htmlspecialchars($rPath, ENT_QUOTES); ?></span>
            <?php if ($rUrl !== '#'): ?>
            <a class="search-result-cta" href="<?php echo htmlspecialchars($rUrl, ENT_QUOTES); ?>">Ansehen →</a>
            <?php endif; ?>
        </div>
    </div>
</article>
