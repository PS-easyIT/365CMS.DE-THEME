<?php
/**
 * Blog-Liste Template – 365Network Dark Navy Dashboard
 *
 * Vom Router bereitgestellte Variablen:
 *   $posts        – array of stdObjects (Beiträge der aktuellen Seite)
 *   $total        – int, Gesamtanzahl
 *   $currentPage  – int, aktuelle Seite
 *   $totalPages   – int, Gesamtzahl Seiten
 *   $perPage      – int, Beiträge pro Seite
 *
 * @package IT_Expert_Network_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Standardwerte
$posts       = $posts       ?? [];
$total       = $total       ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages  = $totalPages  ?? 1;

$siteUrl = SITE_URL;

// Filter-Parameter
$activeCategory = htmlspecialchars($_GET['category'] ?? '', ENT_QUOTES, 'UTF-8');
$activeTag      = htmlspecialchars($_GET['tag'] ?? '', ENT_QUOTES, 'UTF-8');

$pageTitle = 'Blog';
if ($activeCategory) {
    $pageTitle = 'Kategorie: ' . $activeCategory;
} elseif ($activeTag) {
    $pageTitle = 'Tag: ' . $activeTag;
}

// Kategorien + Tags für Sidebar
$categories = [];
$tagCloud   = [];
try {
    $db = \CMS\Database::instance();
    $prefix = $db->getPrefix();

    // Kategorien mit Post-Zähler
    $catStmt = $db->execute(
        "SELECT c.name, c.slug, COUNT(p.id) AS post_count
         FROM {$prefix}post_categories c
         LEFT JOIN {$prefix}posts p ON p.category_id = c.id AND p.status = 'published'
         GROUP BY c.id
         ORDER BY c.name ASC
         LIMIT 15"
    );
    $categories = $catStmt->fetchAll() ?: [];

    // Tags
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
    // Tabelle(n) ggf. nicht vorhanden
}
?>

<main id="content" class="blog-page">
    <div class="blog-container">

        <!-- Page Header -->
        <div class="blog-page-header">
            <h1>📰 <?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="blog-page-desc">
                <?php echo $total; ?> Beiträge
                <?php if ($activeCategory || $activeTag) : ?>
                    · <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog" style="color:var(--accent-color);text-decoration:none;">Alle anzeigen</a>
                <?php endif; ?>
            </p>
        </div>

        <div class="blog-layout">

            <!-- Post List -->
            <div class="blog-posts">
                <?php if (!empty($posts)) : ?>
                    <div class="blog-grid">
                        <?php foreach ($posts as $post) :
                            $pArr     = (array) $post;
                            $pTitle   = htmlspecialchars($pArr['title'] ?? 'Beitrag', ENT_QUOTES, 'UTF-8');
                            $pSlug    = $pArr['slug'] ?? '';
                            $pExcerpt = $pArr['excerpt'] ?? '';
                            if (mb_strlen($pExcerpt) > 160) {
                                $pExcerpt = mb_substr($pExcerpt, 0, 157) . '…';
                            }
                            $pExcerpt = htmlspecialchars(strip_tags($pExcerpt), ENT_QUOTES, 'UTF-8');
                            $pDate    = isset($pArr['published_at']) ? time_ago($pArr['published_at']) : '';
                            $pCat     = htmlspecialchars($pArr['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pImage   = $pArr['featured_image'] ?? '';
                            $pUrl     = htmlspecialchars($siteUrl . '/blog/' . $pSlug, ENT_QUOTES, 'UTF-8');
                            $pAuthor  = htmlspecialchars($pArr['author_name'] ?? 'Redaktion', ENT_QUOTES, 'UTF-8');
                        ?>
                        <article class="blog-card">
                            <?php if ($pImage) : ?>
                            <a href="<?php echo $pUrl; ?>" class="blog-card__image">
                                <img src="<?php echo htmlspecialchars($pImage, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $pTitle; ?>" loading="lazy">
                            </a>
                            <?php endif; ?>
                            <div class="blog-card__body">
                                <?php if ($pCat) : ?>
                                    <span class="blog-card__category"><?php echo $pCat; ?></span>
                                <?php endif; ?>
                                <h2 class="blog-card__title">
                                    <a href="<?php echo $pUrl; ?>"><?php echo $pTitle; ?></a>
                                </h2>
                                <?php if ($pExcerpt) : ?>
                                    <p class="blog-card__excerpt"><?php echo $pExcerpt; ?></p>
                                <?php endif; ?>
                                <div class="blog-card__meta">
                                    <span>👤 <?php echo $pAuthor; ?></span>
                                    <?php if ($pDate) : ?>
                                        <span>📅 <?php echo $pDate; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Paginierung -->
                    <?php if ($totalPages > 1) : ?>
                    <nav class="blog-pagination" aria-label="Seitennavigation">
                        <?php
                        $baseUrl = $siteUrl . '/blog';
                        $qParts  = [];
                        if ($activeCategory) $qParts[] = 'category=' . urlencode($activeCategory);
                        if ($activeTag)      $qParts[] = 'tag=' . urlencode($activeTag);
                        $qBase = $qParts ? '?' . implode('&', $qParts) . '&' : '?';
                        ?>

                        <?php if ($currentPage > 1) : ?>
                        <a href="<?php echo $baseUrl . $qBase . 'p=' . ($currentPage - 1); ?>" class="blog-pagination__btn" aria-label="Vorherige Seite">← Zurück</a>
                        <?php endif; ?>

                        <span class="blog-pagination__info">Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?></span>

                        <?php if ($currentPage < $totalPages) : ?>
                        <a href="<?php echo $baseUrl . $qBase . 'p=' . ($currentPage + 1); ?>" class="blog-pagination__btn" aria-label="Nächste Seite">Weiter →</a>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>

                <?php else : ?>
                    <!-- Empty State -->
                    <div class="blog-empty">
                        <p style="font-size:2.5rem;margin:0;">📭</p>
                        <p><strong>Keine Artikel gefunden</strong></p>
                        <?php if ($activeCategory || $activeTag) : ?>
                            <p style="color:var(--muted-color);">Für diese Auswahl gibt es noch keine Beiträge.</p>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog" class="btn-accent" style="margin-top:1rem;">Alle Artikel anzeigen</a>
                        <?php else : ?>
                            <p style="color:var(--muted-color);">Die ersten Artikel erscheinen hier, sobald sie veröffentlicht werden.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="blog-sidebar" aria-label="Blog Sidebar">

                <!-- Suche -->
                <div class="blog-sidebar__widget">
                    <h3>🔍 Suche</h3>
                    <form action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/search" method="GET" role="search">
                        <div style="display:flex;gap:.5rem;">
                            <input type="search" name="q" placeholder="Artikel suchen …" autocomplete="off"
                                   value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   style="flex:1;padding:.5rem .75rem;border:1px solid var(--border-color);border-radius:var(--border-radius);background:var(--bg-secondary);color:var(--text-primary);font-size:.85rem;">
                            <button type="submit" aria-label="Suchen" style="padding:.5rem .75rem;background:var(--accent-color);color:#fff;border:none;border-radius:var(--border-radius);cursor:pointer;">🔍</button>
                        </div>
                    </form>
                </div>

                <!-- Kategorien -->
                <?php if (!empty($categories)) : ?>
                <div class="blog-sidebar__widget">
                    <h3>📂 Kategorien</h3>
                    <ul class="blog-sidebar__cat-list">
                        <?php foreach ($categories as $cat) :
                            $cArr = (array) $cat;
                            $cName  = htmlspecialchars($cArr['name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $cSlug  = urlencode($cArr['slug'] ?? '');
                            $cCount = (int)($cArr['post_count'] ?? 0);
                            $isActive = ($activeCategory === ($cArr['slug'] ?? ''));
                        ?>
                        <li<?php echo $isActive ? ' class="active"' : ''; ?>>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog?category=<?php echo $cSlug; ?>">
                                <?php echo $cName; ?>
                            </a>
                            <?php if ($cCount > 0) : ?>
                                <span class="cat-count"><?php echo $cCount; ?></span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if (!empty($tagCloud)) : ?>
                <div class="blog-sidebar__widget">
                    <h3>🏷️ Tags</h3>
                    <div class="blog-sidebar__tags">
                        <?php foreach ($tagCloud as $tag) : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog?tag=<?php echo urlencode($tag); ?>"
                           class="tag-pill<?php echo ($activeTag === $tag) ? ' active' : ''; ?>">
                            <?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </aside>

        </div><!-- /.blog-layout -->

    </div><!-- /.blog-container -->
</main>

<style>
/* ===== Blog Page – 365Network Theme ===== */
.blog-page {
    padding: 2rem 0;
    min-height: 60vh;
}
.blog-container {
    max-width: var(--container-width, 1280px);
    margin: 0 auto;
    padding: 0 1.5rem;
}
.blog-page-header {
    margin-bottom: 2rem;
}
.blog-page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--heading-color, var(--text-primary));
    margin: 0 0 .25rem;
}
.blog-page-desc {
    font-size: .875rem;
    color: var(--muted-color, #94a3b8);
    margin: 0;
}
.blog-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
    align-items: start;
}
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}
.blog-card {
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: var(--border-radius, 8px);
    overflow: hidden;
    transition: var(--transition, all .3s ease);
}
.blog-card:hover {
    box-shadow: var(--shadow-md, 0 4px 12px rgba(0,0,0,.12));
    transform: translateY(-2px);
}
.blog-card__image {
    display: block;
    aspect-ratio: 16/9;
    overflow: hidden;
}
.blog-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
}
.blog-card:hover .blog-card__image img {
    transform: scale(1.04);
}
.blog-card__body {
    padding: 1.25rem;
}
.blog-card__category {
    display: inline-block;
    padding: .125rem .5rem;
    background: rgba(200,149,46,.12);
    color: var(--accent-color, #e8a838);
    border-radius: 4px;
    font-size: .6875rem;
    font-weight: 600;
    margin-bottom: .5rem;
}
.blog-card__title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 .5rem;
    line-height: 1.4;
}
.blog-card__title a {
    color: var(--heading-color, var(--text-primary));
    text-decoration: none;
}
.blog-card__title a:hover {
    color: var(--accent-color, #e8a838);
}
.blog-card__excerpt {
    font-size: .8125rem;
    color: var(--secondary-color, #64748b);
    line-height: 1.55;
    margin: 0 0 .75rem;
}
.blog-card__meta {
    display: flex;
    gap: .75rem;
    font-size: .75rem;
    color: var(--muted-color, #94a3b8);
}

/* Pagination */
.blog-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
    padding: 1rem 0;
}
.blog-pagination__btn {
    padding: .5rem 1rem;
    background: var(--bg-secondary, #f1f5f9);
    color: var(--text-primary, #1e293b);
    border-radius: var(--border-radius, 8px);
    text-decoration: none;
    font-size: .8125rem;
    font-weight: 600;
    transition: var(--transition-fast, all .15s ease);
}
.blog-pagination__btn:hover {
    background: var(--accent-color, #e8a838);
    color: #fff;
}
.blog-pagination__info {
    font-size: .8125rem;
    color: var(--muted-color, #94a3b8);
}

/* Empty State */
.blog-empty {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: var(--border-radius, 8px);
}

/* Sidebar */
.blog-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.blog-sidebar__widget {
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: var(--border-radius, 8px);
    padding: 1.25rem;
}
.blog-sidebar__widget h3 {
    font-size: .9375rem;
    font-weight: 700;
    color: var(--heading-color, var(--text-primary));
    margin: 0 0 .75rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
}
.blog-sidebar__cat-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.blog-sidebar__cat-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .375rem 0;
    border-bottom: 1px solid rgba(255,255,255,.05);
}
.blog-sidebar__cat-list li.active a {
    color: var(--accent-color, #e8a838);
    font-weight: 600;
}
.blog-sidebar__cat-list a {
    color: var(--text-primary, #1e293b);
    text-decoration: none;
    font-size: .8125rem;
}
.blog-sidebar__cat-list a:hover {
    color: var(--accent-color, #e8a838);
}
.blog-sidebar__cat-list .cat-count {
    font-size: .6875rem;
    color: var(--muted-color, #94a3b8);
    background: var(--bg-secondary, #f1f5f9);
    padding: .125rem .375rem;
    border-radius: 4px;
}
.blog-sidebar__tags {
    display: flex;
    flex-wrap: wrap;
    gap: .375rem;
}
.tag-pill {
    display: inline-block;
    padding: .1875rem .5rem;
    background: var(--bg-secondary, #f1f5f9);
    color: var(--secondary-color, #64748b);
    border-radius: 4px;
    font-size: .6875rem;
    text-decoration: none;
    transition: var(--transition-fast, all .15s ease);
}
.tag-pill:hover, .tag-pill.active {
    background: var(--accent-color, #e8a838);
    color: #fff;
}
.btn-accent {
    display: inline-block;
    padding: .625rem 1.25rem;
    background: var(--accent-color, #e8a838);
    color: #fff;
    border: none;
    border-radius: var(--border-radius, 8px);
    text-decoration: none;
    font-weight: 600;
    font-size: .875rem;
    cursor: pointer;
}
.btn-accent:hover {
    filter: brightness(1.1);
}

/* Responsive */
@media (max-width: 1024px) {
    .blog-layout {
        grid-template-columns: 1fr;
    }
    .blog-sidebar {
        flex-direction: row;
        flex-wrap: wrap;
    }
    .blog-sidebar__widget {
        flex: 1 1 250px;
    }
}
@media (max-width: 768px) {
    .blog-grid {
        grid-template-columns: 1fr;
    }
    .blog-page-header h1 {
        font-size: 1.375rem;
    }
}
</style>
