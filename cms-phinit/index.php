<?php
/**
 * Homepage Template – CMS Phinit Theme
 *
 * Layout (alle Sektionen per Customizer konfigurierbar):
 *  1. Repo-Card (optional)
 *  2. Aktuelle Artikel-Liste (horizontal)
 *  3. Info-Cards 2-spaltig (Kategorie-Highlights)
 *  4. Kachel-Grid (paginiert)
 *  5. RSS-Feed-Sektion (cms-feed Plugin)
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$themeManager = \CMS\ThemeManager::instance();
$siteUrl      = SITE_URL;

// ── Customizer-Einstellungen laden ──────────────────────────────────────────
try {
    $_hc = \CMS\Services\ThemeCustomizer::instance();

    // Repo-Card
    $_showRepo     = filter_var($_hc->get('homepage', 'show_repo_card', true), FILTER_VALIDATE_BOOLEAN);
    $_repoTitle    = $_hc->get('homepage', 'repo_card_title', 'PS-easyIT Script-Repository');
    $_repoDesc     = $_hc->get('homepage', 'repo_card_description', '');
    $_repoBadge    = $_hc->get('homepage', 'repo_card_badge', '25+ Repos');
    $_repoBtnText  = $_hc->get('homepage', 'repo_card_btn_text', 'Zum GitHub →');
    $_repoBtnUrl   = $_hc->get('homepage', 'repo_card_btn_url', 'https://github.com/');

    // Artikel-Liste
    $_showList     = filter_var($_hc->get('homepage', 'show_article_list', true), FILTER_VALIDATE_BOOLEAN);
    $_listLabel    = $_hc->get('homepage', 'article_list_label', 'Aktuell');
    $_listCount    = max(1, (int)$_hc->get('homepage', 'article_list_count', 4));
    $_listLinkUrl  = $_hc->get('homepage', 'article_list_link_url', '/blog');
    $_listThumbW   = max(80, (int)$_hc->get('homepage', 'article_thumb_width', 190));
    $_listThumbH   = max(60, (int)$_hc->get('homepage', 'article_thumb_height', 115));
    $_showExcerpt  = filter_var($_hc->get('homepage', 'show_article_excerpt', true), FILTER_VALIDATE_BOOLEAN);
    $_showMeta     = filter_var($_hc->get('homepage', 'show_article_meta', true), FILTER_VALIDATE_BOOLEAN);
    $_showBadge    = filter_var($_hc->get('homepage', 'show_article_badge', true), FILTER_VALIDATE_BOOLEAN);
    $_showMetaCat  = filter_var($_hc->get('homepage', 'show_meta_category', true), FILTER_VALIDATE_BOOLEAN);
    $_showMetaDate = filter_var($_hc->get('homepage', 'show_meta_date', true), FILTER_VALIDATE_BOOLEAN);
    $_showMetaRT   = filter_var($_hc->get('homepage', 'show_meta_readtime', true), FILTER_VALIDATE_BOOLEAN);
    $_listExcLen   = max(60, (int)$_hc->get('typography', 'article_excerpt_length', 180));
    $_tileExcLen   = max(40, (int)$_hc->get('typography', 'tile_excerpt_length', 160));

    // Sidebar neben Artikel-Liste
    $_showListSidebar    = filter_var($_hc->get('homepage', 'show_list_sidebar', false), FILTER_VALIDATE_BOOLEAN);
    $_listSidebarWidth   = max(160, (int)$_hc->get('homepage', 'list_sidebar_width', 260));
    $_listSidebarTitle   = $_hc->get('homepage', 'list_sidebar_title', '');
    $_listSidebarContent = $_hc->get('homepage', 'list_sidebar_content', '');

    // Sidebar Widgets
    $_sbShowProjects   = filter_var($_hc->get('homepage', 'sidebar_show_projects',  true),  FILTER_VALIDATE_BOOLEAN);
    $_sbProj1Name      = $_hc->get('homepage', 'sidebar_project1_name', '365CMS.DE');
    $_sbProj1Desc      = $_hc->get('homepage', 'sidebar_project1_desc', 'Das eigene CMS – modular & flexibel');
    $_sbProj1Url       = $_hc->get('homepage', 'sidebar_project1_url',  'https://365cms.de');
    $_sbProj2Name      = $_hc->get('homepage', 'sidebar_project2_name', '365NETWORK.DE');
    $_sbProj2Desc      = $_hc->get('homepage', 'sidebar_project2_desc', 'Business-Netzwerk-Plattform');
    $_sbProj2Url       = $_hc->get('homepage', 'sidebar_project2_url',  'https://365network.de');
    $_sbShowStatus     = filter_var($_hc->get('homepage', 'sidebar_show_status',    true),  FILTER_VALIDATE_BOOLEAN);
    $_sbStatusLabel    = $_hc->get('homepage', 'sidebar_status_label',  'Dienst-Status');
    $_sbShowDownloads  = filter_var($_hc->get('homepage', 'sidebar_show_downloads', false), FILTER_VALIDATE_BOOLEAN);
    $_sbDownloadsLabel = $_hc->get('homepage', 'sidebar_downloads_label', 'Downloads & Checklisten');
    $_sbDownloadsItems = $_hc->get('homepage', 'sidebar_downloads_items', '');
    $_sbShowSocial     = filter_var($_hc->get('homepage', 'sidebar_show_social',    true),  FILTER_VALIDATE_BOOLEAN);
    $_sbSocialLabel    = $_hc->get('homepage', 'sidebar_social_label',   'Folge uns');
    // Social-URLs aus dem Social-Tab
    $_sbSocialLinkedin = $_hc->get('social', 'social_linkedin', '');
    $_sbSocialGithub   = $_hc->get('social', 'social_github',   '');
    $_sbSocialTwitter  = $_hc->get('social', 'social_twitter',  '');
    $_sbSocialMastodon = $_hc->get('social', 'social_mastodon', '');
    $_sbSocialRss      = $_hc->get('social', 'social_rss',      '');
    $_sbSocialYoutube  = $_hc->get('social', 'social_youtube',  '');
    $_sbSocialXing     = $_hc->get('social', 'social_xing',     '');
    $_sbLabelLinkedin  = $_hc->get('social', 'social_label_linkedin', 'LinkedIn');
    $_sbLabelGithub    = $_hc->get('social', 'social_label_github',   'GitHub');
    $_sbLabelRss       = $_hc->get('social', 'social_label_rss',      'RSS Feed');

    // Sidebar – neue Felder (Identity, Logos, Status-Dienste, Notice, Featured-Posts)
    $_sbShowIdentity      = filter_var($_hc->get('homepage', 'sidebar_show_identity',    true),  FILTER_VALIDATE_BOOLEAN);
    $_sbIdentityLogoUrl   = $_hc->get('homepage', 'sidebar_identity_logo_url',   '');
    $_sbIdentityTagline   = $_hc->get('homepage', 'sidebar_identity_tagline',    '');
    $_sbIdentityLinkUrl   = $_hc->get('homepage', 'sidebar_identity_link_url',   '/');
    $_sbProj1LogoUrl      = $_hc->get('homepage', 'sidebar_project1_logo_url',   '');
    $_sbProj2LogoUrl      = $_hc->get('homepage', 'sidebar_project2_logo_url',   '');
    $_sbStatusServices    = $_hc->get('homepage', 'sidebar_status_services',
        "Microsoft 365|https://status.office365.com|M365\nAzure|https://status.azure.com|AZ\nStarface|https://www.starface.com/support/|SF\nAnyDesk|https://status.anydesk.com|AD\nGitHub|https://githubstatus.com|GH\nCloudflare|https://www.cloudflarestatus.com|CF");
    $_sbShowNotice        = filter_var($_hc->get('homepage', 'sidebar_show_notice',      false), FILTER_VALIDATE_BOOLEAN);
    $_sbNoticeTitle       = $_hc->get('homepage', 'sidebar_notice_title',  '💡 Aktueller Hinweis');
    $_sbNoticeText        = $_hc->get('homepage', 'sidebar_notice_text',   '');
    $_sbNoticeUrl         = $_hc->get('homepage', 'sidebar_notice_url',    '');
    $_sbNoticeUrlText     = $_hc->get('homepage', 'sidebar_notice_url_text', 'Mehr erfahren →');
    // Featured Posts
    $_sbShowFeaturedPosts  = filter_var($_hc->get('homepage', 'sidebar_show_featured_posts', false), FILTER_VALIDATE_BOOLEAN);
    $_sbFeaturedPostsLabel = $_hc->get('homepage', 'sidebar_featured_posts_label', '📌 Empfohlene Artikel');
    $_sbFeaturedPostId1    = (int)$_hc->get('homepage', 'sidebar_featured_post_1', 0);
    $_sbFeaturedPostId2    = (int)$_hc->get('homepage', 'sidebar_featured_post_2', 0);
    $_sbFeaturedPostId3    = (int)$_hc->get('homepage', 'sidebar_featured_post_3', 0);

    // Info-Cards
    $_showInfoGrid = filter_var($_hc->get('homepage', 'show_info_grid', true), FILTER_VALIDATE_BOOLEAN);
    $_c1Title      = $_hc->get('homepage', 'info_card1_title', '🖥️ Admin Anleitungen');
    $_c1Text       = $_hc->get('homepage', 'info_card1_text', 'Schritt-für-Schritt-Tutorials für Microsoft 365 Administration.');
    $_c1LinkText   = $_hc->get('homepage', 'info_card1_link_text', 'Alle Anleitungen ansehen →');
    $_c1LinkUrl    = $_hc->get('homepage', 'info_card1_link_url', '/kategorie/anleitungen');
    $_c1Style      = $_hc->get('homepage', 'info_card1_style', 'default');
    $_c2Title      = $_hc->get('homepage', 'info_card2_title', '🔒 DSGVO & Compliance');
    $_c2Text       = $_hc->get('homepage', 'info_card2_text', 'Konfigurationsanleitungen und Best Practices für Microsoft Purview.');
    $_c2LinkText   = $_hc->get('homepage', 'info_card2_link_text', 'Compliance-Center →');
    $_c2LinkUrl    = $_hc->get('homepage', 'info_card2_link_url', '/kategorie/compliance');
    $_c2Style      = $_hc->get('homepage', 'info_card2_style', 'gold');

    // Info-Card 3 (optional – Repo-Card)
    $_showCard3  = filter_var($_hc->get('homepage', 'show_info_card3', false), FILTER_VALIDATE_BOOLEAN);
    $_c3Title    = $_hc->get('homepage', 'info_card3_title', '💻 GitHub / GitLab');
    $_c3Text     = $_hc->get('homepage', 'info_card3_text', '');
    $_c3LinkText = $_hc->get('homepage', 'info_card3_link_text', 'Zum Repository →');
    $_c3LinkUrl  = $_hc->get('homepage', 'info_card3_link_url', 'https://github.com/');
    $_c3Badge    = $_hc->get('homepage', 'info_card3_badge', '');
    $_c3Style    = $_hc->get('homepage', 'info_card3_style', 'repo');

    // Kachel-Grid
    $_showTileGrid  = filter_var($_hc->get('homepage', 'show_tile_grid', true), FILTER_VALIDATE_BOOLEAN);
    $_tileLabel     = $_hc->get('homepage', 'tile_grid_label', 'Weitere Beiträge');
    $_tileCount     = max(1, (int)$_hc->get('homepage', 'tile_grid_count', 6));
    $_tileCols      = max(2, min(4, (int)$_hc->get('homepage', 'tile_grid_columns', 3)));
    $_showTileExc  = filter_var($_hc->get('homepage', 'show_tile_excerpt', true), FILTER_VALIDATE_BOOLEAN);
    $_showTileCat  = filter_var($_hc->get('homepage', 'show_tile_category', true), FILTER_VALIDATE_BOOLEAN);
    $_showTileDate = filter_var($_hc->get('homepage', 'show_tile_date', true), FILTER_VALIDATE_BOOLEAN);
    $_tileLinkUrl  = $_hc->get('homepage', 'tile_grid_link_url', '/archiv');

    // Sektionsabstände (einzeln steuerbar)
    $_spRepo = max(0, (int)$_hc->get('homepage', 'spacing_repo_card',    32));
    $_spList = max(0, (int)$_hc->get('homepage', 'spacing_article_list', 32));
    $_spInfo = max(0, (int)$_hc->get('homepage', 'spacing_info_cards',   32));
    $_spGrid = max(0, (int)$_hc->get('homepage', 'spacing_tile_grid',    32));
    $_spRss  = max(0, (int)$_hc->get('homepage', 'spacing_rss_feeds',    0));

} catch (\Throwable $_e) {
    // Fallback-Defaults
    $_showRepo    = true;  $_repoTitle = 'PS-easyIT Script-Repository'; $_repoDesc = ''; $_repoBadge = '25+ Repos'; $_repoBtnText = 'Zum GitHub →'; $_repoBtnUrl = '#';
    $_showList    = true;  $_listLabel = 'Aktuell'; $_listCount = 4; $_listLinkUrl = '/blog';
    $_listThumbW  = 190;   $_listThumbH = 115;
    $_showExcerpt = true;  $_showMeta = true; $_showBadge = true;
    $_showMetaCat = true;  $_showMetaDate = true; $_showMetaRT = true;
    $_showInfoGrid = true;
    $_c1Title = '🖥️ Admin Anleitungen'; $_c1Text = ''; $_c1LinkText = 'Anleitungen →'; $_c1LinkUrl = '#'; $_c1Style = 'default';
    $_c2Title = '🔒 DSGVO & Compliance'; $_c2Text = ''; $_c2LinkText = 'Compliance →'; $_c2LinkUrl = '#'; $_c2Style = 'gold';
    $_showCard3 = false; $_c3Title = '💻 GitHub / GitLab'; $_c3Text = ''; $_c3LinkText = 'Zum Repository →'; $_c3LinkUrl = '#'; $_c3Badge = ''; $_c3Style = 'repo';
    $_showTileGrid = true; $_tileLabel = 'Weitere Beiträge'; $_tileCount = 6; $_tileCols = 3;
    $_showTileExc = true;  $_showTileCat = true; $_showTileDate = true; $_tileLinkUrl = '/archiv';
    $_listExcLen = 180; $_tileExcLen = 160;
    $_showListSidebar = false; $_listSidebarWidth = 260; $_listSidebarTitle = ''; $_listSidebarContent = '';
    $_sbShowProjects = true;  $_sbProj1Name = '365CMS.DE';    $_sbProj1Desc = 'Das eigene CMS – modular & flexibel'; $_sbProj1Url = 'https://365cms.de';
    $_sbProj2Name = '365NETWORK.DE'; $_sbProj2Desc = 'Business-Netzwerk-Plattform'; $_sbProj2Url = 'https://365network.de';
    $_sbShowStatus = true;    $_sbStatusLabel = 'Dienst-Status';
    $_sbShowDownloads = false; $_sbDownloadsLabel = 'Downloads & Checklisten'; $_sbDownloadsItems = '';
    $_sbShowSocial = true;    $_sbSocialLabel = 'Folge uns';
    $_sbSocialLinkedin = $_sbSocialGithub = $_sbSocialTwitter = $_sbSocialMastodon = $_sbSocialRss = $_sbSocialYoutube = $_sbSocialXing = '';
    $_sbLabelLinkedin = 'LinkedIn'; $_sbLabelGithub = 'GitHub'; $_sbLabelRss = 'RSS Feed';
    $_sbShowIdentity = true; $_sbIdentityLogoUrl = ''; $_sbIdentityTagline = ''; $_sbIdentityLinkUrl = '/';
    $_sbProj1LogoUrl = ''; $_sbProj2LogoUrl = '';
    $_sbStatusServices = "Microsoft 365|https://status.office365.com|M365\nAzure|https://status.azure.com|AZ\nStarface|https://www.starface.com/support/|SF\nAnyDesk|https://status.anydesk.com|AD\nGitHub|https://githubstatus.com|GH\nCloudflare|https://www.cloudflarestatus.com|CF";
    $_sbShowNotice = false; $_sbNoticeTitle = '💡 Aktueller Hinweis'; $_sbNoticeText = ''; $_sbNoticeUrl = ''; $_sbNoticeUrlText = 'Mehr erfahren →';
    $_sbShowFeaturedPosts = false; $_sbFeaturedPostsLabel = '📌 Empfohlene Artikel';
    $_sbFeaturedPostId1 = 0; $_sbFeaturedPostId2 = 0; $_sbFeaturedPostId3 = 0;
    $_spRepo = $_spList = $_spInfo = $_spGrid = 32; $_spRss = 0;
}

// ── Posts laden (direkt via Database, kein PostService nötig) ──────────────────
try {
    $db     = \CMS\Database::instance();
    $prefix = $db->getPrefix();

    // Artikel-Liste (über den Info-Cards)
    $featuredRows  = $_showList
        ? ($db->get_results(
            "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.views,
                    c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.status = 'published'
             ORDER BY p.published_at DESC
             LIMIT " . (int)$_listCount
          ) ?: [])
        : [];
    $featuredPosts = array_map(fn($r) => (array)$r, $featuredRows);

    // Kachel-Grid mit Paginierung
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $totalPosts  = (int)($db->get_var("SELECT COUNT(*) FROM {$prefix}posts WHERE status = 'published'") ?: 0);
    // Grid zeigt nur Beiträge, die *nicht* bereits in der Artikelliste oben stehen
    $_gridAvail  = max(0, $totalPosts - (int)$_listCount);
    $totalPages  = max(1, (int)ceil($_gridAvail / (int)$_tileCount));
    $_gridOffset = (int)$_listCount + (($currentPage - 1) * (int)$_tileCount);

    $gridRows  = $_showTileGrid
        ? ($db->get_results(
            "SELECT p.id, p.title, p.slug, p.excerpt, LEFT(p.content, 500) AS content, p.featured_image, p.published_at,
                    c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.status = 'published'
             ORDER BY p.published_at DESC
             LIMIT " . (int)$_tileCount . " OFFSET " . (int)$_gridOffset
          ) ?: [])
        : [];
    $gridPosts = array_map(fn($r) => (array)$r, $gridRows);

    // Featured Posts (Sidebar) – lade Posts nach gespeicherten IDs
    $sbFeaturedPosts = [];
    if ($_sbShowFeaturedPosts) {
        $_fpIds = array_filter([$_sbFeaturedPostId1, $_sbFeaturedPostId2, $_sbFeaturedPostId3]);
        if (!empty($_fpIds)) {
            $_fpIn   = implode(',', array_map('intval', $_fpIds));
            $_fpRows = $db->get_results(
                "SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at,
                        c.name AS category_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                 WHERE p.id IN ({$_fpIn}) AND p.status = 'published'"
            ) ?: [];
            // Sortierung nach ursprünglicher Reihenfolge aus den gespeicherten IDs
            $_fpMap = [];
            foreach (array_map(fn($r) => (array)$r, $_fpRows) as $_fpP) {
                $_fpMap[(int)$_fpP['id']] = $_fpP;
            }
            foreach ($_fpIds as $_fpId) {
                if (isset($_fpMap[$_fpId])) {
                    $sbFeaturedPosts[] = $_fpMap[$_fpId];
                }
            }
        }
    }

} catch (\Throwable $e) {
    $featuredPosts   = [];
    $gridPosts       = [];
    $sbFeaturedPosts = [];
    $currentPage     = 1;
    $totalPages      = 1;
}
?>
<?php \CMS\Hooks::doAction('home_content'); ?>
<div class="container" style="padding-top:28px;padding-bottom:0;">

    <!-- ── Repo-Card ─────────────────────────────────────────────── -->
    <?php if ($_showRepo && !empty($_repoTitle)): ?>
    <section class="content-section" style="margin-bottom:<?php echo (int)$_spRepo; ?>px" data-anim>
        <div class="repo-card">
            <div class="repo-card-icon" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="28" height="28" fill="currentColor"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
            </div>
            <div class="repo-card-body">
                <h3><?php echo htmlspecialchars($_repoTitle, ENT_QUOTES); ?></h3>
                <?php if (!empty($_repoDesc)): ?>
                <p><?php echo htmlspecialchars($_repoDesc, ENT_QUOTES); ?></p>
                <?php endif; ?>
            </div>
            <div class="repo-card-actions">
                <?php if (!empty($_repoBadge)): ?>
                <span class="repo-badge"><?php echo htmlspecialchars($_repoBadge, ENT_QUOTES); ?></span>
                <?php endif; ?>
                <?php if (!empty($_repoBtnText) && !empty($_repoBtnUrl)): ?>
                <a href="<?php echo htmlspecialchars($_repoBtnUrl, ENT_QUOTES); ?>"
                   class="btn btn-accent btn-sm" target="_blank" rel="noopener noreferrer">
                    <?php echo htmlspecialchars($_repoBtnText, ENT_QUOTES); ?> →
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Artikel-Liste ─────────────────────────────────────── -->
    <?php if ($_showList && !empty($featuredPosts)): ?>
    <section class="content-section" style="margin-bottom:<?php echo (int)$_spList; ?>px" data-anim>
        <div class="section-header">
            <span class="section-label">📄 <?php echo htmlspecialchars($_listLabel, ENT_QUOTES); ?></span>
        </div>
        <?php if ($_showListSidebar): ?>
        <div class="homepage-list-with-sidebar" style="--hp-sidebar-w:<?php echo (int)$_listSidebarWidth; ?>px">
        <div class="homepage-list-main">
        <?php endif; ?>
        <div class="article-list" style="border:1px solid var(--border-color);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);">
            <?php foreach ($featuredPosts as $post): ?>
            <article class="article-card">

                <div class="article-thumb">
                    <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
                         loading="lazy">
                    <?php else: ?>
                    <div class="article-thumb-placeholder" aria-hidden="true">
                        <span>📄</span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($post['category_name'])): ?>
                    <span class="thumb-badge badge-teal"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>

                <div class="article-body">
                    <h4>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                    </h4>
                    <?php if ($_showExcerpt): ?>
                    <p><?php
                        $excerpt = $post['excerpt'] ?? '';
                        if (empty(trim($excerpt)) && !empty($post['content'])) {
                            $excerpt = mb_strimwidth(strip_tags($post['content']), 0, $_listExcLen, '…');
                        }
                        echo htmlspecialchars(mb_strimwidth($excerpt, 0, $_listExcLen, '…'), ENT_QUOTES);
                    ?></p>
                    <?php endif; ?>
                    <?php if ($_showMeta): ?>
                    <?php
                        $rt = !empty($post['read_time']) ? (int)$post['read_time'] : 0;
                        if ($rt < 1 && !empty($post['content'])) {
                            $rt = max(1, (int)round(str_word_count(strip_tags($post['content'])) / 200));
                        }
                    ?>
                    <div class="article-meta">
                        <?php if ($_showMetaCat && !empty($post['category_name'])): ?>
                        <span class="cat"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        <?php if ($_showMetaDate): ?>
                        <span><?php echo htmlspecialchars(date('j. F Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        <?php if ($_showMetaRT && $rt > 0): ?>
                        <span class="read"><?php echo $rt; ?> Min.</span>
                        <?php endif; ?>
                        <a class="article-meta__more"
                           href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            &hellip; Weiter lesen &rarr;
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($_showListSidebar):
            $_sbFeaturedActive = $_sbShowFeaturedPosts && !empty($sbFeaturedPosts);
            // Projekt-Widget über 2 Featured-Artikeln – immer wenn Featured + Projekte aktiv
            $_sbFeatProjMode   = $_sbFeaturedActive && $_sbShowProjects;
            // Social zusätzlich aktiv? → Karten kompakt (feste Höhe via CSS)
            $_sbFeatSocialMode = $_sbFeaturedActive && $_sbShowSocial;
            // Anzahl Featured-Posts: 2 wenn Projekt-Widget oben sitzt, sonst 3
            $_sbFeatSlice      = $_sbFeatProjMode ? array_slice($sbFeaturedPosts, 0, 2) : $sbFeaturedPosts;
            $_sbAsideClass     = 'homepage-list-sidebar'
                               . ($_sbFeaturedActive  ? ' homepage-list-sidebar--feat'        : '')
                               . ($_sbFeatProjMode    ? ' homepage-list-sidebar--feat-proj'    : '')
                               . ($_sbFeatSocialMode  ? ' homepage-list-sidebar--feat-social'  : '');
        ?>
        </div><!-- /.homepage-list-main -->
        <aside class="<?php echo $_sbAsideClass; ?>">

            <?php /* Widget: Site Identity */ if (!$_sbFeaturedActive && $_sbShowIdentity && (!empty($_sbIdentityLogoUrl) || !empty($_sbIdentityTagline))): ?>
            <div class="sb-widget sb-widget--identity">
                <?php $_idLink = !empty($_sbIdentityLinkUrl) ? $_sbIdentityLinkUrl : '/'; ?>
                <a href="<?php echo htmlspecialchars($_idLink, ENT_QUOTES); ?>" class="sb-identity">
                    <?php if (!empty($_sbIdentityLogoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($_sbIdentityLogoUrl, ENT_QUOTES); ?>"
                         alt="Site Logo" class="sb-identity-logo" loading="lazy">
                    <?php endif; ?>
                    <?php if (!empty($_sbIdentityTagline)): ?>
                    <span class="sb-identity-tagline"><?php echo htmlspecialchars($_sbIdentityTagline, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <?php endif; ?>

            <?php /* Widget: Projekt – oben vor Featured (nur im feat-proj-Modus) */ if ($_sbFeatProjMode): ?>
            <div class="sb-widget sb-widget--projects sb-widget--projects-top">
                <div class="sb-widget-title">🚀 Unsere Projekte</div>
                <div class="sb-project-cards-grid">
                <?php foreach ([
                    [$_sbProj1Name, $_sbProj1Desc, $_sbProj1Url, $_sbProj1LogoUrl],
                    [$_sbProj2Name, $_sbProj2Desc, $_sbProj2Url, $_sbProj2LogoUrl],
                ] as [$pName, $pDesc, $pUrl, $pLogo]):
                    if (empty($pName) || empty($pUrl)) { continue; }
                    $_pInitials = mb_strtoupper(mb_substr(preg_replace('/[^a-z0-9]/iu', '', strip_tags($pName)), 0, 2));
                    $_pBgStyle  = !empty($pLogo)
                        ? 'background-image:url(' . htmlspecialchars($pLogo, ENT_QUOTES) . ');'
                        : '';
                ?>
                <a href="<?php echo htmlspecialchars($pUrl, ENT_QUOTES); ?>"
                   class="sb-project-card"
                   style="<?php echo $_pBgStyle; ?>"
                   target="_blank" rel="noopener noreferrer">
                    <?php if (empty($pLogo)): ?>
                    <div class="sb-project-placeholder-bg"
                         data-initials="<?php echo htmlspecialchars($_pInitials ?: '?', ENT_QUOTES); ?>"
                         aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="sb-project-body">
                        <strong class="sb-project-name"><?php echo htmlspecialchars($pName, ENT_QUOTES); ?></strong>
                        <?php if (!empty($pDesc)): ?>
                        <span class="sb-project-desc"><?php echo htmlspecialchars($pDesc, ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php /* Widget: Featured-Posts (Empfohlene Artikel) */ if ($_sbShowFeaturedPosts && !empty($sbFeaturedPosts)): ?>
            <div class="sb-widget sb-widget--featured">
                <div class="sb-widget-title"><?php echo htmlspecialchars($_sbFeaturedPostsLabel, ENT_QUOTES); ?></div>
                <?php foreach ($_sbFeatSlice as $_fp):
                    $_fpHref  = htmlspecialchars($siteUrl . '/blog/' . ($_fp['slug'] ?? ''), ENT_QUOTES);
                    $_fpTitle = htmlspecialchars($_fp['title'] ?? '', ENT_QUOTES);
                    $_fpDate  = !empty($_fp['published_at']) ? date('j. M Y', strtotime($_fp['published_at'])) : '';
                    $_fpCat   = htmlspecialchars($_fp['category_name'] ?? '', ENT_QUOTES);
                    $_fpThumb = !empty($_fp['featured_image']) ? htmlspecialchars($_fp['featured_image'], ENT_QUOTES) : '';
                ?>
                <a href="<?php echo $_fpHref; ?>" class="sb-featured-post">
                    <?php if (!empty($_fpThumb)): ?>
                    <img src="<?php echo $_fpThumb; ?>" alt="<?php echo $_fpTitle; ?>"
                         class="sb-featured-thumb" loading="lazy" width="64" height="48">
                    <?php else: ?>
                    <div class="sb-featured-thumb sb-featured-thumb--placeholder" aria-hidden="true">
                        <?php echo mb_substr(strip_tags($_fp['title'] ?? '?'), 0, 1); ?>
                    </div>
                    <?php endif; ?>
                    <div class="sb-featured-body">
                        <?php if (!empty($_fpCat)): ?>
                        <span class="sb-featured-cat"><?php echo $_fpCat; ?></span>
                        <?php endif; ?>
                        <div class="sb-featured-body-inner">
                            <span class="sb-featured-title"><?php echo $_fpTitle; ?></span>
                            <?php if (!empty($_fpDate)): ?>
                            <span class="sb-featured-meta"><?php echo $_fpDate; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php elseif ($_sbShowFeaturedPosts && empty($sbFeaturedPosts)): ?>
            <?php /* Featured aktiv aber keine Posts gewählt → nichts anzeigen */ ?>
            <?php endif; ?>

            <?php /* Widget: Projekt-Hinweise – Standard-Position (nur wenn Featured nicht aktiv) */ if (!$_sbShowFeaturedPosts && $_sbShowProjects): ?>
            <div class="sb-widget sb-widget--projects">
                <div class="sb-widget-title">🚀 Unsere Projekte</div>
                <div class="sb-project-cards-grid">
                <?php foreach ([
                    [$_sbProj1Name, $_sbProj1Desc, $_sbProj1Url, $_sbProj1LogoUrl],
                    [$_sbProj2Name, $_sbProj2Desc, $_sbProj2Url, $_sbProj2LogoUrl],
                ] as [$pName, $pDesc, $pUrl, $pLogo]):
                    if (empty($pName) || empty($pUrl)) { continue; }
                    $_pInitials = mb_strtoupper(mb_substr(preg_replace('/[^a-z0-9]/iu', '', strip_tags($pName)), 0, 2));
                    $_pBgStyle  = !empty($pLogo)
                        ? 'background-image:url(' . htmlspecialchars($pLogo, ENT_QUOTES) . ');'
                        : '';
                ?>
                <a href="<?php echo htmlspecialchars($pUrl, ENT_QUOTES); ?>"
                   class="sb-project-card"
                   style="<?php echo $_pBgStyle; ?>"
                   target="_blank" rel="noopener noreferrer">
                    <?php if (empty($pLogo)): ?>
                    <div class="sb-project-placeholder-bg"
                         data-initials="<?php echo htmlspecialchars($_pInitials ?: '?', ENT_QUOTES); ?>"
                         aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="sb-project-body">
                        <strong class="sb-project-name"><?php echo htmlspecialchars($pName, ENT_QUOTES); ?></strong>
                        <?php if (!empty($pDesc)): ?>
                        <span class="sb-project-desc"><?php echo htmlspecialchars($pDesc, ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php /* Widget: Dienst-Status */ if (!$_sbShowFeaturedPosts && $_sbShowStatus): ?>
            <div class="sb-widget sb-widget--status">
                <div class="sb-widget-title">🟢 <?php echo htmlspecialchars($_sbStatusLabel, ENT_QUOTES); ?></div>
                <?php
                $_sbSvcLines = array_filter(array_map('trim', explode("\n", $_sbStatusServices)));
                if (!empty($_sbSvcLines)):
                ?>
                <ul class="sb-status-list">
                <?php foreach ($_sbSvcLines as $_svcLine):
                    $_svcParts = explode('|', $_svcLine, 3);
                    if (empty($_svcParts[0])) { continue; }
                    $_svcName   = trim($_svcParts[0]);
                    $_svcUrl    = trim($_svcParts[1] ?? '#');
                    $_svcShort  = mb_strtoupper(mb_substr(trim($_svcParts[2] ?? $_svcParts[0]), 0, 4));
                ?>
                <li class="sb-status-row">
                    <a href="<?php echo htmlspecialchars($_svcUrl, ENT_QUOTES); ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="sb-status-service-link" title="<?php echo htmlspecialchars($_svcName, ENT_QUOTES); ?> Status">
                        <span class="sb-status-icon"><?php echo htmlspecialchars($_svcShort, ENT_QUOTES); ?></span>
                        <span class="sb-status-name"><?php echo htmlspecialchars($_svcName, ENT_QUOTES); ?></span>
                    </a>
                    <span class="sb-status-dot sb-status-dot--ok" title="Betrieb normal"></span>
                </li>
                <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="sb-status-row" style="padding:.5rem 0;">
                    <span class="sb-status-dot sb-status-dot--ok"></span>
                    <span style="font-size:.82rem;color:var(--text-secondary,#64748b);">Keine bekannten Störungen</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php /* Widget: Downloads */ if (!$_sbShowFeaturedPosts && $_sbShowDownloads && !empty(trim($_sbDownloadsItems))): ?>
            <div class="sb-widget sb-widget--downloads">
                <div class="sb-widget-title">📥 <?php echo htmlspecialchars($_sbDownloadsLabel, ENT_QUOTES); ?></div>
                <ul class="sb-download-list">
                <?php foreach (array_filter(array_map('trim', explode("\n", $_sbDownloadsItems))) as $_dl):
                    $_dlParts = explode('|', $_dl, 2);
                    if (count($_dlParts) < 2 || empty(trim($_dlParts[1]))) { continue; }
                    [$_dlTitle, $_dlUrl] = $_dlParts; ?>
                <li><a href="<?php echo htmlspecialchars(trim($_dlUrl), ENT_QUOTES); ?>"
                       target="_blank" rel="noopener noreferrer">📄 <?php echo htmlspecialchars(trim($_dlTitle), ENT_QUOTES); ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php
            /* Widget: Social Media */
            $_sbSocialSvg = [
                'linkedin' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                'github'   => '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>',
                'twitter'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.736-8.838L1.254 2.25H8.08l4.259 5.629L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>',
                'mastodon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/></svg>',
                'youtube'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                'xing'     => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M18.188 0c-.517 0-.741.325-.927.66 0 0-7.455 13.224-7.702 13.657.015.024 4.919 9.023 4.919 9.023.17.308.436.66.967.66h3.454c.211 0 .375-.078.463-.22.089-.151.089-.346-.009-.536l-4.879-8.916c-.004-.006-.004-.016 0-.022L22.139.756c.095-.191.097-.387.006-.535C22.056.078 21.894 0 21.686 0h-3.498zM3.648 4.74c-.211 0-.385.074-.473.216-.09.149-.078.339.02.527l2.308 4.031c.005.01.005.02 0 .029L2.19 15.85c-.09.172-.085.338.004.484.088.143.256.22.47.22h3.461c.518 0 .766-.348.945-.667l3.338-5.985c-.012-.02-2.303-4.055-2.303-4.055-.17-.31-.432-.648-.962-.648H3.648v-.43z"/></svg>',
                'rss'      => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/></svg>',
            ];
            $_sbSocialList = array_filter([
                'linkedin' => ['url' => $_sbSocialLinkedin, 'label' => $_sbLabelLinkedin],
                'github'   => ['url' => $_sbSocialGithub,   'label' => $_sbLabelGithub],
                'twitter'  => ['url' => $_sbSocialTwitter,  'label' => 'Twitter / X'],
                'mastodon' => ['url' => $_sbSocialMastodon, 'label' => 'Mastodon'],
                'youtube'  => ['url' => $_sbSocialYoutube,  'label' => 'YouTube'],
                'xing'     => ['url' => $_sbSocialXing,     'label' => 'XING'],
                'rss'      => ['url' => $_sbSocialRss,      'label' => $_sbLabelRss],
            ], fn($s) => !empty(trim($s['url'])));
            if ($_sbShowSocial && !empty($_sbSocialList)): ?>
            <div class="sb-widget sb-widget--social">
                <div class="sb-widget-title">👥 <?php echo htmlspecialchars($_sbSocialLabel, ENT_QUOTES); ?></div>
                <div class="sb-social-links">
                    <?php foreach ($_sbSocialList as $_sbKey => $_sbS): ?>
                    <a href="<?php echo htmlspecialchars($_sbS['url'], ENT_QUOTES); ?>"
                       class="sb-social-link sb-social-link--<?php echo htmlspecialchars($_sbKey, ENT_QUOTES); ?>"
                       target="_blank" rel="noopener noreferrer me"
                       aria-label="<?php echo htmlspecialchars($_sbS['label'], ENT_QUOTES); ?>">
                        <?php echo $_sbSocialSvg[$_sbKey] ?? htmlspecialchars($_sbS['label'], ENT_QUOTES); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php /* Widget: Ankündigung / Hinweis */ if (!$_sbFeaturedActive && $_sbShowNotice && !empty($_sbNoticeTitle)): ?>
            <div class="sb-widget sb-widget--notice">
                <div class="sb-notice-body">
                    <div class="sb-widget-title" style="margin-bottom:.5rem;"><?php echo htmlspecialchars($_sbNoticeTitle, ENT_QUOTES); ?></div>
                    <?php if (!empty($_sbNoticeText)): ?>
                    <p class="sb-notice-text"><?php echo htmlspecialchars($_sbNoticeText, ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($_sbNoticeUrl) && !empty($_sbNoticeUrlText)): ?>
                    <a href="<?php echo htmlspecialchars($_sbNoticeUrl, ENT_QUOTES); ?>"
                       class="sb-notice-link">
                        <?php echo htmlspecialchars($_sbNoticeUrlText, ENT_QUOTES); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php /* Freier HTML-Inhalt (Legacy / Zusatz) */ if (!empty(trim($_listSidebarContent))): ?>
            <div class="sb-widget">
                <?php echo strip_tags($_listSidebarContent,
                    '<p><a><strong><em><ul><ol><li><h3><h4><h5><br><hr><span><div><blockquote><code><pre>'); ?>
            </div>
            <?php endif; ?>

        </aside>
        </div><!-- /.homepage-list-with-sidebar -->
        <?php endif; ?>

    </section>
    <?php endif; ?>

    <!-- ── Kategorie-Cards (Info-Grid) ───────────────────────── -->
    <?php if ($_showInfoGrid): ?>
    <section class="content-section" style="margin-bottom:<?php echo (int)$_spInfo; ?>px" data-anim data-anim-delay="1">
        <div class="section-header">
            <span class="section-label section-label--dark">Themenbereiche</span>
        </div>
        <div class="info-grid<?php echo $_showCard3 ? ' info-grid--cols-3' : ''; ?>">
            <div class="info-card<?php echo $_c1Style === 'gold' ? ' info-card--gold' : ''; ?>">
                <h3><?php echo htmlspecialchars($_c1Title, ENT_QUOTES); ?></h3>
                <?php if (!empty($_c1Text)): ?>
                <p><?php echo htmlspecialchars($_c1Text, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_c1LinkUrl) && !empty($_c1LinkText)): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . $_c1LinkUrl, ENT_QUOTES); ?>" class="btn btn-outline btn-sm info-card-cta"><?php echo htmlspecialchars($_c1LinkText, ENT_QUOTES); ?></a>
                <?php endif; ?>
            </div>
            <div class="info-card<?php echo $_c2Style === 'gold' ? ' info-card--gold' : ''; ?>">
                <h3><?php echo htmlspecialchars($_c2Title, ENT_QUOTES); ?></h3>
                <?php if (!empty($_c2Text)): ?>
                <p><?php echo htmlspecialchars($_c2Text, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_c2LinkUrl) && !empty($_c2LinkText)): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . $_c2LinkUrl, ENT_QUOTES); ?>" class="btn btn-outline btn-sm info-card-cta"><?php echo htmlspecialchars($_c2LinkText, ENT_QUOTES); ?></a>
                <?php endif; ?>
            </div>
            <?php if ($_showCard3): ?>
            <?php
                $_c3IsRepo  = $_c3Style === 'repo';
                $_c3IsGold  = $_c3Style === 'gold';
                $_c3Classes = 'info-card' . ($_c3IsRepo ? ' info-card--repo' : ($_c3IsGold ? ' info-card--gold' : ''));
                $_c3FullUrl = str_starts_with($_c3LinkUrl, 'http') ? $_c3LinkUrl : $siteUrl . $_c3LinkUrl;
                $_c3Target  = str_starts_with($_c3LinkUrl, 'http') ? '_blank' : '_self';
            ?>
            <div class="<?php echo $_c3Classes; ?>">
                <?php if ($_c3IsRepo): ?>
                <div class="info-card-repo-icon" aria-hidden="true">
                    <svg viewBox="0 0 16 16" width="22" height="22" fill="currentColor"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
                </div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($_c3Title, ENT_QUOTES); ?></h3>
                <?php if (!empty($_c3Text)): ?>
                <p><?php echo htmlspecialchars($_c3Text, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_c3Badge)): ?>
                <span class="repo-badge" style="align-self:flex-start;margin-bottom:10px;"><?php echo htmlspecialchars($_c3Badge, ENT_QUOTES); ?></span>
                <?php endif; ?>
                <?php if (!empty($_c3FullUrl) && !empty($_c3LinkText)): ?>
                <a href="<?php echo htmlspecialchars($_c3FullUrl, ENT_QUOTES); ?>"
                   class="btn btn-sm info-card-cta <?php echo $_c3IsRepo ? 'btn-accent' : 'btn-outline'; ?>"
                   target="<?php echo $_c3Target; ?>" rel="noopener noreferrer">
                    <?php echo htmlspecialchars($_c3LinkText, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Kachel-Grid ─────────────────────────────────────────── -->
    <?php if ($_showTileGrid && !empty($gridPosts)): ?>
    <section class="content-section" style="margin-bottom:<?php echo (int)$_spGrid; ?>px" data-anim data-anim-delay="2">
        <div class="section-header">
            <span class="section-label">📰 <?php echo htmlspecialchars($_tileLabel, ENT_QUOTES); ?></span>
        </div>

        <div class="posts-grid posts-grid--cols-<?php echo $_tileCols; ?>">
            <?php foreach ($gridPosts as $i => $post): ?>
            <article class="post-card" data-anim data-anim-delay="<?php echo min($i + 1, 4); ?>">

                <?php if (!empty($post['featured_image'])): ?>
                <div class="post-card-thumb">
                    <img src="<?php echo htmlspecialchars($post['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
                         loading="lazy">
                </div>
                <?php else: ?>
                <div class="post-card-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--bg-tertiary);min-height:120px;">
                    <span style="font-size:2rem;opacity:.4;">📄</span>
                </div>
                <?php endif; ?>

                <div class="post-card-body">
                    <?php if ($_showTileCat && !empty($post['category_name'])): ?>
                    <span class="badge badge-neutral" style="align-self:flex-start;"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <h3 class="post-card-title">
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                    </h3>
                    <?php
                    $_tileExc = $post['excerpt'] ?? '';
                    if (empty(trim($_tileExc)) && !empty($post['content'])) {
                        $_tileExc = mb_strimwidth(strip_tags($post['content']), 0, $_tileExcLen, '…');
                    }
                    if ($_showTileExc && !empty(trim($_tileExc))): ?>
                    <p class="post-card-excerpt"><?php echo htmlspecialchars(mb_strimwidth($_tileExc, 0, $_tileExcLen, '…'), ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <div class="post-card-meta">
                        <div class="post-card-meta__left">
                        <?php if ($_showTileCat && !empty($post['category_name'])): ?>
                        <span class="cat"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        <?php if ($_showTileDate): ?>
                        <span><?php echo htmlspecialchars(date('j. M. Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        </div>
                        <a class="post-card-meta__more"
                           href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            &hellip; Weiter &rarr;
                        </a>
                    </div>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <!-- Paginierung -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Seitennavigation">
            <?php if ($currentPage > 1): ?>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $currentPage - 1; ?>" class="page-link" aria-label="Vorherige Seite">← Zurück</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 2): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $p; ?>" class="page-link <?php echo $p === $currentPage ? 'active' : ''; ?>" <?php echo $p === $currentPage ? 'aria-current="page"' : ''; ?>><?php echo $p; ?></a>
                <?php elseif (abs($p - $currentPage) === 3): ?>
                <span class="page-link dots" aria-hidden="true">…</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $currentPage + 1; ?>" class="page-link" aria-label="Nächste Seite">Weiter →</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

    </section>
    <?php endif; ?>

    <!-- ── RSS Feed Sektion (cms-feed Plugin) ─────────────────────────── -->
    <?php
    // Feed-Konfiguration aus Customizer laden
    try {
        $_fc         = \CMS\Services\ThemeCustomizer::instance();
        $_showFeeds  = filter_var($_fc->get('homepage', 'show_feed_section', true), FILTER_VALIDATE_BOOLEAN);
        $_feed1Id    = (int)$_fc->get('homepage', 'feed1_channel_id', 0);
        $_feed2Id    = (int)$_fc->get('homepage', 'feed2_channel_id', 0);
        $_feed1Count = max(1, (int)$_fc->get('homepage', 'feed1_count', 5));
        $_feed2Count = max(1, (int)$_fc->get('homepage', 'feed2_count', 5));
    } catch (\Throwable $_e) {
        $_showFeeds  = false;
        $_feed1Id = $_feed2Id = 0;
        $_feed1Count = $_feed2Count = 5;
    }

    $_feedSections = [];
    if ($_showFeeds
        && \CMS\PluginManager::instance()->isPluginActive('cms-feed')
        && class_exists('CMS_Feed_Database')
    ) {
        try {
            $_feedDb = CMS_Feed_Database::instance();
            foreach ([[$_feed1Id, $_feed1Count], [$_feed2Id, $_feed2Count]] as [$_fId, $_fCount]) {
                if ($_fId <= 0) continue;
                $_chan = $_feedDb->get_channel($_fId);
                if (!$_chan || empty($_chan['is_active'])) continue;
                $_feedSections[] = [
                    'channel' => $_chan,
                    'items'   => $_feedDb->get_items(['channel_id' => $_fId], 0, $_fCount),
                ];
            }
        } catch (\Throwable $_e) {}
    }
    ?>
    <?php if (!empty($_feedSections)): ?>
    <section class="content-section" style="margin-bottom:<?php echo (int)$_spRss; ?>px" data-anim data-anim-delay="3">
        <div class="feed-dual-grid">
            <?php foreach ($_feedSections as $_fsIdx => $_fs): ?>
            <div class="feed-dual-col">
                <div class="section-header">
                    <span class="section-label section-label--dark"><?php echo htmlspecialchars($_fs['channel']['name'] ?? 'Feed', ENT_QUOTES); ?></span>
                </div>
                <ul class="feed-list feed-list--inline">
                    <?php foreach (array_slice($_fs['items'], 0, 4) as $_fi): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($_fi['link'] ?? '#', ENT_QUOTES); ?>"
                           target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($_fi['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                        <span class="meta"><?php
                            $_ts = !empty($_fi['pub_date']) ? strtotime($_fi['pub_date']) : false;
                            echo htmlspecialchars($_ts ? date('j. M Y', $_ts) : '', ENT_QUOTES);
                        ?></span>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($_fs['items'])): ?>
                    <li style="color:var(--text-muted)">Keine Einträge verfügbar.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div><!-- /.container -->
