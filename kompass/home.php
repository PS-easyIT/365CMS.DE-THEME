<?php
declare(strict_types=1);

/**
 * Kompass – Startseite
 *
 * Such-Hero mit „Häufig gesucht“ → Schnellzugriff → Themenbereiche (aus Kategorien)
 * → Aktuelle Informationen + Häufige Fragen → Service & Kontakt. Texte aus dem
 * Customizer (theme.json → customization), Themen und Beiträge live aus der Datenbank.
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$heroTitle = kompass_text('kp_hero', 'hero_title', 'Wie können wir Ihnen helfen?');
$heroText = kompass_text('kp_hero', 'hero_text');
$terms = kompass_popular_terms();

$quickLinks = kompass_quick_links();
$quickHeading = kompass_text('kp_quick', 'quick_heading', 'Schnellzugriff');

$topics = kompass_categories();
$topicsLimit = kompass_int('kp_home', 'topics_count', 0, 0, 24);
if ($topicsLimit > 0) {
    $topics = array_slice($topics, 0, $topicsLimit);
}

$latestCount = kompass_int('kp_home', 'latest_count', 4, 0, 10);
$latest = $latestCount > 0 ? kompass_get_posts(['limit' => $latestCount]) : [];

$faqs = kompass_flag('kp_faq', 'show_faq', true) ? kompass_faqs() : [];
$faqMoreLabel = kompass_text('kp_faq', 'faq_more_label');
$faqMoreUrl = kompass_safe_url(kompass_text('kp_faq', 'faq_more_url'), '');
?>

<section class="kp-hero" aria-labelledby="kp-hero-title">
    <div class="kp-container kp-hero__inner">
        <h1 class="kp-hero__title" id="kp-hero-title"><?php echo kompass_e($heroTitle); ?></h1>
        <?php if ($heroText !== '') : ?>
            <p class="kp-hero__text"><?php echo kompass_e($heroText); ?></p>
        <?php endif; ?>
        <?php kompass_search_form('kp-hero-search', '', 'hero'); ?>
        <?php if ($terms !== []) : ?>
            <div class="kp-hero__terms">
                <p class="kp-hero__terms-label" id="kp-terms-label">Häufig gesucht:</p>
                <ul class="kp-chips" aria-labelledby="kp-terms-label">
                    <?php foreach ($terms as $term) : ?>
                        <li><a class="kp-chip kp-chip--light" href="<?php echo kompass_e(kompass_search_url($term)); ?>"><?php echo kompass_e($term); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($quickLinks !== []) : ?>
    <section class="kp-section kp-section--quick" aria-labelledby="kp-quick-title">
        <div class="kp-container">
            <h2 class="kp-section__title" id="kp-quick-title"><?php echo kompass_e($quickHeading); ?></h2>
            <ul class="kp-quick">
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
        </div>
    </section>
<?php endif; ?>

<?php if ($topics !== []) : ?>
    <section class="kp-section kp-section--surface" aria-labelledby="kp-topics-title">
        <div class="kp-container">
            <header class="kp-section__head">
                <h2 class="kp-section__title" id="kp-topics-title"><?php echo kompass_e(kompass_text('kp_home', 'topics_heading', 'Themenbereiche')); ?></h2>
                <a class="kp-more" href="<?php echo kompass_e(kompass_archive_url('category')); ?>"><span>Alle Themen</span><?php echo kompass_icon('arrow'); ?></a>
            </header>
            <ul class="kp-topics">
                <?php foreach ($topics as $topic) : ?>
                    <li class="kp-topic">
                        <span class="kp-topic__icon"><?php echo kompass_icon(kompass_topic_icon($topic['slug'])); ?></span>
                        <h3 class="kp-topic__title"><a href="<?php echo kompass_e($topic['url']); ?>"><?php echo kompass_e($topic['name']); ?></a></h3>
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
        </div>
    </section>
<?php endif; ?>

<?php if ($latest !== [] || $faqs !== []) : ?>
    <div class="kp-section">
        <div class="kp-container kp-split<?php echo $latest !== [] && $faqs !== [] ? '' : ' kp-split--single'; ?>">
            <?php if ($latest !== []) : ?>
                <section class="kp-split__main" aria-labelledby="kp-latest-title">
                    <header class="kp-section__head">
                        <h2 class="kp-section__title" id="kp-latest-title"><?php echo kompass_e(kompass_text('kp_home', 'latest_heading', 'Aktuelle Informationen')); ?></h2>
                        <a class="kp-more" href="<?php echo kompass_e(kompass_url('/blog')); ?>"><span>Alle Meldungen</span><?php echo kompass_icon('arrow'); ?></a>
                    </header>
                    <ul class="kp-newslist">
                        <?php foreach ($latest as $item) : ?>
                            <?php kompass_news_item($item, 'h3'); ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if ($faqs !== []) : ?>
                <section class="kp-split__side" aria-labelledby="kp-faq-title">
                    <header class="kp-section__head">
                        <h2 class="kp-section__title" id="kp-faq-title"><?php echo kompass_e(kompass_text('kp_faq', 'faq_heading', 'Häufige Fragen')); ?></h2>
                    </header>
                    <div class="kp-faq">
                        <?php foreach ($faqs as $index => $faq) : ?>
                            <details class="kp-faq__item"<?php echo $index === 0 ? ' open' : ''; ?>>
                                <summary class="kp-faq__question"><?php echo kompass_e($faq['q']); ?></summary>
                                <div class="kp-faq__answer"><p><?php echo nl2br(kompass_e($faq['a'])); ?></p></div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($faqMoreLabel !== '' && $faqMoreUrl !== '') : ?>
                        <p class="kp-faq__more"><a class="kp-more" href="<?php echo kompass_e(kompass_url($faqMoreUrl)); ?>"><span><?php echo kompass_e($faqMoreLabel); ?></span><?php echo kompass_icon('arrow'); ?></a></p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div class="kp-section kp-section--service">
    <div class="kp-container">
        <?php kompass_service_box('h2'); ?>
    </div>
</div>
