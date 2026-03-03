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

// ── TOC + CSRF + Kommentar-Handler ────────────────────────────────────
$tocItems = [];
$content  = $post['content'] ?? '';
preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/i', $content, $m, PREG_SET_ORDER);
foreach ($m as $match) {
    $tocItems[] = ['level' => (int)$match[1], 'id' => $match[2], 'text' => strip_tags($match[3])];
}

try {
    $csrfToken = \CMS\Security::instance()->generateToken('comment_post_' . ($post['id'] ?? 0));
} catch (\Throwable $e) { $csrfToken = ''; }

$commentError = $commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!empty($csrfToken) && !\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $name  = htmlspecialchars(trim($_POST['comment_name']  ?? ''), ENT_QUOTES);
        $email = filter_var(trim($_POST['comment_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $text  = htmlspecialchars(trim($_POST['comment_text'] ?? ''), ENT_QUOTES);
        if (empty($name) || !$email || empty($text)) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $postService->addComment((int)($post['id'] ?? 0), ['name' => $name, 'email' => (string)$email, 'text' => $text]);
                header('Location: ' . htmlspecialchars($siteUrl . '/' . ($post['slug'] ?? ''), ENT_QUOTES) . '?commented=1');
                exit;
            } catch (\Throwable $ex) { $commentError = 'Fehler beim Speichern des Kommentars.'; }
        }
    }
}

$readTime = !empty($post['read_time']) ? (int)$post['read_time']
          : (function_exists('phinit_reading_time') ? phinit_reading_time($content) : 0);
?>

<div class="container" style="padding-top:28px;padding-bottom:40px;">
<div class="content-layout">

    <!-- ── Haupt-Artikelspalte ────────────────────────────────────────── -->
    <div class="main-column">
        <article itemscope itemtype="https://schema.org/TechArticle">

            <!-- Post-Header -->
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
                        <?php if ($readTime): ?>
                        <span>⏱ <?php echo $readTime; ?> Min. Lesezeit</span>
                        <?php endif; ?>
                        <?php if ($techTime): ?>
                        <span>🕐 Dauer: <?php echo htmlspecialchars($techTime, ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- ── Tech-Karte ────────────────────────────────────────── -->
            <?php if ($hasTechData): ?>
            <div class="tech-card" data-anim data-anim-delay="1" role="complementary" aria-label="Technische Informationen">
                <div class="tech-card__header">
                    <span class="tech-card__icon" aria-hidden="true">⚙️</span>
                    <span class="tech-card__title">Technische Details</span>
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
            <div class="post-body" itemprop="articleBody" data-anim data-anim-delay="2">
                <?php echo $content; ?>

                <!-- Share-Buttons -->
                <div class="post-share">
                    <span>Teilen:</span>
                    <?php
                    $postUrl   = htmlspecialchars(urlencode($siteUrl . '/' . ($post['slug'] ?? '')), ENT_QUOTES);
                    $postTitle = htmlspecialchars(urlencode($post['title'] ?? ''), ENT_QUOTES);
                    ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postUrl; ?>" class="share-btn fb" target="_blank" rel="noopener noreferrer">f Facebook</a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer">𝕏 Twitter</a>
                    <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer">in LinkedIn</a>
                    <button class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
                </div>
            </div>

            <!-- Vor-/Nächster Artikel -->
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

            <!-- Kommentare -->
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
    </div><!-- /.main-column -->

    <!-- ── Sidebar ────────────────────────────────────────────────────── -->
    <aside class="sidebar" aria-label="Seitenleiste">

        <!-- TOC -->
        <?php if (!empty($tocItems)): ?>
        <div class="toc">
            <div class="toc-title">📋 Inhaltsverzeichnis</div>
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
            <ul class="toc-list" style="list-style:none;padding-left:0;">
                <?php if ($techOs): ?><li><strong>OS:</strong> <?php echo htmlspecialchars($techOs, ENT_QUOTES); ?></li><?php endif; ?>
                <?php if ($techVersion): ?><li><strong>Version:</strong> <code class="inline-code"><?php echo htmlspecialchars($techVersion, ENT_QUOTES); ?></code></li><?php endif; ?>
                <?php if ($techTested): ?><li><strong>Getestet:</strong> <?php echo htmlspecialchars(date('M Y', strtotime($techTested)), ENT_QUOTES); ?></li><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Kategorien-Widget -->
        <div class="toc" style="border-left-color:var(--accent-color);">
            <div class="toc-title">🗂 Kategorien</div>
            <ul class="toc-list" role="list">
                <li><a href="<?php echo htmlspecialchars(SITE_URL, ENT_QUOTES); ?>/microsoft-365">Microsoft 365</a></li>
                <li><a href="<?php echo htmlspecialchars(SITE_URL, ENT_QUOTES); ?>/powershell">PowerShell</a></li>
                <li><a href="<?php echo htmlspecialchars(SITE_URL, ENT_QUOTES); ?>/linux">Linux & BASH</a></li>
                <li><a href="<?php echo htmlspecialchars(SITE_URL, ENT_QUOTES); ?>/intune">Intune & MDM</a></li>
                <li><a href="<?php echo htmlspecialchars(SITE_URL, ENT_QUOTES); ?>/datenschutz">Datenschutz</a></li>
            </ul>
        </div>

    </aside>
</div><!-- /.content-layout -->
</div><!-- /.container -->
