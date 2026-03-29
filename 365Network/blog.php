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
$blogBaseUrl = theme_safe_url($siteUrl . '/blog', $siteUrl . '/blog');

// Filter-Parameter
$activeCategoryRaw = function_exists('sanitize_key')
    ? sanitize_key((string) ($_GET['category'] ?? ''))
    : preg_replace('/[^a-z0-9_-]/i', '', (string) ($_GET['category'] ?? ''));
$activeTagRaw = trim(strip_tags((string) ($_GET['tag'] ?? '')));
$searchQuery = trim(strip_tags((string) ($_GET['q'] ?? '')));

$activeCategory = htmlspecialchars($activeCategoryRaw, ENT_QUOTES, 'UTF-8');
$activeTag      = htmlspecialchars($activeTagRaw, ENT_QUOTES, 'UTF-8');

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
                    · <a href="<?php echo htmlspecialchars($blogBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="blog-page-link-reset">Alle anzeigen</a>
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
                            $pSlugPath = implode('/', array_map('rawurlencode', array_filter(explode('/', trim((string) $pSlug, '/')), static fn(string $segment): bool => $segment !== '')));
                            $pUrlRaw   = theme_safe_url($siteUrl . '/blog/' . $pSlugPath, $blogBaseUrl);
                            $pUrl      = htmlspecialchars($pUrlRaw, ENT_QUOTES, 'UTF-8');
                            $pImage    = theme_safe_url((string) ($pArr['featured_image'] ?? ''));
                            $pAuthor  = htmlspecialchars($pArr['author_name'] ?? 'Redaktion', ENT_QUOTES, 'UTF-8');
                        ?>
                        <article class="blog-card">
                            <?php if ($pImage) : ?>
                            <a href="<?php echo $pUrl; ?>" class="blog-card__image">
                                <img src="<?php echo htmlspecialchars($pImage, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $pTitle; ?>" loading="lazy" width="640" height="360">
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
                        <?php $paginationState = ['category' => $activeCategoryRaw, 'tag' => $activeTagRaw]; ?>

                        <?php if ($currentPage > 1) : ?>
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/blog', $paginationState, ['p' => (string) ($currentPage - 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="blog-pagination__btn" aria-label="Vorherige Seite">← Zurück</a>
                        <?php endif; ?>

                        <span class="blog-pagination__info">Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?></span>

                        <?php if ($currentPage < $totalPages) : ?>
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/blog', $paginationState, ['p' => (string) ($currentPage + 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="blog-pagination__btn" aria-label="Nächste Seite">Weiter →</a>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>

                <?php else : ?>
                    <!-- Empty State -->
                    <div class="blog-empty">
                        <p class="blog-empty__icon">📭</p>
                        <p><strong>Keine Artikel gefunden</strong></p>
                        <?php if ($activeCategory || $activeTag) : ?>
                            <p class="blog-empty__text">Für diese Auswahl gibt es noch keine Beiträge.</p>
                            <a href="<?php echo htmlspecialchars($blogBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn-accent blog-empty__action">Alle Artikel anzeigen</a>
                        <?php else : ?>
                            <p class="blog-empty__text">Die ersten Artikel erscheinen hier, sobald sie veröffentlicht werden.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="blog-sidebar" aria-label="Blog Sidebar">

                <!-- Suche -->
                <div class="blog-sidebar__widget">
                    <h3>🔍 Suche</h3>
                    <form action="<?php echo htmlspecialchars(theme_safe_url($siteUrl . '/search', $siteUrl . '/search'), ENT_QUOTES, 'UTF-8'); ?>" method="GET" role="search">
                        <div class="blog-sidebar__search-row">
                            <input type="search" name="q" placeholder="Artikel suchen …" autocomplete="off"
                                   value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="blog-sidebar__search-input">
                            <button type="submit" aria-label="Suchen" class="blog-sidebar__search-button">🔍</button>
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
                            $cSlugValue = (string) ($cArr['slug'] ?? '');
                            $cCount = (int)($cArr['post_count'] ?? 0);
                            $isActive = ($activeCategoryRaw === $cSlugValue);
                        ?>
                        <li<?php echo $isActive ? ' class="active"' : ''; ?>>
                            <a href="<?php echo htmlspecialchars(theme_build_query_url('/blog', [], ['category' => $cSlugValue]), ENT_QUOTES, 'UTF-8'); ?>">
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
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/blog', [], ['tag' => $tag]), ENT_QUOTES, 'UTF-8'); ?>"
                           class="tag-pill<?php echo ($activeTagRaw === $tag) ? ' active' : ''; ?>">
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
