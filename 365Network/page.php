<?php
/**
 * Page Template - Einzelne Seite aus DB
 *
 * Erhält $page als Array mit den Feldern:
 * id, title, slug, content, meta_description, status, created_at, updated_at
 *
 * @package IT_Expert_Network_Theme
 * @var array $page
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (empty($page) || !is_array($page)) {
    http_response_code(404);
    \CMS\ThemeManager::instance()->render('404');
    return;
}

$pageTitle   = $page['title'] ?? '';
$pageContent = $page['content'] ?? '';
$pageSlug    = $page['slug'] ?? '';
$updatedAt   = $page['updated_at'] ?? '';
$updatedTimestamp = $updatedAt ? strtotime((string) $updatedAt) : false;
$safePageContent = theme_sanitize_html((string) $pageContent, 'default');

// SEO: Seitentitel im <head> aktualisieren (über Output-Buffer nicht möglich nach Header-Output)
// → Seitentitel wird über ThemeManager::getSiteTitle() in header.php ausgegeben.
// Hier setzen wir ihn für künftige Nutzung über eine Hook-Variable.
?>

<main id="main" class="site-main" role="main">
    <div class="container">
        <article class="content-area content-area--page">

            <?php if ($pageTitle && trim($pageTitle) !== '') : ?>
                <header class="entry-header">
                    <h1 class="page-title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                </header>
            <?php endif; ?>

            <?php if ($safePageContent !== '') : ?>
                <div class="page-content entry-content">
                    <?php echo $safePageContent; ?>
                </div>
            <?php else : ?>
                <div class="page-content page-content--empty">
                    <p>Diese Seite enthält noch keinen Inhalt.</p>
                </div>
            <?php endif; ?>

            <?php if ($updatedTimestamp) : ?>
                <footer class="entry-footer entry-footer--page">
                    <span class="entry-meta entry-meta--subtle">
                        Zuletzt aktualisiert: <?php echo htmlspecialchars(date('d.m.Y', $updatedTimestamp), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </footer>
            <?php endif; ?>

        </article>
    </div>
</main>
