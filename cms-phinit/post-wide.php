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
$db      = \CMS\Database::instance();
$pfx     = $db->getPrefix();

// ── Customizer ─────────────────────────────────────────────────────────
$cz = null;
try { $cz = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable) {}
$czGet = function (string $cat, string $key, mixed $default = '') use ($cz): mixed {
    if (!$cz) return $default;
    try { return $cz->get($cat, $key, $default); } catch (\Throwable) { return $default; }
};
$czBool = function (string $cat, string $key, bool $default = true) use ($czGet): bool {
    return filter_var($czGet($cat, $key, $default), FILTER_VALIDATE_BOOLEAN);
};

// Customizer-Werte (posts / layout)
$showPostHero     = $czBool('posts', 'show_post_hero', true);
$showPostMeta     = $czBool('posts', 'show_post_meta', true);
$showReadingTime  = $czBool('posts', 'show_reading_time', true);
$readingTimeWpm   = max(100, (int)$czGet('posts', 'reading_time_wpm', 200));
$showToc          = $czBool('posts', 'show_toc', true);
$tocMinHeadings   = max(1, (int)$czGet('posts', 'toc_min_headings', 3));
$tocHeaderText    = (string)$czGet('posts', 'toc_header_text', 'Inhaltsverzeichnis');
$showShareButtons = $czBool('posts', 'show_share_buttons', true);
$showComments     = $czBool('posts', 'show_comments', true);
$commentsHeader   = (string)$czGet('posts', 'comments_header', '💬 Kommentare');
$commentFormHeader = (string)$czGet('posts', 'comment_form_header', 'Kommentar hinterlassen');
$showPostTags     = $czBool('posts', 'show_post_tags', true);

// ── Daten laden ────────────────────────────────────────────────────────
if (isset($post) && !empty($post)) {
    $post = is_object($post) ? (array)$post : (array)$post;
} else {
    $rawPath = (string)preg_replace('#^/blog/#i', '', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $slug    = trim($rawPath, '/');
    if (empty($slug)) { http_response_code(404); get_theme_part('404'); exit; }
    try {
        $postObj = $db->get_row(
            "SELECT p.*, u.display_name AS author_name, c.name AS category_name
             FROM {$pfx}posts p
             LEFT JOIN {$pfx}users u ON u.id = p.author_id
             LEFT JOIN {$pfx}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'",
            [$slug]
        );
        $post = $postObj ? (array)$postObj : null;
    } catch (\Throwable $e) { $post = null; }
}
if (!$post) { http_response_code(404); get_theme_part('404'); exit; }
try { $db->execute("UPDATE {$pfx}posts SET views = views + 1 WHERE id = ?", [(int)($post['id'] ?? 0)]); } catch (\Throwable) {}
try {
    $prevOb = $db->get_row("SELECT id, title, slug FROM {$pfx}posts WHERE status='published' AND published_at < ? AND id != ? ORDER BY published_at DESC LIMIT 1", [$post['published_at'] ?? '9999-12-31', (int)($post['id'] ?? 0)]);
    $nextOb = $db->get_row("SELECT id, title, slug FROM {$pfx}posts WHERE status='published' AND published_at > ? AND id != ? ORDER BY published_at ASC LIMIT 1",  [$post['published_at'] ?? '0001-01-01', (int)($post['id'] ?? 0)]);
    $prevPost = $prevOb ? (array)$prevOb : null;
    $nextPost = $nextOb ? (array)$nextOb : null;
} catch (\Throwable) { $prevPost = null; $nextPost = null; }
try {
    $commentRows  = $db->get_results("SELECT id, author, content, post_date FROM {$pfx}comments WHERE post_id = ? AND status = 'approved' ORDER BY post_date ASC", [(int)($post['id'] ?? 0)]) ?: [];
    $comments     = array_map(fn($c) => (array)$c, $commentRows);
    $commentCount = count($comments);
} catch (\Throwable) { $comments = []; $commentCount = 0; }

// ── Auto-ID-Injection + TOC ────────────────────────────────────────────
$content   = $post['content'] ?? '';
$usedSlugs = [];
$content   = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h\1>/i', function ($m) use (&$usedSlugs) {
    $level = $m[1]; $attrs = $m[2]; $inner = $m[3];
    if (preg_match('/id="[^"]+"/i', $attrs)) return $m[0];
    $text = strip_tags($inner);
    $slug = mb_strtolower(trim($text));
    $slug = str_replace(['ä','ö','ü','ß'], ['ae','oe','ue','ss'], $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-') ?: 'h';
    $base = $slug;
    $i = 2;
    while (isset($usedSlugs[$slug])) { $slug = $base . '-' . $i++; }
    $usedSlugs[$slug] = true;
    return "<h{$level}{$attrs} id=\"{$slug}\">{$inner}</h{$level}>";
}, $content) ?? $content;

$tocItems = [];
if ($showToc) {
    preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/i', $content, $tm, PREG_SET_ORDER);
    foreach ($tm as $match) {
        $tocItems[] = ['level' => (int)$match[1], 'id' => $match[2], 'text' => strip_tags($match[3])];
    }
    if (count($tocItems) < $tocMinHeadings) $tocItems = [];
}

// ── CSRF & Kommentar-Handler ───────────────────────────────────────────
$commentError = $commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $name    = htmlspecialchars(trim($_POST['comment_name']  ?? ''), ENT_QUOTES);
        $email   = filter_var(trim($_POST['comment_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $text    = htmlspecialchars(trim($_POST['comment_text'] ?? ''), ENT_QUOTES);
        if (empty($name) || !$email || empty($text)) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $db->execute(
                    "INSERT INTO {$pfx}comments (post_id, author, author_email, author_ip, content, status) VALUES (?, ?, ?, ?, ?, 'pending')",
                    [(int)($post['id'] ?? 0), $name, (string)$email, $_SERVER['REMOTE_ADDR'] ?? '', $text]
                );
                header('Location: ' . htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES) . '?commented=1#comments');
                exit;
            } catch (\Throwable $ex) { $commentError = 'Fehler beim Speichern des Kommentars.'; }
        }
    }
}
try { $csrfToken = \CMS\Security::instance()->generateToken('comment_post_' . ($post['id'] ?? 0)); } catch (\Throwable $e) { $csrfToken = ''; }
// ── Reading Time ───────────────────────────────────────────────────────
$readTime = function_exists('phinit_reading_time') ? phinit_reading_time($content, $readingTimeWpm) : 0;
?>

<div class="container post-container">
<article class="article-layout--wide" itemscope itemtype="https://schema.org/BlogPosting">

    <!-- ── Post-Header ───────────────────────────────────────────────── -->
    <header class="post-header" data-anim>
        <?php if ($showPostHero && !empty($post['featured_image'])): ?>
        <img class="post-hero-img"
             src="<?php echo htmlspecialchars($post['featured_image'], ENT_QUOTES); ?>"
             alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>"
             loading="eager" itemprop="image">
        <?php endif; ?>

        <div class="post-header-body">
            <?php if (!empty($post['category_name'])): ?>
            <div class="post-cats">
                <a href="<?php echo htmlspecialchars($siteUrl . '/kategorie/' . urlencode($post['category_name']), ENT_QUOTES); ?>" class="badge badge-teal">
                    <?php echo htmlspecialchars($post['category_name'], ENT_QUOTES); ?>
                </a>
            </div>
            <?php endif; ?>

            <h1 class="post-title" itemprop="headline">
                <?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES); ?>
            </h1>
            <?php if ($showPostMeta): ?>
            <div class="post-meta">
                <span>📅 <strong itemprop="datePublished" content="<?php echo htmlspecialchars($post['published_at'] ?? '', ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(date('j. F Y', strtotime($post['published_at'] ?? 'now')), ENT_QUOTES); ?>
                </strong></span>
                <?php if (!empty($post['author_name'])): ?>
                <span>👤 <strong itemprop="author"><?php echo htmlspecialchars($post['author_name'], ENT_QUOTES); ?></strong></span>
                <?php endif; ?>
                <?php if ($showReadingTime && $readTime): ?>
                <span>⏱ <?php echo $readTime; ?> Min. Lesezeit</span>
                <?php endif; ?>
                <?php if ($commentCount > 0): ?>
                <span><a href="#comments" style="color:inherit;">💬 <?php echo $commentCount; ?> Kommentar<?php echo $commentCount !== 1 ? 'e' : ''; ?></a></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- ── Inline-TOC (aufklappbar) ──────────────────────────────────── -->
    <?php if (!empty($tocItems)): ?>
    <details class="toc-inline" data-anim data-anim-delay="1">
        <summary class="toc-inline__toggle">📋 <?php echo htmlspecialchars($tocHeaderText, ENT_QUOTES); ?></summary>
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
    <div class="post-body" itemprop="articleBody" data-photoswipe data-anim data-anim-delay="2">
        <?php echo $content; ?>

        <!-- Share-Buttons -->
        <?php if ($showShareButtons): ?>
        <div class="post-share">
            <span>Teilen:</span>
            <?php
            $postUrl   = htmlspecialchars(urlencode($siteUrl . '/blog/' . ($post['slug'] ?? '')), ENT_QUOTES);
            $postTitle = htmlspecialchars(urlencode($post['title'] ?? ''), ENT_QUOTES);
            ?>
            <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in LinkedIn</a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">𝕏 Twitter</a>
            <a href="mailto:?subject=<?php echo $postTitle; ?>&body=<?php echo $postUrl; ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
            <button class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Vor-/Nächster Artikel ──────────────────────────────────────── -->
    <?php if ($prevPost || $nextPost): ?>
    <nav class="post-nav" aria-label="Artikel-Navigation">
        <?php if ($prevPost): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($prevPost['slug'] ?? ''), ENT_QUOTES); ?>">
            <span class="direction">← Vorheriger Beitrag</span>
            <span class="nav-title"><?php echo htmlspecialchars($prevPost['title'] ?? '', ENT_QUOTES); ?></span>
        </a>
        <?php else: ?><span></span><?php endif; ?>
        <?php if ($nextPost): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($nextPost['slug'] ?? ''), ENT_QUOTES); ?>">
            <span class="direction">Nächster Beitrag →</span>
            <span class="nav-title"><?php echo htmlspecialchars($nextPost['title'] ?? '', ENT_QUOTES); ?></span>
        </a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <!-- ── Kommentare ─────────────────────────────────────────────────── -->
    <?php if ($showComments): ?>
    <section class="comments-section" id="comments">
        <h2 class="comments-title"><?php echo htmlspecialchars($commentsHeader, ENT_QUOTES); ?></h2>

        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
                <div class="comment-avatar" aria-hidden="true">
                    <?php echo htmlspecialchars(strtoupper(substr($comment['author'] ?? 'A', 0, 1)), ENT_QUOTES); ?>
                </div>
                <div class="comment-body-wrap">
                    <div class="comment-author-line">
                        <span class="comment-author"><?php echo htmlspecialchars($comment['author'] ?? '', ENT_QUOTES); ?></span>
                        <span class="comment-date"><?php echo htmlspecialchars(date('j. F Y', strtotime($comment['post_date'] ?? 'now')), ENT_QUOTES); ?></span>
                    </div>
                    <p class="comment-text"><?php echo htmlspecialchars($comment['content'] ?? '', ENT_QUOTES); ?></p>
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
            <h4><?php echo htmlspecialchars($commentFormHeader, ENT_QUOTES); ?></h4>
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
    <?php endif; ?>

    <!-- Tags -->
    <?php
    if ($showPostTags) {
        $tagRows = [];
        try {
            $tagRows = $db->get_results(
                "SELECT t.name, t.slug FROM {$pfx}tags t
                 INNER JOIN {$pfx}post_tags pt ON pt.tag_id = t.id
                 WHERE pt.post_id = ? ORDER BY t.name ASC",
                [(int)($post['id'] ?? 0)]
            ) ?: [];
        } catch (\Throwable) {}
        if (!empty($tagRows)): ?>
        <div class="post-tags" data-anim>
            <span>🏷️ Tags:</span>
            <?php foreach ($tagRows as $tag): ?>
            <a href="<?php echo htmlspecialchars($siteUrl . '/tag/' . ($tag['slug'] ?? ''), ENT_QUOTES); ?>" class="tag-link">
                <?php echo htmlspecialchars($tag['name'] ?? '', ENT_QUOTES); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif;
    }
    ?>

</article>
</div><!-- /.container -->
