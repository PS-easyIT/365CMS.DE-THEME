<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Suche
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter::renderSearch): $results (Arrays mit _type,
 * _type_label, slug, title, meta_description …) und $query.
 *
 * @package Showcase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : [];
$searchQuery = mb_substr(trim(strip_tags((string) ($query ?? ''))), 0, 120, 'UTF-8');
$count = count($results);
$suggestions = [
    ['label' => 'Funktionen im Überblick', 'url' => showcase_url('/#funktionen'), 'icon' => 'blocks'],
    ['label' => 'Sicherheitskonzept', 'url' => showcase_url('/#sicherheit'), 'icon' => 'shield'],
    ['label' => 'Installation', 'url' => showcase_url('/#installation'), 'icon' => 'download'],
    ['label' => 'Neuigkeiten & Release Notes', 'url' => showcase_url('/blog'), 'icon' => 'sparkles'],
];
?>

<header class="sc-pagehero">
    <div class="sc-pagehero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-pagehero__inner">
        <nav class="sc-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo showcase_e(showcase_url('/')); ?>">Start</a></li>
                <li><span aria-current="page">Suche</span></li>
            </ol>
        </nav>
        <h1 class="sc-pagehero__title">
            <?php if ($searchQuery !== '') : ?>
                <?php echo $count; ?> <?php echo $count === 1 ? 'Ergebnis' : 'Ergebnisse'; ?> für „<?php echo showcase_e($searchQuery); ?>“
            <?php else : ?>
                Wonach suchen Sie?
            <?php endif; ?>
        </h1>
        <?php showcase_search_form('sc-search-page', $searchQuery, 'hero'); ?>
    </div>
</header>

<div class="sc-container sc-archive sc-archive--narrow">
    <?php if ($results !== []) : ?>
        <ol class="sc-results">
            <?php foreach ($results as $result) :
                $result = is_object($result) ? get_object_vars($result) : $result;
                if (!is_array($result)) {
                    continue;
                }
                $url = showcase_safe_url(theme_search_result_url($result), showcase_url('/'));
                $resultTitle = trim((string) ($result['title'] ?? $result['name'] ?? ''));
                $text = showcase_excerpt($result, 220, false);
                $type = trim((string) ($result['_type_label'] ?? ''));
                ?>
                <li class="sc-results__item">
                    <?php if ($type !== '') : ?><span class="sc-pill"><?php echo showcase_e($type); ?></span><?php endif; ?>
                    <h2 class="sc-results__title"><a href="<?php echo showcase_e($url); ?>"><?php echo showcase_e($resultTitle !== '' ? $resultTitle : $url); ?></a></h2>
                    <?php if ($text !== '') : ?>
                        <p><?php echo showcase_e($text); ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php else : ?>
        <?php if ($searchQuery !== '') : ?>
            <p class="sc-empty">Zu „<?php echo showcase_e($searchQuery); ?>“ gibt es keine Treffer. Versuchen Sie einen allgemeineren Begriff oder starten Sie bei einem dieser Einstiege:</p>
        <?php endif; ?>
        <h2 class="sc-subheading">Beliebte Einstiege</h2>
        <ul class="sc-quicklinks">
            <?php foreach ($suggestions as $suggestion) : ?>
                <li>
                    <a href="<?php echo showcase_e($suggestion['url']); ?>">
                        <span class="sc-quicklinks__icon"><?php echo showcase_icon($suggestion['icon']); ?></span>
                        <span><?php echo showcase_e($suggestion['label']); ?></span>
                        <?php echo showcase_icon('arrow'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
