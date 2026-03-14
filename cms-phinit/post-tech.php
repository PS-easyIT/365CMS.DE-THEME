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

try {
    $cz = \CMS\Services\ThemeCustomizer::instance();
} catch (\Throwable) {
    $cz = null;
}

$czGet = function (string $cat, string $key, mixed $default = '') use ($cz): mixed {
    if (!$cz) {
        return $default;
    }

    try {
        return $cz->get($cat, $key, $default);
    } catch (\Throwable) {
        return $default;
    }
};

$czBool = function (string $cat, string $key, bool $default = true) use ($czGet): bool {
    return filter_var($czGet($cat, $key, $default), FILTER_VALIDATE_BOOLEAN);
};

$showPostHero      = $czBool('posts', 'show_post_hero', true);
$showPostMeta      = $czBool('posts', 'show_post_meta', true);
$showReadingTime   = $czBool('posts', 'show_reading_time', true);
$readingTimeWpm    = max(50, (int) $czGet('posts', 'reading_time_wpm', 200));
$showToc           = $czBool('posts', 'show_toc', true);
$tocMinHeadings    = max(1, (int) $czGet('posts', 'toc_min_headings', 2));
$tocHeaderText     = '📋 ' . (string) $czGet('posts', 'toc_header_text', 'Inhaltsverzeichnis');
$showTechCard      = $czBool('posts', 'show_tech_card', true);
$techCardHeader    = (string) $czGet('posts', 'tech_card_header', 'Technische Details');
$showShareButtons  = $czBool('posts', 'show_share_buttons', true);
$showComments      = $czBool('posts', 'show_comments', true);
$commentsHeader    = (string) $czGet('posts', 'comments_header', '💬 Kommentare');
$commentFormHeader = (string) $czGet('posts', 'comment_form_header', 'Kommentar hinterlassen');
$socialHeader      = (string) $czGet('posts', 'sidebar_social_header', 'Folge mir');

if (isset($post) && !empty($post)) {
    $post = is_object($post) ? (array) $post : (array) $post;
} else {
    $rawPath = (string) preg_replace('#^/blog/#i', '', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $slug = trim($rawPath, '/');

    if ($slug === '') {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }

    try {
        $postObj = $db->get_row(
            "SELECT p.*, u.display_name AS author_name, c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}users u ON u.id = p.author_id
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'",
            [$slug]
        );
        $post = $postObj ? (array) $postObj : null;
    } catch (\Throwable) {
        $post = null;
    }
}

if (!$post) {
    http_response_code(404);
    get_theme_part('404');
    exit;
}

try {
    $db->execute("UPDATE {$prefix}posts SET views = views + 1 WHERE id = ?", [(int) ($post['id'] ?? 0)]);
} catch (\Throwable) {
}

try {
    $prevOb = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts WHERE status = 'published' AND published_at < ? AND id != ? ORDER BY published_at DESC LIMIT 1",
        [$post['published_at'] ?? '9999-12-31', (int) ($post['id'] ?? 0)]
    );
    $nextOb = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts WHERE status = 'published' AND published_at > ? AND id != ? ORDER BY published_at ASC LIMIT 1",
        [$post['published_at'] ?? '0001-01-01', (int) ($post['id'] ?? 0)]
    );
    $prevPost = $prevOb ? (array) $prevOb : null;
    $nextPost = $nextOb ? (array) $nextOb : null;
} catch (\Throwable) {
    $prevPost = null;
    $nextPost = null;
}

try {
    $commentRows = $db->get_results(
        "SELECT id, author, content, post_date FROM {$prefix}comments WHERE post_id = ? AND status = 'approved' ORDER BY post_date ASC",
        [(int) ($post['id'] ?? 0)]
    ) ?: [];
    $comments = array_map(static fn($comment) => (array) $comment, $commentRows);
    $commentCount = count($comments);
} catch (\Throwable) {
    $comments = [];
    $commentCount = 0;
}

$meta = is_array($post['meta'] ?? null) ? $post['meta'] : [];
$techOs = (string) ($meta['os'] ?? '');
$techVersion = (string) ($meta['version'] ?? '');
$techTested = (string) ($meta['last_tested'] ?? '');
$techDiff = (string) ($meta['difficulty'] ?? '');
$techPrereqs = is_array($meta['prerequisites'] ?? null) ? $meta['prerequisites'] : [];
$techTime = (string) ($meta['time_needed'] ?? '');
$hasTechData = $techOs !== '' || $techVersion !== '' || !empty($techPrereqs) || $techDiff !== '';

$diffLabels = [
    'beginner' => ['label' => 'Einsteiger', 'class' => 'badge--green'],
    'intermediate' => ['label' => 'Fortgeschritten', 'class' => 'badge--yellow'],
    'advanced' => ['label' => 'Experte', 'class' => 'badge--orange'],
    'expert' => ['label' => 'Profi', 'class' => 'badge--red'],
];
$diffInfo = $diffLabels[$techDiff] ?? null;

$tocItems = [];
$content = (string) ($post['content'] ?? '');
$usedSlugs = [];
$content = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h\1>/si', function ($matches) use (&$usedSlugs) {
    $tag = $matches[1];
    $attrs = $matches[2];
    $inner = $matches[3];

    if (preg_match('/\bid=["\']([^"\']+)["\']/i', $attrs)) {
        return $matches[0];
    }

    $text = phinit_display_text(strip_tags($inner));
    $slug = mb_strtolower($text, 'UTF-8');
    $slug = preg_replace('/[äÄ]/', 'ae', $slug);
    $slug = preg_replace('/[öÖ]/', 'oe', $slug);
    $slug = preg_replace('/[üÜ]/', 'ue', $slug);
    $slug = preg_replace('/ß/', 'ss', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim((string) $slug, '-') ?: 'heading';
    $base = $slug;
    $i = 2;

    while (in_array($slug, $usedSlugs, true)) {
        $slug = $base . '-' . $i++;
    }

    $usedSlugs[] = $slug;

    return "<h{$tag}{$attrs} id=\"{$slug}\">{$inner}</h{$tag}>";
}, $content) ?? $content;

$post['content'] = $content;

if ($showToc) {
    preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/si', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $tocItems[] = [
            'level' => (int) $match[1],
            'id' => $match[2],
            'text' => phinit_display_text(strip_tags($match[3])),
        ];
    }

    if (count($tocItems) < $tocMinHeadings) {
        $tocItems = [];
    }
}

$content = phinit_enhance_content_images($content);

$commentError = '';
$commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $honeypot = trim((string) ($_POST['comment_hp'] ?? ''));

    if ($honeypot !== '') {
        header('Location: ' . $siteUrl . '/blog/' . rawurlencode((string) ($post['slug'] ?? '')) . '?commented=1#comments');
        exit;
    }

    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $commentUser = \CMS\Auth::isLoggedIn() ? \CMS\Auth::getCurrentUser() : null;
        $commentUserId = !empty($commentUser->id) ? (int) $commentUser->id : null;
        $name = trim((string) ($_POST['comment_name'] ?? ''));
        $emailRaw = trim((string) ($_POST['comment_email'] ?? ''));
        $email = $commentUserId ? $emailRaw : filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
        $text = trim((string) ($_POST['comment_text'] ?? ''));

        if ($text === '' || ($commentUserId === null && ($name === '' || !$email))) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $newId = \CMS\Services\CommentService::getInstance()->createPendingComment(
                    (int) ($post['id'] ?? 0),
                    $name,
                    (string) $email,
                    $text,
                    (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
                    $commentUserId
                );
                if ($newId === false) {
                    $commentError = 'Bitte alle Pflichtfelder korrekt ausfüllen.';
                } else {
                    header('Location: ' . $siteUrl . '/blog/' . rawurlencode((string) ($post['slug'] ?? '')) . '?commented=1#comments');
                    exit;
                }
            } catch (\Throwable) {
                $commentError = 'Fehler beim Speichern des Kommentars.';
            }
        }
    }
}

if ((int) ($_GET['commented'] ?? 0) === 1) {
    $commentSuccess = '✅ Danke! Dein Kommentar wurde gespeichert und wartet auf Freigabe.';
}

try {
    $csrfToken = \CMS\Security::instance()->generateToken('comment_' . ($post['id'] ?? 0));
} catch (\Throwable) {
    $csrfToken = '';
}

$readTime = function_exists('phinit_reading_time')
    ? phinit_reading_time($content, $readingTimeWpm)
    : max(1, (int) ceil(str_word_count(strip_tags($content)) / $readingTimeWpm));
$readingTime = $readTime;
$favoriteControl = phinit_get_favorite_control('post', (int) ($post['id'] ?? 0), [
    'title' => (string) ($post['title'] ?? 'Beitrag'),
    'url' => '/blog/' . rawurlencode((string) ($post['slug'] ?? '')),
    'excerpt' => trim((string) ($post['excerpt'] ?? '')),
    'featured_image' => (string) ($post['featured_image'] ?? ''),
    'badge' => (string) ($post['category_name'] ?? 'Beitrag'),
]);
$extraMetaItems = $techTime !== '' ? ['🕐 Dauer: ' . $techTime] : [];

$socialLinks = [
    ['key' => 'social_linkedin', 'icon' => 'in', 'label' => $czGet('social', 'social_label_linkedin', 'LinkedIn')],
    ['key' => 'social_github', 'icon' => '🐙', 'label' => $czGet('social', 'social_label_github', 'GitHub')],
    ['key' => 'social_twitter', 'icon' => '𝕏', 'label' => 'Twitter / X'],
    ['key' => 'social_mastodon', 'icon' => '🐘', 'label' => 'Mastodon'],
    ['key' => 'social_youtube', 'icon' => '▶', 'label' => 'YouTube'],
    ['key' => 'social_xing', 'icon' => 'X', 'label' => 'XING'],
    ['key' => 'social_rss', 'icon' => '📡', 'label' => $czGet('social', 'social_label_rss', 'RSS-Feed')],
];
$activeSocial = array_filter(
    $socialLinks,
    fn(array $socialLink): bool => !empty($czGet('social', (string) $socialLink['key'], ''))
);

$catRows = [];
try {
    $catRows = $db->get_results(
        "SELECT c.name, c.slug, COUNT(pc.post_id) AS cnt
           FROM {$prefix}categories c
           LEFT JOIN {$prefix}post_categories pc ON pc.category_id = c.id
           GROUP BY c.id ORDER BY c.name ASC"
    ) ?: [];
} catch (\Throwable) {
}
?>

<div class="container post-container">
<div class="content-layout">

    <div class="main-column">
        <article itemscope itemtype="https://schema.org/TechArticle">
            <?php include __DIR__ . '/partials/post-header.php'; ?>
            <?php include __DIR__ . '/partials/post-tech-card.php'; ?>

            <div class="post-body" itemprop="articleBody" data-photoswipe data-anim data-anim-delay="2">
                <?php echo $content; ?>

                <?php if ($showShareButtons): ?>
                <div class="post-share">
                    <span>Teilen:</span>
                    <?php
                    $postUrl = htmlspecialchars(urlencode($siteUrl . '/blog/' . ($post['slug'] ?? '')), ENT_QUOTES);
                    $postTitle = htmlspecialchars(urlencode($post['title'] ?? ''), ENT_QUOTES);
                    ?>
                    <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer">in LinkedIn</a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer">𝕏 Twitter</a>
                    <a href="mailto:?subject=<?php echo $postTitle; ?>&body=<?php echo $postUrl; ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
                    <button class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
                </div>
                <?php endif; ?>
            </div>

            <?php include __DIR__ . '/partials/post-navigation.php'; ?>
            <?php include __DIR__ . '/partials/post-comments.php'; ?>
        </article>
    </div>

    <aside class="sidebar" aria-label="Seitenleiste">
        <?php include __DIR__ . '/partials/post-sidebar-toc.php'; ?>
        <?php include __DIR__ . '/partials/post-tech-sidebar-info.php'; ?>

        <?php if (!empty($catRows)): ?>
        <div class="toc toc--accent">
            <div class="toc-title">🗂 Kategorien</div>
            <ul class="toc-list" role="list">
                <?php foreach ($catRows as $cat): ?>
                <li>
                    <a href="<?php echo htmlspecialchars(SITE_URL . '/kategorie/' . ($cat['slug'] ?? ''), ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars((string) ($cat['name'] ?? ''), ENT_QUOTES); ?>
                        <span class="toc-muted-count">(<?php echo (int) ($cat['cnt'] ?? 0); ?>)</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php include __DIR__ . '/partials/post-sidebar-social.php'; ?>
    </aside>
</div><!-- /.content-layout -->
</div><!-- /.container -->
