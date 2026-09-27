<?php
declare(strict_types=1);

/**
 * Feder – Beitragsarchiv (Blog, Kategorie, Schlagwort, Autor:in)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $posts (Objekte), $currentPage, $totalPages,
 * optional $total, $category / $tag (Arrays), $author (Array), $query (Suche im Archiv),
 * $isOverview + $overviewItems (Kategorie-/Schlagwort-Übersicht).
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$posts = isset($posts) && is_array($posts) ? $posts : [];
$currentPage = max(1, (int) ($currentPage ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$total = (int) ($total ?? count($posts));
$archiveQuery = isset($query) && is_string($query) ? trim($query) : '';
$isOverview = !empty($isOverview);
$overviewItems = isset($overviewItems) && is_array($overviewItems) ? $overviewItems : [];
$showReadingTime = feder_flag('feder_article', 'show_reading_time', true);

$kicker = 'Archiv';
$title = 'Alle Texte';
$description = '';
$pageParam = 'p';
$searchable = false;

if (isset($author) && is_array($author)) {
    $kicker = 'Autor:in';
    $title = trim((string) ($author['display_name'] ?? 'Autor:in'));
    $description = trim((string) ($author['bio'] ?? ''));
    $pageParam = 'page';
} elseif (isset($category) && is_array($category)) {
    $kicker = $isOverview ? 'Übersicht' : 'Thema';
    $title = trim((string) ($category['name'] ?? 'Thema'));
    $description = trim((string) ($category['description'] ?? ''));
    $searchable = true;
} elseif (isset($tag) && is_array($tag)) {
    $kicker = $isOverview ? 'Übersicht' : 'Schlagwort';
    $title = $isOverview ? trim((string) ($tag['name'] ?? 'Schlagwörter')) : '#' . trim((string) ($tag['name'] ?? ''));
    $description = trim((string) ($tag['description'] ?? ''));
    $searchable = true;
}

$authorAvatar = isset($author) && is_array($author) ? feder_media_url($author['avatar_url'] ?? '') : '';
$formAction = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
?>

<header class="fd-archive-head fd-measure">
    <?php if ($authorAvatar !== '') : ?>
        <img class="fd-archive-head__avatar" src="<?php echo feder_e($authorAvatar); ?>" alt="" width="80" height="80">
    <?php endif; ?>
    <p class="fd-kicker"><?php echo feder_e($kicker); ?></p>
    <h1 class="fd-archive-head__title"><?php echo feder_e($title); ?></h1>
    <?php if ($description !== '') : ?>
        <p class="fd-archive-head__text"><?php echo feder_e($description); ?></p>
    <?php endif; ?>
    <p class="fd-meta">
        <?php if ($isOverview) : ?>
            <?php echo (int) $total; ?> <?php echo $total === 1 ? 'Eintrag' : 'Einträge'; ?>
        <?php else : ?>
            <?php echo (int) $total; ?> <?php echo $total === 1 ? 'Beitrag' : 'Beiträge'; ?><?php if ($totalPages > 1) : ?> · Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?><?php endif; ?>
        <?php endif; ?>
    </p>
    <?php if ($searchable) : ?>
        <form class="fd-searchform fd-searchform--inline" method="get" action="<?php echo feder_e($formAction); ?>" role="search">
            <label for="fd-archive-q" class="fd-visually-hidden">In diesem Archiv suchen</label>
            <input id="fd-archive-q" type="search" name="q" value="<?php echo feder_e($archiveQuery); ?>" placeholder="In „<?php echo feder_e($title); ?>“ suchen …" maxlength="120">
            <button type="submit" class="fd-button fd-button--ghost"><?php echo feder_icon('search'); ?><span>Filtern</span></button>
        </form>
    <?php endif; ?>
</header>

<?php if ($isOverview) : ?>
    <section class="fd-overview fd-wide" aria-label="<?php echo feder_e($title); ?>">
        <?php if ($overviewItems === []) : ?>
            <p class="fd-empty fd-measure">Keine Einträge gefunden.</p>
        <?php else : ?>
            <ul class="fd-overview__grid">
                <?php foreach ($overviewItems as $item) :
                    $itemUrl = feder_safe_url((string) ($item['url'] ?? ''), '');
                    if ($itemUrl === '') {
                        continue;
                    }
                    $count = (int) ($item['count'] ?? 0);
                    ?>
                    <li>
                        <a class="fd-overview__card" href="<?php echo feder_e(feder_url($itemUrl)); ?>">
                            <span class="fd-overview__title"><?php echo feder_e((string) ($item['title'] ?? '')); ?></span>
                            <?php if (trim((string) ($item['description'] ?? '')) !== '') : ?>
                                <span class="fd-overview__text"><?php echo feder_e((string) $item['description']); ?></span>
                            <?php endif; ?>
                            <span class="fd-overview__count"><?php echo $count; ?> <?php echo $count === 1 ? 'Beitrag' : 'Beiträge'; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php else : ?>
    <section class="fd-archive fd-measure" aria-label="Beiträge">
        <?php if ($posts === []) : ?>
            <div class="fd-empty">
                <p><?php echo $archiveQuery !== '' ? 'Keine Beiträge passen zu „' . feder_e($archiveQuery) . '“.' : 'Hier ist noch nichts erschienen.'; ?></p>
                <p><a class="fd-link-arrow" href="<?php echo feder_e(feder_url('/blog')); ?>">Zu allen Texten<?php echo feder_icon('arrow'); ?></a></p>
            </div>
        <?php else : ?>
            <ol class="fd-cards">
                <?php foreach ($posts as $item) :
                    $item = is_array($item) ? (object) $item : $item;
                    if (!is_object($item)) {
                        continue;
                    }
                    $itemUrl = feder_post_link($item);
                    $itemImage = feder_media_url($item->featured_image ?? '');
                    $itemCategory = trim((string) ($item->category_name ?? ''));
                    $itemExcerpt = feder_excerpt($item, 200);
                    $itemDate = $item->published_at ?? $item->created_at ?? '';
                    ?>
                    <li class="fd-card<?php echo $itemImage !== '' ? ' fd-card--media' : ''; ?>">
                        <article>
                            <div class="fd-card__body">
                                <p class="fd-meta">
                                    <time datetime="<?php echo feder_e(feder_format_date($itemDate, 'iso')); ?>"><?php echo feder_e(feder_format_date($itemDate)); ?></time>
                                    <?php if ($itemCategory !== '') : ?>
                                        <span class="fd-dot" aria-hidden="true">·</span>
                                        <a href="<?php echo feder_e(feder_archive_url('category', (string) ($item->category_slug ?? ''))); ?>"><?php echo feder_e($itemCategory); ?></a>
                                    <?php endif; ?>
                                </p>
                                <h2 class="fd-card__title"><a href="<?php echo feder_e($itemUrl); ?>"><?php echo feder_e((string) ($item->title ?? '')); ?></a></h2>
                                <?php if ($itemExcerpt !== '') : ?>
                                    <p class="fd-card__excerpt"><?php echo feder_e($itemExcerpt); ?></p>
                                <?php endif; ?>
                                <?php if ($showReadingTime && isset($item->content)) : ?>
                                    <p class="fd-meta"><?php echo feder_icon('clock'); ?><span><?php echo feder_reading_time((string) $item->content); ?> Min. Lesezeit</span></p>
                                <?php endif; ?>
                            </div>
                            <?php if ($itemImage !== '') : ?>
                                <a class="fd-card__media" href="<?php echo feder_e($itemUrl); ?>" tabindex="-1" aria-hidden="true">
                                    <img src="<?php echo feder_e($itemImage); ?>" alt="" width="320" height="200" loading="lazy" decoding="async">
                                </a>
                            <?php endif; ?>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php feder_pagination($currentPage, $totalPages, $pageParam); ?>
