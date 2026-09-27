<?php
declare(strict_types=1);

/**
 * Feder – Startseite
 *
 * Intro (Autor:in) → hervorgehobener neuester Beitrag → Beitragsliste nach Jahren
 * → Themen → Newsletter-Box. Der Router übergibt bei der Home-Route keine Daten,
 * daher werden Beiträge hier über die Theme-Helfer geladen.
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$siteTitle = feder_site_title();
$authorName = feder_text('feder_author', 'author_name') ?: $siteTitle;
$authorRole = feder_text('feder_author', 'author_role', 'Autorin & Redakteurin');
$authorImage = feder_media_url(feder_text('feder_author', 'author_image'));
$authorUrl = feder_text('feder_author', 'author_url', '/ueber-uns');

$introKicker = feder_text('feder_home', 'intro_kicker', (string) Feder_Theme::instance()->getConfig('intro_kicker', ''));
$introHeadline = feder_text('feder_home', 'intro_headline', (string) Feder_Theme::instance()->getConfig('intro_headline', $siteTitle));
$introText = feder_text('feder_home', 'intro_text', (string) Feder_Theme::instance()->getConfig('intro_text', ''));

$showFeatured = feder_flag('feder_home', 'show_featured', true);
$streamCount = (int) feder_css_number(feder_setting('feder_home', 'stream_count', 8), 8, 3, 20);
$showReadingTime = feder_flag('feder_article', 'show_reading_time', true);

$featured = $showFeatured ? (feder_get_posts(['limit' => 1])[0] ?? null) : null;
$stream = feder_get_posts(['limit' => $streamCount, 'exclude' => $featured !== null ? [(int) $featured->id] : []]);
$totalPosts = feder_count_posts();
$topics = feder_flag('feder_home', 'show_topics', true) ? feder_get_categories(12) : [];
$socialLinks = feder_social_links();
?>

<section class="fd-intro fd-measure" aria-labelledby="fd-intro-title">
    <div class="fd-intro__portrait" aria-hidden="true">
        <?php if ($authorImage !== '') : ?>
            <img src="<?php echo feder_e($authorImage); ?>" alt="" width="96" height="96" loading="eager" decoding="async">
        <?php else : ?>
            <span><?php echo feder_e(feder_initials($authorName)); ?></span>
        <?php endif; ?>
    </div>
    <?php if ($introKicker !== '') : ?>
        <p class="fd-kicker"><?php echo feder_e($introKicker); ?></p>
    <?php endif; ?>
    <h1 class="fd-intro__title" id="fd-intro-title"><?php echo feder_e($introHeadline); ?></h1>
    <?php if ($introText !== '') : ?>
        <p class="fd-intro__text"><?php echo nl2br(feder_e($introText), false); ?></p>
    <?php endif; ?>
    <p class="fd-intro__byline">
        <span class="fd-intro__author"><?php echo feder_e($authorName); ?></span>
        <?php if ($authorRole !== '') : ?><span class="fd-dot" aria-hidden="true">·</span><span><?php echo feder_e($authorRole); ?></span><?php endif; ?>
        <?php if (feder_safe_url($authorUrl) !== '') : ?>
            <span class="fd-dot" aria-hidden="true">·</span><a href="<?php echo feder_e(feder_url($authorUrl)); ?>">Mehr über mich</a>
        <?php endif; ?>
    </p>
    <?php if ($socialLinks !== []) : ?>
        <ul class="fd-social fd-social--center" aria-label="Profile und Feed">
            <?php foreach ($socialLinks as $link) : ?>
                <li><a href="<?php echo feder_e($link['url']); ?>"<?php echo preg_match('#^https?://#i', $link['url']) === 1 && $link['icon'] !== 'rss' ? ' target="_blank" rel="me noopener noreferrer"' : ''; ?>><?php echo feder_icon($link['icon']); ?><span class="fd-visually-hidden"><?php echo feder_e($link['label']); ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php if ($featured !== null) :
    $featuredUrl = feder_post_link($featured);
    $featuredImage = feder_media_url($featured->featured_image ?? '');
    $featuredExcerpt = feder_excerpt($featured, 260);
    $featuredCategory = trim((string) ($featured->category_name ?? ''));
    ?>
    <section class="fd-featured fd-wide" aria-labelledby="fd-featured-title">
        <article class="fd-featured__card<?php echo $featuredImage === '' ? ' fd-featured__card--text' : ''; ?>">
            <?php if ($featuredImage !== '') : ?>
                <a class="fd-featured__media" href="<?php echo feder_e($featuredUrl); ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo feder_e($featuredImage); ?>" alt="" width="960" height="540" loading="eager" decoding="async">
                </a>
            <?php endif; ?>
            <div class="fd-featured__body">
                <p class="fd-kicker">
                    <span>Neu</span>
                    <?php if ($featuredCategory !== '') : ?>
                        <span class="fd-dot" aria-hidden="true">·</span>
                        <a href="<?php echo feder_e(feder_archive_url('category', (string) ($featured->category_slug ?? ''))); ?>"><?php echo feder_e($featuredCategory); ?></a>
                    <?php endif; ?>
                </p>
                <h2 class="fd-featured__title" id="fd-featured-title"><a href="<?php echo feder_e($featuredUrl); ?>"><?php echo feder_e((string) ($featured->title ?? '')); ?></a></h2>
                <?php if ($featuredExcerpt !== '') : ?>
                    <p class="fd-featured__excerpt"><?php echo feder_e($featuredExcerpt); ?></p>
                <?php endif; ?>
                <p class="fd-meta">
                    <time datetime="<?php echo feder_e(feder_format_date($featured->published_at ?? $featured->created_at ?? '', 'iso')); ?>"><?php echo feder_e(feder_format_date($featured->published_at ?? $featured->created_at ?? '')); ?></time>
                    <?php if ($showReadingTime) : ?>
                        <span class="fd-dot" aria-hidden="true">·</span><span><?php echo feder_reading_time((string) ($featured->content ?? '')); ?> Min. Lesezeit</span>
                    <?php endif; ?>
                </p>
                <a class="fd-link-arrow" href="<?php echo feder_e($featuredUrl); ?>">Weiterlesen<?php echo feder_icon('arrow'); ?></a>
            </div>
        </article>
    </section>
<?php endif; ?>

<section class="fd-stream fd-measure" aria-labelledby="fd-stream-title">
    <h2 class="fd-section-title" id="fd-stream-title"><?php echo feder_e(feder_text('feder_home', 'stream_heading', 'Neueste Texte')); ?></h2>

    <?php if ($stream === [] && $featured === null) : ?>
        <p class="fd-empty">Noch keine Beiträge veröffentlicht. Der erste Text erscheint bald.</p>
    <?php else : ?>
        <?php
        $currentYear = '';
        $listOpen = false;
        foreach ($stream as $item) :
            $year = feder_format_date($item->published_at ?? $item->created_at ?? '', 'year');
            if ($year !== $currentYear) :
                if ($listOpen) {
                    echo '</ol>';
                }
                $currentYear = $year;
                $listOpen = true;
                ?>
                <h3 class="fd-year"><?php echo feder_e($year); ?></h3>
                <ol class="fd-postlist">
            <?php endif;
            $itemUrl = feder_post_link($item);
            $itemCategory = trim((string) ($item->category_name ?? ''));
            ?>
            <li class="fd-postlist__item">
                <article>
                    <time class="fd-postlist__date" datetime="<?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'iso')); ?>"><?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'day')); ?></time>
                    <div class="fd-postlist__body">
                        <h4 class="fd-postlist__title"><a href="<?php echo feder_e($itemUrl); ?>"><?php echo feder_e((string) ($item->title ?? '')); ?></a></h4>
                        <?php $itemExcerpt = feder_excerpt($item, 150); ?>
                        <?php if ($itemExcerpt !== '') : ?>
                            <p class="fd-postlist__excerpt"><?php echo feder_e($itemExcerpt); ?></p>
                        <?php endif; ?>
                        <p class="fd-meta">
                            <?php if ($itemCategory !== '') : ?>
                                <a href="<?php echo feder_e(feder_archive_url('category', (string) ($item->category_slug ?? ''))); ?>"><?php echo feder_e($itemCategory); ?></a>
                            <?php endif; ?>
                            <?php if ($showReadingTime) : ?>
                                <?php if ($itemCategory !== '') : ?><span class="fd-dot" aria-hidden="true">·</span><?php endif; ?>
                                <span><?php echo feder_reading_time((string) ($item->content ?? '')); ?> Min.</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </article>
            </li>
        <?php endforeach; ?>
        <?php if ($listOpen) : ?>
            </ol>
        <?php endif; ?>

        <?php if ($totalPosts > count($stream) + ($featured !== null ? 1 : 0)) : ?>
            <p class="fd-stream__more"><a class="fd-button fd-button--ghost" href="<?php echo feder_e(feder_url('/blog')); ?>">Alle <?php echo (int) $totalPosts; ?> Texte ansehen<?php echo feder_icon('arrow'); ?></a></p>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php if ($topics !== []) : ?>
    <section class="fd-topics fd-measure" aria-labelledby="fd-topics-title">
        <h2 class="fd-section-title" id="fd-topics-title"><?php echo feder_e(feder_text('feder_home', 'topics_heading', 'Themen')); ?></h2>
        <ul class="fd-chips">
            <?php foreach ($topics as $topic) : ?>
                <li><a class="fd-chip" href="<?php echo feder_e($topic['url']); ?>"><?php echo feder_e($topic['name']); ?><span class="fd-chip__count"><?php echo (int) $topic['count']; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<?php if (feder_flag('feder_home', 'show_newsletter', true)) :
    $boxHeading = feder_text('feder_home', 'newsletter_heading', 'Neue Texte per E-Mail');
    $boxText = feder_text('feder_home', 'newsletter_text');
    $boxLabel = feder_text('feder_home', 'newsletter_cta_label', 'Newsletter abonnieren');
    $boxUrl = feder_safe_url(feder_text('feder_home', 'newsletter_cta_url', '/kontakt'));
    ?>
    <aside class="fd-note fd-measure" aria-label="<?php echo feder_e($boxHeading); ?>">
        <h2 class="fd-note__title"><?php echo feder_e($boxHeading); ?></h2>
        <?php if ($boxText !== '') : ?>
            <p><?php echo feder_e($boxText); ?></p>
        <?php endif; ?>
        <div class="fd-note__actions">
            <?php if ($boxUrl !== '' && $boxLabel !== '') : ?>
                <a class="fd-button" href="<?php echo feder_e(feder_url($boxUrl)); ?>"><?php echo feder_e($boxLabel); ?></a>
            <?php endif; ?>
            <?php if (feder_flag('feder_author', 'show_rss', true)) : ?>
                <a class="fd-button fd-button--ghost" href="<?php echo feder_e(feder_feed_url()); ?>"><?php echo feder_icon('rss'); ?><span>RSS-Feed</span></a>
            <?php endif; ?>
        </div>
    </aside>
<?php endif; ?>
