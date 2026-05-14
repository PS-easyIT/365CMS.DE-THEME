<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Head_Trait
{
    private function getBreadcrumbLocale(): string
    {
        $path = $this->getHeadRequestPath();

        if (function_exists('phinit_resolve_request_context')) {
            $context = phinit_resolve_request_context($path);
            $locale = strtolower(trim((string) ($context['locale'] ?? 'de')));

            if ($locale !== '') {
                return $locale;
            }
        }

        return function_exists('phinit_get_current_locale')
            ? (string) phinit_get_current_locale()
            : 'de';
    }

    private function buildAbsoluteLocalizedUrl(string $path, string $locale): string
    {
        $siteUrl = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        $localizedPath = function_exists('phinit_localized_path')
            ? phinit_localized_path($path, $locale)
            : $path;

        $localizedPath = trim($localizedPath) !== '' ? $localizedPath : '/';

        if ($siteUrl === '') {
            return $localizedPath;
        }

        return $siteUrl . $localizedPath;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildBreadcrumbSchemaData(): ?array
    {
        $settings = $this->getHeadCustomizerSettings();
        if (!$settings['breadcrumb_schema']) {
            return null;
        }

        $locale = $this->getBreadcrumbLocale();
        $homeLabel = function_exists('phinit_t') ? phinit_t('home', [], $locale) : ($locale === 'en' ? 'Home' : 'Startseite');
        $homeUrl = $this->buildAbsoluteLocalizedUrl('/', $locale);
        $currentPost = $this->getCurrentHeadPost();

        if (is_array($currentPost)) {
            if (!$settings['breadcrumb_on_posts']) {
                return null;
            }

            $postTitle = phinit_display_text((string) ($currentPost['title'] ?? ''));
            if ($postTitle === '') {
                return null;
            }

            return [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $homeLabel,
                        'item' => $homeUrl,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Blog',
                        'item' => $this->buildAbsoluteLocalizedUrl('/blog', $locale),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $postTitle,
                    ],
                ],
            ];
        }

        $currentPage = $this->getCurrentHeadPage();
        if (!is_array($currentPage) || !$settings['breadcrumb_on_pages']) {
            return null;
        }

        $pageTitle = phinit_display_text((string) ($currentPage['title'] ?? ''));
        if ($pageTitle === '') {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => $homeLabel,
                    'item' => $homeUrl,
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $pageTitle,
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function outputJsonLd(array $schema): void
    {
        echo '<script type="application/ld+json">'
            . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>' . "\n";
    }

    private function getHeadRequestPath(): string
    {
        if (method_exists($this, 'getRequestContext')) {
            $context = $this->getRequestContext();
            return (string) ($context['path'] ?? '/');
        }

        return phinit_current_request_path();
    }

    /**
     * @return array{
     *   meta_robots:string,
     *   canonical_self:bool,
     *   og_site_name:string,
     *   og_type_default:string,
     *   twitter_card_type:string,
     *   noindex_search:bool,
     *   noindex_404:bool,
     *   structured_data:bool,
     *   breadcrumb_schema:bool,
     *   show_breadcrumb:bool,
     *   breadcrumb_on_posts:bool,
     *   breadcrumb_on_pages:bool,
     *   og_default_image:string,
     *   primary_color:string,
     *   author_name:string,
     *   author_avatar_url:string
     * }
     */
    private function getHeadCustomizerSettings(): array
    {
        static $resolved = false;
        static $settings = [
            'meta_robots' => 'index,follow',
            'canonical_self' => true,
            'og_site_name' => '',
            'og_type_default' => 'website',
            'twitter_card_type' => 'summary_large_image',
            'noindex_search' => true,
            'noindex_404' => true,
            'structured_data' => true,
            'breadcrumb_schema' => true,
            'show_breadcrumb' => true,
            'breadcrumb_on_posts' => true,
            'breadcrumb_on_pages' => true,
            'og_default_image' => '',
            'primary_color' => '#1e3a5f',
            'author_name' => '',
            'author_avatar_url' => '',
        ];

        if ($resolved) {
            return $settings;
        }

        $resolved = true;

        try {
            $cz = \CMS\Services\ThemeCustomizer::instance();
        } catch (\Throwable) {
            return $settings;
        }

        try {
            $settings['meta_robots'] = (string) $cz->get('seo', 'meta_robots', $settings['meta_robots']);
            $settings['canonical_self'] = filter_var($cz->get('seo', 'canonical_self', $settings['canonical_self']), FILTER_VALIDATE_BOOLEAN);
            $settings['og_site_name'] = (string) $cz->get('seo', 'og_site_name', $settings['og_site_name']);
            $settings['og_type_default'] = (string) $cz->get('seo', 'og_type_default', $settings['og_type_default']);
            $settings['twitter_card_type'] = (string) $cz->get('seo', 'twitter_card_type', $settings['twitter_card_type']);
            $settings['noindex_search'] = filter_var($cz->get('seo', 'noindex_search', $settings['noindex_search']), FILTER_VALIDATE_BOOLEAN);
            $settings['noindex_404'] = filter_var($cz->get('seo', 'noindex_404', $settings['noindex_404']), FILTER_VALIDATE_BOOLEAN);
            $settings['structured_data'] = filter_var($cz->get('seo', 'structured_data', $settings['structured_data']), FILTER_VALIDATE_BOOLEAN);
            $settings['breadcrumb_schema'] = filter_var($cz->get('seo', 'breadcrumb_schema', $settings['breadcrumb_schema']), FILTER_VALIDATE_BOOLEAN);
            $settings['show_breadcrumb'] = filter_var($cz->get('layout', 'show_breadcrumb', $settings['show_breadcrumb']), FILTER_VALIDATE_BOOLEAN);
            $settings['breadcrumb_on_posts'] = filter_var($cz->get('layout', 'breadcrumb_on_posts', $settings['breadcrumb_on_posts']), FILTER_VALIDATE_BOOLEAN);
            $settings['breadcrumb_on_pages'] = filter_var($cz->get('layout', 'breadcrumb_on_pages', $settings['breadcrumb_on_pages']), FILTER_VALIDATE_BOOLEAN);
            $settings['og_default_image'] = (string) $cz->get('advanced', 'og_default_image', $settings['og_default_image']);
            $settings['primary_color'] = (string) $cz->get('colors', 'primary_color', $settings['primary_color']);
            $settings['author_name'] = (string) $cz->get('posts', 'author_name', $settings['author_name']);
            $settings['author_avatar_url'] = (string) $cz->get('posts', 'author_avatar_url', $settings['author_avatar_url']);
        } catch (\Throwable) {
        }

        return $settings;
    }

    private function buildHeadDescription(string $fallback, ?string $excerpt = null, ?string $content = null, int $limit = 200): string
    {
        $candidate = trim((string) $excerpt);
        if ($candidate === '') {
            $candidate = trim(strip_tags((string) $content));
        }

        if ($candidate === '') {
            $candidate = trim($fallback);
        }

        return mb_substr($candidate, 0, $limit);
    }

    private function resolveHeadPostSlug(?string $path = null): ?string
    {
        $resolvedPath = $path ?? $this->getHeadRequestPath();
        $postSlug = null;

        try {
            if (class_exists('CMS\Services\PermalinkService')) {
                $postSlug = \CMS\Services\PermalinkService::getInstance()->extractPostSlugFromPath($resolvedPath);
            }
        } catch (\Throwable) {
            $postSlug = null;
        }

        if (($postSlug === null || $postSlug === '') && preg_match('#^/blog/([\w-]+)$#', $resolvedPath, $matches) === 1) {
            $postSlug = (string) ($matches[1] ?? '');
        }

        if (!is_string($postSlug) || trim($postSlug) === '') {
            return null;
        }

        return trim($postSlug);
    }

    private function getCurrentHeadPost(): ?array
    {
        if ($this->currentHeadPostResolved) {
            return $this->currentHeadPostCache;
        }

        $this->currentHeadPostResolved = true;
        $this->currentHeadPostCache = null;

        $path = $this->getHeadRequestPath();
        $postSlug = $this->resolveHeadPostSlug($path);

        if (!is_string($postSlug) || trim($postSlug) === '') {
            return null;
        }

        $globalPost = $GLOBALS['post'] ?? null;
        if (is_object($globalPost)) {
            $globalPost = (array) $globalPost;
        }

        if (is_array($globalPost) && trim((string) ($globalPost['slug'] ?? '')) === $postSlug) {
            $this->currentHeadPostCache = [
                'slug' => (string) ($globalPost['slug'] ?? ''),
                'title' => (string) ($globalPost['title'] ?? ''),
                'excerpt' => (string) ($globalPost['excerpt'] ?? ''),
                'featured_image' => (string) ($globalPost['featured_image'] ?? ''),
                'published_at' => (string) ($globalPost['published_at'] ?? ''),
                'updated_at' => (string) ($globalPost['updated_at'] ?? ''),
                'author_name' => (string) ($globalPost['author_name'] ?? $globalPost['author_display_name'] ?? 'Autor'),
            ];

            return $this->currentHeadPostCache;
        }

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->prefix();
            $row = $db->get_row(
                "SELECT p.slug, p.title, p.excerpt, p.featured_image, p.published_at, p.updated_at,
                        COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}users u ON u.id = p.author_id
                 WHERE p.slug = ? AND " . phinit_post_publication_where('p') . " LIMIT 1",
                [$postSlug]
            );

            $this->currentHeadPostCache = $row ? (array) $row : null;
        } catch (\Throwable) {
            $this->currentHeadPostCache = null;
        }

        return $this->currentHeadPostCache;
    }

    private function getCurrentHeadPage(): ?array
    {
        static $resolved = false;
        static $cache = null;

        if ($resolved) {
            return $cache;
        }

        $resolved = true;
        $cache = null;

        $resolvedLocale = 'de';
        $basePath = '/';
        if (method_exists($this, 'getRequestContext')) {
            $context = $this->getRequestContext();
            if (empty($context['isPageDetail']) || !empty($context['isPost'])) {
                return null;
            }

            $basePath = trim((string) ($context['path'] ?? '/'));
            if (function_exists('phinit_resolve_request_context')) {
                $localizedContext = phinit_resolve_request_context($basePath);
                $basePath = trim((string) ($localizedContext['base_uri'] ?? $basePath));
                $resolvedLocale = strtolower(trim((string) ($localizedContext['locale'] ?? 'de')));
            }
        } else {
            $headPath = $this->getHeadRequestPath();
            if (function_exists('phinit_resolve_request_context')) {
                $localizedContext = phinit_resolve_request_context($headPath);
                $basePath = trim((string) ($localizedContext['base_uri'] ?? $headPath));
                $resolvedLocale = strtolower(trim((string) ($localizedContext['locale'] ?? 'de')));
            } else {
                $basePath = trim($headPath);
            }
        }

        $slug = trim($basePath, '/');

        if ($slug === '' || str_contains($slug, '/')) {
            return null;
        }

        $globalPage = $GLOBALS['page'] ?? null;
        if (is_object($globalPage)) {
            $globalPage = (array) $globalPage;
        }

        if (is_array($globalPage)) {
            $globalSlug = trim((string) ($globalPage['slug'] ?? ''), '/');
            if ($globalSlug === $slug) {
                $cache = $globalPage;

                return $cache;
            }
        }

        try {
            $page = function_exists('phinit_get_page_by_request_path')
                ? phinit_get_page_by_request_path($basePath, $resolvedLocale)
                : \CMS\PageManager::instance()->getPageBySlug($slug, $resolvedLocale);

            if (is_object($page)) {
                $page = (array) $page;
            }

            $cache = is_array($page) ? $page : null;
        } catch (\Throwable) {
            $cache = null;
        }

        return $cache;
    }

    private function getCurrentHeadPageTitle(): ?string
    {
        if ($this->currentHeadPageTitleResolved) {
            return $this->currentHeadPageTitleCache;
        }

        $this->currentHeadPageTitleResolved = true;
        $this->currentHeadPageTitleCache = null;

        $currentPage = $this->getCurrentHeadPage();
        $this->currentHeadPageTitleCache = is_array($currentPage)
            ? phinit_display_text((string) ($currentPage['title'] ?? ''))
            : null;

        return $this->currentHeadPageTitleCache;
    }

    public function outputMetaTags(): void
    {
        $tm = \CMS\ThemeManager::instance();
        $siteTitle = $tm->getSiteTitle() ?? '';
        $sitDesc = $tm->getSiteDescription() ?? '';
        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri = $this->getHeadRequestPath();
        $settings = $this->getHeadCustomizerSettings();
        $currentPage = $this->getCurrentHeadPage();

        $ogTitle = $siteTitle;
        $ogDesc = $sitDesc;
        $ogImg = '';
        $ogType = 'website';
        $canonical = $siteUrl . $uri;

        $metaRobots = $settings['meta_robots'];
        $canonicalSelf = $settings['canonical_self'];
        $ogType = $settings['og_type_default'];

        $httpCode = http_response_code();
        if ($settings['noindex_404'] && $httpCode === 404) {
            $metaRobots = 'noindex,follow';
        } elseif ($settings['noindex_search'] && $uri === '/search') {
            $metaRobots = 'noindex,follow';
        }

        $currentPost = $this->getCurrentHeadPost();
        if (is_array($currentPost)) {
            $ogTitle = ((string) ($currentPost['title'] ?? '')) . ' – ' . $siteTitle;
            $postExcerpt = function_exists('phinit_excerpt_plain_text')
                ? phinit_excerpt_plain_text((string) ($currentPost['excerpt'] ?? ''))
                : strip_tags((string) ($currentPost['excerpt'] ?? ''));
            $ogDesc = $this->buildHeadDescription($sitDesc, $postExcerpt, null);
            $ogImg = function_exists('phinit_normalize_public_media_url')
                ? phinit_normalize_public_media_url((string) ($currentPost['featured_image'] ?? ''), true)
                : (string) ($currentPost['featured_image'] ?? '');
            $ogType = 'article';
        } elseif (is_array($currentPage)) {
            $pageTitle = phinit_display_text((string) ($currentPage['title'] ?? ''));
            if ($pageTitle !== '') {
                $ogTitle = $pageTitle . ' – ' . $siteTitle;
            }
            $ogDesc = $this->buildHeadDescription(
                $sitDesc,
                (string) ($currentPage['excerpt'] ?? ''),
                (string) ($currentPage['content'] ?? '')
            );
            $ogImg = function_exists('phinit_normalize_public_media_url')
                ? phinit_normalize_public_media_url((string) ($currentPage['featured_image'] ?? ''), true)
                : (string) ($currentPage['featured_image'] ?? '');
        }

        if (empty($ogImg)) {
            $ogImg = $settings['og_default_image'];
        }

        $themeColor = $settings['primary_color'] !== '' ? $settings['primary_color'] : '#1e3a5f';

        $ogSiteFinal = !empty($settings['og_site_name']) ? $settings['og_site_name'] : $siteTitle;

        echo '<meta name="description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="robots" content="' . htmlspecialchars($metaRobots, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="theme-color" content="' . htmlspecialchars($themeColor, ENT_QUOTES) . '">' . "\n";
        if ($canonicalSelf) {
            echo '<link rel="canonical" href="' . htmlspecialchars($canonical, ENT_QUOTES) . '">' . "\n";
        }
        echo '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars($siteTitle, ENT_QUOTES) . ' RSS" href="' . htmlspecialchars($siteUrl . '/feed', ENT_QUOTES) . '">' . "\n";

        echo '<meta property="og:type" content="' . htmlspecialchars($ogType, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . htmlspecialchars($ogSiteFinal, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:title" content="' . htmlspecialchars($ogTitle, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:url" content="' . htmlspecialchars($canonical, ENT_QUOTES) . '">' . "\n";
        if (!empty($ogImg)) {
            echo '<meta property="og:image" content="' . htmlspecialchars($ogImg, ENT_QUOTES) . '">' . "\n";
        }

        $twitterCard = $settings['twitter_card_type'];
        $twitterCardFinal = (!empty($ogImg) && $twitterCard === 'summary_large_image') ? 'summary_large_image' : $twitterCard;
        echo '<meta name="twitter:card" content="' . htmlspecialchars($twitterCardFinal, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . htmlspecialchars($ogTitle, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES) . '">' . "\n";
        if (!empty($ogImg)) {
            echo '<meta name="twitter:image" content="' . htmlspecialchars($ogImg, ENT_QUOTES) . '">' . "\n";
        }
    }

    public function outputSchemaOrg(): void
    {
        $settings = $this->getHeadCustomizerSettings();
        if (!$settings['structured_data']) {
            return;
        }

        $tm = \CMS\ThemeManager::instance();
        $siteTitle = $tm->getSiteTitle() ?? '';
        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri = $this->getHeadRequestPath();
        $breadcrumbSchema = $this->buildBreadcrumbSchemaData();

        $currentPage = $this->getCurrentHeadPage();
        $webSite = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteTitle,
            'url' => $siteUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $siteUrl . '/search?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
        echo '<script type="application/ld+json">' . json_encode($webSite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

        $currentPost = $this->getCurrentHeadPost();
        if (!is_array($currentPost) && !is_array($currentPage)) {
            return;
        }

        if (is_array($currentPage) && !is_array($currentPost)) {
            $pageSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => (string) ($currentPage['title'] ?? ''),
                'headline' => (string) ($currentPage['title'] ?? ''),
                'description' => $this->buildHeadDescription(
                    '',
                    (string) ($currentPage['excerpt'] ?? ''),
                    (string) ($currentPage['content'] ?? '')
                ),
                'url' => $siteUrl . $uri,
            ];

            if (!empty($currentPage['updated_at'])) {
                $pageSchema['dateModified'] = (string) $currentPage['updated_at'];
            }

            if (!empty($currentPage['featured_image'])) {
                $pageSchema['image'] = function_exists('phinit_normalize_public_media_url')
                    ? phinit_normalize_public_media_url((string) $currentPage['featured_image'], true)
                    : (string) $currentPage['featured_image'];
            }

            echo '<script type="application/ld+json">' . json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            if (is_array($breadcrumbSchema)) {
                $this->outputJsonLd($breadcrumbSchema);
            }
            return;
        }

        try {
            $authorName = $currentPost['author_name'] ?? $settings['author_name'];
            $authorAvatar = $settings['author_avatar_url'];
            $orgImg = $settings['og_default_image'];
            $publisher = ['@type' => 'Organization', 'name' => $siteTitle];
            if (!empty($orgImg)) {
                $publisher['logo'] = ['@type' => 'ImageObject', 'url' => $orgImg];
            }
            $bp = [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $currentPost['title'] ?? '',
                'description' => mb_substr(strip_tags((string) ($currentPost['excerpt'] ?? '')), 0, 200),
                'url' => function_exists('phinit_build_post_url')
                    ? phinit_build_post_url($currentPost, function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de')
                    : ($siteUrl . '/blog/' . (string) ($currentPost['slug'] ?? '')),
                'datePublished' => (string) ($currentPost['published_at'] ?? ''),
                'dateModified' => !empty($currentPost['updated_at']) ? (string) $currentPost['updated_at'] : (string) ($currentPost['published_at'] ?? ''),
                'publisher' => $publisher,
            ];
            if (!empty($authorName)) {
                $bp['author'] = ['@type' => 'Person', 'name' => $authorName];
                if (!empty($authorAvatar)) {
                    $bp['author']['image'] = $authorAvatar;
                }
            }
            if (!empty($currentPost['featured_image'])) {
                $bp['image'] = function_exists('phinit_normalize_public_media_url')
                    ? phinit_normalize_public_media_url((string) $currentPost['featured_image'], true)
                    : (string) $currentPost['featured_image'];
            }
            echo '<script type="application/ld+json">' . json_encode($bp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            if (is_array($breadcrumbSchema)) {
                $this->outputJsonLd($breadcrumbSchema);
            }
        } catch (\Throwable) {
        }
    }

    public function outputBreadcrumb(): void
    {
        if ($this->breadcrumbOutput) {
            return;
        }

        $settings = $this->getHeadCustomizerSettings();
        if (is_array($this->getCurrentHeadPost()) || is_array($this->getCurrentHeadPage())) {
            $this->breadcrumbOutput = true;
            return;
        }

        if (!$settings['show_breadcrumb']) {
            return;
        }

        $onPosts = $settings['breadcrumb_on_posts'];
        $onPages = $settings['breadcrumb_on_pages'];

        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri = $this->getHeadRequestPath();
        if ($uri === '/' || $uri === '') {
            return;
        }

        $isPost = preg_match('#^/blog/[\w-]+$#', $uri);
        $archiveRequest = function_exists('cms_parse_archive_request_path') ? cms_parse_archive_request_path($uri) : null;
        $isPage = !$isPost && $uri !== '/blog' && $archiveRequest === null && !str_starts_with($uri, '/member') && $uri !== '/search';
        if ($isPost && !$onPosts) {
            return;
        }
        if ($isPage && !$onPages) {
            return;
        }

        $crumbs = [['label' => 'Home', 'url' => $siteUrl . '/']];
        $title = '';
        try {
            if (($currentPost = $this->getCurrentHeadPost()) !== null) {
                $crumbs[] = ['label' => 'Blog', 'url' => $siteUrl . '/blog'];
                $title = phinit_display_text((string) ($currentPost['title'] ?? ''));
            } elseif ($uri === '/blog') {
                $title = 'Blog';
            } elseif (is_array($archiveRequest)) {
                $crumbs[] = ['label' => 'Blog', 'url' => $siteUrl . '/blog'];
                $archiveSlug = rawurldecode((string) ($archiveRequest['tail'] ?? ''));
                $title = $archiveSlug !== ''
                    ? phinit_display_text(ucwords(str_replace('-', ' ', $archiveSlug)))
                    : ((string) ($archiveRequest['type'] ?? '') === 'tag' ? 'Tag' : 'Kategorie');
            } elseif (str_starts_with($uri, '/member')) {
                $crumbs[] = ['label' => 'Member', 'url' => $siteUrl . '/member'];
                $memberLabels = [
                    '/member/profile' => 'Profil',
                    '/member/favorites' => 'Favoriten',
                    '/member/security' => 'Sicherheit',
                    '/member/comments' => 'Kommentare',
                    '/member/newsletter' => 'Newsletter',
                    '/member/feeds' => 'Feed-Abos',
                    '/member/forum' => 'Forum',
                ];
                $memberLabel = array_find($memberLabels, static fn(string $_label, string $route): bool => str_starts_with($uri, $route));
                if (is_string($memberLabel)) {
                    $title = $memberLabel;
                }
                if (!$title && $uri !== '/member') {
                    $title = 'Dashboard';
                }
            } elseif ($uri === '/search') {
                $q = phinit_input_string($_GET, 'q', '', 200);
                $title = $q ? 'Suche: ' . phinit_display_text($q) : 'Suche';
            } else {
                $slug = ltrim($uri, '/');
                if (!str_contains($slug, '/')) {
                    $pageTitle = $this->getCurrentHeadPageTitle();
                    $title = $pageTitle !== null ? $pageTitle : phinit_display_text(ucwords(str_replace('-', ' ', $slug)));
                }
            }
        } catch (\Throwable) {
        }

        if (!$title) {
            return;
        }

        $this->breadcrumbOutput = true;

        $ldItems = [];
        foreach ($crumbs as $i => $c) {
            $ldItems[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['label'], 'item' => $c['url']];
        }
        $ldItems[] = ['@type' => 'ListItem', 'position' => count($ldItems) + 1, 'name' => strip_tags($title), 'item' => $siteUrl . $uri];
        if ($settings['breadcrumb_schema']) {
            echo '<script type="application/ld+json">' . json_encode(
                ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $ldItems],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . '</script>' . "\n";
        }

        echo '<nav class="breadcrumb-nav" aria-label="Breadcrumb">' . "\n";
        echo '<div class="container"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">' . "\n";
        foreach ($crumbs as $i => $c) {
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . htmlspecialchars($c['url'], ENT_QUOTES) . '" itemprop="item"><span itemprop="name">' . htmlspecialchars($c['label'], ENT_QUOTES) . '</span></a>';
            echo '<meta itemprop="position" content="' . ($i + 1) . '">';
            echo '</li><li class="sep" aria-hidden="true">›</li>';
        }
        echo '<li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span>';
        echo '<meta itemprop="position" content="' . count($ldItems) . '">';
        echo '</li>' . "\n";
        echo '</ol></div>' . "\n";
        echo '</nav>' . "\n";
    }

    public function filterPageTitle(string $siteTitle): string
    {
        $uri = $this->getHeadRequestPath();
        $siteTitle = phinit_display_text($siteTitle);

        try {
            if (($currentPost = $this->getCurrentHeadPost()) !== null) {
                return phinit_display_text((string) ($currentPost['title'] ?? '')) . ' – ' . $siteTitle;
            }

            $skipRoutes = ['', '/', 'blog', 'login', 'register', 'logout', 'search', 'feed', 'member'];
            $slug = ltrim($uri, '/');
            if (!empty($slug) && !in_array($slug, $skipRoutes, true) && !str_contains($slug, '/')) {
                $pageTitle = $this->getCurrentHeadPageTitle();
                if ($pageTitle !== null && $pageTitle !== '') {
                    return $pageTitle . ' – ' . $siteTitle;
                }
            }

            $archiveRequest = function_exists('cms_parse_archive_request_path') ? cms_parse_archive_request_path($uri) : null;
            if (is_array($archiveRequest)) {
                $label = ucwords(str_replace('-', ' ', rawurldecode((string) ($archiveRequest['tail'] ?? ''))));
                return $label . ' – ' . $siteTitle;
            }

            if (str_starts_with($uri, '/member')) {
                $memberTitles = [
                    '/member/dashboard' => 'Dashboard',
                    '/member/profile' => 'Mein Profil',
                    '/member/favorites' => 'Favoriten',
                    '/member/comments' => 'Meine Kommentare',
                    '/member/newsletter' => 'Newsletter',
                    '/member/feeds' => 'Feed-Abos',
                    '/member/forum' => 'Forum',
                    '/member/security' => 'Sicherheit',
                    '/member/notifications' => 'Benachrichtigungen',
                ];
                $memberTitle = array_find($memberTitles, static fn(string $_label, string $route): bool => str_starts_with($uri, $route));
                if (is_string($memberTitle)) {
                    return $memberTitle . ' – ' . $siteTitle;
                }
                return 'Member-Bereich – ' . $siteTitle;
            }

            if ($uri === '/blog') {
                return 'Blog – ' . $siteTitle;
            }

            if ($uri === '/search') {
                $q = phinit_input_string($_GET, 'q', '', 200);
                if ($q) {
                    return 'Suche: ' . phinit_display_text($q) . ' – ' . $siteTitle;
                }
                return 'Suche – ' . $siteTitle;
            }
        } catch (\Throwable $e) {
        }

        return $siteTitle . ' – IT-Blog & Tutorials';
    }

    public function bodyClass(string $classes): string
    {
        $add = [];
        $uri = phinit_current_request_path();
        $baseUri = $uri;

        try {
            $baseUri = (string) (\CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($uri)['base_uri'] ?? $uri);
        } catch (\Throwable) {
        }

        if ($baseUri === '/' || $baseUri === '') {
            $add[] = 'home';
        } else {
            $add[] = 'singular';
        }
        if (preg_match('#^/blog/.+#', $uri)) {
            $add[] = 'is-post';
        }
        if (str_starts_with($uri, '/member') || str_starts_with($uri, '/dashboard')) {
            $add[] = 'is-member';
        }

        if ($baseUri === '/kb' || str_starts_with($baseUri, '/kb/') || $baseUri === '/glossar') {
            $add[] = 'is-knowledgebase';
            if ($baseUri === '/glossar') {
                $add[] = 'is-knowledgebase-glossary';
            } else {
                $add[] = $baseUri === '/kb' ? 'is-knowledgebase-archive' : 'is-knowledgebase-single';
            }
        }

        return trim($classes . ' ' . implode(' ', $add));
    }
}
