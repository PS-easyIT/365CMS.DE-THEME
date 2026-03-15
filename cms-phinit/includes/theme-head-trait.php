<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Head_Trait
{
    private function getHeadRequestPath(): string
    {
        if (method_exists($this, 'getRequestContext')) {
            $context = $this->getRequestContext();
            return (string) ($context['path'] ?? '/');
        }

        return (string) (strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/');
    }

    private function getCurrentHeadPost(): ?array
    {
        if ($this->currentHeadPostResolved) {
            return $this->currentHeadPostCache;
        }

        $this->currentHeadPostResolved = true;
        $this->currentHeadPostCache = null;

        $path = $this->getHeadRequestPath();
        $postSlug = null;

        if (method_exists($this, 'getRequestContext')) {
            $context = $this->getRequestContext();
            if (!empty($context['isPost'])) {
                $postSlug = $context['postSlug'] ?? null;
            }
        }

        if (($postSlug === null || $postSlug === '') && preg_match('#^/blog/([\w-]+)$#', $path, $matches) === 1) {
            $postSlug = (string) ($matches[1] ?? '');
        }

        if (!is_string($postSlug) || trim($postSlug) === '') {
            return null;
        }

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->prefix();
            $row = $db->get_row(
                "SELECT p.slug, p.title, p.excerpt, p.featured_image, p.published_at, p.updated_at,
                        COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}users u ON u.id = p.author_id
                 WHERE p.slug = ? AND p.status = 'published' LIMIT 1",
                [$postSlug]
            );

            $this->currentHeadPostCache = $row ? (array) $row : null;
        } catch (\Throwable) {
            $this->currentHeadPostCache = null;
        }

        return $this->currentHeadPostCache;
    }

    private function getCurrentHeadPageTitle(): ?string
    {
        if ($this->currentHeadPageTitleResolved) {
            return $this->currentHeadPageTitleCache;
        }

        $this->currentHeadPageTitleResolved = true;
        $this->currentHeadPageTitleCache = null;

        $path = $this->getHeadRequestPath();
        $slug = ltrim($path, '/');
        if ($slug === '' || str_contains($slug, '/')) {
            return null;
        }

        $skipRoutes = ['blog', 'login', 'register', 'logout', 'search', 'feed', 'member', 'sitemap', 'autoren', 'authors'];
        if (in_array($slug, $skipRoutes, true)) {
            return null;
        }

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->prefix();
            $title = $db->get_var(
                "SELECT title FROM {$prefix}pages WHERE slug = ? AND status = 'published' LIMIT 1",
                [$slug]
            );
            $this->currentHeadPageTitleCache = $title ? phinit_display_text((string) $title) : null;
        } catch (\Throwable) {
            $this->currentHeadPageTitleCache = null;
        }

        return $this->currentHeadPageTitleCache;
    }

    public function outputMetaTags(): void
    {
        $tm = \CMS\ThemeManager::instance();
        $siteTitle = $tm->getSiteTitle() ?? '';
        $sitDesc = $tm->getSiteDescription() ?? '';
        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri = $this->getHeadRequestPath();

        $ogTitle = $siteTitle;
        $ogDesc = $sitDesc;
        $ogImg = '';
        $ogType = 'website';
        $canonical = $siteUrl . $uri;

        $cz = null;
        try {
            $cz = \CMS\Services\ThemeCustomizer::instance();
        } catch (\Throwable) {
        }

        $metaRobots = $cz ? (string) $cz->get('seo', 'meta_robots', 'index,follow') : 'index,follow';
        $canonicalSelf = $cz ? filter_var($cz->get('seo', 'canonical_self', true), FILTER_VALIDATE_BOOLEAN) : true;
        $ogSiteName = $cz ? (string) $cz->get('seo', 'og_site_name', '') : '';
        $ogTypeDefault = $cz ? (string) $cz->get('seo', 'og_type_default', 'website') : 'website';
        $twitterCard = $cz ? (string) $cz->get('seo', 'twitter_card_type', 'summary_large_image') : 'summary_large_image';
        $noindexSearch = $cz ? filter_var($cz->get('seo', 'noindex_search', true), FILTER_VALIDATE_BOOLEAN) : true;
        $noindex404 = $cz ? filter_var($cz->get('seo', 'noindex_404', true), FILTER_VALIDATE_BOOLEAN) : true;

        $ogType = $ogTypeDefault;

        $httpCode = http_response_code();
        if ($noindex404 && $httpCode === 404) {
            $metaRobots = 'noindex,follow';
        } elseif ($noindexSearch && $uri === '/search') {
            $metaRobots = 'noindex,follow';
        }

        $currentPost = $this->getCurrentHeadPost();
        if (is_array($currentPost)) {
            $ogTitle = ((string) ($currentPost['title'] ?? '')) . ' – ' . $siteTitle;
            $ogDesc = mb_substr(function_exists('phinit_excerpt_plain_text') ? phinit_excerpt_plain_text((string) ($currentPost['excerpt'] ?? '')) : strip_tags((string) ($currentPost['excerpt'] ?? '')), 0, 200);
            if (empty($ogDesc)) {
                $ogDesc = mb_substr($sitDesc, 0, 200);
            }
            $ogImg = (string) ($currentPost['featured_image'] ?? '');
            $ogType = 'article';
        }

        if (empty($ogImg) && $cz) {
            try {
                $ogImg = (string) $cz->get('advanced', 'og_default_image', '');
            } catch (\Throwable) {
            }
        }

        $themeColor = '#1e3a5f';
        try {
            if ($cz) {
                $tc = $cz->get('colors', 'primary_color', '#1e3a5f');
                if (!empty($tc)) {
                    $themeColor = $tc;
                }
            }
        } catch (\Throwable) {
        }

        $ogSiteFinal = !empty($ogSiteName) ? $ogSiteName : $siteTitle;

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
        try {
            if (!filter_var(\CMS\Services\ThemeCustomizer::instance()->get('seo', 'structured_data', true), FILTER_VALIDATE_BOOLEAN)) {
                return;
            }
        } catch (\Throwable) {
        }

        $tm = \CMS\ThemeManager::instance();
        $siteTitle = $tm->getSiteTitle() ?? '';
        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri = $this->getHeadRequestPath();

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
        if (!is_array($currentPost)) {
            return;
        }
        try {
            $cz = null;
            try {
                $cz = \CMS\Services\ThemeCustomizer::instance();
            } catch (\Throwable) {
            }
            $authorName = $currentPost['author_name'] ?? ($cz ? (string) $cz->get('posts', 'author_name', '') : '');
            $authorAvatar = $cz ? (string) $cz->get('posts', 'author_avatar_url', '') : '';
            $orgImg = $cz ? (string) $cz->get('advanced', 'og_default_image', '') : '';
            $publisher = ['@type' => 'Organization', 'name' => $siteTitle];
            if (!empty($orgImg)) {
                $publisher['logo'] = ['@type' => 'ImageObject', 'url' => $orgImg];
            }
            $bp = [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $currentPost['title'] ?? '',
                'description' => mb_substr(strip_tags((string) ($currentPost['excerpt'] ?? '')), 0, 200),
                'url' => $siteUrl . '/blog/' . (string) ($currentPost['slug'] ?? ''),
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
                $bp['image'] = $currentPost['featured_image'];
            }
            echo '<script type="application/ld+json">' . json_encode($bp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        } catch (\Throwable) {
        }
    }

    public function outputBreadcrumb(): void
    {
        if ($this->breadcrumbOutput) {
            return;
        }

        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $show = filter_var($c->get('layout', 'show_breadcrumb', true), FILTER_VALIDATE_BOOLEAN);
            if (!$show) {
                return;
            }
            $onPosts = filter_var($c->get('layout', 'breadcrumb_on_posts', true), FILTER_VALIDATE_BOOLEAN);
            $onPages = filter_var($c->get('layout', 'breadcrumb_on_pages', true), FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable) {
            $onPosts = true;
            $onPages = true;
        }

        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri = $this->getHeadRequestPath();
        if ($uri === '/' || $uri === '') {
            return;
        }

        $isPost = preg_match('#^/blog/[\w-]+$#', $uri);
        $isPage = !$isPost && $uri !== '/blog' && !str_starts_with($uri, '/kategorie/') && !str_starts_with($uri, '/member') && $uri !== '/search';
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
            } elseif (preg_match('#^/kategorie/([\w-]+)$#', $uri, $m)) {
                $crumbs[] = ['label' => 'Blog', 'url' => $siteUrl . '/blog'];
                $title = phinit_display_text(ucwords(str_replace('-', ' ', $m[1])));
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
                foreach ($memberLabels as $route => $label) {
                    if (str_starts_with($uri, $route)) {
                        $title = $label;
                        break;
                    }
                }
                if (!$title && $uri !== '/member') {
                    $title = 'Dashboard';
                }
            } elseif ($uri === '/search') {
                $q = trim($_GET['q'] ?? '');
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
        $bcSchema = true;
        try {
            $bcSchema = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('seo', 'breadcrumb_schema', true), FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable) {
        }
        if ($bcSchema) {
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

            if (preg_match('#^/kategorie/([\.\w-]+)$#', $uri, $m)) {
                $label = ucwords(str_replace('-', ' ', $m[1]));
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
                foreach ($memberTitles as $route => $label) {
                    if (str_starts_with($uri, $route)) {
                        return $label . ' – ' . $siteTitle;
                    }
                }
                return 'Member-Bereich – ' . $siteTitle;
            }

            if ($uri === '/blog') {
                return 'Blog – ' . $siteTitle;
            }

            if ($uri === '/search') {
                $q = trim($_GET['q'] ?? '');
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
        $uri = (string) strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
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
        return trim($classes . ' ' . implode(' ', $add));
    }
}
