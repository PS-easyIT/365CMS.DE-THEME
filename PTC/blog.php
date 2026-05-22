<?php
/**
 * PTC Theme – Blog-Übersicht
 *
 * Vom Router bereitgestellte Variablen:
 *   $posts        – array of stdObjects (Beiträge der aktuellen Seite)
 *   $total        – int, Gesamtanzahl
 *   $currentPage  – int, aktuelle Seite
 *   $totalPages   – int, Gesamtzahl Seiten
 *   $perPage      – int, Beiträge pro Seite
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* ─── Standardwerte ─── */
$posts       = $posts       ?? [];
$total       = $total       ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages  = $totalPages  ?? 1;
$siteUrl     = ptc_site_url();

/* ─── Customizer-Einstellungen ─── */
$blogLayout      = ptc_customizer_get('blog', 'blog_layout',              'grid');
$blogColumns     = ptc_customizer_get('blog', 'blog_columns',             '3');
$showSidebar     = ptc_customizer_get('blog', 'blog_show_sidebar',        '1');
$showFeatured    = ptc_customizer_get('blog', 'blog_show_featured_image', '1');
$showExcerpt     = ptc_customizer_get('blog', 'blog_show_excerpt',        '1');
$showDate        = ptc_customizer_get('blog', 'blog_show_date',           '1');
$showAuthor      = ptc_customizer_get('blog', 'blog_show_author',         '1');
$showCategory    = ptc_customizer_get('blog', 'blog_show_category',       '1');
$heroTitle       = ptc_customizer_get('blog', 'blog_hero_title',          'Unser Blog');
$heroSubtitle    = ptc_customizer_get('blog', 'blog_hero_subtitle',       'Aktuelle Beiträge, Einblicke und Neuigkeiten');

/* ─── Filter-Parameter ─── */
$activeCategory = htmlspecialchars($_GET['category'] ?? '', ENT_QUOTES, 'UTF-8');
$activeTag      = htmlspecialchars($_GET['tag'] ?? '', ENT_QUOTES, 'UTF-8');

$pageTitle = $heroTitle;
if ($activeCategory) {
    $pageTitle = 'Kategorie: ' . $activeCategory;
} elseif ($activeTag) {
    $pageTitle = 'Tag: ' . $activeTag;
}

/* ─── Sidebar-Daten ─── */
$categories = [];
$tagCloud   = [];
if ($showSidebar) {
    try {
        $db     = \CMS\Database::instance();
        $prefix = $db->getPrefix();

        $catStmt = $db->execute(
            "SELECT c.name, c.slug, COUNT(p.id) AS post_count
             FROM {$prefix}post_categories c
             LEFT JOIN {$prefix}posts p ON p.category_id = c.id AND p.status = 'published'
             GROUP BY c.id
             ORDER BY c.name ASC
             LIMIT 15"
        );
        $categories = $catStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $tagStmt = $db->execute(
            "SELECT tags FROM {$prefix}posts WHERE status = 'published' AND tags IS NOT NULL AND tags != ''"
        );
        $tagRows = $tagStmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        $tagCounts = [];
        foreach ($tagRows as $row) {
            foreach (array_map('trim', explode(',', $row)) as $tag) {
                if ($tag) {
                    $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                }
            }
        }
        arsort($tagCounts);
        $tagCloud = array_keys(array_slice($tagCounts, 0, 20));
    } catch (\Throwable $e) {
        // Tabellen ggf. nicht vorhanden
    }
}

$gridClass = $blogLayout === 'list'
    ? 'ptc-blog-grid ptc-blog-grid--list'
    : 'ptc-blog-grid ptc-blog-grid--cols-' . ((int) $blogColumns);
?>

<!-- ── Blog Hero ── -->
<section class="ptc-blog-hero">
    <div class="ptc-container">
        <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if ($heroSubtitle && !$activeCategory && !$activeTag) : ?>
            <p><?php echo htmlspecialchars($heroSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <div class="ptc-blog-hero__meta">
            <span><?php echo $total; ?> Beiträge</span>
            <?php if ($activeCategory || $activeTag) : ?>
                <span>·</span>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog">Alle anzeigen</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── Blog Content ── -->
<div class="ptc-blog-content">
    <div class="ptc-container">
        <div class="ptc-blog-layout<?php echo $showSidebar ? ' ptc-blog-layout--with-sidebar' : ''; ?>">

            <!-- Posts -->
            <div class="ptc-blog-posts">
                <?php if (!empty($posts)) : ?>
                    <div class="<?php echo $gridClass; ?>">
                        <?php foreach ($posts as $post) :
                            $p         = (array) $post;
                            $pTitle    = htmlspecialchars($p['title'] ?? 'Beitrag', ENT_QUOTES, 'UTF-8');
                            $pSlug     = $p['slug'] ?? '';
                            $pExcerpt  = $p['excerpt'] ?? '';
                            if (mb_strlen($pExcerpt) > 160) {
                                $pExcerpt = mb_substr($pExcerpt, 0, 157) . '…';
                            }
                            $pExcerpt  = htmlspecialchars(strip_tags($pExcerpt), ENT_QUOTES, 'UTF-8');
                            $pDate     = isset($p['published_at']) ? date('d.m.Y', strtotime($p['published_at'])) : '';
                            $pCat      = htmlspecialchars($p['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pCatSlug  = $p['category_slug'] ?? '';
                            $pImage    = $p['featured_image'] ?? '';
                            $pUrl      = htmlspecialchars($siteUrl . '/blog/' . $pSlug, ENT_QUOTES, 'UTF-8');
                            $pAuthor   = htmlspecialchars($p['author_name'] ?? 'Redaktion', ENT_QUOTES, 'UTF-8');
                            $pTags     = array_filter(array_map('trim', explode(',', $p['tags'] ?? '')));
                            $hasImage  = $showFeatured && $pImage;
                        ?>
                        <article class="ptc-blog-card<?php echo $hasImage ? ' ptc-blog-card--has-image' : ''; ?>">
                            <?php if ($hasImage) : ?>
                            <a href="<?php echo $pUrl; ?>" class="ptc-blog-card__image">
                                <img src="<?php echo htmlspecialchars($pImage, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $pTitle; ?>" loading="lazy">
                            </a>
                            <?php endif; ?>
                            <div class="ptc-blog-card__body">
                                <h2 class="ptc-blog-card__title">
                                    <a href="<?php echo $pUrl; ?>"><?php echo $pTitle; ?></a>
                                </h2>
                                <div class="ptc-blog-card__tags-row">
                                    <?php if ($showCategory && $pCat) : ?>
                                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog?category=' . urlencode($pCatSlug), ENT_QUOTES, 'UTF-8'); ?>"
                                           class="ptc-blog-card__category"><?php echo $pCat; ?></a>
                                    <?php endif; ?>
                                    <?php foreach ($pTags as $pTag) :
                                        $tEsc = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog?tag=' . urlencode($pTag), ENT_QUOTES, 'UTF-8'); ?>"
                                           class="ptc-blog-card__tag"><?php echo $tEsc; ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($showExcerpt && $pExcerpt) : ?>
                                    <p class="ptc-blog-card__excerpt"><?php echo $pExcerpt; ?></p>
                                <?php endif; ?>
                                <div class="ptc-blog-card__footer">
                                    <?php if ($showAuthor) : ?>
                                        <span class="ptc-blog-card__author">👤 <?php echo $pAuthor; ?></span>
                                    <?php endif; ?>
                                    <?php if ($showDate && $pDate) : ?>
                                        <span class="ptc-blog-card__date">📅 <?php echo $pDate; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Paginierung -->
                    <?php if ($totalPages > 1) : ?>
                    <nav class="ptc-blog-pagination" aria-label="Seitennavigation">
                        <?php
                        $baseUrl = $siteUrl . '/blog';
                        $qParts  = [];
                        if ($activeCategory) $qParts[] = 'category=' . urlencode($activeCategory);
                        if ($activeTag)      $qParts[] = 'tag=' . urlencode($activeTag);
                        $qBase = $qParts ? '?' . implode('&', $qParts) . '&' : '?';
                        ?>
                        <?php if ($currentPage > 1) : ?>
                        <a href="<?php echo htmlspecialchars($baseUrl . $qBase . 'p=' . ($currentPage - 1), ENT_QUOTES, 'UTF-8'); ?>"
                           class="ptc-blog-pagination__btn" aria-label="Vorherige Seite">← Zurück</a>
                        <?php endif; ?>
                        <span class="ptc-blog-pagination__info">Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?></span>
                        <?php if ($currentPage < $totalPages) : ?>
                        <a href="<?php echo htmlspecialchars($baseUrl . $qBase . 'p=' . ($currentPage + 1), ENT_QUOTES, 'UTF-8'); ?>"
                           class="ptc-blog-pagination__btn" aria-label="Nächste Seite">Weiter →</a>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>

                <?php else : ?>
                    <!-- Empty State -->
                    <div class="ptc-blog-empty">
                        <p class="ptc-empty-icon" aria-hidden="true">📭</p>
                        <p><strong>Keine Artikel gefunden</strong></p>
                        <?php if ($activeCategory || $activeTag) : ?>
                            <p>Für diese Auswahl gibt es noch keine Beiträge.</p>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog"
                               class="btn-ptc btn-ptc-primary ptc-section-cta--spaced">Alle Artikel anzeigen</a>
                        <?php else : ?>
                            <p>Die ersten Artikel erscheinen hier, sobald sie veröffentlicht werden.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <?php if ($showSidebar) : ?>
            <aside class="ptc-blog-sidebar" aria-label="Blog Sidebar">

                <!-- Suche -->
                <div class="ptc-blog-sidebar__widget">
                    <h3>🔍 Suche</h3>
                    <form action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/search" method="GET" role="search">
                        <div class="ptc-blog-sidebar__search">
                            <input type="search" name="q" placeholder="Artikel suchen …" autocomplete="off"
                                   value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" aria-label="Suchen">🔍</button>
                        </div>
                    </form>
                </div>

                <!-- Kategorien -->
                <?php if (!empty($categories)) : ?>
                <div class="ptc-blog-sidebar__widget">
                    <h3>📂 Kategorien</h3>
                    <ul class="ptc-blog-sidebar__list">
                        <?php foreach ($categories as $cat) :
                            $cName  = htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $cSlug  = htmlspecialchars($cat['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $cCount = (int)($cat['post_count'] ?? 0);
                            $isActive = ($activeCategory === $cSlug);
                        ?>
                        <li<?php echo $isActive ? ' class="active"' : ''; ?>>
                            <a href="<?php echo htmlspecialchars($siteUrl . '/blog?category=' . urlencode($cSlug), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo $cName; ?>
                                <span class="ptc-blog-sidebar__count"><?php echo $cCount; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if (!empty($tagCloud)) : ?>
                <div class="ptc-blog-sidebar__widget">
                    <h3>🏷️ Tags</h3>
                    <div class="ptc-blog-sidebar__tags">
                        <?php foreach ($tagCloud as $tag) :
                            $tEsc    = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');
                            $isActive = ($activeTag === $tag);
                        ?>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog?tag=' . urlencode($tag), ENT_QUOTES, 'UTF-8'); ?>"
                           class="ptc-tag<?php echo $isActive ? ' ptc-tag--active' : ''; ?>"><?php echo $tEsc; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </aside>
            <?php endif; ?>

        </div>
    </div>
</div>
