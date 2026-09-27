<?php
declare(strict_types=1);

/**
 * Kompass – Suche
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter::renderSearch): $results (Arrays mit _type,
 * _type_label, slug, title, meta_description …) und $query.
 *
 * Ohne Treffer: Suchtipps, „Häufig gesucht“, Themenbereiche und Schnellzugriffe.
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : [];
$searchQuery = mb_substr(trim(strip_tags((string) ($query ?? ''))), 0, 120, 'UTF-8');
$count = count($results);
$terms = $count === 0 ? kompass_popular_terms() : [];
$topics = $count === 0 ? kompass_categories() : [];
$quickLinks = $count === 0 ? kompass_quick_links() : [];
?>

<header class="kp-pagehead kp-pagehead--search">
    <div class="kp-container">
        <nav class="kp-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kompass_e(kompass_url('/')); ?>">Start</a></li>
                <li><span aria-current="page">Suche</span></li>
            </ol>
        </nav>
        <h1 class="kp-pagehead__title"><?php echo $searchQuery !== '' ? 'Suchergebnisse' : 'Suche'; ?></h1>
        <?php kompass_search_form('kp-search-page', $searchQuery, 'page'); ?>
        <?php if ($searchQuery !== '') : ?>
            <p class="kp-search__summary">
                <strong><?php echo $count; ?> Treffer</strong> für „<?php echo kompass_e($searchQuery); ?>“
            </p>
        <?php endif; ?>
    </div>
</header>

<div class="kp-container kp-archive kp-archive--sidebar">
    <div class="kp-archive__main">
        <?php if ($results !== []) : ?>
            <ol class="kp-results">
                <?php foreach ($results as $result) :
                    $result = is_object($result) ? get_object_vars($result) : $result;
                    if (!is_array($result)) {
                        continue;
                    }
                    $url = kompass_safe_url(theme_search_result_url($result), kompass_url('/'));
                    $resultTitle = trim((string) ($result['title'] ?? $result['name'] ?? ''));
                    $text = kompass_excerpt($result, 240, false);
                    $type = trim((string) ($result['_type_label'] ?? ''));
                    $date = kompass_format_date((string) ($result['published_at'] ?? ''));
                    ?>
                    <li class="kp-results__item">
                        <p class="kp-results__meta">
                            <?php if ($type !== '') : ?><span class="kp-badge"><?php echo kompass_e($type); ?></span><?php endif; ?>
                            <?php if ($date !== '') : ?><span><?php echo kompass_e($date); ?></span><?php endif; ?>
                        </p>
                        <h2 class="kp-results__title"><a href="<?php echo kompass_e($url); ?>"><?php echo kompass_e($resultTitle !== '' ? $resultTitle : $url); ?></a></h2>
                        <?php if ($text !== '') : ?>
                            <p class="kp-results__text"><?php echo kompass_e($text); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php elseif ($searchQuery !== '') : ?>
            <div class="kp-empty kp-empty--box">
                <h2 class="kp-empty__title"><?php echo kompass_icon('info'); ?>Zu „<?php echo kompass_e($searchQuery); ?>“ wurde nichts gefunden.</h2>
                <p>So finden Sie trotzdem, was Sie suchen:</p>
                <ul>
                    <li>Prüfen Sie die Schreibweise.</li>
                    <li>Verwenden Sie allgemeinere oder weniger Suchbegriffe.</li>
                    <li>Probieren Sie ein anderes Wort mit gleicher Bedeutung (z. B. „Formular“ statt „Vordruck“).</li>
                    <li>Stöbern Sie in den Themenbereichen oder fragen Sie uns direkt.</li>
                </ul>
            </div>
        <?php else : ?>
            <p class="kp-search__hint">Geben Sie einen Suchbegriff ein – zum Beispiel ein Anliegen, ein Formular oder eine Frage.</p>
        <?php endif; ?>

        <?php if ($terms !== []) : ?>
            <section class="kp-subtopics" aria-labelledby="kp-search-terms">
                <h2 class="kp-subtopics__title" id="kp-search-terms">Häufig gesucht</h2>
                <ul class="kp-chips">
                    <?php foreach ($terms as $term) : ?>
                        <li><a class="kp-chip" href="<?php echo kompass_e(kompass_search_url($term)); ?>"><?php echo kompass_e($term); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if ($quickLinks !== []) : ?>
            <section class="kp-subtopics" aria-labelledby="kp-search-quick">
                <h2 class="kp-subtopics__title" id="kp-search-quick">Schnellzugriff</h2>
                <ul class="kp-quick kp-quick--compact">
                    <?php foreach ($quickLinks as $link) : ?>
                        <li>
                            <a class="kp-quick__link" href="<?php echo kompass_e($link['url']); ?>">
                                <span class="kp-quick__icon"><?php echo kompass_icon($link['icon']); ?></span>
                                <span class="kp-quick__label"><?php echo kompass_e($link['label']); ?></span>
                                <?php echo kompass_icon('chevron', 'kp-icon kp-quick__chevron'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </div>

    <aside class="kp-archive__aside" aria-label="Weitere Orientierung">
        <?php if ($topics !== []) : ?>
            <?php kompass_topic_nav(); ?>
        <?php endif; ?>
        <?php kompass_help_card(); ?>
    </aside>
</div>
