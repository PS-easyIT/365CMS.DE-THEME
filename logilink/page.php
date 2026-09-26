<?php
/**
 * LogiLink Theme – Singular Page Template
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}


// Der Core übergibt die Seite als Array ($page); 'content' ist bereits gerendertes, sanitiertes HTML.
$page = isset($page) && is_array($page) && $page !== [] ? (object) $page : null;

$safe    = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$homeUrl = htmlspecialchars(theme_route_url('home'), ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="ll-main ll-prose-wrap" role="main">
    <div class="ll-container">
        <?php if (!empty($page)) : ?>
            <article class="ll-card ll-page-card">
                <?php if (!empty($page->title)) : ?>
                    <h1><?php echo $safe((string) $page->title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($page->content)) : ?>
                    <div class="ll-prose">
                        <?php echo (string) $page->content; ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php else : ?>
            <div class="ll-error-card ll-card">
                <p>Seite nicht gefunden.</p>
                <div class="ll-error-actions">
                    <a href="<?php echo $homeUrl; ?>" class="ll-btn ll-btn-primary">Zur Startseite</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
