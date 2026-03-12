<?php
/**
 * 404 Not Found Template – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

// Aktuelle Beiträge als Vorschlag laden
$recentPosts = [];
try {
    $db     = \CMS\Database::instance();
    $prefix = $db->getPrefix();
    $rows   = $db->get_results(
        "SELECT p.title, p.slug, p.featured_image, p.published_at, c.name AS category_name
         FROM {$prefix}posts p
         LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
         WHERE p.status = 'published'
         ORDER BY p.published_at DESC
         LIMIT 3"
    );
    $recentPosts = $rows ? array_map(fn($r) => (array)$r, $rows) : [];
} catch (\Throwable $e) {}
?>

<div class="container error-shell">

    <!-- 404 Hero -->
    <div class="error-hero" data-anim>
        <div class="error-code">404</div>
        <h1 class="error-title">Seite nicht gefunden</h1>
        <p class="error-desc">
            Die gesuchte Seite existiert nicht oder wurde verschoben.
            Überprüfe die Adresse oder kehre zur Startseite zurück.
        </p>
        <div class="error-actions">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zur Startseite</a>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog" class="btn btn-outline">📰 Blog durchsuchen</a>
        </div>
    </div>

    <!-- Aktuelle Beiträge als Vorschlag -->
    <?php if (!empty($recentPosts)): ?>
    <section class="error-suggestions" data-anim data-anim-delay="1">
        <h2 class="error-suggestions__title">Vielleicht interessieren dich diese Beiträge:</h2>
        <div class="posts-grid posts-grid--cols-3">
            <?php foreach ($recentPosts as $p): ?>
            <article class="post-card">
                <?php if (!empty($p['featured_image'])): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>" class="post-card__image">
                    <img src="<?php echo htmlspecialchars($p['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($p['title'] ?? '', ENT_QUOTES); ?>"
                         loading="lazy">
                    <?php if (!empty($p['category_name'])): ?>
                    <span class="post-card__badge"><?php echo phinit_escape_text($p['category_name'] ?? ''); ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <div class="post-card__body">
                    <h3 class="post-card__title">
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo phinit_escape_text($p['title'] ?? ''); ?>
                        </a>
                    </h3>
                    <?php if (!empty($p['published_at'])): ?>
                    <div class="post-card__meta">
                        <span>📅 <?php echo htmlspecialchars(date('j. M Y', strtotime($p['published_at'])), ENT_QUOTES); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div><!-- /.container -->
