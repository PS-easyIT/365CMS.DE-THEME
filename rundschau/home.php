<?php
declare(strict_types=1);

/**
 * Rundschau – Startseite
 *
 * Top-Themen (Aufmacher + vier Meldungen) mit Seitenleiste „Neueste Meldungen“ und
 * „Meistgelesen“ → Ressort-Blöcke → „Im Fokus“ (Schlagwort) → Newsletter-Band.
 * Der Router übergibt bei der Home-Route keine Daten; alle Inhalte kommen live aus der Datenbank.
 *
 * @package Rundschau_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$top = rundschau_get_posts(['limit' => 5, 'with_content' => true]);
$lead = $top[0] ?? null;
$secondary = array_slice($top, 1, 4);
$shownIds = rundschau_ids($top);

$latest = rundschau_get_posts(['limit' => rundschau_int('rs_home', 'latest_count', 8, 4, 15)]);
$popular = rundschau_popular_posts(
    rundschau_int('rs_home', 'popular_count', 5, 3, 10),
    rundschau_int('rs_home', 'popular_days', 30, 0, 365)
);

$ressortBlocks = [];
foreach (rundschau_ressorts(rundschau_int('rs_home', 'ressort_count', 4, 0, 8)) as $ressort) {
    $items = rundschau_get_posts(['limit' => 4, 'category_id' => (int) $ressort['id'], 'exclude' => $shownIds, 'with_content' => true]);
    if ($items === []) {
        continue;
    }
    $shownIds = array_merge($shownIds, rundschau_ids($items));
    $ressortBlocks[] = ['ressort' => $ressort, 'items' => $items];
}

$focusTag = rundschau_text('rs_home', 'focus_tag');
$focusItems = $focusTag !== '' ? rundschau_get_posts(['limit' => 4, 'tag' => $focusTag]) : [];
?>
<div class="rs-container rs-home">
    <h1 class="rs-visually-hidden"><?php echo rundschau_e(rundschau_site_title()); ?> – Nachrichten</h1>

    <?php if ($lead === null) : ?>
        <div class="rs-empty">
            <h2>Noch keine Meldungen</h2>
            <p>Sobald Beiträge veröffentlicht sind, erscheinen hier Top-Themen, Ressorts und die meistgelesenen Meldungen.</p>
        </div>
    <?php else : ?>
        <section class="rs-top" aria-labelledby="rs-top-title">
            <h2 class="rs-section-title" id="rs-top-title"><span><?php echo rundschau_e(rundschau_text('rs_home', 'top_heading', 'Top-Themen')); ?></span></h2>
            <div class="rs-top__grid">
                <div class="rs-top__main">
                    <?php rundschau_teaser($lead, 'lead', 'h3'); ?>
                    <?php if ($secondary !== []) : ?>
                        <div class="rs-grid rs-grid--2">
                            <?php foreach ($secondary as $item) : ?>
                                <?php rundschau_teaser($item, 'card', 'h3'); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="rs-top__aside" aria-label="Neueste und meistgelesene Meldungen">
                    <?php if ($latest !== []) : ?>
                        <section class="rs-box" aria-labelledby="rs-latest-title">
                            <h2 class="rs-box__title" id="rs-latest-title"><span class="rs-live-dot" aria-hidden="true"></span><?php echo rundschau_e(rundschau_text('rs_home', 'latest_heading', 'Neueste Meldungen')); ?></h2>
                            <ol class="rs-timeline">
                                <?php foreach ($latest as $item) :
                                    $date = (string) ($item->published_at ?? $item->created_at ?? '');
                                    ?>
                                    <li class="rs-timeline__item">
                                        <time datetime="<?php echo rundschau_e(rundschau_format_date($date, 'iso')); ?>"><?php echo rundschau_e(rundschau_time_label($date)); ?></time>
                                        <div>
                                            <?php echo rundschau_kicker($item, 'rs-kicker rs-kicker--small'); ?>
                                            <a class="rs-timeline__link" href="<?php echo rundschau_e(rundschau_post_link($item)); ?>"><?php echo rundschau_e((string) ($item->title ?? '')); ?></a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                            <a class="rs-more" href="<?php echo rundschau_e(rundschau_url('/blog')); ?>">Alle Meldungen<?php echo rundschau_icon('arrow'); ?></a>
                        </section>
                    <?php endif; ?>

                    <?php if ($popular !== []) : ?>
                        <section class="rs-box rs-box--popular" aria-labelledby="rs-popular-title">
                            <h2 class="rs-box__title" id="rs-popular-title"><?php echo rundschau_e(rundschau_text('rs_home', 'popular_heading', 'Meistgelesen')); ?></h2>
                            <ol class="rs-popular">
                                <?php foreach ($popular as $item) : ?>
                                    <li class="rs-popular__item">
                                        <a href="<?php echo rundschau_e(rundschau_post_link($item)); ?>"><?php echo rundschau_e((string) ($item->title ?? '')); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endif; ?>
                </aside>
            </div>
        </section>
    <?php endif; ?>

    <?php foreach ($ressortBlocks as $index => $block) :
        $ressort = $block['ressort'];
        $items = $block['items'];
        $headingId = 'rs-ressort-' . (int) $ressort['id'];
        ?>
        <section class="rs-ressort <?php echo rundschau_e(rundschau_ressort_class((string) $ressort['slug'])); ?>" aria-labelledby="<?php echo rundschau_e($headingId); ?>">
            <header class="rs-ressort__head">
                <h2 class="rs-ressort__title" id="<?php echo rundschau_e($headingId); ?>"><a href="<?php echo rundschau_e($ressort['url']); ?>"><?php echo rundschau_e($ressort['name']); ?></a></h2>
                <a class="rs-more" href="<?php echo rundschau_e($ressort['url']); ?>">Mehr aus <?php echo rundschau_e($ressort['name']); ?><?php echo rundschau_icon('arrow'); ?></a>
            </header>
            <div class="rs-ressort__grid<?php echo $index % 2 === 1 ? ' rs-ressort__grid--flip' : ''; ?>">
                <div class="rs-ressort__lead">
                    <?php rundschau_teaser($items[0], 'feature', 'h3'); ?>
                </div>
                <?php if (count($items) > 1) : ?>
                    <ul class="rs-ressort__list">
                        <?php foreach (array_slice($items, 1) as $item) : ?>
                            <li><?php rundschau_teaser($item, 'compact', 'h3'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<?php if ($focusItems !== []) : ?>
    <section class="rs-focus" aria-labelledby="rs-focus-title">
        <div class="rs-container">
            <header class="rs-focus__head">
                <h2 class="rs-focus__title" id="rs-focus-title"><?php echo rundschau_e(rundschau_text('rs_home', 'focus_heading', 'Im Fokus')); ?></h2>
                <a class="rs-more rs-more--light" href="<?php echo rundschau_e(rundschau_archive_url('tag', $focusTag)); ?>">Alle Beiträge zum Thema<?php echo rundschau_icon('arrow'); ?></a>
            </header>
            <div class="rs-grid rs-grid--4">
                <?php foreach ($focusItems as $item) : ?>
                    <?php rundschau_teaser($item, 'card', 'h3'); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (rundschau_flag('rs_home', 'show_newsletter', true)) :
    $nlUrl = rundschau_safe_url(rundschau_text('rs_home', 'newsletter_cta_url', '/kontakt'));
    $nlLabel = rundschau_text('rs_home', 'newsletter_cta_label', 'Kostenlos abonnieren');
    ?>
    <section class="rs-newsletter" aria-labelledby="rs-newsletter-title">
        <div class="rs-container rs-newsletter__inner">
            <div>
                <h2 class="rs-newsletter__title" id="rs-newsletter-title"><?php echo rundschau_e(rundschau_text('rs_home', 'newsletter_heading', 'Der Morgen-Überblick')); ?></h2>
                <p><?php echo rundschau_e(rundschau_text('rs_home', 'newsletter_text')); ?></p>
            </div>
            <div class="rs-newsletter__actions">
                <?php if ($nlUrl !== '' && $nlLabel !== '') : ?>
                    <a class="rs-button rs-button--accent" href="<?php echo rundschau_e(rundschau_url($nlUrl)); ?>"><?php echo rundschau_e($nlLabel); ?></a>
                <?php endif; ?>
                <a class="rs-button rs-button--ghost" href="<?php echo rundschau_e(rundschau_url('/feed')); ?>"><?php echo rundschau_icon('rss'); ?><span>RSS</span></a>
            </div>
        </div>
    </section>
<?php endif; ?>
