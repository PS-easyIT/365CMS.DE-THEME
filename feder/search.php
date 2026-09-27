<?php
declare(strict_types=1);

/**
 * Feder – Suche
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter::renderSearch): $results (Arrays mit _type,
 * _type_label, slug, title, meta_description …) und $query (Suchbegriff).
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : [];
$searchQuery = trim(strip_tags((string) ($query ?? '')));
$searchQuery = mb_substr($searchQuery, 0, 120, 'UTF-8');
$count = count($results);
$topics = $count === 0 ? feder_get_categories(8) : [];
?>

<header class="fd-archive-head fd-measure">
    <p class="fd-kicker">Suche</p>
    <h1 class="fd-archive-head__title">
        <?php if ($searchQuery !== '') : ?>
            <?php echo $count; ?> <?php echo $count === 1 ? 'Treffer' : 'Treffer'; ?> für „<?php echo feder_e($searchQuery); ?>“
        <?php else : ?>
            Was möchten Sie lesen?
        <?php endif; ?>
    </h1>
    <?php feder_search_form('fd-search-page', $searchQuery, 'fd-searchform--large'); ?>
</header>

<section class="fd-archive fd-measure" aria-label="Suchergebnisse">
    <?php if ($results !== []) : ?>
        <ol class="fd-results">
            <?php foreach ($results as $result) :
                $result = is_object($result) ? get_object_vars($result) : $result;
                if (!is_array($result)) {
                    continue;
                }
                $resultUrl = feder_safe_url(theme_search_result_url($result), feder_url('/'));
                $resultTitle = trim((string) ($result['title'] ?? $result['name'] ?? ''));
                $resultText = feder_excerpt($result, 220, false);
                $resultType = trim((string) ($result['_type_label'] ?? ''));
                $resultDate = (string) ($result['published_at'] ?? '');
                ?>
                <li class="fd-results__item">
                    <p class="fd-meta">
                        <?php if ($resultType !== '') : ?><span class="fd-badge"><?php echo feder_e($resultType); ?></span><?php endif; ?>
                        <?php if ($resultDate !== '' && feder_format_date($resultDate) !== '') : ?>
                            <time datetime="<?php echo feder_e(feder_format_date($resultDate, 'iso')); ?>"><?php echo feder_e(feder_format_date($resultDate)); ?></time>
                        <?php endif; ?>
                    </p>
                    <h2 class="fd-results__title"><a href="<?php echo feder_e($resultUrl); ?>"><?php echo feder_e($resultTitle !== '' ? $resultTitle : $resultUrl); ?></a></h2>
                    <?php if ($resultText !== '') : ?>
                        <p class="fd-results__text"><?php echo feder_e($resultText); ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php elseif ($searchQuery !== '') : ?>
        <div class="fd-empty">
            <p>Zu „<?php echo feder_e($searchQuery); ?>“ wurde nichts gefunden. Versuchen Sie einen anderen Begriff oder stöbern Sie in den Themen.</p>
        </div>
    <?php endif; ?>

    <?php if ($topics !== []) : ?>
        <h2 class="fd-section-title">Themen</h2>
        <ul class="fd-chips">
            <?php foreach ($topics as $topic) : ?>
                <li><a class="fd-chip" href="<?php echo feder_e($topic['url']); ?>"><?php echo feder_e($topic['name']); ?><span class="fd-chip__count"><?php echo (int) $topic['count']; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
