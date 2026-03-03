<?php
/**
 * Einzelartikel – Vollbreite-Template (ohne Sidebar)
 *
 * Template-ID: post-wide
 * Unterschiede zu post.php:
 *  - Kein 2-Spalten-Layout, kein .sidebar
 *  - TOC als eingebautes, aufklappbares <details> vor dem Artikel-Body
 *  - Breiter zentrierter Artikel (max. 860 px)
 *  - Für lange Leseartikel, Tutorials, Guides geeignet
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

// ── Daten laden ──────────────────────────────────────────────────────────
try {
    $postService = \CMS\Services\PostService::instance();
    $slug        = trim($_GET['slug'] ?? $_SERVER['REQUEST_URI'] ?? '', '/ ');
    $post        = $postService->getPostBySlug($slug);
    if (!$post) {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }
    $prevPost     = $postService->getPrevPost((int)($post['id'] ?? 0)) ?: null;
    $nextPost     = $postService->getNextPost((int)($post['id'] ?? 0)) ?: null;
    $comments     = $postService->getComments((int)($post['id'] ?? 0)) ?: [];
    $commentCount = count($comments);
} catch (\Throwable $e) {
    $post = null; $prevPost = null; $nextPost = null;
    $comments = []; $commentCount = 0;
}

if (!$post) { get_theme_part('404'); exit; }

// ── TOC aus Überschriften ──────────────────────────────────────────────
$tocItems = [];
$content  = $post['content'] ?? '';
preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/i', $content, $m, PREG_SET_ORDER);
foreach ($m as $match) {
    $tocItems[] = ['level' => (int)$match[1], 'id' => $match[2], 'text' => strip_tags($match[3])];
}

// ── CSRF & Kommentar-Handler ───────────────────────────────────────────
try {
    $csrfToken = \CMS\Security::instance()->generateToken('comment_post_' . ($post['id'] ?? 0));
} catch (\Throwable $e) { $csrfToken = ''; }

$commentError = $commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!empty($csrfToken) && !\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $name    = htmlspecialchars(trim($_POST['comment_name']  ?? ''), ENT_QUOTES);
        $email   = filter_var(trim($_POST['comment_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $text    = htmlspecialchars(trim($_POST['comment_text'] ?? ''), ENT_QUOTES);
        if (empty($name) || !$email || empty($text)) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $postService->addComment((int)($post['id'] ?? 0), [
                    'name' => $name, 'email' => (string)$email, 'text' => $text,
                ]);
                header('Location: ' . htmlspecialchars($siteUrl . '/' . ($post['slug'] ?? ''), ENT_QUOTES) . '?commented=1');
                exit;
            } catch (\Throwable $ex) { $commentError = 'Fehler beim Speichern des Kommentars.'; }
        }
    }
}
?>

<div class="container" style="padding-top:28px;padding-bottom:40px;">
<article class="article-layout--wide" itemscope itemtype="https://schema.org/BlogPosting">

    <!-- ── Post-Header ───────────────────────────────────────────────── -->
    <header class="post-header" data-anim>
        <?php if (!empty($post['thumbnail'])): ?>
        <img class="post-hero-img"
             src="<?php echo htmlspecialchars($post['thumbnail'], ENT_QUOTES); ?>"
             alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
             loading="eager" itemprop="image">
        <?php endif; ?>

        <div class="post-header-body">
            <?php $cats = (array)($post['categories'] ?? (isset($post['category']) ? [$post['category']] : [])); ?>
            <?php if (!empty($cats)): ?>
            <div class="post-cats">
                <?php foreach ($cats as $cat): ?>
                <a href="<?php echo htmlspecialchars($siteUrl . '/kategorie/' . urlencode($cat), ENT_QUOTES); ?>" class="badge badge-teal">
                    <?php echo htmlspecialchars($cat, ENT_QUOTES); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h1 class="post-title" itemprop="headline">
                <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
            </h1>
            <div class="post-meta">
                <span>📅 <strong itemprop="datePublished" content="<?php echo htmlspecialchars($post['published_at'] ?? '', ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(date('j. F Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?>
                </strong></span>
                <?php if (!empty($post['author'])): ?>
                <span>👤 <strong itemprop="author"><?php echo htmlspecialchars($post['author'], ENT_QUOTES); ?></strong></span>
                <?php endif; ?>
                <?php
                $readTime = !empty($post['read_time']) ? (int)$post['read_time']
                          : (function_exists('phinit_reading_time') ? phinit_reading_time($content) : 0);
                if ($readTime): ?>
                <span>⏱ <?php echo $readTime; ?> Min. Lesezeit</span>
                <?php endif; ?>
                <?php if ($commentCount > 0): ?>
                <span><a href="#comments" style="color:inherit;">💬 <?php echo $commentCount; ?> Kommentar<?php echo $commentCount !== 1 ? 'e' : ''; ?></a></span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- ── Inline-TOC (aufklappbar) ──────────────────────────────────── -->
    <?php if (!empty($tocItems)): ?>
    <details class="toc-inline" data-anim data-anim-delay="1">
        <summary class="toc-inline__toggle">📋 Inhaltsverzeichnis anzeigen</summary>
        <nav class="toc-inline__body">
            <ul role="list">
                <?php foreach ($tocItems as $item): ?>
                <li class="<?php echo $item['level'] === 3 ? 'toc-h3' : ''; ?>">
                    <a href="#<?php echo htmlspecialchars($item['id'], ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($item['text'], ENT_QUOTES); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </details>
    <?php endif; ?>

    <!-- ── Artikel-Body ──────────────────────────────────────────────── -->
    <div class="post-body" itemprop="articleBody" data-anim data-anim-delay="2">
        <?php echo $content; ?>

        <!-- Share-Buttons -->
        <div class="post-share">
            <span>Teilen:</span>
            <?php
            $postUrl   = htmlspecialchars(urlencode($siteUrl . '/' . ($post['slug'] ?? '')), ENT_QUOTES);
            $postTitle = htmlspecialchars(urlencode($post['title'] ?? ''), ENT_QUOTES);
            ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postUrl; ?>" class="share-btn fb" target="_blank" rel="noopener noreferrer" aria-label="Facebook">f Facebook</a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">𝕏 Twitter</a>
            <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in LinkedIn</a>
            <button class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
        </div>
    </div>

    <!-- ── Vor-/Nächster Artikel ──────────────────────────────────────── -->
    <?php if ($prevPost || $nextPost): ?>
    <nav class="post-nav" aria-label="Artikel-Navigation">
        <?php if ($prevPost): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($prevPost['slug'] ?? ''), ENT_QUOTES); ?>">
            <span class="direction">← Vorheriger Beitrag</span>
            <span class="nav-title"><?php echo htmlspecialchars($prevPost['title'] ?? '', ENT_QUOTES); ?></span>
        </a>
        <?php else: ?><span></span><?php endif; ?>
        <?php if ($nextPost): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($nextPost['slug'] ?? ''), ENT_QUOTES); ?>">
            <span class="direction">Nächster Beitrag →</span>
            <span class="nav-title"><?php echo htmlspecialchars($nextPost['title'] ?? '', ENT_QUOTES); ?></span>
        </a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <!-- ── Kommentare ─────────────────────────────────────────────────── -->
    <section class="comments-section" id="comments">
        <h2 class="comments-title">💬 Hinterlasse einen Kommentar</h2>

        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
                <div class="comment-avatar" aria-hidden="true">
                    <?php echo htmlspecialchars(strtoupper(substr($comment['name'] ?? 'A', 0, 1)), ENT_QUOTES); ?>
                </div>
                <div class="comment-body-wrap">
                    <div class="comment-author-line">
                        <span class="comment-author"><?php echo htmlspecialchars($comment['name'] ?? '', ENT_QUOTES); ?></span>
                        <span class="comment-date"><?php echo htmlspecialchars(date('j. F Y', strtotime($comment['created_at'] ?? 'now')), ENT_QUOTES); ?></span>
                    </div>
                    <p class="comment-text"><?php echo htmlspecialchars($comment['text'] ?? '', ENT_QUOTES); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:var(--text-muted);font-size:var(--fs-sm);padding:12px 0;">Noch keine Kommentare. Sei der Erste!</p>
        <?php endif; ?>

        <?php if ($commentError): ?>
        <div style="background:#fee2e2;border:1px solid #f87171;border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:14px;font-size:var(--fs-sm);color:#991b1b;">
            ❌ <?php echo htmlspecialchars($commentError, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <div class="comment-form-wrap">
            <h4>Kommentar hinterlassen</h4>
            <form method="POST" action="#comments" novalidate>
                <input type="hidden" name="submit_comment" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                <input type="hidden" name="post_id"    value="<?php echo (int)($post['id'] ?? 0); ?>">
                <div class="form-group" style="margin-bottom:12px;">
                    <label for="comment_text">Kommentar <span style="color:#ef4444;">*</span></label>
                    <textarea id="comment_text" name="comment_text" class="form-control" required placeholder="Dein Kommentar …" rows="4"></textarea>
                </div>
                <div class="form-row" style="margin-bottom:12px;">
                    <div class="form-group">
                        <label for="comment_name">Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="comment_name" name="comment_name" class="form-control" required placeholder="Dein Name">
                    </div>
                    <div class="form-group">
                        <label for="comment_email">E-Mail <span style="color:#ef4444;">*</span></label>
                        <input type="email" id="comment_email" name="comment_email" class="form-control" required placeholder="dein@email.de">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Kommentar abschicken</button>
            </form>
        </div>
    </section>

</article>
</div><!-- /.container -->
