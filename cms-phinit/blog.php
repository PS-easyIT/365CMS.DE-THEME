<?php
/**
 * Blog-Archiv Template – CMS Phinit Theme
 *
 * Zeigt alle Artikel als Listcards (wie Artikel-Liste auf Homepage).
 * Akzeptiert ?page= (von index.php Pagination) und ?p= (Router-Standard).
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

// Pagination: ?page= (von index.php verlinkt) hat Vorrang vor ?p= und Router-Wert
$_blogPage = max(1, (int)($_GET['page'] ?? $_GET['p'] ?? (isset($currentPage) ? (int)$currentPage : 1)));
$_blogPer  = isset($perPage) && (int)$perPage > 0 ? (int)$perPage : 10;

// Eigene DB-Abfrage – stellt korrekte Daten für aktuelle Seite sicher
try {
    $_bDb   = \CMS\Database::instance();
    $_bPfx  = $_bDb->getPrefix();

    $_bTotal = (int)($_bDb->get_var("SELECT COUNT(*) FROM {$_bPfx}posts WHERE status = 'published'") ?: 0);
    $_bPages = max(1, (int)ceil($_bTotal / $_blogPer));
    $_blogPage = min($_blogPage, $_bPages);
    $_bOffset  = ($_blogPage - 1) * $_blogPer;

    $_bRows = $_bDb->get_results(
        "SELECT p.id, p.title, p.slug, p.excerpt, LEFT(p.content, 500) AS content,
                p.featured_image, p.published_at, p.views,
                c.name AS category_name
         FROM {$_bPfx}posts p
         LEFT JOIN {$_bPfx}post_categories c ON c.id = p.category_id
         WHERE p.status = 'published'
         ORDER BY p.published_at DESC
         LIMIT {$_blogPer} OFFSET {$_bOffset}"
    ) ?: [];
    $_bPosts = array_map(fn($r) => (array)$r, $_bRows);
} catch (\Throwable $_bE) {
    $_bPosts = [];
    $_bTotal = 0;
    $_bPages = 1;
}

// Customizer-Einstellungen
try {
    $_bc  = \CMS\Services\ThemeCustomizer::instance();
    $_bShowExcerpt = filter_var($_bc->get('homepage', 'show_article_excerpt', true), FILTER_VALIDATE_BOOLEAN);
    $_bShowMeta    = filter_var($_bc->get('homepage', 'show_article_meta', true), FILTER_VALIDATE_BOOLEAN);
    $_bThumbW      = max(80, (int)$_bc->get('homepage', 'article_thumb_width', 190));
    $_bThumbH      = max(60, (int)$_bc->get('homepage', 'article_thumb_height', 115));
    $_bExcLen      = max(60, (int)$_bc->get('typography', 'article_excerpt_length', 180));
} catch (\Throwable $_bE) {
    $_bShowExcerpt = true;  $_bShowMeta = true;
    $_bThumbW      = 190;   $_bThumbH   = 115;
    $_bExcLen      = 180;
}
?>

<div class="container" style="padding-top:28px;padding-bottom:0;">
    <?php if (!empty($_bPosts)): ?>

    <div class="article-list" style="border:1px solid var(--border-color);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);" data-anim>
        <?php foreach ($_bPosts as $_bP):
            $_bExc = $_bP['excerpt'] ?? '';
            if (empty(trim($_bExc)) && !empty($_bP['content'])) {
                $_bExc = mb_strimwidth(strip_tags($_bP['content']), 0, $_bExcLen, '…');
            }
        ?>
        <article class="article-card">

            <div class="article-thumb">
                <?php if (!empty($_bP['featured_image'])): ?>
                <img src="<?php echo htmlspecialchars($_bP['featured_image'], ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars($_bP['title'] ?? '', ENT_QUOTES); ?>"
                     width="<?php echo $_bThumbW; ?>" height="<?php echo $_bThumbH; ?>" loading="lazy">
                <?php else: ?>
                <div class="article-thumb-placeholder" aria-hidden="true"><span>📄</span></div>
                <?php endif; ?>
                <?php if (!empty($_bP['category_name'])): ?>
                <span class="thumb-badge badge-teal"><?php echo htmlspecialchars($_bP['category_name'], ENT_QUOTES); ?></span>
                <?php endif; ?>
            </div>

            <div class="article-body">
                <h4>
                    <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($_bP['slug'] ?? ''), ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($_bP['title'] ?? '', ENT_QUOTES); ?>
                    </a>
                </h4>
                <?php if ($_bShowExcerpt && !empty(trim($_bExc))): ?>
                <p><?php echo htmlspecialchars(mb_strimwidth($_bExc, 0, $_bExcLen, '…'), ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if ($_bShowMeta): ?>
                <?php
                    $_bRT = 0;
                    if (!empty($_bP['content'])) {
                        $_bRT = max(1, (int)round(str_word_count(strip_tags($_bP['content'] ?? '')) / 200));
                    }
                ?>
                <div class="article-meta">
                    <?php if (!empty($_bP['category_name'])): ?>
                    <span class="cat"><?php echo htmlspecialchars($_bP['category_name'], ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($_bP['published_at'])): ?>
                    <span><?php echo htmlspecialchars(date('j. F Y', strtotime($_bP['published_at'])), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <?php if ($_bRT > 0): ?>
                    <span class="read"><?php echo $_bRT; ?> Min.</span>
                    <?php endif; ?>
                    <a class="article-meta__more"
                       href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($_bP['slug'] ?? ''), ENT_QUOTES); ?>">
                        &hellip; Weiter lesen &rarr;
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($_bPages > 1): ?>
    <nav class="pagination" aria-label="Archiv-Seitennavigation" style="margin-top:24px;">
        <?php if ($_blogPage > 1): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $_blogPage - 1; ?>"
           class="page-link" aria-label="Vorherige Seite">← Zurück</a>
        <?php endif; ?>

        <?php for ($_pg = 1; $_pg <= $_bPages; $_pg++): ?>
            <?php if ($_pg === 1 || $_pg === $_bPages || abs($_pg - $_blogPage) <= 2): ?>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $_pg; ?>"
               class="page-link <?php echo $_pg === $_blogPage ? 'active' : ''; ?>"
               <?php echo $_pg === $_blogPage ? 'aria-current="page"' : ''; ?>>
                <?php echo $_pg; ?>
            </a>
            <?php elseif (abs($_pg - $_blogPage) === 3): ?>
            <span class="page-link dots" aria-hidden="true">…</span>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($_blogPage < $_bPages): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $_blogPage + 1; ?>"
           class="page-link" aria-label="Nächste Seite">Weiter →</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php else: ?>
    <div class="empty-state" style="text-align:center;padding:4rem 2rem;" data-anim>
        <p style="font-size:3rem;margin:0 0 1rem;">📭</p>
        <p><strong>Keine Beiträge vorhanden</strong></p>
        <p style="color:var(--text-muted);max-width:400px;margin:.5rem auto 1.5rem;">Bald gibt es hier spannende Artikel.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary" style="margin-top:1rem;">← Zur Startseite</a>
    </div>
    <?php endif; ?>

</div><!-- /.container -->
