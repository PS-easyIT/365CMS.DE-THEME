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
    $postProvidedByRouter = true;
} else {
    $postProvidedByRouter = false;
    $rawPath = (string) preg_replace('#^/blog/#i', '', phinit_current_request_path());
    $slug = trim($rawPath, '/');

    if ($slug === '') {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }

    try {
        $postObj = $db->get_row(
            "SELECT p.*, COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name, c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}users u ON u.id = p.author_id
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND " . phinit_post_publication_where('p'),
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

if (!$postProvidedByRouter && !empty($post['content'])) {
    $post['content'] = phinit_prepare_renderable_content((string) $post['content'], 'post', (int) ($post['id'] ?? 0));
}

if (!$postProvidedByRouter) {
    try {
        $db->execute("UPDATE {$prefix}posts SET views = views + 1 WHERE id = ?", [(int) ($post['id'] ?? 0)]);
    } catch (\Throwable) {
    }
}

try {
    $prevOb = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts WHERE " . phinit_post_publication_where() . " AND COALESCE(published_at, created_at) < ? AND id != ? ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT 1",
        [$post['published_at'] ?? ($post['created_at'] ?? '9999-12-31'), (int) ($post['id'] ?? 0)]
    );
    $nextOb = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts WHERE " . phinit_post_publication_where() . " AND COALESCE(published_at, created_at) > ? AND id != ? ORDER BY COALESCE(published_at, created_at) ASC, id ASC LIMIT 1",
        [$post['published_at'] ?? ($post['created_at'] ?? '0001-01-01'), (int) ($post['id'] ?? 0)]
    );
    $prevPost = $prevOb ? (array) $prevOb : null;
    $nextPost = $nextOb ? (array) $nextOb : null;
} catch (\Throwable) {
    $prevPost = null;
    $nextPost = null;
}

try {
    $commentRows = \CMS\Services\CommentService::getInstance()->getApprovedForPost((int) ($post['id'] ?? 0));
    $comments = array_map(static fn($comment) => (array) $comment, $commentRows);
    $commentCount = count($comments);
} catch (\Throwable) {
    $comments = [];
    $commentCount = 0;
}

$meta = function_exists('phinit_decode_post_template_meta') ? phinit_decode_post_template_meta($post) : (is_array($post['meta'] ?? null) ? $post['meta'] : []);
$post['meta'] = $meta;
$techOs = (string) ($meta['os'] ?? $meta['tool'] ?? '');
$techVersion = (string) ($meta['version'] ?? '');
$techTested = (string) ($meta['last_tested'] ?? '');
$techDiff = (string) ($meta['difficulty'] ?? '');
$techPrereqs = is_array($meta['prerequisites'] ?? null) ? $meta['prerequisites'] : [];
$techTime = (string) ($meta['time_needed'] ?? '');
$hasTechData = $techOs !== '' || $techVersion !== '' || !empty($techPrereqs) || $techDiff !== '' || trim((string) ($meta['website_url'] ?? '')) !== '' || trim((string) ($meta['github_url'] ?? '')) !== '';

$diffLabels = [
    'beginner' => ['label' => 'Einsteiger', 'class' => 'badge--green'],
    'intermediate' => ['label' => 'Fortgeschritten', 'class' => 'badge--yellow'],
    'advanced' => ['label' => 'Experte', 'class' => 'badge--orange'],
    'expert' => ['label' => 'Profi', 'class' => 'badge--red'],
];
$diffInfo = $diffLabels[$techDiff] ?? null;

$tocItems = [];
$content = phinit_sanitize_renderable_content((string) ($post['content'] ?? ''), 'default');
$headingData = phinit_with_heading_ids($content, [2, 3, 4, 5, 6]);
$content = $headingData['html'];
$post['content'] = $content;

if ($showToc) {
    $tocItems = $headingData['toc'];

    if (count($tocItems) < $tocMinHeadings) {
        $tocItems = [];
    }
}

$content = phinit_enhance_content_images($content);

$commentError = '';
$commentSuccess = '';

if (phinit_input_int($_GET, 'commented', 0, 0, 1) === 1) {
    $commentSuccess = '✅ Danke! Dein Kommentar wurde gespeichert und wartet auf Freigabe.';
}

$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$publishedAt = (string) ($post['published_at'] ?? '');
$updatedAt = (string) ($post['updated_at'] ?? '');
$showUpdatedBadge = $updatedAt !== '' && $updatedAt !== $publishedAt;
$authorId = (int) ($post['author_id'] ?? 0);
$authorBoxUrl = $authorId > 0
    ? (function_exists('phinit_localized_href') ? phinit_localized_href('/author/user-' . $authorId, $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/author/user-' . $authorId)
    : '';
$authorBoxContext = function_exists('phinit_build_author_box_context')
    ? phinit_build_author_box_context([
        'category' => 'posts',
        'show_key' => 'show_author_box',
        'entity_name' => (string) ($post['author_name'] ?? ''),
        'author_url' => $authorBoxUrl,
        'site_url' => $siteUrl,
        'locale' => $currentLocale,
    ])
    : [];

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
    'url' => (string) (parse_url(function_exists('phinit_build_post_url') ? phinit_build_post_url($post, $currentLocale) : ('/blog/' . rawurlencode((string) ($post['slug'] ?? ''))), PHP_URL_PATH) ?: '/'),
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

            <div class="post-body" itemprop="articleBody" data-photoswipe data-anim data-anim-delay="2">
                <?php phinit_render_prepared_content($content); ?>

                <?php if ($showUpdatedBadge): ?>
                <div class="post-footer-meta" aria-label="Beitragsmetadaten">
                    <span class="post-footer-badge post-footer-badge--updated">
                        <span class="post-footer-badge__icon" aria-hidden="true">🔄</span>
                        <span class="post-footer-badge__label"><?php echo htmlspecialchars(phinit_t('updated_label', [], $currentLocale), ENT_QUOTES); ?></span>
                        <time datetime="<?php echo htmlspecialchars($updatedAt, ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars(phinit_format_date($updatedAt, 'numeric', $currentLocale), ENT_QUOTES); ?>
                        </time>
                    </span>
                </div>
                <?php endif; ?>

                <?php if ($showShareButtons): ?>
                <div class="post-share">
                    <span>Teilen:</span>
                    <?php
                    $shareUrl = function_exists('phinit_build_post_url') ? phinit_build_post_url($post, $currentLocale) : ($siteUrl . '/blog/' . ($post['slug'] ?? ''));
                    $shareTitle = trim((string) ($post['title'] ?? ''));
                    $linkedinShareHref = 'https://www.linkedin.com/shareArticle?' . http_build_query([
                        'url' => $shareUrl,
                        'title' => $shareTitle,
                    ], '', '&', PHP_QUERY_RFC3986);
                    $twitterShareHref = 'https://twitter.com/intent/tweet?' . http_build_query([
                        'url' => $shareUrl,
                        'text' => $shareTitle,
                    ], '', '&', PHP_QUERY_RFC3986);
                    $emailShareHref = 'mailto:?' . http_build_query([
                        'subject' => $shareTitle,
                        'body' => $shareUrl,
                    ], '', '&', PHP_QUERY_RFC3986);
                    ?>
                    <a href="<?php echo htmlspecialchars($linkedinShareHref, ENT_QUOTES); ?>" class="share-btn li" target="_blank" rel="noopener noreferrer">in LinkedIn</a>
                    <a href="<?php echo htmlspecialchars($twitterShareHref, ENT_QUOTES); ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer">𝕏 Twitter</a>
                    <a href="<?php echo htmlspecialchars($emailShareHref, ENT_QUOTES); ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
                    <button type="button" class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
                </div>
                <?php endif; ?>
            </div>

            <?php include __DIR__ . '/partials/post-navigation.php'; ?>
            <?php if (!empty($authorBoxContext['show'])): ?>
            <?php get_theme_part('partials/post-author-box', $authorBoxContext); ?>
            <?php endif; ?>
            <?php include __DIR__ . '/partials/post-comments.php'; ?>
        </article>
    </div>

    <aside class="sidebar" aria-label="Seitenleiste">
        <?php get_theme_part('partials/post-template-meta-card', ['post' => $post]); ?>
        <?php include __DIR__ . '/partials/post-sidebar-toc.php'; ?>

        <?php if (!empty($catRows)): ?>
        <div class="toc toc--accent">
            <div class="toc-title">🗂 Kategorien</div>
            <ul class="toc-list" role="list">
                <?php foreach ($catRows as $cat): ?>
                <li>
                    <a href="<?php echo htmlspecialchars(function_exists('cms_get_archive_url') ? cms_get_archive_url('category', (string) ($cat['slug'] ?? ''), function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de') : SITE_URL . '/kategorie/' . ($cat['slug'] ?? ''), ENT_QUOTES); ?>">
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
