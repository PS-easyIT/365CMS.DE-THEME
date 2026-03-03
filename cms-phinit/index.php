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

    // Kachel-Grid
    $_showTileGrid  = filter_var($_hc->get('homepage', 'show_tile_grid', true), FILTER_VALIDATE_BOOLEAN);
    $_tileLabel     = $_hc->get('homepage', 'tile_grid_label', 'Weitere Beiträge');
    $_tileCount     = max(1, (int)$_hc->get('homepage', 'tile_grid_count', 6));
    $_tileCols      = max(2, min(4, (int)$_hc->get('homepage', 'tile_grid_columns', 3)));
    $_showTileExc   = filter_var($_hc->get('homepage', 'show_tile_excerpt', true), FILTER_VALIDATE_BOOLEAN);
    $_showTileCat   = filter_var($_hc->get('homepage', 'show_tile_category', true), FILTER_VALIDATE_BOOLEAN);
    $_showTileDate  = filter_var($_hc->get('homepage', 'show_tile_date', true), FILTER_VALIDATE_BOOLEAN);
    $_tileLinkUrl   = $_hc->get('homepage', 'tile_grid_link_url', '/archiv');

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
    $_showTileGrid = true; $_tileLabel = 'Weitere Beiträge'; $_tileCount = 6; $_tileCols = 3;
    $_showTileExc = true;  $_showTileCat = true; $_showTileDate = true; $_tileLinkUrl = '/archiv';
}

// ── Posts laden (direkt via Database, kein PostService nötig) ──────────────────
try {
    $db     = \CMS\Database::instance();
    $prefix = $db->getPrefix();

    // Artikel-Liste (über den Info-Cards)
    $featuredRows  = $_showList
        ? ($db->get_results(
            "SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, p.views,
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
    $totalPages  = max(1, (int)ceil($totalPosts / $_tileCount));

    $gridRows  = $_showTileGrid
        ? ($db->get_results(
            "SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at,
                    c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.status = 'published'
             ORDER BY p.published_at DESC
             LIMIT " . (int)$_tileCount . " OFFSET " . (int)(($currentPage - 1) * $_tileCount)
          ) ?: [])
        : [];
    $gridPosts = array_map(fn($r) => (array)$r, $gridRows);

} catch (\Throwable $e) {
    $featuredPosts = [];
    $gridPosts     = [];
    $currentPage   = 1;
    $totalPages    = 1;
}
?>

<div class="container" style="padding-top:28px;padding-bottom:40px;">

    <!-- ── Repo-Card ─────────────────────────────────────────────── -->
    <?php if ($_showRepo && !empty($_repoTitle)): ?>
    <section class="content-section" data-anim>
        <div class="repo-card">
            <div class="repo-card-body">
                <h3><?php echo htmlspecialchars($_repoTitle, ENT_QUOTES); ?></h3>
                <?php if (!empty($_repoDesc)): ?>
                <p><?php echo htmlspecialchars($_repoDesc, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_repoBtnText) && !empty($_repoBtnUrl)): ?>
                <a href="<?php echo htmlspecialchars($_repoBtnUrl, ENT_QUOTES); ?>"
                   class="btn btn-primary btn-sm" target="_blank" rel="noopener noreferrer">
                    <?php echo htmlspecialchars($_repoBtnText, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
            </div>
            <?php if (!empty($_repoBadge)): ?>
            <div class="repo-card-badge">
                <span><?php echo htmlspecialchars($_repoBadge, ENT_QUOTES); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Artikel-Liste ─────────────────────────────────────── -->
    <?php if ($_showList && !empty($featuredPosts)): ?>
    <section class="content-section" data-anim>
        <div class="section-header">
            <span class="section-label">📄 <?php echo htmlspecialchars($_listLabel, ENT_QUOTES); ?></span>
            <?php if (!empty($_listLinkUrl)): ?>
            <a href="<?php echo htmlspecialchars($siteUrl . $_listLinkUrl, ENT_QUOTES); ?>">Alle Beiträge →</a>
            <?php endif; ?>
        </div>

        <div class="article-list" style="border:1px solid var(--border-color);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);">
            <?php foreach ($featuredPosts as $post): ?>
            <article class="article-card">

                <?php if (!empty($post['featured_image'])): ?>
                <div class="article-thumb">
                    <img src="<?php echo htmlspecialchars($post['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
                         width="<?php echo $_listThumbW; ?>" height="<?php echo $_listThumbH; ?>" loading="lazy">
                    <?php if ($_showBadge && !empty($post['category_name'])): ?>
                    <span class="thumb-badge badge-teal"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="article-body">
                    <?php if ($_showMeta): ?>
                    <div class="article-meta">
                        <?php if ($_showMetaCat && !empty($post['category_name'])): ?>
                        <span class="cat"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        <?php if ($_showMetaDate): ?>
                        <span><?php echo htmlspecialchars(date('j. F Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        <?php if ($_showMetaRT && !empty($post['read_time'])): ?>
                        <span class="read"><?php echo (int)$post['read_time']; ?> Min. Lesezeit</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <h4>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                    </h4>
                    <?php if ($_showExcerpt && !empty($post['excerpt'])): ?>
                    <p><?php echo htmlspecialchars(mb_strimwidth($post['excerpt'] ?? '', 0, 160, '…'), ENT_QUOTES); ?></p>
                    <?php endif; ?>
                </div>

            </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Kategorie-Cards (Info-Grid) ───────────────────────── -->
    <?php if ($_showInfoGrid): ?>
    <section class="content-section" data-anim data-anim-delay="1">
        <div class="section-header">
            <span class="section-label gold">📂 Themenbereiche</span>
        </div>
        <div class="info-grid">
            <div class="info-card<?php echo $_c1Style === 'gold' ? ' info-card--gold' : ''; ?>">
                <h3><?php echo htmlspecialchars($_c1Title, ENT_QUOTES); ?></h3>
                <?php if (!empty($_c1Text)): ?>
                <p><?php echo htmlspecialchars($_c1Text, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_c1LinkUrl) && !empty($_c1LinkText)): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . $_c1LinkUrl, ENT_QUOTES); ?>" class="btn btn-outline btn-sm"><?php echo htmlspecialchars($_c1LinkText, ENT_QUOTES); ?></a>
                <?php endif; ?>
            </div>
            <div class="info-card<?php echo $_c2Style === 'gold' ? ' info-card--gold' : ''; ?>">
                <h3><?php echo htmlspecialchars($_c2Title, ENT_QUOTES); ?></h3>
                <?php if (!empty($_c2Text)): ?>
                <p><?php echo htmlspecialchars($_c2Text, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_c2LinkUrl) && !empty($_c2LinkText)): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . $_c2LinkUrl, ENT_QUOTES); ?>" class="btn btn-outline btn-sm"><?php echo htmlspecialchars($_c2LinkText, ENT_QUOTES); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Kachel-Grid ─────────────────────────────────────────── -->
    <?php if ($_showTileGrid && !empty($gridPosts)): ?>
    <section class="content-section" data-anim data-anim-delay="2">
        <div class="section-header">
            <span class="section-label">📰 <?php echo htmlspecialchars($_tileLabel, ENT_QUOTES); ?></span>
            <?php if (!empty($_tileLinkUrl)): ?>
            <a href="<?php echo htmlspecialchars($siteUrl . $_tileLinkUrl, ENT_QUOTES); ?>">Archiv →</a>
            <?php endif; ?>
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
                    <?php if ($_showTileExc && !empty($post['excerpt'])): ?>
                    <p class="post-card-excerpt"><?php echo htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <?php if ($_showTileDate || $_showTileCat): ?>
                    <div class="post-card-meta">
                        <?php if ($_showTileCat && !empty($post['category_name'])): ?>
                        <span class="cat"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        <?php if ($_showTileDate): ?>
                        <span><?php echo htmlspecialchars(date('j. M. Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <!-- Paginierung -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Seitennavigation">
            <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo $currentPage - 1; ?>" class="page-link" aria-label="Vorherige Seite">← Zurück</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 2): ?>
                <a href="?page=<?php echo $p; ?>" class="page-link <?php echo $p === $currentPage ? 'active' : ''; ?>" <?php echo $p === $currentPage ? 'aria-current="page"' : ''; ?>><?php echo $p; ?></a>
                <?php elseif (abs($p - $currentPage) === 3): ?>
                <span class="page-link dots" aria-hidden="true">…</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo $currentPage + 1; ?>" class="page-link" aria-label="Nächste Seite">Weiter →</a>
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
    <section class="content-section" data-anim data-anim-delay="3">
        <div class="section-header">
            <span class="section-label gold">📡 Externe Feeds</span>
        </div>
        <div class="feed-grid">
            <?php foreach ($_feedSections as $_fs): ?>
            <div class="feed-card">
                <h4><?php echo htmlspecialchars($_fs['channel']['name'] ?? '', ENT_QUOTES); ?></h4>
                <ul class="feed-list">
                    <?php foreach ($_fs['items'] as $_fi): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($_fi['link'] ?? '#', ENT_QUOTES); ?>"
                           target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($_fi['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                        <div class="meta"><?php
                            $_ts = !empty($_fi['pub_date']) ? strtotime($_fi['pub_date']) : false;
                            echo htmlspecialchars($_ts ? date('j. M Y', $_ts) : '', ENT_QUOTES);
                        ?></div>
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
