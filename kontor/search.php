<?php
declare(strict_types=1);

/**
 * Kontor – Suche
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter::renderSearch): $results (Arrays mit _type,
 * _type_label, slug, title, meta_description …) und $query.
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : [];
$searchQuery = mb_substr(trim(strip_tags((string) ($query ?? ''))), 0, 120, 'UTF-8');
$count = count($results);
$services = $count === 0 ? kontor_services() : [];
?>

<header class="kt-pagehero">
    <div class="kt-container">
        <p class="kt-eyebrow">Suche</p>
        <h1 class="kt-pagehero__title">
            <?php if ($searchQuery !== '') : ?>
                <?php echo $count; ?> <?php echo $count === 1 ? 'Ergebnis' : 'Ergebnisse'; ?> für „<?php echo kontor_e($searchQuery); ?>“
            <?php else : ?>
                Wonach suchen Sie?
            <?php endif; ?>
        </h1>
        <div class="kt-pagehero__search">
            <?php kontor_search_form('kt-search-page', $searchQuery); ?>
        </div>
    </div>
</header>

<div class="kt-container kt-content kt-content--sidebar">
    <div class="kt-content__main">
        <?php if ($results !== []) : ?>
            <ol class="kt-results">
                <?php foreach ($results as $result) :
                    $result = is_object($result) ? get_object_vars($result) : $result;
                    if (!is_array($result)) {
                        continue;
                    }
                    $url = kontor_safe_url(theme_search_result_url($result), kontor_url('/'));
                    $resultTitle = trim((string) ($result['title'] ?? $result['name'] ?? ''));
                    $text = kontor_excerpt($result, 220, false);
                    $type = trim((string) ($result['_type_label'] ?? ''));
                    ?>
                    <li class="kt-results__item">
                        <?php if ($type !== '') : ?><span class="kt-tag"><?php echo kontor_e($type); ?></span><?php endif; ?>
                        <h2 class="kt-results__title"><a href="<?php echo kontor_e($url); ?>"><?php echo kontor_e($resultTitle !== '' ? $resultTitle : $url); ?></a></h2>
                        <?php if ($text !== '') : ?>
                            <p><?php echo kontor_e($text); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php elseif ($searchQuery !== '') : ?>
            <div class="kt-empty">
                <p>Zu „<?php echo kontor_e($searchQuery); ?>“ haben wir nichts gefunden. Vielleicht hilft Ihnen einer unserer Leistungsbereiche weiter – oder Sie fragen uns direkt.</p>
            </div>
        <?php endif; ?>

        <?php if ($services !== []) : ?>
            <h2 class="kt-subheading">Unsere Leistungen</h2>
            <ul class="kt-services kt-services--compact">
                <?php foreach ($services as $service) : ?>
                    <li class="kt-service">
                        <span class="kt-service__icon"><?php echo kontor_icon($service['icon']); ?></span>
                        <h3 class="kt-service__title"><a href="<?php echo kontor_e(kontor_url($service['url'] !== '' ? $service['url'] : '/#leistungen')); ?>"><?php echo kontor_e($service['title']); ?></a></h3>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="kt-content__aside">
        <?php kontor_contact_card(); ?>
    </div>
</div>
