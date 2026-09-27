<?php
declare(strict_types=1);

/**
 * Rundschau – Suche
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter::renderSearch): $results (Arrays mit _type,
 * _type_label, slug, title, meta_description …) und $query.
 *
 * @package Rundschau_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : [];
$searchQuery = mb_substr(trim(strip_tags((string) ($query ?? ''))), 0, 120, 'UTF-8');
$count = count($results);
$ressorts = array_values(array_filter(rundschau_categories(), static fn(array $c): bool => $c['parent_id'] === 0 && $c['total'] > 0));
$latest = $count === 0 ? rundschau_get_posts(['limit' => 6]) : [];
?>

<header class="rs-pagehead">
    <div class="rs-container">
        <p class="rs-pagehead__kicker">Suche</p>
        <h1 class="rs-pagehead__title">
            <?php if ($searchQuery !== '') : ?>
                <?php echo $count; ?> Treffer für „<?php echo rundschau_e($searchQuery); ?>“
            <?php else : ?>
                Nachrichten durchsuchen
            <?php endif; ?>
        </h1>
        <?php rundschau_search_form('rs-search-page', $searchQuery, 'rs-searchform--large'); ?>
    </div>
</header>

<div class="rs-container rs-layout rs-layout--sidebar">
    <div class="rs-layout__main">
        <?php if ($results !== []) : ?>
            <ol class="rs-results">
                <?php foreach ($results as $result) :
                    $result = is_object($result) ? get_object_vars($result) : $result;
                    if (!is_array($result)) {
                        continue;
                    }
                    $url = rundschau_safe_url(theme_search_result_url($result), rundschau_url('/'));
                    $resultTitle = trim((string) ($result['title'] ?? $result['name'] ?? ''));
                    $text = rundschau_excerpt($result, 220, false);
                    $type = trim((string) ($result['_type_label'] ?? ''));
                    $date = (string) ($result['published_at'] ?? '');
                    ?>
                    <li class="rs-results__item">
                        <p class="rs-meta">
                            <?php if ($type !== '') : ?><span class="rs-badge"><?php echo rundschau_e($type); ?></span><?php endif; ?>
                            <?php if (rundschau_format_date($date) !== '') : ?><time datetime="<?php echo rundschau_e(rundschau_format_date($date, 'iso')); ?>"><?php echo rundschau_e(rundschau_format_date($date, 'short')); ?></time><?php endif; ?>
                        </p>
                        <h2 class="rs-results__title"><a href="<?php echo rundschau_e($url); ?>"><?php echo rundschau_e($resultTitle !== '' ? $resultTitle : $url); ?></a></h2>
                        <?php if ($text !== '') : ?>
                            <p class="rs-results__text"><?php echo rundschau_e($text); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php elseif ($searchQuery !== '') : ?>
            <div class="rs-empty">
                <p>Zu „<?php echo rundschau_e($searchQuery); ?>“ haben wir leider nichts gefunden. Prüfen Sie die Schreibweise oder versuchen Sie einen allgemeineren Begriff.</p>
            </div>
        <?php endif; ?>

        <?php if ($latest !== []) : ?>
            <h2 class="rs-section-title"><span>Neueste Meldungen</span></h2>
            <div class="rs-list">
                <?php foreach ($latest as $item) : ?>
                    <?php rundschau_teaser($item, 'row', 'h3'); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="rs-layout__aside" aria-label="Ressorts">
        <?php if ($ressorts !== []) : ?>
            <section class="rs-box" aria-labelledby="rs-search-ressorts">
                <h2 class="rs-box__title" id="rs-search-ressorts">Ressorts</h2>
                <ul class="rs-ressortlist">
                    <?php foreach ($ressorts as $ressort) : ?>
                        <li class="<?php echo rundschau_e(rundschau_ressort_class($ressort['slug'])); ?>"><a href="<?php echo rundschau_e($ressort['url']); ?>"><span><?php echo rundschau_e($ressort['name']); ?></span><span class="rs-ressortlist__count"><?php echo (int) $ressort['total']; ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </aside>
</div>
