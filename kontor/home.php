<?php
declare(strict_types=1);

/**
 * Kontor – Startseite
 *
 * Hero → Kundenleiste → Leistungen → Über uns → Kennzahlen → Ablauf → Referenz → FAQ
 * → Aktuelles (Beiträge). Das Kontakt-Band folgt im Footer. Alle Texte stammen aus dem
 * Customizer (theme.json → customization), die Beiträge live aus der Datenbank.
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$heroImage = kontor_media_url(kontor_text('kt_hero', 'image_url'));
$trustPoints = kontor_lines(kontor_text('kt_hero', 'trust_points'));
$badgeValue = kontor_text('kt_hero', 'badge_value');
$badgeLabel = kontor_text('kt_hero', 'badge_label');
$services = kontor_services();
$clients = array_values(array_filter(array_map('trim', explode(',', kontor_text('kt_trust', 'trust_names')))));

$stats = [];
for ($i = 1; $i <= 4; $i++) {
    $value = kontor_text('kt_stats', 'stat_' . $i . '_value');
    if ($value !== '') {
        $stats[] = ['value' => $value, 'label' => kontor_text('kt_stats', 'stat_' . $i . '_label')];
    }
}

$steps = [];
for ($i = 1; $i <= 4; $i++) {
    $title = kontor_text('kt_process', 'step_' . $i . '_title');
    if ($title !== '') {
        $steps[] = ['title' => $title, 'text' => kontor_text('kt_process', 'step_' . $i . '_text')];
    }
}

$faqs = [];
for ($i = 1; $i <= 5; $i++) {
    $question = kontor_text('kt_faq', 'faq_' . $i . '_q');
    $answer = kontor_text('kt_faq', 'faq_' . $i . '_a');
    if ($question !== '' && $answer !== '') {
        $faqs[] = ['q' => $question, 'a' => $answer];
    }
}

$news = kontor_flag('kt_news', 'show_news', true) ? kontor_get_posts(['limit' => kontor_int('kt_news', 'news_count', 3, 2, 6)]) : [];
$aboutImage = kontor_media_url(kontor_text('kt_about', 'about_image'));
$aboutPoints = kontor_lines(kontor_text('kt_about', 'about_points'));
?>

<section class="kt-hero" aria-labelledby="kt-hero-title">
    <div class="kt-container kt-hero__grid">
        <div class="kt-hero__content">
            <?php if (kontor_text('kt_hero', 'eyebrow') !== '') : ?>
                <p class="kt-eyebrow kt-eyebrow--light"><?php echo kontor_e(kontor_text('kt_hero', 'eyebrow')); ?></p>
            <?php endif; ?>
            <h1 class="kt-hero__title" id="kt-hero-title"><?php echo kontor_highlight(kontor_text('kt_hero', 'headline', kontor_site_title())); ?></h1>
            <?php if (kontor_text('kt_hero', 'text') !== '') : ?>
                <p class="kt-hero__text"><?php echo kontor_e(kontor_text('kt_hero', 'text')); ?></p>
            <?php endif; ?>
            <div class="kt-hero__actions">
                <?php echo kontor_button(kontor_text('kt_hero', 'cta_primary_label'), kontor_text('kt_hero', 'cta_primary_url', '/kontakt'), 'accent', true); ?>
                <?php echo kontor_button(kontor_text('kt_hero', 'cta_secondary_label'), kontor_text('kt_hero', 'cta_secondary_url', '#leistungen'), 'outline-light'); ?>
            </div>
            <?php if ($trustPoints !== []) : ?>
                <ul class="kt-hero__points">
                    <?php foreach ($trustPoints as $point) : ?>
                        <li><?php echo kontor_icon('check'); ?><span><?php echo kontor_e($point); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="kt-hero__visual<?php echo $heroImage === '' ? ' kt-hero__visual--pattern' : ''; ?>">
            <?php if ($heroImage !== '') : ?>
                <img src="<?php echo kontor_e($heroImage); ?>" alt="" width="720" height="560" loading="eager" decoding="async" fetchpriority="high">
            <?php else : ?>
                <div class="kt-hero__pattern" aria-hidden="true">
                    <?php foreach (array_slice($services, 0, 4) as $service) : ?>
                        <span class="kt-hero__tile"><?php echo kontor_icon($service['icon']); ?><span><?php echo kontor_e($service['title']); ?></span></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($badgeValue !== '') : ?>
                <p class="kt-hero__badge">
                    <span class="kt-hero__badge-stars" aria-hidden="true"><?php echo str_repeat(kontor_icon('star'), 5); ?></span>
                    <strong><?php echo kontor_e($badgeValue); ?></strong>
                    <?php if ($badgeLabel !== '') : ?><span><?php echo kontor_e($badgeLabel); ?></span><?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (kontor_flag('kt_trust', 'show_trust', true) && $clients !== []) : ?>
    <section class="kt-trust" aria-labelledby="kt-trust-title">
        <div class="kt-container">
            <h2 class="kt-trust__title" id="kt-trust-title"><?php echo kontor_e(kontor_text('kt_trust', 'trust_heading')); ?></h2>
            <ul class="kt-trust__list">
                <?php foreach ($clients as $client) : ?>
                    <li><?php echo kontor_e($client); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php endif; ?>

<?php if ($services !== []) : ?>
    <section class="kt-section" id="leistungen" aria-labelledby="kt-services-title">
        <div class="kt-container">
            <header class="kt-section__head">
                <p class="kt-eyebrow">Leistungen</p>
                <h2 class="kt-section__title" id="kt-services-title"><?php echo kontor_highlight(kontor_text('kt_services', 'services_heading', 'Unsere Leistungen')); ?></h2>
                <?php if (kontor_text('kt_services', 'services_intro') !== '') : ?>
                    <p class="kt-section__intro"><?php echo kontor_e(kontor_text('kt_services', 'services_intro')); ?></p>
                <?php endif; ?>
            </header>
            <ul class="kt-services">
                <?php foreach ($services as $service) : ?>
                    <li class="kt-service">
                        <span class="kt-service__icon"><?php echo kontor_icon($service['icon']); ?></span>
                        <h3 class="kt-service__title">
                            <?php if ($service['url'] !== '') : ?>
                                <a href="<?php echo kontor_e(kontor_url($service['url'], kontor_is_home_request())); ?>"><?php echo kontor_e($service['title']); ?></a>
                            <?php else : ?>
                                <?php echo kontor_e($service['title']); ?>
                            <?php endif; ?>
                        </h3>
                        <?php if ($service['text'] !== '') : ?>
                            <p class="kt-service__text"><?php echo kontor_e($service['text']); ?></p>
                        <?php endif; ?>
                        <?php if ($service['url'] !== '') : ?>
                            <span class="kt-service__more" aria-hidden="true">Mehr erfahren<?php echo kontor_icon('arrow'); ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php endif; ?>

<?php if (kontor_flag('kt_about', 'show_about', true)) : ?>
    <section class="kt-section kt-section--sand" id="ueber-uns" aria-labelledby="kt-about-title">
        <div class="kt-container kt-about">
            <div class="kt-about__media<?php echo $aboutImage === '' ? ' kt-about__media--empty' : ''; ?>">
                <?php if ($aboutImage !== '') : ?>
                    <img src="<?php echo kontor_e($aboutImage); ?>" alt="" width="640" height="520" loading="lazy" decoding="async">
                <?php else : ?>
                    <span class="kt-about__monogram" aria-hidden="true"><?php echo kontor_e(kontor_initials(kontor_company_name())); ?></span>
                <?php endif; ?>
                <?php if ($stats !== []) : ?>
                    <p class="kt-about__stat"><strong><?php echo kontor_e($stats[0]['value']); ?></strong><span><?php echo kontor_e($stats[0]['label']); ?></span></p>
                <?php endif; ?>
            </div>
            <div class="kt-about__content">
                <p class="kt-eyebrow"><?php echo kontor_e(kontor_text('kt_about', 'about_eyebrow', 'Über uns')); ?></p>
                <h2 class="kt-section__title" id="kt-about-title"><?php echo kontor_highlight(kontor_text('kt_about', 'about_heading')); ?></h2>
                <?php foreach (kontor_lines(kontor_text('kt_about', 'about_text')) as $paragraph) : ?>
                    <p class="kt-about__text"><?php echo kontor_e($paragraph); ?></p>
                <?php endforeach; ?>
                <?php if ($aboutPoints !== []) : ?>
                    <ul class="kt-checklist">
                        <?php foreach ($aboutPoints as $point) : ?>
                            <li><?php echo kontor_icon('check'); ?><span><?php echo kontor_e($point); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php echo kontor_button(kontor_text('kt_about', 'about_cta_label'), kontor_text('kt_about', 'about_cta_url'), 'primary', true); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (kontor_flag('kt_stats', 'show_stats', true) && $stats !== []) : ?>
    <section class="kt-stats" aria-label="Kennzahlen">
        <div class="kt-container">
            <dl class="kt-stats__grid">
                <?php foreach ($stats as $stat) : ?>
                    <div class="kt-stats__item">
                        <dt><?php echo kontor_e($stat['label']); ?></dt>
                        <dd><?php echo kontor_e($stat['value']); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </section>
<?php endif; ?>

<?php if (kontor_flag('kt_process', 'show_process', true) && $steps !== []) : ?>
    <section class="kt-section" aria-labelledby="kt-process-title">
        <div class="kt-container">
            <header class="kt-section__head">
                <p class="kt-eyebrow">Ablauf</p>
                <h2 class="kt-section__title" id="kt-process-title"><?php echo kontor_highlight(kontor_text('kt_process', 'process_heading', 'So arbeiten wir zusammen')); ?></h2>
            </header>
            <ol class="kt-steps">
                <?php foreach ($steps as $index => $step) : ?>
                    <li class="kt-step">
                        <span class="kt-step__number" aria-hidden="true"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                        <h3 class="kt-step__title"><?php echo kontor_e($step['title']); ?></h3>
                        <?php if ($step['text'] !== '') : ?>
                            <p><?php echo kontor_e($step['text']); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>
<?php endif; ?>

<?php if (kontor_flag('kt_testimonial', 'show_testimonial', true) && kontor_text('kt_testimonial', 'quote') !== '') :
    $person = kontor_text('kt_testimonial', 'person');
    ?>
    <section class="kt-section kt-section--tight" aria-label="Kundenstimme">
        <div class="kt-container">
            <figure class="kt-testimonial">
                <?php echo kontor_icon('quote', 'kt-testimonial__mark'); ?>
                <blockquote class="kt-testimonial__quote"><p><?php echo kontor_e(kontor_text('kt_testimonial', 'quote')); ?></p></blockquote>
                <?php if ($person !== '') : ?>
                    <figcaption class="kt-testimonial__person">
                        <span class="kt-testimonial__avatar" aria-hidden="true"><?php echo kontor_e(kontor_initials($person)); ?></span>
                        <span><strong><?php echo kontor_e($person); ?></strong><?php if (kontor_text('kt_testimonial', 'role') !== '') : ?><br><?php echo kontor_e(kontor_text('kt_testimonial', 'role')); ?><?php endif; ?></span>
                    </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    </section>
<?php endif; ?>

<?php if (kontor_flag('kt_faq', 'show_faq', true) && $faqs !== []) : ?>
    <section class="kt-section kt-section--sand" aria-labelledby="kt-faq-title">
        <div class="kt-container kt-faq">
            <header class="kt-faq__head">
                <p class="kt-eyebrow">FAQ</p>
                <h2 class="kt-section__title" id="kt-faq-title"><?php echo kontor_highlight(kontor_text('kt_faq', 'faq_heading', 'Häufige Fragen')); ?></h2>
                <p class="kt-section__intro">Ihre Frage ist nicht dabei? Wir antworten gern persönlich.</p>
                <?php echo kontor_button(kontor_text('header', 'header_cta_label', 'Beratung anfragen'), kontor_text('header', 'header_cta_url', '/kontakt'), 'primary', true); ?>
            </header>
            <div class="kt-faq__list">
                <?php foreach ($faqs as $index => $faq) : ?>
                    <details class="kt-faq__item"<?php echo $index === 0 ? ' open' : ''; ?>>
                        <summary><span><?php echo kontor_e($faq['q']); ?></span><?php echo kontor_icon('plus', 'kt-faq__icon'); ?></summary>
                        <div class="kt-faq__answer">
                            <?php foreach (kontor_lines($faq['a']) as $line) : ?>
                                <p><?php echo kontor_e($line); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($news !== []) : ?>
    <section class="kt-section" aria-labelledby="kt-news-title">
        <div class="kt-container">
            <header class="kt-section__head kt-section__head--row">
                <div>
                    <p class="kt-eyebrow">Blog</p>
                    <h2 class="kt-section__title" id="kt-news-title"><?php echo kontor_highlight(kontor_text('kt_news', 'news_heading', 'Aktuelles')); ?></h2>
                </div>
                <a class="kt-link-arrow" href="<?php echo kontor_e(kontor_url('/blog')); ?>">Alle Beiträge<?php echo kontor_icon('arrow'); ?></a>
            </header>
            <div class="kt-postgrid">
                <?php foreach ($news as $item) : ?>
                    <?php kontor_post_card($item, 'h3'); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
