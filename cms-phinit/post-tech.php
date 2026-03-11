<?php
/**
 * Einzelartikel – Tech-Artikel-Template (mit Tech-Karte)
 *
 * Template-ID: post-tech
 * Unterschiede zu post.php:
 *  - .tech-card vor dem Artikel-Body mit technischen Metadaten
 *    (Betriebssystem, Version, Getestet am, Voraussetzungen, Schwierigkeit)
 *  - Metadaten werden aus $post['meta'] gelesen
 *  - Sidebar enthält zusätzlich eine kompakte Tech-Side-Info
 *  - Ansonsten identisch mit dem Standard-Template
 *
 * Pflichtfeld-Schlüssel in post[meta]:
 *   os            – z. B. "Windows Server 2022", "Ubuntu 22.04"
 *   version       – z. B. "PowerShell 7.4"
 *   last_tested   – YYYY-MM-DD
 *   difficulty    – "beginner" | "intermediate" | "advanced" | "expert"
 *   prerequisites – String-Array, z. B. ["Admin-Rechte", ".NET 8"]
 *   time_needed   – z. B. "30 Minuten"
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$db      = \CMS\Database::instance();
$prefix  = $db->getPrefix();

// ── Customizer laden ───────────────────────────────────────────────────
try { $cz = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable) { $cz = null; }
$czGet  = function (string $cat, string $key, mixed $default = '') use ($cz): mixed {
    if (!$cz) return $default;
    try { return $cz->get($cat, $key, $default); } catch (\Throwable) { return $default; }
};
$czBool = function (string $cat, string $key, bool $default = true) use ($czGet): bool {
    return filter_var($czGet($cat, $key, $default), FILTER_VALIDATE_BOOLEAN);
};
$showPostHero    = $czBool('posts', 'show_post_hero', true);
$showPostMeta    = $czBool('posts', 'show_post_meta', true);
$showReadingTime = $czBool('posts', 'show_reading_time', true);
$readingTimeWpm  = max(50, (int)$czGet('posts', 'reading_time_wpm', 200));
$showToc         = $czBool('posts', 'show_toc', true);
$tocMinHeadings  = max(1, (int)$czGet('posts', 'toc_min_headings', 2));
$tocHeaderText   = (string)$czGet('posts', 'toc_header_text', '📋 Inhaltsverzeichnis');
$showTechCard    = $czBool('posts', 'show_tech_card', true);
$techCardHeader  = (string)$czGet('posts', 'tech_card_header', 'Technische Details');
$showShareButtons = $czBool('posts', 'show_share_buttons', true);
$showComments    = $czBool('posts', 'show_comments', true);
$commentsHeader  = (string)$czGet('posts', 'comments_header', '💬 Kommentare');
$commentFormHeader = (string)$czGet('posts', 'comment_form_header', 'Kommentar hinterlassen');

// ── Daten laden ──────────────────────────────────────────────────────────
if (isset($post) && !empty($post)) {
    $post = is_object($post) ? (array)$post : (array)$post;
} else {
    $rawPath = (string)preg_replace('#^/blog/#i', '', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $slug    = trim($rawPath, '/');
    if (empty($slug)) { http_response_code(404); get_theme_part('404'); exit; }
    try {
        $postObj = $db->get_row(
            "SELECT p.*, u.display_name AS author_name, c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}users u ON u.id = p.author_id
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'",
            [$slug]
        );
        $post = $postObj ? (array)$postObj : null;
    } catch (\Throwable $e) { $post = null; }
}
if (!$post) { http_response_code(404); get_theme_part('404'); exit; }
try { $db->execute("UPDATE {$prefix}posts SET views = views + 1 WHERE id = ?", [(int)($post['id'] ?? 0)]); } catch (\Throwable) {}
try {
    $prevOb = $db->get_row("SELECT id, title, slug FROM {$prefix}posts WHERE status='published' AND published_at < ? AND id != ? ORDER BY published_at DESC LIMIT 1", [$post['published_at'] ?? '9999-12-31', (int)($post['id'] ?? 0)]);
    $nextOb = $db->get_row("SELECT id, title, slug FROM {$prefix}posts WHERE status='published' AND published_at > ? AND id != ? ORDER BY published_at ASC LIMIT 1",  [$post['published_at'] ?? '0001-01-01', (int)($post['id'] ?? 0)]);
    $prevPost = $prevOb ? (array)$prevOb : null;
    $nextPost = $nextOb ? (array)$nextOb : null;
} catch (\Throwable) { $prevPost = null; $nextPost = null; }
try {
    $commentRows  = $db->get_results("SELECT id, author, content, post_date FROM {$prefix}comments WHERE post_id = ? AND status = 'approved' ORDER BY post_date ASC", [(int)($post['id'] ?? 0)]) ?: [];
    $comments     = array_map(fn($c) => (array)$c, $commentRows);
    $commentCount = count($comments);
} catch (\Throwable) { $comments = []; $commentCount = 0; }

// ── Tech-Metadaten auslesen ────────────────────────────────────────────
$meta          = is_array($post['meta'] ?? null) ? $post['meta'] : [];
$techOs        = (string)($meta['os']           ?? '');
$techVersion   = (string)($meta['version']      ?? '');
$techTested    = (string)($meta['last_tested']  ?? '');
$techDiff      = (string)($meta['difficulty']   ?? '');
$techPrereqs   = (array) ($meta['prerequisites'] ?? []);
$techTime      = (string)($meta['time_needed']  ?? '');
$hasTechData   = $techOs || $techVersion || !empty($techPrereqs) || $techDiff;

$diffLabels = [
    'beginner'     => ['label' => 'Einsteiger',     'class' => 'badge--green'],
    'intermediate' => ['label' => 'Fortgeschritten', 'class' => 'badge--yellow'],
    'advanced'     => ['label' => 'Experte',         'class' => 'badge--orange'],
    'expert'       => ['label' => 'Profi',           'class' => 'badge--red'],
];
$diffInfo = $diffLabels[$techDiff] ?? null;

// ── TOC (mit Auto-ID Injection) ──────────────────────────────────────
$tocItems  = [];
$content   = $post['content'] ?? '';
$usedSlugs = [];
$content = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h\1>/si', function ($m) use (&$usedSlugs) {
    $tag = $m[1]; $attrs = $m[2]; $inner = $m[3];
    if (preg_match('/\bid=["\']([^"\']+)["\']/i', $attrs)) return $m[0];
    $text = trim(strip_tags($inner));
    $slug = mb_strtolower($text, 'UTF-8');
    $slug = preg_replace('/[äÄ]/', 'ae', $slug);
    $slug = preg_replace('/[öÖ]/', 'oe', $slug);
    $slug = preg_replace('/[üÜ]/', 'ue', $slug);
    $slug = preg_replace('/ß/', 'ss', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-') ?: 'heading';
    $base = $slug; $i = 2;
    while (in_array($slug, $usedSlugs, true)) { $slug = $base . '-' . $i++; }
    $usedSlugs[] = $slug;
    return "<h{$tag}{$attrs} id=\"{$slug}\">{$inner}</h{$tag}>";
}, $content);
$post['content'] = $content;

if ($showToc) {
    preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/si', $content, $m, PREG_SET_ORDER);
    foreach ($m as $match) {
        $tocItems[] = ['level' => (int)$match[1], 'id' => $match[2], 'text' => strip_tags($match[3])];
    }
    if (count($tocItems) < $tocMinHeadings) { $tocItems = []; }
}

// ── Kommentar-Handler (zuerst), dann CSRF-Token generieren ──────────────
$commentError = $commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $name  = htmlspecialchars(trim($_POST['comment_name']  ?? ''), ENT_QUOTES);
        $email = filter_var(trim($_POST['comment_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $text  = htmlspecialchars(trim($_POST['comment_text'] ?? ''), ENT_QUOTES);
        if (empty($name) || !$email || empty($text)) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $db->execute(
                    "INSERT INTO {$prefix}comments (post_id, author, author_email, author_ip, content, status) VALUES (?, ?, ?, ?, ?, 'pending')",
                    [(int)($post['id'] ?? 0), $name, (string)$email, $_SERVER['REMOTE_ADDR'] ?? '', $text]
                );
                header('Location: ' . htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_QUOTES) . '?commented=1#comments');
                exit;
            } catch (\Throwable $ex) { $commentError = 'Fehler beim Speichern des Kommentars.'; }
        }
    }
}
try { $csrfToken = \CMS\Security::instance()->generateToken('comment_post_' . ($post['id'] ?? 0)); } catch (\Throwable $e) { $csrfToken = ''; }

$readTime = function_exists('phinit_reading_time')
    ? phinit_reading_time($content, $readingTimeWpm)
    : max(1, (int)ceil(str_word_count(strip_tags($content)) / $readingTimeWpm));
?>

<div class="container post-container">
<div class="content-layout">

    <!-- ── Haupt-Artikelspalte ────────────────────────────────────────── -->
    <div class="main-column">
        <article itemscope itemtype="https://schema.org/TechArticle">

            <!-- Post-Header -->
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
                        <?php if ($techTime): ?>
                        <span>🕐 Dauer: <?php echo htmlspecialchars($techTime, ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </header>

            <!-- ── Tech-Karte ────────────────────────────────────────── -->
            <?php if ($showTechCard && $hasTechData): ?>
            <div class="tech-card" data-anim data-anim-delay="1" role="complementary" aria-label="Technische Informationen">
                <div class="tech-card__header">
                    <span class="tech-card__icon" aria-hidden="true">⚙️</span>
                    <span class="tech-card__title"><?php echo htmlspecialchars($techCardHeader, ENT_QUOTES); ?></span>
                    <?php if ($diffInfo): ?>
                    <span class="badge tech-card__diff <?php echo htmlspecialchars($diffInfo['class'], ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($diffInfo['label'], ENT_QUOTES); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <dl class="tech-card__grid">
                    <?php if ($techOs): ?>
                    <div class="tech-card__item">
                        <dt>Betriebssystem</dt>
                        <dd><?php echo htmlspecialchars($techOs, ENT_QUOTES); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($techVersion): ?>
                    <div class="tech-card__item">
                        <dt>Version</dt>
                        <dd><code class="inline-code"><?php echo htmlspecialchars($techVersion, ENT_QUOTES); ?></code></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($techTested): ?>
                    <div class="tech-card__item">
                        <dt>Zuletzt getestet</dt>
                        <dd><?php echo htmlspecialchars(date('F Y', strtotime($techTested)), ENT_QUOTES); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($readTime): ?>
                    <div class="tech-card__item">
                        <dt>Lesezeit</dt>
                        <dd><?php echo $readTime; ?> Min.</dd>
                    </div>
                    <?php endif; ?>
                </dl>
                <?php if (!empty($techPrereqs)): ?>
                <div class="tech-card__prereqs">
                    <strong>⚠️ Voraussetzungen:</strong>
                    <ul>
                        <?php foreach ($techPrereqs as $prereq): ?>
                        <li><?php echo htmlspecialchars((string)$prereq, ENT_QUOTES); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Artikel-Body -->
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
                    <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer">in LinkedIn</a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer">𝕏 Twitter</a>
                    <a href="mailto:?subject=<?php echo $postTitle; ?>&body=<?php echo $postUrl; ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
                    <button class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Vor-/Nächster Artikel -->
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

            <!-- Kommentare -->
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
                    <p class="comment-empty-state">Noch keine Kommentare. Sei der Erste!</p>
                <?php endif; ?>

                <?php if ($commentError): ?>
                <div class="alert-box alert-box--error">
                    ❌ <?php echo htmlspecialchars($commentError, ENT_QUOTES); ?>
                </div>
                <?php endif; ?>

                <div class="comment-form-wrap">
                    <h4><?php echo htmlspecialchars($commentFormHeader, ENT_QUOTES); ?></h4>
                    <form method="POST" action="#comments" novalidate>
                        <input type="hidden" name="submit_comment" value="1">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                        <input type="hidden" name="post_id"    value="<?php echo (int)($post['id'] ?? 0); ?>">
                        <div class="form-group form-group--spaced">
                            <label for="comment_text">Kommentar <span class="field-required">*</span></label>
                            <textarea id="comment_text" name="comment_text" class="form-control" required placeholder="Dein Kommentar …" rows="4"></textarea>
                        </div>
                        <div class="form-row form-row--spaced">
                            <div class="form-group">
                                <label for="comment_name">Name <span class="field-required">*</span></label>
                                <input type="text" id="comment_name" name="comment_name" class="form-control" required placeholder="Dein Name">
                            </div>
                            <div class="form-group">
                                <label for="comment_email">E-Mail <span class="field-required">*</span></label>
                                <input type="email" id="comment_email" name="comment_email" class="form-control" required placeholder="dein@email.de">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Kommentar abschicken</button>
                    </form>
                </div>
            </section>
            <?php endif; ?>

        </article>
    </div><!-- /.main-column -->

    <!-- ── Sidebar ────────────────────────────────────────────────────── -->
    <aside class="sidebar" aria-label="Seitenleiste">

        <!-- TOC -->
        <?php if (!empty($tocItems)): ?>
        <div class="toc">
            <div class="toc-title">📋 <?php echo htmlspecialchars($tocHeaderText, ENT_QUOTES); ?></div>
            <ul class="toc-list" role="list">
                <?php foreach ($tocItems as $item): ?>
                <li class="<?php echo $item['level'] === 3 ? 'toc-h3' : ''; ?>">
                    <a href="#<?php echo htmlspecialchars($item['id'], ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($item['text'], ENT_QUOTES); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Tech-Sidebar: Kompakte Versionsinformation (wenn vorhanden) -->
        <?php if ($techOs || $techVersion): ?>
        <div class="toc tech-sidebar-info">
            <div class="toc-title">🖥️ Umgebung</div>
            <ul class="toc-list toc-list--plain">
                <?php if ($techOs): ?><li><strong>OS:</strong> <?php echo htmlspecialchars($techOs, ENT_QUOTES); ?></li><?php endif; ?>
                <?php if ($techVersion): ?><li><strong>Version:</strong> <code class="inline-code"><?php echo htmlspecialchars($techVersion, ENT_QUOTES); ?></code></li><?php endif; ?>
                <?php if ($techTested): ?><li><strong>Getestet:</strong> <?php echo htmlspecialchars(date('M Y', strtotime($techTested)), ENT_QUOTES); ?></li><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Kategorien-Widget -->
        <?php
        $catRows = [];
        try {
            $catRows = $db->get_results(
                "SELECT c.name, c.slug, COUNT(pc.post_id) AS cnt
                   FROM {$pfx}categories c
                   LEFT JOIN {$pfx}post_categories pc ON pc.category_id = c.id
                   GROUP BY c.id ORDER BY c.name ASC"
            ) ?: [];
        } catch (\Throwable) {}
        ?>
        <?php if (!empty($catRows)): ?>
        <div class="toc toc--accent">
            <div class="toc-title">🗂 Kategorien</div>
            <ul class="toc-list" role="list">
                <?php foreach ($catRows as $cat): ?>
                <li>
                    <a href="<?php echo htmlspecialchars(SITE_URL . '/kategorie/' . ($cat['slug'] ?? ''), ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($cat['name'] ?? '', ENT_QUOTES); ?>
                        <span class="toc-muted-count">(<?php echo (int)($cat['cnt'] ?? 0); ?>)</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Social-Widget -->
        <?php
        $socialLinks = [
            ['key' => 'social_linkedin',  'icon' => 'in', 'label' => $czGet('social', 'social_label_linkedin', 'LinkedIn')],
            ['key' => 'social_github',    'icon' => '🐙', 'label' => $czGet('social', 'social_label_github', 'GitHub')],
            ['key' => 'social_twitter',   'icon' => '𝕏',  'label' => 'Twitter / X'],
            ['key' => 'social_mastodon',  'icon' => '🐘', 'label' => 'Mastodon'],
            ['key' => 'social_youtube',   'icon' => '▶',  'label' => 'YouTube'],
            ['key' => 'social_xing',      'icon' => 'X',  'label' => 'XING'],
            ['key' => 'social_rss',       'icon' => '📡', 'label' => $czGet('social', 'social_label_rss', 'RSS-Feed')],
        ];
        $activeSocial = array_filter($socialLinks, fn($s) => !empty($czGet('social', $s['key'], '')));
        ?>
        <?php if (!empty($activeSocial)): ?>
        <div class="toc">
            <div class="toc-title">🌐 <?php echo htmlspecialchars($czGet('posts', 'sidebar_social_header', 'Folge mir'), ENT_QUOTES); ?></div>
            <ul class="toc-list social-sidebar-list" role="list">
                <?php foreach ($activeSocial as $s): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($czGet('social', $s['key'], ''), ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon"><?php echo $s['icon']; ?></span> <?php echo htmlspecialchars($s['label'], ENT_QUOTES); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

    </aside>
</div><!-- /.content-layout -->
</div><!-- /.container -->
