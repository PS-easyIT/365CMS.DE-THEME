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

// Suche + Pagination
$_bQuery  = trim($_GET['q'] ?? '');
$_blogPage = max(1, (int)($_GET['page'] ?? $_GET['p'] ?? (isset($currentPage) ? (int)$currentPage : 1)));
$_blogPer  = isset($perPage) && (int)$perPage > 0 ? (int)$perPage : 10;

// Eigene DB-Abfrage mit optionaler Volltextsuche (Prepared Statements)
try {
    $_bDb   = \CMS\Database::instance();
    $_bPfx  = $_bDb->getPrefix();
    $_bPdo  = $_bDb->getPdo();

    $_bWhere = "p.status = 'published'";
    $_bBind  = [];
    if ($_bQuery !== '') {
        $_bWhere .= " AND (p.title LIKE ? OR p.excerpt LIKE ?)";
        $_bLike   = '%' . $_bQuery . '%';
        $_bBind   = [$_bLike, $_bLike];
    }

    $_stmtCnt = $_bPdo->prepare("SELECT COUNT(*) FROM {$_bPfx}posts p WHERE {$_bWhere}");
    $_stmtCnt->execute($_bBind);
    $_bTotal   = (int)$_stmtCnt->fetchColumn();

    $_bPages   = max(1, (int)ceil($_bTotal / $_blogPer));
    $_blogPage = min($_blogPage, max(1, $_bPages));
    $_bOffset  = ($_blogPage - 1) * $_blogPer;

    $_stmtRows = $_bPdo->prepare(
        "SELECT p.id, p.title, p.slug, p.excerpt, p.content,
                p.featured_image, p.published_at, p.views,
                c.name AS category_name
         FROM {$_bPfx}posts p
         LEFT JOIN {$_bPfx}post_categories c ON c.id = p.category_id
         WHERE {$_bWhere}
         ORDER BY p.published_at DESC
         LIMIT ? OFFSET ?"
    );
    $_stmtRows->execute(array_merge($_bBind, [$_blogPer, $_bOffset]));
    $_bPosts = $_stmtRows->fetchAll(\PDO::FETCH_ASSOC) ?: [];
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
    $_bExcLen      = max(10, (int)$_bc->get('typography', 'article_excerpt_length', 180));
} catch (\Throwable $_bE) {
    $_bShowExcerpt = true;  $_bShowMeta = true;
    $_bThumbW      = 190;   $_bThumbH   = 115;
    $_bExcLen      = 180;
}
?>

<div class="container blog-shell">

    <!-- Archiv-Navigation: Startseite + Suchfeld -->
    <div class="blog-archive-bar">
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/"
           class="blog-archive-back">&#8592; Startseite</a>
        <form class="blog-search-form" method="GET"
              action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog">
            <input type="search" name="q"
                   placeholder="Beiträge durchsuchen&hellip;"
                   value="<?php echo htmlspecialchars($_bQuery, ENT_QUOTES); ?>">
            <button type="submit" aria-label="Suchen">&#128269;</button>
        </form>
    </div>

    <?php if ($_bQuery !== ''): ?>
    <p class="blog-search-hint">
        Suchergebnisse für <strong>&bdquo;<?php echo htmlspecialchars($_bQuery, ENT_QUOTES); ?>&ldquo;</strong>
        &mdash; <?php echo $_bTotal; ?> Treffer
        &nbsp;<a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog">&#10005; Zurücksetzen</a>
    </p>
    <?php endif; ?>

    <?php if (!empty($_bPosts)): ?>

    <div class="article-list article-list--framed" data-anim>
        <?php foreach ($_bPosts as $_bP):
            get_theme_part('partials/post-card', [
                'card'         => $_bP,
                'siteUrl'      => $siteUrl,
                'show_excerpt' => $_bShowExcerpt,
                'show_meta'    => $_bShowMeta,
                'exc_len'      => $_bExcLen,
            ]);
        endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($_bPages > 1): ?>
    <nav class="pagination pagination--spaced" aria-label="Archiv-Seitennavigation">
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
    <div class="empty-state" data-anim>
        <p class="empty-state__icon">📭</p>
        <p><strong>Keine Beiträge vorhanden</strong></p>
        <p class="empty-state__text">Bald gibt es hier spannende Artikel.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary empty-state__action">← Zur Startseite</a>
    </div>
    <?php endif; ?>

</div><!-- /.container -->
