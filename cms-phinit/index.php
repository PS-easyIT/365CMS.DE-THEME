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

require_once __DIR__ . '/includes/theme-home-helpers.php';

$siteUrl      = SITE_URL;

// ── Customizer-Einstellungen laden ──────────────────────────────────────────
$homepageViewModel = phinit_get_homepage_view_model();
extract($homepageViewModel, EXTR_SKIP);

// ── Startseiten-Daten laden (Posts, Grid-Paginierung, Sidebar-Featured) ──────
extract(phinit_get_homepage_posts_payload($homepageViewModel), EXTR_SKIP);
?>
<style>
    .home-shell {
        --home-sp-repo: <?php echo (int)$_spRepo; ?>px;
        --home-sp-list: <?php echo (int)$_spList; ?>px;
        --home-sp-info: <?php echo (int)$_spInfo; ?>px;
        --home-sp-grid: <?php echo (int)$_spGrid; ?>px;
        --home-sp-rss: <?php echo (int)$_spRss; ?>px;
        --hp-sidebar-w: <?php echo (int)$_listSidebarWidth; ?>px;
        --hp-tile-thumb-h: <?php echo (int)$_tileImageH; ?>px;
        --home-sp-top: <?php echo (int)$_homeHeaderSpacing; ?>px;
    }
    <?php if (!empty($_sbProj1LogoUrl)): ?>
    .sb-project-card--project1 { background-image: url('<?php echo htmlspecialchars($_sbProj1LogoUrl, ENT_QUOTES); ?>'); }
    <?php endif; ?>
    <?php if (!empty($_sbProj2LogoUrl)): ?>
    .sb-project-card--project2 { background-image: url('<?php echo htmlspecialchars($_sbProj2LogoUrl, ENT_QUOTES); ?>'); }
    <?php endif; ?>
</style>
<?php \CMS\Hooks::doAction('home_content'); ?>
<div class="container home-shell">

    <!-- ── Repo-Card ─────────────────────────────────────────────── -->
    <?php if ($_showRepo && !empty($_repoTitle)): ?>
    <section class="content-section home-section home-section--repo" data-anim>
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
    <?php get_theme_part('partials/home-article-list', array_merge($homepageViewModel, [
        'featuredPosts' => $featuredPosts,
        'sbFeaturedPosts' => $sbFeaturedPosts,
        'siteUrl' => $siteUrl,
    ])); ?>

    <!-- ── Kategorie-Cards (Info-Grid) ───────────────────────── -->
    <?php if ($_showInfoGrid): ?>
    <section class="content-section home-section home-section--info" data-anim data-anim-delay="1">
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
                <span class="repo-badge repo-badge--inline"><?php echo htmlspecialchars($_c3Badge, ENT_QUOTES); ?></span>
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
    <section class="content-section home-section home-section--grid" data-anim data-anim-delay="2">
        <div class="section-header">
            <span class="section-label">📰 <?php echo htmlspecialchars($_tileLabel, ENT_QUOTES); ?></span>
        </div>

        <div class="posts-grid posts-grid--cols-<?php echo $_tileCols; ?>">
            <?php foreach ($gridPosts as $i => $post): ?>
            <article class="post-card" data-anim data-anim-delay="<?php echo min($i + 1, 4); ?>">

                <?php if (!empty($post['featured_image'])): ?>
                <div class="post-card-thumb">
                    <?php if ($_showTileCat && !empty($post['category_name'])): ?>
                    <span class="post-card-badge"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($post['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
                         loading="lazy">
                </div>
                <?php else: ?>
                <div class="post-card-thumb post-card-thumb--placeholder">
                    <?php if ($_showTileCat && !empty($post['category_name'])): ?>
                    <span class="post-card-badge"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                    <?php endif; ?>
                    <span class="post-card-thumb__icon">📄</span>
                </div>
                <?php endif; ?>

                <div class="post-card-body">
                    <h3 class="post-card-title">
                        <a href="<?php echo htmlspecialchars((string) ($post['permalink'] ?? ($siteUrl . '/blog/' . ($post['slug'] ?? ''))), ENT_QUOTES); ?>">
                            <?php echo phinit_escape_text($post['title'] ?? ''); ?>
                        </a>
                    </h3>
                    <?php
                    $_tileExc = function_exists('phinit_excerpt_plain_text')
                        ? phinit_excerpt_plain_text((string)($post['excerpt'] ?? ''))
                        : strip_tags((string)($post['excerpt'] ?? ''));
                    if (empty(trim($_tileExc)) && !empty($post['content'])) {
                        $_tileExc = function_exists('phinit_excerpt_plain_text')
                            ? phinit_excerpt_plain_text((string)$post['content'])
                            : strip_tags((string)$post['content']);
                    }
                    if ($_showTileExc && !empty(trim($_tileExc))): ?>
                    <p class="post-card-excerpt"><?php echo htmlspecialchars(mb_strimwidth($_tileExc, 0, $_tileExcLen, '…'), ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <div class="post-card-meta">
                        <div class="post-card-meta__left">
                        <?php if ($_showTileCat && !empty($post['category_name'])): ?>
                        <span class="cat"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                        <?php endif; ?>
                        <?php if ($_showTileDate): ?>
                        <?php $postDateRaw = $post['published_at'] ?? ($post['created_at'] ?? ''); ?>
                        <span><?php echo htmlspecialchars(!empty($postDateRaw) ? date('j. M. Y', strtotime((string)$postDateRaw)) : '', ENT_QUOTES); ?></span>
                        <?php endif; ?>
                        </div>
                        <a class="post-card-meta__more"
                                    href="<?php echo htmlspecialchars((string) ($post['permalink'] ?? ($siteUrl . '/blog/' . ($post['slug'] ?? ''))), ENT_QUOTES); ?>">
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
    <section class="content-section home-section home-section--rss" data-anim data-anim-delay="3">
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
                    <li class="feed-list__empty">Keine Einträge verfügbar.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div><!-- /.container -->
