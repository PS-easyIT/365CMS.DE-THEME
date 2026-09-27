<?php
declare(strict_types=1);

/**
 * Rundschau – Meldungsarchiv (Alle Meldungen, Ressort, Thema, Autor:in, Übersichten)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $posts (Objekte), $currentPage, $totalPages,
 * optional $total, $category / $tag (Arrays), $author (Array), $query,
 * $isOverview + $overviewItems (Kategorie-/Schlagwort-Übersicht).
 *
 * @package Rundschau_Theme
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
$showSidebar = rundschau_flag('layout', 'show_sidebar', true);

$kicker = 'Archiv';
$title = 'Alle Meldungen';
$description = '';
$pageParam = 'p';
$ressortClass = '';
$searchable = false;
$crumbs = [['label' => 'Startseite', 'url' => rundschau_url('/')]];

if (isset($author) && is_array($author)) {
    $kicker = 'Autor:in';
    $title = trim((string) ($author['display_name'] ?? 'Autor:in'));
    $description = trim((string) ($author['bio'] ?? ''));
    $pageParam = 'page';
} elseif (isset($category) && is_array($category)) {
    $kicker = $isOverview ? 'Übersicht' : 'Ressort';
    $title = trim((string) ($category['name'] ?? 'Ressort'));
    $description = trim((string) ($category['description'] ?? ''));
    $ressortClass = $isOverview ? '' : rundschau_ressort_class((string) ($category['slug'] ?? ''));
    $searchable = true;
    if (!$isOverview) {
        $crumbs[] = ['label' => 'Ressorts', 'url' => rundschau_archive_url('category')];
    }
} elseif (isset($tag) && is_array($tag)) {
    $kicker = $isOverview ? 'Übersicht' : 'Thema';
    $title = trim((string) ($tag['name'] ?? 'Themen'));
    $description = trim((string) ($tag['description'] ?? ''));
    $searchable = true;
    if (!$isOverview) {
        $crumbs[] = ['label' => 'Themen A–Z', 'url' => rundschau_archive_url('tag')];
    }
}

$popular = $showSidebar && !$isOverview ? rundschau_popular_posts(5, rundschau_int('rs_home', 'popular_days', 30, 0, 365)) : [];
$ressorts = $showSidebar && !$isOverview ? array_values(array_filter(rundschau_categories(), static fn(array $c): bool => $c['parent_id'] === 0 && $c['total'] > 0)) : [];
$formAction = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
$authorAvatar = isset($author) && is_array($author) ? rundschau_media_url($author['avatar_url'] ?? '') : '';
?>

<header class="rs-pagehead <?php echo rundschau_e($ressortClass); ?>">
    <div class="rs-container">
        <nav class="rs-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <?php foreach ($crumbs as $crumb) : ?>
                    <li><a href="<?php echo rundschau_e($crumb['url']); ?>"><?php echo rundschau_e($crumb['label']); ?></a></li>
                <?php endforeach; ?>
                <li><span aria-current="page"><?php echo rundschau_e($title); ?></span></li>
            </ol>
        </nav>
        <div class="rs-pagehead__row">
            <?php if ($authorAvatar !== '') : ?>
                <img class="rs-pagehead__avatar" src="<?php echo rundschau_e($authorAvatar); ?>" alt="" width="72" height="72">
            <?php endif; ?>
            <div>
                <p class="rs-pagehead__kicker"><?php echo rundschau_e($kicker); ?></p>
                <h1 class="rs-pagehead__title"><?php echo rundschau_e($title); ?></h1>
                <?php if ($description !== '') : ?>
                    <p class="rs-pagehead__text"><?php echo rundschau_e($description); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="rs-pagehead__tools">
            <p class="rs-meta"><?php echo (int) $total; ?> <?php echo $isOverview ? ($total === 1 ? 'Eintrag' : 'Einträge') : ($total === 1 ? 'Meldung' : 'Meldungen'); ?><?php if ($totalPages > 1) : ?> · Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?><?php endif; ?></p>
            <?php if ($searchable) : ?>
                <form class="rs-searchform rs-searchform--compact" method="get" action="<?php echo rundschau_e($formAction); ?>" role="search">
                    <label for="rs-archive-q" class="rs-visually-hidden">In „<?php echo rundschau_e($title); ?>“ suchen</label>
                    <input id="rs-archive-q" type="search" name="q" value="<?php echo rundschau_e($archiveQuery); ?>" placeholder="In „<?php echo rundschau_e($title); ?>“ suchen …" maxlength="120">
                    <button type="submit" class="rs-button"><?php echo rundschau_icon('search'); ?><span>Filtern</span></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="rs-container rs-layout<?php echo ($showSidebar && !$isOverview) ? ' rs-layout--sidebar' : ''; ?>">
    <div class="rs-layout__main">
        <?php if ($isOverview) : ?>
            <?php if ($overviewItems === []) : ?>
                <p class="rs-empty">Keine Einträge gefunden.</p>
            <?php else : ?>
                <ul class="rs-overview">
                    <?php foreach ($overviewItems as $item) :
                        $url = rundschau_safe_url((string) ($item['url'] ?? ''), '');
                        if ($url === '') {
                            continue;
                        }
                        $count = (int) ($item['count'] ?? 0);
                        $isCategory = isset($category);
                        ?>
                        <li>
                            <a class="rs-overview__card <?php echo $isCategory ? rundschau_e(rundschau_ressort_class((string) ($item['slug'] ?? ''))) : ''; ?>" href="<?php echo rundschau_e(rundschau_url($url)); ?>">
                                <span class="rs-overview__title"><?php echo $isCategory ? '' : '#'; ?><?php echo rundschau_e((string) ($item['title'] ?? '')); ?></span>
                                <?php if (trim((string) ($item['description'] ?? '')) !== '') : ?>
                                    <span class="rs-overview__text"><?php echo rundschau_e((string) $item['description']); ?></span>
                                <?php endif; ?>
                                <span class="rs-overview__count"><?php echo $count; ?> <?php echo $count === 1 ? 'Meldung' : 'Meldungen'; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php elseif ($posts === []) : ?>
            <div class="rs-empty">
                <p><?php echo $archiveQuery !== '' ? 'Keine Meldungen zu „' . rundschau_e($archiveQuery) . '“.' : 'In diesem Bereich sind noch keine Meldungen erschienen.'; ?></p>
                <a class="rs-more" href="<?php echo rundschau_e(rundschau_url('/blog')); ?>">Alle Meldungen<?php echo rundschau_icon('arrow'); ?></a>
            </div>
        <?php else : ?>
            <div class="rs-list">
                <?php foreach ($posts as $index => $item) :
                    $item = is_array($item) ? (object) $item : $item;
                    if (!is_object($item)) {
                        continue;
                    }
                    rundschau_teaser($item, $index === 0 && $currentPage === 1 && $archiveQuery === '' ? 'lead' : 'row', 'h2');
                endforeach; ?>
            </div>
        <?php endif; ?>

        <?php rundschau_pagination($currentPage, $totalPages, $pageParam); ?>
    </div>

    <?php if ($showSidebar && !$isOverview) : ?>
        <aside class="rs-layout__aside" aria-label="Seitenleiste">
            <?php if ($popular !== []) : ?>
                <section class="rs-box rs-box--popular" aria-labelledby="rs-aside-popular">
                    <h2 class="rs-box__title" id="rs-aside-popular"><?php echo rundschau_e(rundschau_text('rs_home', 'popular_heading', 'Meistgelesen')); ?></h2>
                    <ol class="rs-popular">
                        <?php foreach ($popular as $item) : ?>
                            <li class="rs-popular__item"><a href="<?php echo rundschau_e(rundschau_post_link($item)); ?>"><?php echo rundschau_e((string) ($item->title ?? '')); ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endif; ?>
            <?php if ($ressorts !== []) : ?>
                <section class="rs-box" aria-labelledby="rs-aside-ressorts">
                    <h2 class="rs-box__title" id="rs-aside-ressorts">Ressorts</h2>
                    <ul class="rs-ressortlist">
                        <?php foreach ($ressorts as $ressort) : ?>
                            <li class="<?php echo rundschau_e(rundschau_ressort_class($ressort['slug'])); ?>"><a href="<?php echo rundschau_e($ressort['url']); ?>"><span><?php echo rundschau_e($ressort['name']); ?></span><span class="rs-ressortlist__count"><?php echo (int) $ressort['total']; ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </aside>
    <?php endif; ?>
</div>
