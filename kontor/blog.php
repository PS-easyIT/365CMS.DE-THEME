<?php
declare(strict_types=1);

/**
 * Kontor – Beitragsarchiv „Aktuelles“ (Blog, Kategorie, Schlagwort, Autor:in, Übersichten)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $posts (Objekte), $currentPage, $totalPages,
 * optional $total, $category / $tag (Arrays), $author (Array), $query,
 * $isOverview + $overviewItems (Kategorie-/Schlagwort-Übersicht).
 *
 * @package Kontor_Theme
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

$eyebrow = 'Blog';
$title = kontor_text('kt_news', 'news_heading', 'Aktuelles & Insights');
$title = str_replace('*', '', $title);
$description = 'Neuigkeiten, Praxistipps und Hintergründe aus unserer Arbeit.';
$pageParam = 'p';
$activeSlug = '';

if (isset($author) && is_array($author)) {
    $eyebrow = 'Autor:in';
    $title = trim((string) ($author['display_name'] ?? 'Autor:in'));
    $description = trim((string) ($author['bio'] ?? ''));
    $pageParam = 'page';
} elseif (isset($category) && is_array($category)) {
    $eyebrow = $isOverview ? 'Übersicht' : 'Kategorie';
    $title = trim((string) ($category['name'] ?? 'Kategorie'));
    $description = trim((string) ($category['description'] ?? ''));
    $activeSlug = trim((string) ($category['slug'] ?? ''));
} elseif (isset($tag) && is_array($tag)) {
    $eyebrow = $isOverview ? 'Übersicht' : 'Thema';
    $title = $isOverview ? trim((string) ($tag['name'] ?? 'Themen')) : '#' . trim((string) ($tag['name'] ?? ''));
    $description = trim((string) ($tag['description'] ?? ''));
}

$categories = !$isOverview && !isset($author) ? kontor_get_categories() : [];
$formAction = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
$searchable = isset($category) || isset($tag);
?>

<header class="kt-pagehero">
    <div class="kt-container">
        <nav class="kt-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kontor_e(kontor_url('/')); ?>">Start</a></li>
                <?php if (isset($category) || isset($tag) || isset($author)) : ?>
                    <li><a href="<?php echo kontor_e(kontor_url('/blog')); ?>">Aktuelles</a></li>
                <?php endif; ?>
                <li><span aria-current="page"><?php echo kontor_e($title); ?></span></li>
            </ol>
        </nav>
        <p class="kt-eyebrow"><?php echo kontor_e($eyebrow); ?></p>
        <h1 class="kt-pagehero__title"><?php echo kontor_e($title); ?></h1>
        <?php if ($description !== '') : ?>
            <p class="kt-pagehero__text"><?php echo kontor_e($description); ?></p>
        <?php endif; ?>
        <?php if ($searchable) : ?>
            <form class="kt-searchform kt-searchform--inline" method="get" action="<?php echo kontor_e($formAction); ?>" role="search">
                <label for="kt-archive-q" class="kt-visually-hidden">In „<?php echo kontor_e($title); ?>“ suchen</label>
                <input id="kt-archive-q" type="search" name="q" value="<?php echo kontor_e($archiveQuery); ?>" placeholder="In „<?php echo kontor_e($title); ?>“ suchen …" maxlength="120">
                <button type="submit" class="kt-button kt-button--primary"><?php echo kontor_icon('search'); ?><span>Filtern</span></button>
            </form>
        <?php endif; ?>
    </div>
</header>

<div class="kt-container kt-archive">
    <?php if ($categories !== []) : ?>
        <nav class="kt-filter" aria-label="Kategorien">
            <ul>
                <li><a href="<?php echo kontor_e(kontor_url('/blog')); ?>"<?php echo $activeSlug === '' && !isset($tag) ? ' aria-current="page"' : ''; ?>>Alle</a></li>
                <?php foreach ($categories as $item) : ?>
                    <li><a href="<?php echo kontor_e($item['url']); ?>"<?php echo $item['slug'] === $activeSlug ? ' aria-current="page"' : ''; ?>><?php echo kontor_e($item['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php if ($isOverview) : ?>
        <?php if ($overviewItems === []) : ?>
            <p class="kt-empty">Keine Einträge gefunden.</p>
        <?php else : ?>
            <ul class="kt-topics">
                <?php foreach ($overviewItems as $item) :
                    $url = kontor_safe_url((string) ($item['url'] ?? ''), '');
                    if ($url === '') {
                        continue;
                    }
                    $count = (int) ($item['count'] ?? 0);
                    ?>
                    <li>
                        <a class="kt-topic" href="<?php echo kontor_e(kontor_url($url)); ?>">
                            <span class="kt-topic__title"><?php echo kontor_e((string) ($item['title'] ?? '')); ?></span>
                            <?php if (trim((string) ($item['description'] ?? '')) !== '') : ?>
                                <span class="kt-topic__text"><?php echo kontor_e((string) $item['description']); ?></span>
                            <?php endif; ?>
                            <span class="kt-topic__count"><?php echo $count; ?> <?php echo $count === 1 ? 'Beitrag' : 'Beiträge'; ?><?php echo kontor_icon('arrow'); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php elseif ($posts === []) : ?>
        <div class="kt-empty">
            <p><?php echo $archiveQuery !== '' ? 'Keine Beiträge zu „' . kontor_e($archiveQuery) . '“ gefunden.' : 'Hier sind noch keine Beiträge erschienen.'; ?></p>
            <a class="kt-link-arrow" href="<?php echo kontor_e(kontor_url('/blog')); ?>">Alle Beiträge<?php echo kontor_icon('arrow'); ?></a>
        </div>
    <?php else : ?>
        <p class="kt-archive__count"><?php echo (int) $total; ?> <?php echo $total === 1 ? 'Beitrag' : 'Beiträge'; ?><?php if ($totalPages > 1) : ?> · Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?><?php endif; ?></p>
        <div class="kt-postgrid">
            <?php foreach ($posts as $item) :
                $item = is_array($item) ? (object) $item : $item;
                if (is_object($item)) {
                    kontor_post_card($item, 'h2');
                }
            endforeach; ?>
        </div>
    <?php endif; ?>

    <?php kontor_pagination($currentPage, $totalPages, $pageParam); ?>
</div>
