<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Blog & Archive (Neuigkeiten, Kategorie, Schlagwort, Autor:in, Übersichten)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $posts (Objekte), $currentPage, $totalPages,
 * optional $total, $category / $tag (Arrays), $author (Array), $query,
 * $isOverview + $overviewItems (Kategorie-/Schlagwort-Übersicht).
 *
 * @package Showcase_Theme
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

$isAuthor = isset($author) && is_array($author);
$isCategory = !$isAuthor && isset($category) && is_array($category);
$isTag = !$isAuthor && !$isCategory && isset($tag) && is_array($tag);

$eyebrow = 'Blog';
$title = 'Neuigkeiten';
$description = 'Releases, Hintergründe und Anleitungen rund um 365CMS.';
$pageParam = 'p';
$activeSlug = '';

if ($isAuthor) {
    $eyebrow = 'Autor:in';
    $title = trim((string) ($author['display_name'] ?? 'Autor:in'));
    $description = trim((string) ($author['bio'] ?? ''));
    $pageParam = 'page';
} elseif ($isCategory) {
    $eyebrow = $isOverview ? 'Übersicht' : 'Kategorie';
    $title = $isOverview ? 'Kategorien' : trim((string) ($category['name'] ?? 'Kategorie'));
    $description = trim((string) ($category['description'] ?? ''));
    $activeSlug = $isOverview ? '' : trim((string) ($category['slug'] ?? ''));
} elseif ($isTag) {
    $eyebrow = $isOverview ? 'Übersicht' : 'Schlagwort';
    $title = $isOverview ? 'Schlagwörter' : '#' . trim((string) ($tag['name'] ?? ''));
    $description = trim((string) ($tag['description'] ?? ''));
}

$categories = !$isOverview && !$isAuthor && !$isTag ? showcase_get_categories() : [];
$filterable = ($isCategory || $isTag) && !$isOverview;
$formAction = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
?>

<header class="sc-pagehero">
    <div class="sc-pagehero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-pagehero__inner">
        <nav class="sc-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo showcase_e(showcase_url('/')); ?>">Start</a></li>
                <?php if ($isAuthor || $isCategory || $isTag) : ?>
                    <li><a href="<?php echo showcase_e(showcase_url('/blog')); ?>">Neuigkeiten</a></li>
                <?php endif; ?>
                <li><span aria-current="page"><?php echo showcase_e($title); ?></span></li>
            </ol>
        </nav>
        <p class="sc-eyebrow sc-eyebrow--light"><?php echo showcase_e($eyebrow); ?></p>
        <h1 class="sc-pagehero__title"><?php echo showcase_e($title); ?></h1>
        <?php if ($description !== '') : ?>
            <p class="sc-pagehero__lead"><?php echo showcase_e($description); ?></p>
        <?php endif; ?>
        <?php if ($filterable) : ?>
            <form class="sc-searchform sc-searchform--hero" method="get" action="<?php echo showcase_e($formAction); ?>" role="search">
                <label for="sc-archive-q" class="sc-visually-hidden">In „<?php echo showcase_e($title); ?>“ suchen</label>
                <?php echo showcase_icon('search', 'sc-icon sc-searchform__icon'); ?>
                <input id="sc-archive-q" type="search" name="q" value="<?php echo showcase_e($archiveQuery); ?>" placeholder="In „<?php echo showcase_e($title); ?>“ suchen …" maxlength="120" autocomplete="off">
                <button type="submit" class="sc-button sc-button--primary"><span>Filtern</span></button>
            </form>
        <?php endif; ?>
    </div>
</header>

<div class="sc-container sc-archive">
    <?php if ($categories !== []) : ?>
        <nav class="sc-filter" aria-label="Kategorien">
            <ul>
                <li><a href="<?php echo showcase_e(showcase_url('/blog')); ?>"<?php echo $activeSlug === '' ? ' aria-current="page"' : ''; ?>>Alle</a></li>
                <?php foreach ($categories as $item) : ?>
                    <li><a href="<?php echo showcase_e($item['url']); ?>"<?php echo $item['slug'] === $activeSlug ? ' aria-current="page"' : ''; ?>><?php echo showcase_e($item['name']); ?> <span class="sc-filter__count"><?php echo (int) $item['count']; ?></span></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php if ($isOverview) : ?>
        <?php if ($overviewItems === []) : ?>
            <p class="sc-empty">Noch keine Einträge vorhanden.</p>
        <?php else : ?>
            <ul class="sc-terms">
                <?php foreach ($overviewItems as $item) :
                    $url = showcase_safe_url((string) ($item['url'] ?? ''), '');
                    if ($url === '') {
                        continue;
                    }
                    $count = (int) ($item['count'] ?? 0);
                    ?>
                    <li>
                        <a class="sc-term" href="<?php echo showcase_e(showcase_url($url)); ?>" data-sc-reveal>
                            <span class="sc-term__title"><?php echo $isTag ? '#' : ''; ?><?php echo showcase_e((string) ($item['title'] ?? '')); ?></span>
                            <?php if (trim((string) ($item['description'] ?? '')) !== '') : ?>
                                <span class="sc-term__text"><?php echo showcase_e((string) $item['description']); ?></span>
                            <?php endif; ?>
                            <span class="sc-term__count"><?php echo $count; ?> <?php echo $count === 1 ? 'Beitrag' : 'Beiträge'; ?><?php echo showcase_icon('arrow'); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php elseif ($posts === []) : ?>
        <div class="sc-empty">
            <p><?php echo $archiveQuery !== '' ? 'Keine Beiträge zu „' . showcase_e($archiveQuery) . '“ gefunden.' : 'Hier sind noch keine Beiträge erschienen.'; ?></p>
            <a class="sc-more" href="<?php echo showcase_e(showcase_url('/blog')); ?>"><span>Alle Neuigkeiten</span><?php echo showcase_icon('arrow'); ?></a>
        </div>
    <?php else : ?>
        <p class="sc-archive__count"><?php echo (int) $total; ?> <?php echo $total === 1 ? 'Beitrag' : 'Beiträge'; ?><?php if ($archiveQuery !== '') : ?> zu „<?php echo showcase_e($archiveQuery); ?>“<?php endif; ?><?php if ($totalPages > 1) : ?> · Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?><?php endif; ?></p>
        <div class="sc-postgrid">
            <?php foreach ($posts as $item) :
                $item = is_array($item) ? (object) $item : $item;
                if (is_object($item)) {
                    showcase_post_card($item, 'h2');
                }
            endforeach; ?>
        </div>
    <?php endif; ?>

    <?php showcase_pagination($currentPage, $totalPages, $pageParam); ?>
</div>
