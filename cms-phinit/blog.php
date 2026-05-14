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
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';

// Suche + Pagination
$_bQuery  = phinit_input_string($_GET, 'q', '', 200);
$_blogDefaultPage = isset($currentPage) ? (int) $currentPage : 1;
$_blogPage = phinit_input_int($_GET, 'page', phinit_input_int($_GET, 'p', $_blogDefaultPage, 1), 1);
$_blogPer  = isset($perPage) && (int)$perPage > 0 ? (int)$perPage : 10;

// Eigene DB-Abfrage mit optionaler Volltextsuche (Prepared Statements)
try {
    $_bDb   = \CMS\Database::instance();
    $_bPfx  = $_bDb->getPrefix();
    $_bPdo  = $_bDb->getPdo();
    $_bLocaleCondition = function_exists('phinit_build_homepage_post_locale_condition')
        ? phinit_build_homepage_post_locale_condition($currentLocale)
        : '';

    $_bWhere = phinit_post_publication_where('p') . "{$_bLocaleCondition}";
    $_bBind  = [];
    if ($_bQuery !== '') {
        if ($currentLocale === 'en') {
            $_bWhere .= " AND (COALESCE(NULLIF(p.title_en, ''), p.title) LIKE ? OR COALESCE(NULLIF(p.excerpt_en, ''), p.excerpt) LIKE ?)";
        } else {
            $_bWhere .= " AND (p.title LIKE ? OR p.excerpt LIKE ?)";
        }
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
        "SELECT p.*, 
                c.name AS category_name,
                c.slug AS category_slug
         FROM {$_bPfx}posts p
         LEFT JOIN {$_bPfx}post_categories c ON c.id = p.category_id
         WHERE {$_bWhere}
            ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
         LIMIT ? OFFSET ?"
    );
    $_stmtRows->execute(array_merge($_bBind, [$_blogPer, $_bOffset]));
    $_bPosts = $_stmtRows->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    if (function_exists('phinit_prepare_homepage_posts')) {
        $_bPosts = phinit_prepare_homepage_posts($_bPosts, $currentLocale);
    }
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
    <?php
    get_theme_part('partials/blog-archive-toolbar', [
        'siteUrl' => $siteUrl,
        'blogQuery' => $_bQuery,
        'blogTotal' => $_bTotal,
        'blogBaseUrl' => function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog',
    ]);
    ?>

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

    <?php
    get_theme_part('partials/blog-archive-pagination', [
        'siteUrl' => $siteUrl,
        'blogPage' => $_blogPage,
        'blogPages' => $_bPages,
        'blogBaseUrl' => function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog',
        'queryParams' => $_bQuery !== '' ? ['q' => $_bQuery] : [],
    ]);
    ?>

    <?php else: ?>
    <div class="empty-state" data-anim>
        <p class="empty-state__icon">📭</p>
        <p><strong>Keine Beiträge vorhanden</strong></p>
        <p class="empty-state__text">Bald gibt es hier spannende Artikel.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary empty-state__action">← Zur Startseite</a>
    </div>
    <?php endif; ?>

</div><!-- /.container -->
