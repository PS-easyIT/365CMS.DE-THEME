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
        <div class="error-hero__panel">
            <span class="error-hero__eyebrow">Fehlerseite</span>
            <div class="error-code">404</div>
            <h1 class="error-title">Seite nicht gefunden</h1>
            <p class="error-desc">
                Die gesuchte Seite existiert nicht oder wurde verschoben.
                Vielleicht ist nur ein Link verrutscht &mdash; der gute Inhalt ist trotzdem nicht weit weg.
            </p>
            <div class="error-actions">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zur Startseite</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog" class="btn btn-outline">📰 Beiträge entdecken</a>
            </div>
            <div class="error-help-grid">
                <div class="error-help-card">
                    <span class="error-help-card__icon" aria-hidden="true">🔎</span>
                    <strong class="error-help-card__title">URL prüfen</strong>
                    <p class="error-help-card__text">Oft fehlt nur ein Zeichen oder ein alter Link zeigt auf eine verschobene Seite.</p>
                </div>
                <div class="error-help-card">
                    <span class="error-help-card__icon" aria-hidden="true">🧭</span>
                    <strong class="error-help-card__title">Neu orientieren</strong>
                    <p class="error-help-card__text">Über die Startseite oder das Blog-Archiv findest du schnell zurück in den richtigen Bereich.</p>
                </div>
                <div class="error-help-card">
                    <span class="error-help-card__icon" aria-hidden="true">✨</span>
                    <strong class="error-help-card__title">Etwas Neues lesen</strong>
                    <p class="error-help-card__text">Wenn die Zielseite weg ist, wartet darunter vielleicht schon der nächste interessante Beitrag.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktuelle Beiträge als Vorschlag -->
    <?php if (!empty($recentPosts)): ?>
    <section class="error-suggestions" data-anim data-anim-delay="1">
        <div class="error-suggestions__head">
            <span class="error-suggestions__eyebrow">Lesestoff statt Sackgasse</span>
        </div>
        <div class="posts-grid posts-grid--cols-3 error-suggestions__grid">
            <?php foreach ($recentPosts as $p): ?>
            <article class="post-card">
                <?php if (!empty($p['featured_image'])): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>" class="post-card-thumb">
                    <img src="<?php echo htmlspecialchars($p['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($p['title'] ?? '', ENT_QUOTES); ?>"
                        <?php echo phinit_image_loading_attributes(); ?>>
                    <?php if (!empty($p['category_name'])): ?>
                    <span class="post-card-badge"><?php echo phinit_escape_text($p['category_name'] ?? ''); ?></span>
                    <?php endif; ?>
                </a>
                <?php else: ?>
                <div class="post-card-thumb post-card-thumb--placeholder">
                    <span class="post-card-thumb__icon">📄</span>
                </div>
                <?php endif; ?>
                <div class="post-card-body">
                    <h3 class="post-card-title">
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo phinit_escape_text($p['title'] ?? ''); ?>
                        </a>
                    </h3>
                    <?php if (!empty($p['published_at'])): ?>
                    <div class="post-card-meta">
                        <div class="post-card-meta__left">
                            <span>📅 <?php echo htmlspecialchars(date('j. M Y', strtotime($p['published_at'])), ENT_QUOTES); ?></span>
                        </div>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>" class="post-card-meta__more">Weiter lesen →</a>
                    </div>
                    <?php else: ?>
                    <div class="post-card-meta">
                        <span class="post-card-meta__left">Neue Empfehlung</span>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($p['slug'] ?? ''), ENT_QUOTES); ?>" class="post-card-meta__more">Weiter lesen →</a>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div><!-- /.container -->
