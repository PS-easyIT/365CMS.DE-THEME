<?php
/**
 * Einzelner Blog-Beitrag – MedCare Pro Theme
 *
 * Erwartet: $post (object|null)
 *
 * @package MedCarePro_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe    = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$blogUrl    = $safe(theme_route_url('blog'));
$homeUrl    = $safe(theme_route_url('home'));
$doctorsUrl = $safe(theme_route_url('doctors'));

if (empty($post)) {
    try {
        $post = \CMS\Services\PostService::getCurrent();
    } catch (\Throwable) {
        $post = null;
    }
}
?>
<main id="main" class="mc-main mc-blog-single" role="main">
    <div class="mc-container mc-blog-single__container">

        <?php if (!empty($post)) :
            $title    = $safe((string) ($post->title ?? ''));
            $content  = (string) ($post->content ?? '');
            $date     = isset($post->created_at) ? date('d. F Y', strtotime((string) $post->created_at)) : '';
            $author   = $safe((string) ($post->author_name ?? ''));
            $category = $safe((string) ($post->category_name ?? ''));
            $imgUrl   = $safe((string) ($post->thumbnail_url ?? ''));
            $showPriv = filter_var(mc_get_setting('dsgvo_medical', 'show_privacy_on_forms', true), FILTER_VALIDATE_BOOLEAN);
            $privacyNote = $showPriv
                ? (string) mc_get_setting('dsgvo_medical', 'privacy_form_text', '')
                : '';
        ?>

        <nav class="mc-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo $homeUrl; ?>">Startseite</a>
            <span class="mc-breadcrumb__sep" aria-hidden="true">›</span>
            <a href="<?php echo $blogUrl; ?>">Gesundheitsratgeber</a>
            <span class="mc-breadcrumb__sep" aria-hidden="true">›</span>
            <span aria-current="page"><?php echo $title; ?></span>
        </nav>

        <article class="mc-blog-single__article" aria-labelledby="post-title">
            <header class="mc-blog-single__header">
                <?php if ($category !== '') : ?>
                <span class="mc-specialty-badge mc-blog-single__category"><?php echo $category; ?></span>
                <?php endif; ?>

                <h1 id="post-title" class="mc-blog-single__title"><?php echo $title; ?></h1>

                <div class="mc-blog-single__meta">
                    <?php if ($author !== '') : ?>
                    <span class="mc-blog-single__author"><?php echo $author; ?></span>
                    <?php endif; ?>
                    <?php if ($date !== '') : ?>
                    <time class="mc-blog-single__date"><?php echo $date; ?></time>
                    <?php endif; ?>
                    <span class="mc-blog-single__back">
                        <a href="<?php echo $blogUrl; ?>">← Zurück zum Ratgeber</a>
                    </span>
                </div>
            </header>

            <?php if ($imgUrl !== '') : ?>
            <img src="<?php echo $imgUrl; ?>" alt="" class="mc-blog-single__hero-img">
            <?php endif; ?>

            <?php if ($content !== '') : ?>
            <div class="mc-card mc-blog-single__content prose">
                <?php echo \CMS\Helpers\ContentHelper::processContent($content); ?>
            </div>
            <?php endif; ?>

            <div class="mc-medical-disclaimer" role="note">
                <strong class="mc-medical-disclaimer__tag">
                    <span aria-hidden="true">⚕</span> Medizinischer Hinweis
                </strong>
                <p>
                    Dieser Artikel dient ausschließlich der allgemeinen Information. Er ersetzt keine professionelle ärztliche Beratung.
                    Bei gesundheitlichen Beschwerden wenden Sie sich bitte an einen Arzt.
                </p>
            </div>

            <?php if ($privacyNote !== '') : ?>
            <p class="mc-dsgvo-note mc-blog-single__privacy"><?php echo $safe($privacyNote); ?></p>
            <?php endif; ?>
        </article>

        <nav class="mc-blog-single__nav" aria-label="Beitragsnavigation">
            <a href="<?php echo $blogUrl; ?>" class="mc-btn mc-btn-outline">← Alle Beiträge</a>
            <a href="<?php echo $doctorsUrl; ?>" class="mc-btn mc-btn-primary">Passenden Arzt finden</a>
        </nav>

        <?php else : ?>
        <div class="mc-card mc-empty-state">
            <div class="mc-empty-state__icon" aria-hidden="true">🔍</div>
            <h1 class="mc-empty-state__title">Beitrag nicht gefunden</h1>
            <p class="mc-empty-state__text">Dieser Beitrag existiert leider nicht mehr.</p>
            <a href="<?php echo $blogUrl; ?>" class="mc-btn mc-btn-primary mc-empty-state__action">Zurück zum Ratgeber</a>
        </div>
        <?php endif; ?>

    </div>
</main>
<?php get_footer(); ?>
