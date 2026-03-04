<?php
/**
 * Blog-Listing Template – CMS Phinit Theme
 *
 * Wird vom Router via ThemeManager::render('blog', [...]) aufgerufen.
 * Übergebene Variablen (via extract):
 *   $posts       – Array von Post-Objekten
 *   $total       – Gesamtanzahl veröffentlichter Beiträge
 *   $currentPage – Aktuelle Seitennummer
 *   $totalPages  – Maximale Seitenanzahl
 *   $perPage     – Beiträge pro Seite
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

// Variablen aus Router normalisieren
$posts       = isset($posts)       ? (array)$posts       : [];
$total       = isset($total)       ? (int)$total         : 0;
$currentPage = isset($currentPage) ? (int)$currentPage   : 1;
$totalPages  = isset($totalPages)  ? (int)$totalPages     : 1;

// Customizer-Einstellungen
try {
    $c = \CMS\Services\ThemeCustomizer::instance();
    $_showExcerpt = filter_var($c->get('homepage', 'show_article_excerpt', true), FILTER_VALIDATE_BOOLEAN);
    $_showMeta    = filter_var($c->get('homepage', 'show_article_meta', true), FILTER_VALIDATE_BOOLEAN);
} catch (\Throwable $e) {
    $_showExcerpt = true;
    $_showMeta    = true;
}
?>

<div class="container" style="padding-top:28px;padding-bottom:40px;">

    <!-- Page Header -->
    <div class="blog-page-header" data-anim>
        <h1>📰 Blog</h1>
        <p style="color:var(--text-muted);margin-top:6px;">
            <?php echo $total; ?> Beiträge veröffentlicht
            <?php if ($totalPages > 1): ?>
             – Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?>
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($posts)): ?>
    <div class="posts-grid posts-grid--cols-3" data-anim data-anim-delay="1">
        <?php foreach ($posts as $postObj):
            $p = is_object($postObj) ? (array)$postObj : (array)$postObj;
            $readTime = function_exists('phinit_reading_time') && !empty($p['content'])
                ? phinit_reading_time($p['content']) : 0;
        ?>
        <article class="post-card">
            <?php if (!empty($p['featured_image'])): ?>
            <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>" class="post-card-thumb">
                <img src="<?php echo htmlspecialchars($p['featured_image'], ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars($p['title'] ?? '', ENT_QUOTES); ?>"
                     loading="lazy">
                <?php if (!empty($p['category_name'])): ?>
                <span class="post-card-badge"><?php echo htmlspecialchars($p['category_name'], ENT_QUOTES); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <div class="post-card-body">
                <h2 class="post-card-title">
                    <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($p['title'] ?? 'Ohne Titel', ENT_QUOTES); ?>
                    </a>
                </h2>

                <?php if ($_showExcerpt && !empty($p['excerpt'])): ?>
                <p class="post-card-excerpt"><?php echo htmlspecialchars($p['excerpt'], ENT_QUOTES); ?></p>
                <?php endif; ?>

                <?php if ($_showMeta): ?>
                <div class="post-card-meta">
                    <?php if (!empty($p['published_at'])): ?>
                    <span>📅 <?php echo htmlspecialchars(date('j. M Y', strtotime($p['published_at'])), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <?php if ($readTime > 0): ?>
                    <span>⏱ <?php echo $readTime; ?> Min.</span>
                    <?php endif; ?>
                    <?php if (isset($p['views'])): ?>
                    <span>👁 <?php echo number_format((int)$p['views']); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="blog-pagination" aria-label="Blog-Seiten">
        <?php if ($currentPage > 1): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?p=<?php echo $currentPage - 1; ?>" class="btn btn-sm btn-outline">← Zurück</a>
        <?php endif; ?>

        <span class="blog-pagination__info">Seite <?php echo $currentPage; ?> / <?php echo $totalPages; ?></span>

        <?php if ($currentPage < $totalPages): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?p=<?php echo $currentPage + 1; ?>" class="btn btn-sm btn-outline">Weiter →</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:4rem 2rem;" data-anim>
        <p style="font-size:3rem;margin:0 0 1rem;">📭</p>
        <h2 style="color:var(--text-primary);">Noch keine Beiträge</h2>
        <p style="color:var(--text-muted);max-width:400px;margin:.5rem auto 1.5rem;">Bald gibt es hier spannende Artikel zu IT-Themen, PowerShell, Microsoft 365 und mehr.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zur Startseite</a>
    </div>
    <?php endif; ?>

</div><!-- /.container -->
