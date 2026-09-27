<?php
declare(strict_types=1);

/**
 * Kontor – Fallback-Template
 *
 * Greift für Templates ohne eigene Datei (z. B. „authors“, „sitemap“). Mit $posts
 * (Archive) wird das Archiv-Template genutzt, sonst eine Übersicht aus Leistungen und Beiträgen.
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($posts) && is_array($posts)) {
    require __DIR__ . '/blog.php';
    return;
}

$services = kontor_services();
$recent = kontor_get_posts(['limit' => 6]);
?>

<header class="kt-pagehero">
    <div class="kt-container">
        <p class="kt-eyebrow">Übersicht</p>
        <h1 class="kt-pagehero__title"><?php echo kontor_e(kontor_company_name()); ?></h1>
        <?php if (kontor_site_description() !== '') : ?>
            <p class="kt-pagehero__text"><?php echo kontor_e(kontor_site_description()); ?></p>
        <?php endif; ?>
    </div>
</header>

<div class="kt-container kt-archive">
    <?php if ($services !== []) : ?>
        <h2 class="kt-subheading">Leistungen</h2>
        <ul class="kt-services kt-services--compact">
            <?php foreach ($services as $service) : ?>
                <li class="kt-service">
                    <span class="kt-service__icon"><?php echo kontor_icon($service['icon']); ?></span>
                    <h3 class="kt-service__title"><a href="<?php echo kontor_e(kontor_url($service['url'] !== '' ? $service['url'] : '/#leistungen')); ?>"><?php echo kontor_e($service['title']); ?></a></h3>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($recent !== []) : ?>
        <h2 class="kt-subheading">Aktuelles</h2>
        <div class="kt-postgrid">
            <?php foreach ($recent as $item) : ?>
                <?php kontor_post_card($item, 'h3'); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
