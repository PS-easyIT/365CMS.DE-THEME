<?php
declare(strict_types=1);

/**
 * Kompass – Archiv (Aktuelles, Thema, Schlagwort, Autor:in, Themenbereiche, Themen A–Z)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $posts (Objekte), $currentPage, $totalPages,
 * optional $total, $category / $tag (Arrays), $author (Array), $query,
 * $isOverview + $overviewItems (Kategorie-/Schlagwort-Übersicht).
 *
 * @package Kompass_Theme
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

$title = kompass_text('kp_home', 'latest_heading', 'Aktuelle Informationen');
$description = 'Neuigkeiten, Hinweise und Änderungen im Überblick.';
$eyebrow = '';
$icon = 'calendar';
$pageParam = 'p';
$activeSlug = '';
$crumbs = [];
$topicContext = ['parent' => null, 'children' => []];

if ($isAuthor) {
    $eyebrow = 'Autor:in';
    $title = trim((string) ($author['display_name'] ?? 'Autor:in'));
    $description = trim((string) ($author['bio'] ?? ''));
    $icon = 'people';
    $pageParam = 'page';
    $crumbs[] = ['label' => 'Aktuelles', 'url' => kompass_url('/blog')];
} elseif ($isCategory && $isOverview) {
    $title = 'Themenbereiche';
    $description = trim((string) ($category['description'] ?? '')) ?: 'Alle Themen im Überblick.';
    $icon = 'compass';
} elseif ($isCategory) {
    $eyebrow = 'Thema';
    $activeSlug = trim((string) ($category['slug'] ?? ''));
    $title = trim((string) ($category['name'] ?? 'Thema'));
    $description = trim((string) ($category['description'] ?? ''));
    $icon = kompass_topic_icon($activeSlug);
    $topicContext = kompass_topic_context($activeSlug);
    $crumbs[] = ['label' => 'Themen', 'url' => kompass_archive_url('category')];
    if ($topicContext['parent'] !== null) {
        $crumbs[] = ['label' => $topicContext['parent']['name'], 'url' => $topicContext['parent']['url']];
    }
} elseif ($isTag && $isOverview) {
    $title = 'Themen A–Z';
    $description = 'Alle Stichwörter alphabetisch geordnet – vom Antrag bis zur Zuständigkeit.';
    $icon = 'document';
} elseif ($isTag) {
    $eyebrow = 'Stichwort';
    $title = trim((string) ($tag['name'] ?? ''));
    $description = trim((string) ($tag['description'] ?? ''));
    $icon = 'document';
    $crumbs[] = ['label' => 'Themen A–Z', 'url' => kompass_archive_url('tag')];
}

$filterable = ($isCategory || $isTag) && !$isOverview;
$formAction = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
$showSidebar = !$isOverview;

$topics = [];
$azGroups = [];
if ($isOverview && $isCategory) {
    $topics = kompass_categories();
}
if ($isOverview && ($isTag || ($isCategory && $topics === []))) {
    $azGroups = kompass_alpha_groups($overviewItems);
}
?>

<header class="kp-pagehead">
    <div class="kp-container">
        <nav class="kp-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kompass_e(kompass_url('/')); ?>">Start</a></li>
                <?php foreach ($crumbs as $crumb) : ?>
                    <li><a href="<?php echo kompass_e($crumb['url']); ?>"><?php echo kompass_e($crumb['label']); ?></a></li>
                <?php endforeach; ?>
                <li><span aria-current="page"><?php echo kompass_e($title); ?></span></li>
            </ol>
        </nav>
        <div class="kp-pagehead__row">
            <span class="kp-pagehead__icon"><?php echo kompass_icon($icon); ?></span>
            <div>
                <?php if ($eyebrow !== '') : ?>
                    <p class="kp-eyebrow"><?php echo kompass_e($eyebrow); ?></p>
                <?php endif; ?>
                <h1 class="kp-pagehead__title"><?php echo kompass_e($title); ?></h1>
                <?php if ($description !== '') : ?>
                    <p class="kp-pagehead__lead"><?php echo kompass_e($description); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($filterable) : ?>
            <form class="kp-searchform kp-searchform--filter" method="get" action="<?php echo kompass_e($formAction); ?>" role="search">
                <label for="kp-archive-q" class="kp-searchform__label">In „<?php echo kompass_e($title); ?>“ suchen</label>
                <div class="kp-searchform__row">
                    <input id="kp-archive-q" type="search" name="q" value="<?php echo kompass_e($archiveQuery); ?>" maxlength="120" autocomplete="off">
                    <button type="submit" class="kp-button kp-button--primary"><?php echo kompass_icon('search'); ?><span>Filtern</span></button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</header>

<div class="kp-container kp-archive<?php echo $showSidebar ? ' kp-archive--sidebar' : ''; ?>">
    <div class="kp-archive__main">
        <?php if ($topicContext['children'] !== []) : ?>
            <section class="kp-subtopics" aria-labelledby="kp-subtopics-title">
                <h2 class="kp-subtopics__title" id="kp-subtopics-title">Unterthemen</h2>
                <ul class="kp-chips">
                    <?php foreach ($topicContext['children'] as $child) : ?>
                        <li><a class="kp-chip" href="<?php echo kompass_e($child['url']); ?>"><?php echo kompass_e($child['name']); ?> <span class="kp-chip__count">(<?php echo (int) $child['count']; ?>)</span></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if ($isOverview && $topics !== []) : ?>
            <ul class="kp-topics">
                <?php foreach ($topics as $topic) : ?>
                    <li class="kp-topic">
                        <span class="kp-topic__icon"><?php echo kompass_icon(kompass_topic_icon($topic['slug'])); ?></span>
                        <h2 class="kp-topic__title"><a href="<?php echo kompass_e($topic['url']); ?>"><?php echo kompass_e($topic['name']); ?></a></h2>
                        <?php if ($topic['description'] !== '') : ?>
                            <p class="kp-topic__text"><?php echo kompass_e($topic['description']); ?></p>
                        <?php endif; ?>
                        <?php if ($topic['children'] !== []) : ?>
                            <ul class="kp-topic__children" aria-label="Unterthemen von <?php echo kompass_e($topic['name']); ?>">
                                <?php foreach ($topic['children'] as $child) : ?>
                                    <li><a href="<?php echo kompass_e($child['url']); ?>"><?php echo kompass_e($child['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <p class="kp-topic__count"><?php echo (int) $topic['count']; ?> <?php echo $topic['count'] === 1 ? 'Beitrag' : 'Beiträge'; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($isOverview) : ?>
            <?php if ($azGroups === []) : ?>
                <p class="kp-empty">Noch keine Einträge vorhanden.</p>
            <?php else : ?>
                <nav class="kp-az__nav" aria-label="Sprung zu Buchstabe">
                    <ul>
                        <?php foreach (array_merge(range('A', 'Z'), ['#']) as $letter) : ?>
                            <?php if (isset($azGroups[$letter])) : ?>
                                <li><a href="#kp-az-<?php echo $letter === '#' ? 'other' : $letter; ?>"><?php echo kompass_e($letter); ?></a></li>
                            <?php elseif ($letter !== '#') : ?>
                                <li><span class="kp-az__empty" aria-hidden="true"><?php echo kompass_e($letter); ?></span></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <div class="kp-az">
                    <?php foreach ($azGroups as $letter => $items) :
                        $anchor = 'kp-az-' . ($letter === '#' ? 'other' : $letter);
                        ?>
                        <section class="kp-az__group" aria-labelledby="<?php echo kompass_e($anchor); ?>">
                            <h2 class="kp-az__letter" id="<?php echo kompass_e($anchor); ?>"><?php echo kompass_e($letter === '#' ? '0–9' : $letter); ?></h2>
                            <ul class="kp-az__list">
                                <?php foreach ($items as $item) :
                                    $url = kompass_safe_url((string) ($item['url'] ?? ''), '');
                                    if ($url === '') {
                                        continue;
                                    }
                                    $count = (int) ($item['count'] ?? 0);
                                    ?>
                                    <li><a href="<?php echo kompass_e(kompass_url($url)); ?>"><?php echo kompass_e((string) ($item['title'] ?? '')); ?></a> <span class="kp-az__count"><?php echo $count; ?> <?php echo $count === 1 ? 'Beitrag' : 'Beiträge'; ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php elseif ($posts === []) : ?>
            <div class="kp-empty">
                <p><?php echo $archiveQuery !== '' ? 'Zu „' . kompass_e($archiveQuery) . '“ gibt es hier keine Einträge.' : 'Hier sind noch keine Informationen veröffentlicht.'; ?></p>
                <a class="kp-more" href="<?php echo kompass_e(kompass_url('/blog')); ?>"><span>Alle aktuellen Informationen</span><?php echo kompass_icon('arrow'); ?></a>
            </div>
        <?php else : ?>
            <p class="kp-archive__count">
                <?php echo (int) $total; ?> <?php echo $total === 1 ? 'Eintrag' : 'Einträge'; ?><?php if ($archiveQuery !== '') : ?> zu „<?php echo kompass_e($archiveQuery); ?>“<?php endif; ?><?php if ($totalPages > 1) : ?> · Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?><?php endif; ?>
            </p>
            <ul class="kp-newslist kp-newslist--archive">
                <?php foreach ($posts as $item) :
                    $item = is_array($item) ? (object) $item : $item;
                    if (is_object($item)) {
                        kompass_news_item($item, 'h2', true, true);
                    }
                endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php kompass_pagination($currentPage, $totalPages, $pageParam); ?>
    </div>

    <?php if ($showSidebar) : ?>
        <aside class="kp-archive__aside" aria-label="Weitere Orientierung">
            <?php kompass_topic_nav($activeSlug); ?>
            <?php kompass_help_card(); ?>
        </aside>
    <?php endif; ?>
</div>
