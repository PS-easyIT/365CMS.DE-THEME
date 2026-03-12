<?php
/**
 * Partial: Sticky Sidebar (TOC, Social, Related Posts, Kategorien)
 *
 * Wird via get_theme_part('partials/sidebar', $vars) eingebunden.
 * Alle Variablen müssen explizit übergeben werden (Funktions-Scope).
 *
 * @var bool     $show_toc          TOC anzeigen
 * @var array    $toc_items         TOC-Items [level, id, text]
 * @var bool     $toc_sticky        Sticky-Klasse
 * @var string   $toc_header        TOC-Titeltext
 * @var bool     $show_social       Social-Widget anzeigen
 * @var string   $social_header     Social-Widget-Titel
 * @var string   $social_linkedin   LinkedIn-URL
 * @var string   $social_github     GitHub-URL
 * @var string   $social_twitter    Twitter/X-URL
 * @var string   $social_mastodon   Mastodon-URL
 * @var string   $social_youtube    YouTube-URL
 * @var string   $social_xing       XING-URL
 * @var string   $social_rss        RSS-URL
 * @var bool     $show_related      Related-Posts anzeigen
 * @var string   $related_header    Related-Titeltext
 * @var array    $related_posts     Related-Posts [title, slug]
 * @var array    $post_tags         Artikel-Tags [name, slug]
 * @var string   $site_url          Basis-URL
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$show_toc       = $show_toc       ?? true;
$toc_items      = $toc_items      ?? [];
$toc_sticky     = $toc_sticky     ?? true;
$toc_header     = $toc_header     ?? '📋 Inhaltsverzeichnis';
$show_social    = $show_social    ?? true;
$social_header  = $social_header  ?? 'Folge uns';
$social_linkedin= $social_linkedin?? '';
$social_github  = $social_github  ?? '';
$social_twitter = $social_twitter ?? '';
$social_mastodon= $social_mastodon?? '';
$social_youtube = $social_youtube ?? '';
$social_xing    = $social_xing    ?? '';
$social_rss     = $social_rss     ?? '';
$show_related   = $show_related   ?? true;
$related_header = $related_header ?? 'Ähnliche Artikel';
$related_posts  = $related_posts  ?? [];
$post_tags      = $post_tags      ?? [];
$site_url       = $site_url       ?? (defined('SITE_URL') ? SITE_URL : '');
?>
<aside class="sidebar" aria-label="Seitenleiste">

    <!-- TOC -->
    <?php if ($show_toc && !empty($toc_items)): ?>
    <div class="toc<?php echo $toc_sticky ? ' toc-sticky' : ''; ?>">
        <div class="toc-title"><?php echo htmlspecialchars($toc_header, ENT_QUOTES); ?></div>
        <div class="toc-panel">
            <ul class="toc-list" role="list">
                <?php foreach ($toc_items as $_sb_item): ?>
                <li class="<?php echo (int)($_sb_item['level'] ?? 2) === 3 ? 'toc-h3' : ''; ?>">
                    <a href="#<?php echo htmlspecialchars($_sb_item['id'] ?? '', ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($_sb_item['text'] ?? '', ENT_QUOTES); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Social Icons -->
    <?php if ($show_social): ?>
    <?php $_sb_hasSocial = !empty($social_linkedin) || !empty($social_github) || !empty($social_twitter)
                       || !empty($social_mastodon)  || !empty($social_youtube) || !empty($social_xing)
                       || !empty($social_rss); ?>
    <?php if ($_sb_hasSocial): ?>
    <div class="social-widget">
        <div class="social-widget-title"><?php echo htmlspecialchars($social_header, ENT_QUOTES); ?></div>
        <div class="social-icons">
            <?php if (!empty($social_linkedin)): ?>
            <a href="<?php echo htmlspecialchars($social_linkedin, ENT_QUOTES); ?>" class="li"
               target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in</a>
            <?php endif; ?>
            <?php if (!empty($social_github)): ?>
            <a href="<?php echo htmlspecialchars($social_github, ENT_QUOTES); ?>" class="gh"
               target="_blank" rel="noopener noreferrer" aria-label="GitHub">gh</a>
            <?php endif; ?>
            <?php if (!empty($social_twitter)): ?>
            <a href="<?php echo htmlspecialchars($social_twitter, ENT_QUOTES); ?>" class="tw"
               target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">𝕏</a>
            <?php endif; ?>
            <?php if (!empty($social_mastodon)): ?>
            <a href="<?php echo htmlspecialchars($social_mastodon, ENT_QUOTES); ?>" class="ma"
               target="_blank" rel="noopener noreferrer me" aria-label="Mastodon">🦣</a>
            <?php endif; ?>
            <?php if (!empty($social_youtube)): ?>
            <a href="<?php echo htmlspecialchars($social_youtube, ENT_QUOTES); ?>" class="yt"
               target="_blank" rel="noopener noreferrer" aria-label="YouTube">▶</a>
            <?php endif; ?>
            <?php if (!empty($social_xing)): ?>
            <a href="<?php echo htmlspecialchars($social_xing, ENT_QUOTES); ?>" class="xi"
               target="_blank" rel="noopener noreferrer" aria-label="XING">X</a>
            <?php endif; ?>
            <?php if (!empty($social_rss)): ?>
            <a href="<?php echo htmlspecialchars($social_rss, ENT_QUOTES); ?>" class="rss"
               aria-label="RSS-Feed">⊞</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Ähnliche Artikel -->
    <?php if ($show_related && !empty($related_posts)): ?>
    <div class="toc toc--accent">
        <div class="toc-title">📰 <?php echo htmlspecialchars($related_header, ENT_QUOTES); ?></div>
        <ul class="toc-list" role="list">
            <?php foreach ($related_posts as $_sb_rel): ?>
            <li>
                <a href="<?php echo htmlspecialchars($site_url . '/blog/' . ($_sb_rel['slug'] ?? ''), ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars($_sb_rel['title'] ?? '', ENT_QUOTES); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Tags-Widget -->
    <?php if (!empty($post_tags)): ?>
    <div class="toc toc--primary">
        <div class="toc-title">🏷 Tags</div>
        <ul class="toc-list" role="list">
            <?php foreach ($post_tags as $_sb_tag): ?>
            <li>
                <a href="<?php echo htmlspecialchars($site_url . '/tag/' . urlencode(phinit_display_text($_sb_tag['slug'] ?? $_sb_tag['name'] ?? '')), ENT_QUOTES); ?>">
                    <?php echo phinit_escape_text($_sb_tag['name'] ?? ''); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

</aside>
