<?php
/**
 * Homepage Template – CMS Phinit Theme
 *
 * Layout:
 *  1. Featured Artikel (3 horizontal, großes Bild)
 *  2. Info-Cards 2-spaltig (Kategorien-Highlight)
 *  3. 3-spaltige Post-Grid
 *  4. RSS-Feed-Sektion 2-spaltig
 *  5. Pagination
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$themeManager = \CMS\ThemeManager::instance();
$siteUrl      = SITE_URL;

// Posts laden
try {
    $postService = \CMS\Services\PostService::instance();

    // Featured: neueste 3 Posts
    $featuredPosts = $postService->getPosts(['limit' => 3, 'status' => 'published', 'orderby' => 'date', 'order' => 'DESC']);

    // Paginierung
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $perPage     = 9;
    $totalPosts  = $postService->countPosts(['status' => 'published']);
    $totalPages  = (int)ceil($totalPosts / $perPage);
    $gridPosts   = $postService->getPosts(['limit' => $perPage, 'offset' => ($currentPage - 1) * $perPage, 'status' => 'published']);
} catch (\Throwable $e) {
    $featuredPosts = [];
    $gridPosts     = [];
    $currentPage   = 1;
    $totalPages    = 1;
}
?>

<div class="container" style="padding-top:28px;padding-bottom:40px;">

    <!-- ── Featured Artikel ─────────────────────────────────────── -->
    <?php if (!empty($featuredPosts)): ?>
    <section class="content-section" data-anim>
        <div class="section-header">
            <span class="section-label">📄 Aktuelle Beiträge</span>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog">Alle Beiträge →</a>
        </div>

        <div class="article-list" style="border:1px solid var(--border-color);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);">
            <?php foreach ($featuredPosts as $post): ?>
            <article class="article-card">

                <?php if (!empty($post['thumbnail'])): ?>
                <div class="article-thumb">
                    <img src="<?php echo htmlspecialchars($post['thumbnail'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
                         width="200" loading="lazy">
                    <?php if (!empty($post['category'])): ?>
                    <span class="thumb-badge badge-teal"><?php echo htmlspecialchars($post['category'], ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="article-body">
                    <div class="article-meta">
                        <span class="cat"><?php echo htmlspecialchars($post['category'] ?? 'Allgemein', ENT_QUOTES); ?></span>
                        <span><?php echo htmlspecialchars(date('j. F Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?></span>
                        <?php if (!empty($post['read_time'])): ?>
                        <span class="read"><?php echo (int)$post['read_time']; ?> Min. Lesezeit</span>
                        <?php endif; ?>
                    </div>
                    <h4>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                    </h4>
                    <?php if (!empty($post['excerpt'])): ?>
                    <p><?php echo htmlspecialchars(mb_strimwidth($post['excerpt'] ?? '', 0, 160, '…'), ENT_QUOTES); ?></p>
                    <?php endif; ?>
                </div>

            </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Kategorien-Highlight 2-spaltig ───────────────────────── -->
    <section class="content-section" data-anim data-anim-delay="1">
        <div class="section-header">
            <span class="section-label gold">📂 Themenbereiche</span>
        </div>
        <div class="info-grid">
            <div class="info-card">
                <h3><span class="icon">🔵</span> Microsoft 365 Anleitungen</h3>
                <p>Schritt-für-Schritt-Tutorials für Exchange, Teams, SharePoint und alle Microsoft 365 Dienste. Von den Grundlagen bis zu erweiterten Admin-Aufgaben.</p>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/microsoft-365" class="btn btn-outline btn-sm">Mehr erfahren →</a>
            </div>
            <div class="info-card">
                <h3><span class="icon">🟠</span> Microsoft 365 Datenschutz</h3>
                <p>DSGVO-konforme Konfiguration, Compliance-Einstellungen und Datenschutz-Best Practices für Microsoft 365 Umgebungen inklusive Checklisten und Vorlagen.</p>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutz" class="btn btn-outline btn-sm">Mehr erfahren →</a>
            </div>
        </div>
    </section>

    <!-- ── 3-spaltige Post-Grid ─────────────────────────────────── -->
    <?php if (!empty($gridPosts)): ?>
    <section class="content-section" data-anim data-anim-delay="2">
        <div class="section-header">
            <span class="section-label">📰 Weitere Beiträge</span>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/archiv">Archiv →</a>
        </div>

        <div class="posts-grid">
            <?php foreach ($gridPosts as $i => $post): ?>
            <article class="post-card" data-anim data-anim-delay="<?php echo min($i + 1, 4); ?>">

                <?php if (!empty($post['thumbnail'])): ?>
                <div class="post-card-thumb">
                    <img src="<?php echo htmlspecialchars($post['thumbnail'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
                         loading="lazy">
                </div>
                <?php else: ?>
                <div class="post-card-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--bg-tertiary);min-height:120px;">
                    <span style="font-size:2rem;opacity:.4;">📄</span>
                </div>
                <?php endif; ?>

                <div class="post-card-body">
                    <?php if (!empty($post['category'])): ?>
                    <span class="badge badge-neutral" style="align-self:flex-start;"><?php echo htmlspecialchars($post['category'], ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <h3 class="post-card-title">
                        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($post['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
                        </a>
                    </h3>
                    <?php if (!empty($post['excerpt'])): ?>
                    <p class="post-card-excerpt"><?php echo htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <div class="post-card-meta">
                        <span class="cat"><?php echo htmlspecialchars($post['category'] ?? '', ENT_QUOTES); ?></span>
                        <span><?php echo htmlspecialchars(date('j. M. Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?></span>
                    </div>
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
