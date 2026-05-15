<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Assets_Trait
{
    private ?string $homepageLeadImageCache = null;

    private function getCurrentTemplatePagePayload(): ?array
    {
        $page = $GLOBALS['page'] ?? null;

        if (is_object($page)) {
            $page = (array) $page;
        }

        return is_array($page) ? $page : null;
    }

    private function getResolvedCurrentPagePayload(string $path): ?array
    {
        $page = $this->getCurrentTemplatePagePayload();
        if (is_array($page)) {
            return $page;
        }

        if (!function_exists('phinit_get_page_by_request_path')) {
            return null;
        }

        $slug = trim($path, '/');
        if ($slug === '' || str_contains($slug, '/')) {
            return null;
        }

        $resolvedPage = phinit_get_page_by_request_path($path);

        return is_array($resolvedPage) ? $resolvedPage : null;
    }

    private function isHubPagePayload(?array $page): bool
    {
        if (!is_array($page)) {
            return false;
        }

        $contentType = strtolower(trim((string) ($page['content_type'] ?? '')));
        if ($contentType === 'hub') {
            return true;
        }

        $content = (string) ($page['content'] ?? '');

        return $content !== '' && str_contains($content, 'cms-hub-site');
    }

    private function getCustomizerSettingWithFallback(string $category, string $key, mixed $default = null, array $legacyKeys = []): mixed
    {
        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $value = $customizer->get($category, $key, null);

            if ($value !== null && $value !== '') {
                return $value;
            }

            foreach ($legacyKeys as $legacyKey) {
                $legacyValue = $customizer->get($category, (string) $legacyKey, null);
                if ($legacyValue !== null && $legacyValue !== '') {
                    return $legacyValue;
                }
            }
        } catch (\Throwable $e) {
        }

        return $default;
    }

    private function getRequestPath(): string
    {
        $requestUri = phinit_current_request_path();

        try {
            $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($requestUri);
            $baseUri = (string) ($context['base_uri'] ?? $requestUri);
            return $baseUri !== '' ? $baseUri : '/';
        } catch (\Throwable $e) {
            return $requestUri;
        }
    }

    /**
     * @return array{path:string,isAuthOrMember:bool,isBlogListing:bool,isPageExtras:bool,isPost:bool,isHubSite:bool,isPageDetail:bool,isRichContent:bool,isTemplateStyles:bool,postSlug:?string}
     */
    private function getRequestContext(): array
    {
        if (is_array($this->requestContextCache)) {
            return $this->requestContextCache;
        }

        $path = $this->getRequestPath();
        $isAuthOrMember = $this->isAuthOrMemberRequest($path);
        $isPageExtras = $this->isPageExtrasRequest($path);
        $isRootHubDomain = false;

        if ($path === '/' && !$isAuthOrMember && !$isPageExtras) {
            try {
                $host = phinit_current_host();
                if ($host !== '') {
                    $siteTableService = \CMS\Services\SiteTableService::getInstance();
                    $isRootHubDomain = $siteTableService->getHubPageByDomain($host, 'de') !== null
                        || $siteTableService->getHubPageByDomain($host, 'en') !== null;
                }
            } catch (\Throwable) {
                $isRootHubDomain = false;
            }
        }

        $isBlogListing = !$isRootHubDomain && $this->isBlogListingRequest($path);
        $postSlug = null;
        $isPost = false;

        if (method_exists($this, 'getCurrentHeadPost')) {
            try {
                $currentHeadPost = $this->getCurrentHeadPost();
                if (is_array($currentHeadPost) && !empty($currentHeadPost['slug'])) {
                    $postSlug = (string) $currentHeadPost['slug'];
                    $isPost = true;
                }
            } catch (\Throwable) {
                $postSlug = null;
                $isPost = false;
            }
        }

        if (!$isPost) {
            try {
                if (class_exists('CMS\Services\PermalinkService')) {
                    $postSlug = \CMS\Services\PermalinkService::getInstance()->extractPostSlugFromPath($path);
                }
            } catch (\Throwable) {
                $postSlug = null;
            }

            if (($postSlug === null || $postSlug === '') && preg_match('#^/blog/(?P<slug>[^/]+)$#', $path, $matches) === 1) {
                $postSlug = rawurldecode((string) ($matches['slug'] ?? ''));
            }

            if (is_string($postSlug) && trim($postSlug) !== '') {
                try {
                    $db = \CMS\Database::instance();
                    $row = $db->get_row(
                        "SELECT id FROM {$db->prefix()}posts WHERE slug = ? AND " . phinit_post_publication_where() . " LIMIT 1",
                        [$postSlug]
                    );
                    $isPost = $row !== null;
                } catch (\Throwable) {
                    $isPost = false;
                }
            }
        }

        $currentTemplatePage = $this->getResolvedCurrentPagePayload($path);
        $isHubSite = $this->isHubPagePayload($currentTemplatePage);
        if (!$isPost && !$isBlogListing && !$isAuthOrMember && !$isPageExtras) {
            if (!$isHubSite) {
                try {
                    $siteTableService = \CMS\Services\SiteTableService::getInstance();
                    if ($path === '/') {
                        $host = phinit_current_host();
                        if ($host !== '') {
                            $isHubSite = $siteTableService->getHubPageByDomain($host, 'de') !== null
                                || $siteTableService->getHubPageByDomain($host, 'en') !== null;
                        }
                    } else {
                        $slug = trim($path, '/');
                        if ($slug !== '' && !str_contains($slug, '/')) {
                            $isHubSite = $siteTableService->hubExistsBySlug($slug);
                        }
                    }
                } catch (\Throwable) {
                    $isHubSite = false;
                }
            }
        }

        $isPageDetail = false;
        if (!$isHubSite && !$isAuthOrMember && !$isPageExtras && !$isBlogListing && !$isPost) {
            $slug = trim($path, '/');
            $isPageDetail = $slug !== '' && !str_contains($slug, '/');
        }

        $this->requestContextCache = [
            'path' => $path,
            'isAuthOrMember' => $isAuthOrMember,
            'isBlogListing' => $isBlogListing,
            'isPageExtras' => $isPageExtras,
            'isPost' => $isPost,
            'isHubSite' => $isHubSite,
            'isPageDetail' => $isPageDetail,
            'isRichContent' => !$isHubSite && !$isAuthOrMember && !$isPageExtras && !$isBlogListing && ($isPost || $isPageDetail),
            'isTemplateStyles' => !$isHubSite && !$isAuthOrMember && !$isPageExtras && !$isBlogListing && ($isPost || $isPageDetail),
            'postSlug' => is_string($postSlug) && $postSlug !== '' ? $postSlug : null,
        ];

        return $this->requestContextCache;
    }

    private function emitStylesheet(string $href, bool $async = false): void
    {
        $escapedHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');

        if (!$async) {
            echo '<link rel="stylesheet" href="' . $escapedHref . '">' . "\n";
            return;
        }

        echo '<link rel="preload" as="style" href="' . $escapedHref . '" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        echo '<noscript><link rel="stylesheet" href="' . $escapedHref . '"></noscript>' . "\n";
    }

    public function outputCriticalResourceHints(): void
    {
        $requestContext = $this->getRequestContext();
        $requestPath = (string) ($requestContext['path'] ?? '/');
        if (!in_array($requestPath, ['/', '/blog'], true)) {
            return;
        }

        $homepageLeadImage = $this->getHomepageLeadImageUrl();
        if ($homepageLeadImage === '') {
            return;
        }

        echo '<link rel="preload" as="image" href="' . htmlspecialchars($homepageLeadImage, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function themeAssetUrl(string $relativePath, string|int $version): string
    {
        return CMS_PHINIT_THEME_URL . ltrim($relativePath, '/') . '?v=' . rawurlencode((string) $version);
    }

    private function isAuthOrMemberRequest(string $path): bool
    {
        return in_array($path, ['/login', '/register', '/forgot-password', '/cms-login', '/cms-register', '/cms-password-forgot'], true)
            || str_starts_with($path, '/member')
            || str_starts_with($path, '/dashboard');
    }

    private function isBlogListingRequest(string $path): bool
    {
        return $path === '/'
            || $path === '/blog'
            || (function_exists('cms_is_archive_request_path') && cms_is_archive_request_path($path, 'category'))
            || (function_exists('cms_is_archive_request_path') && cms_is_archive_request_path($path, 'tag'))
            || str_starts_with($path, '/author/');
    }

    private function isPageExtrasRequest(string $path): bool
    {
        return $path === '/search'
            || $path === '/404'
            || $path === '/error'
            || http_response_code() === 404;
    }

    private function isCookieConsentPageRequest(string $path, ?array $page = null): bool
    {
        if (is_array($page)) {
            $contentType = strtolower(trim((string) ($page['content_type'] ?? '')));
            $slug = strtolower(trim((string) ($page['slug'] ?? '')));

            if ($contentType === 'cookie_consent') {
                return true;
            }

            if (in_array($slug, ['cookie-einstellungen', 'cookie-settings'], true)) {
                return true;
            }
        }

        return in_array($path, ['/cookie-einstellungen', '/cookie-settings'], true);
    }

    private function isImageArchiveRequest(string $path, ?array $page = null): bool
    {
        if (is_array($page) && function_exists('phinit_is_image_archive_page') && phinit_is_image_archive_page($page)) {
            return true;
        }

        $slug = trim($path, '/');

        return $slug !== '' && in_array($slug, phinit_image_archive_page_slugs(), true);
    }

    private function isSpecialPageRequest(string $path): bool
    {
        if (in_array($path, ['/sitemap', '/autoren', '/authors'], true)) {
            return true;
        }

        if (!function_exists('cms_parse_archive_request_path')) {
            return false;
        }

        $archiveRequest = cms_parse_archive_request_path($path);
        if (!is_array($archiveRequest)) {
            return false;
        }

        $archiveType = (string) ($archiveRequest['type'] ?? '');
        $archiveTail = trim((string) ($archiveRequest['tail'] ?? ''));

        return in_array($archiveType, ['category', 'tag'], true) && $archiveTail === '';
    }

    private function isKnowledgebaseRequest(string $path): bool
    {
        return $path === '/kb'
            || str_starts_with($path, '/kb/')
            || $path === '/glossar';
    }

    public function enqueueStyles(): void
    {
        $requestContext = $this->getRequestContext();
        $requestPath = $requestContext['path'];
        $currentPage = $this->getResolvedCurrentPagePayload((string) $requestPath);
        $isHubSiteRequest = $requestContext['isHubSite'];
        $loadHomepageBlogCss = $requestContext['isBlogListing'];
        $loadMemberAuthCss = $requestContext['isAuthOrMember'];
        $loadPostDetailCss = $requestContext['isPost'];
        $loadPostSidebarCss = $requestContext['isPost'];
        $loadPageDetailCss = $requestContext['isPageDetail'];
        $loadCookieConsentCss = $this->isCookieConsentPageRequest($requestPath, $currentPage);
        $loadImageArchiveCss = $this->isImageArchiveRequest($requestPath, $currentPage);
        $loadSpecialPagesCss = $this->isSpecialPageRequest($requestPath);
        $loadPageExtrasCss = $requestContext['isPageExtras'];
        $loadRichContentCss = $requestContext['isRichContent'];
        $loadTemplateCss = $requestContext['isTemplateStyles'];
        $loadContentCardsCss = $loadHomepageBlogCss || $loadPageExtrasCss;
        $loadKnowledgebaseCss = $this->isKnowledgebaseRequest($requestPath);

        $cssFile = CMS_PHINIT_THEME_DIR . 'style.css';
        $headerNavigationCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/header-navigation.css';
        $uiChromeCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/ui-chrome.css';
        $templateCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/templates.css';
        $contentCardsCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/content-cards.css';
        $memberAuthCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/member-auth.css';
        $postDetailCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/post-detail.css';
        $pageDetailCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-detail.css';
        $postSidebarCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/post-sidebar.css';
        $pageExtrasCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-extras.css';
        $pageCookieConsentCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-cookie-consent.css';
        $imageArchiveCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-image-archive.css';
        $specialPagesCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-special-pages.css';
        $richContentCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/rich-content.css';
        $homepageBlogCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/homepage-blog.css';
        $hubSitesCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/hub-sites.css';
        $knowledgebaseCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-knowledgebase.css';
        $footerConsentCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/footer-consent.css';

        $cbVersion = '';
        try {
            $cbVersion = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'cache_buster_css', '');
        } catch (\Throwable $e) {
        }

        $assetVersion = static function (string $file) use ($cbVersion): string {
            $customVersion = trim((string) $cbVersion);
            $fileVersion = file_exists($file) ? (string) filemtime($file) : CMS_PHINIT_THEME_VERSION;

            return $customVersion !== '' ? $customVersion . '-' . $fileVersion : $fileVersion;
        };

        $version = $assetVersion($cssFile);
        $this->emitStylesheet($this->themeAssetUrl('style.css', $version));

        if (file_exists($headerNavigationCssFile)) {
            $headerNavigationVersion = $assetVersion($headerNavigationCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/header-navigation.css', $headerNavigationVersion));
        }

        $uiChromeIsCritical = $loadPageDetailCss || $loadPostDetailCss || $isHubSiteRequest;
        if (file_exists($uiChromeCssFile)) {
            $uiChromeVersion = $assetVersion($uiChromeCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/ui-chrome.css', $uiChromeVersion), !$uiChromeIsCritical);
        }

        if ($loadTemplateCss && file_exists($templateCssFile)) {
            $templateVersion = $assetVersion($templateCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/templates.css', $templateVersion));
        }

        $contentCardsIsCritical = $loadPageExtrasCss;
        if ($loadContentCardsCss && file_exists($contentCardsCssFile)) {
            $contentCardsVersion = $assetVersion($contentCardsCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/content-cards.css', $contentCardsVersion), !$contentCardsIsCritical);
        }

        if ($loadMemberAuthCss && file_exists($memberAuthCssFile)) {
            $memberAuthVersion = $assetVersion($memberAuthCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/member-auth.css', $memberAuthVersion));
        }

        if ($loadPostDetailCss && file_exists($postDetailCssFile)) {
            $postDetailVersion = $assetVersion($postDetailCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/post-detail.css', $postDetailVersion));
        }

        if ($loadPostSidebarCss && file_exists($postSidebarCssFile)) {
            $postSidebarVersion = $assetVersion($postSidebarCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/post-sidebar.css', $postSidebarVersion));
        }

        if ($loadPageDetailCss && file_exists($pageDetailCssFile)) {
            $pageDetailVersion = $assetVersion($pageDetailCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-detail.css', $pageDetailVersion));
        }

        if ($loadCookieConsentCss && file_exists($pageCookieConsentCssFile)) {
            $pageCookieConsentVersion = $assetVersion($pageCookieConsentCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-cookie-consent.css', $pageCookieConsentVersion));
        }

        if ($loadImageArchiveCss && file_exists($imageArchiveCssFile)) {
            $imageArchiveVersion = $assetVersion($imageArchiveCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-image-archive.css', $imageArchiveVersion));
        }

        if ($loadSpecialPagesCss && file_exists($specialPagesCssFile)) {
            $specialPagesVersion = $assetVersion($specialPagesCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-special-pages.css', $specialPagesVersion));
        }

        if ($loadPageExtrasCss && file_exists($pageExtrasCssFile)) {
            $pageExtrasVersion = $assetVersion($pageExtrasCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-extras.css', $pageExtrasVersion));
        }

        if ($loadRichContentCss && file_exists($richContentCssFile)) {
            $richContentVersion = $assetVersion($richContentCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/rich-content.css', $richContentVersion));
        }

        if ($loadHomepageBlogCss && file_exists($homepageBlogCssFile)) {
            $homepageBlogVersion = $assetVersion($homepageBlogCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/homepage-blog.css', $homepageBlogVersion));
        }

        if ($isHubSiteRequest && file_exists($hubSitesCssFile)) {
            $hubSitesVersion = $assetVersion($hubSitesCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/hub-sites.css', $hubSitesVersion));
        }

        if ($loadKnowledgebaseCss && file_exists($knowledgebaseCssFile)) {
            $knowledgebaseVersion = $assetVersion($knowledgebaseCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-knowledgebase.css', $knowledgebaseVersion));
        }

        if (file_exists($footerConsentCssFile)) {
            $footerConsentVersion = $assetVersion($footerConsentCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/footer-consent.css', $footerConsentVersion), true);
        }

        try {
            $css = $this->generatePhinitCSS();
            if (!empty(trim($css))) {
                echo '<style id="cms-phinit-customizer-css">' . "\n" . $css . "\n" . '</style>' . "\n";
            }
        } catch (\Throwable $e) {
        }
    }

    public function enqueueScripts(): void
    {
        if ($this->scriptsOutput) {
            return;
        }

        $this->scriptsOutput = true;

        $requestContext = $this->getRequestContext();
        $requestPath = $requestContext['path'];
        $deferScripts = filter_var(
            $this->getCustomizerSettingWithFallback('performance', 'defer_scripts', true),
            FILTER_VALIDATE_BOOLEAN
        );
        $deferAttr = $deferScripts ? ' defer' : '';

        $scripts = [
            'assets/js/navigation.js',
        ];

        $homeBasePath = $requestPath;
        try {
            if (class_exists('CMS\\Services\\ContentLocalizationService')) {
                $localizedContext = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($requestPath);
                $homeBasePath = (string) ($localizedContext['base_uri'] ?? $requestPath);
            }
        } catch (\Throwable) {
            $homeBasePath = $requestPath;
        }

        if ($homeBasePath === '' || $homeBasePath === '/') {
            $scripts[] = 'assets/js/homepage-widgets.js';
        }

        foreach ($scripts as $scriptRelativePath) {
            $scriptFile = CMS_PHINIT_THEME_DIR . str_replace('/', DIRECTORY_SEPARATOR, $scriptRelativePath);
            if (!file_exists($scriptFile)) {
                continue;
            }

            $version = filemtime($scriptFile);
            echo '<script src="' . $this->themeAssetUrl($scriptRelativePath, $version) . '"' . $deferAttr . '></script>' . "\n";
        }

        if (str_starts_with($requestPath, '/member') && function_exists('cms_asset_url')) {
            echo '<script src="' . htmlspecialchars(cms_asset_url('js/member-dashboard.js'), ENT_QUOTES, 'UTF-8') . '"' . $deferAttr . '></script>' . "\n";
        }
    }

    private function isLocalFontsEnabled(): bool
    {
        try {
            $db = \CMS\Database::instance();
            $row = $db->get_row(
                "SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = 'privacy_use_local_fonts' LIMIT 1"
            );

            if ($row !== null) {
                $value = strtolower(trim((string) ($row->option_value ?? '')));
                if (in_array($value, ['1', 'true', 'yes', 'on'], true)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
        }

        try {
            return \CMS\Services\SettingsService::getInstance()->getBool('privacy', 'use_local_fonts', false);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return array<int, string>
     */
    private function getBaseRequestedLocalFontSlugs(): array
    {
        $requestedSlugs = [];

        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $requestedSlugs = [
                (string) $customizer->get('typography', 'font_family_ui', 'inter'),
                (string) $customizer->get('typography', 'font_family_brand', 'space-grotesk'),
                (string) $customizer->get('typography', 'font_family_code', 'jetbrains-mono'),
            ];
        } catch (\Throwable $e) {
            $requestedSlugs = ['inter', 'space-grotesk', 'jetbrains-mono'];
        }

        $normalized = [];
        foreach ($requestedSlugs as $slug) {
            $slug = $this->sanitizeFontSlug($slug);
            if ($slug === '' || in_array($slug, ['system', 'system-mono'], true)) {
                continue;
            }
            $normalized[] = $slug;
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @return array<int, string>
     */
    private function getRequestedLocalFontSlugs(): array
    {
        $requestedSlugs = $this->getBaseRequestedLocalFontSlugs();
        $normalized = [];

        foreach ($requestedSlugs as $slug) {
            $normalized[] = $slug;
            if (isset(self::LOCAL_FONT_SLUG_ALIASES[$slug])) {
                $normalized[] = self::LOCAL_FONT_SLUG_ALIASES[$slug];
            }
        }

        return array_values(array_unique($normalized));
    }

    private function sanitizeFontSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9_-]+/i', '-', $slug) ?? '';
        return trim($slug, '-');
    }

    /**
     * @return array<string, string>
     */
    private function getLocalFontCssMap(): array
    {
        try {
            $db = \CMS\Database::instance();
            $fonts = $db->get_results(
                "SELECT slug, css_path FROM {$db->getPrefix()}custom_fonts WHERE css_path IS NOT NULL AND css_path != ''"
            ) ?: [];
        } catch (\Throwable $e) {
            return [];
        }

        $fontMap = [];
        foreach ($fonts as $font) {
            $slug = $this->sanitizeFontSlug((string) ($font->slug ?? ''));
            $cssPath = trim((string) ($font->css_path ?? ''));
            if ($slug === '' || $cssPath === '') {
                continue;
            }

            $cssFile = ABSPATH . ltrim($cssPath, '/');
            if (!is_file($cssFile)) {
                continue;
            }

            $fontMap[$slug] = rtrim((string) SITE_URL, '/') . '/' . ltrim($cssPath, '/');
        }

        return $fontMap;
    }

    /**
     * @return array<int, string>
     */
    private function getRequestedLocalFontCssUrls(): array
    {
        $fontMap = $this->getLocalFontCssMap();
        $urls = [];

        foreach ($this->getRequestedLocalFontSlugs() as $slug) {
            if (isset($fontMap[$slug])) {
                $urls[] = $fontMap[$slug];
            }
        }

        return array_values(array_unique($urls));
    }

    private function canServeRequestedFontsLocally(): bool
    {
        $fontMap = $this->getLocalFontCssMap();

        foreach ($this->getBaseRequestedLocalFontSlugs() as $slug) {
            $candidates = [$slug];
            if (isset(self::LOCAL_FONT_SLUG_ALIASES[$slug])) {
                $candidates[] = self::LOCAL_FONT_SLUG_ALIASES[$slug];
            }

            $available = array_find($candidates, static fn(string $candidate): bool => isset($fontMap[$candidate])) !== null;

            if (!$available) {
                return false;
            }
        }

        return true;
    }

    public function registerRequiredLocalFonts(array $slugs): array
    {
        try {
            foreach ($this->getRequestedLocalFontSlugs() as $slug) {
                $slugs[] = $slug;
            }
        } catch (\Throwable $e) {
            return array_values(array_unique($slugs));
        }

        return array_values(array_unique($slugs));
    }

    public function outputPreconnect(): void
    {
        $localFonts = $this->isLocalFontsEnabled() || $this->canServeRequestedFontsLocally();

        $dnsPrefetch = filter_var(
            $this->getCustomizerSettingWithFallback('performance', 'dns_prefetch', true),
            FILTER_VALIDATE_BOOLEAN
        );

        if (!$localFonts) {
            $preconnectFonts = filter_var(
                $this->getCustomizerSettingWithFallback('performance', 'preconnect_fonts', true, ['preconnect_google_fonts']),
                FILTER_VALIDATE_BOOLEAN
            );
            if ($preconnectFonts) {
                echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
                echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
                if ($dnsPrefetch) {
                    echo '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' . "\n";
                    echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">' . "\n";
                }
            }
        }

        if ($dnsPrefetch) {
            try {
                $extra = (string) $this->getCustomizerSettingWithFallback('performance', 'preconnect_extra', '');
                foreach (array_filter(array_map('trim', explode("\n", $extra))) as $extraUrl) {
                    $safeUrl = filter_var($extraUrl, FILTER_VALIDATE_URL) ? htmlspecialchars($extraUrl, ENT_QUOTES) : '';
                    if (!empty($safeUrl)) {
                        echo '<link rel="dns-prefetch" href="' . $safeUrl . '">' . "\n";
                    }
                }
            } catch (\Throwable) {
            }
        }
    }

    public function outputGoogleFonts(): void
    {
        if ($this->isLocalFontsEnabled()) {
            return;
        }

        $localCssUrls = $this->getRequestedLocalFontCssUrls();
        if ($this->canServeRequestedFontsLocally() && $localCssUrls !== []) {
            foreach ($localCssUrls as $localCssUrl) {
                $this->emitStylesheet((string) $localCssUrl, true);
            }
            return;
        }

        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $ui = $c->get('typography', 'font_family_ui', 'inter');
            $brand = $c->get('typography', 'font_family_brand', 'space-grotesk');
            $code = $c->get('typography', 'font_family_code', 'jetbrains-mono');
            $fontMap = [
                'barlow' => 'Barlow:wght@400;500;600;700',
                'barlow-condensed' => 'Barlow+Condensed:wght@500;600;700;800',
                'inter' => 'Inter:wght@400;500;600;700',
                'space-grotesk' => 'Space+Grotesk:wght@500;600;700',
                'sora' => 'Sora:wght@500;600;700',
                'roboto' => 'Roboto:wght@400;500;700',
                'open-sans' => 'Open+Sans:wght@400;600;700',
                'lato' => 'Lato:wght@400;700',
                'montserrat' => 'Montserrat:wght@400;600;700',
                'poppins' => 'Poppins:wght@400;500;600;700',
                'source-sans' => 'Source+Sans+3:wght@400;600;700',
                'nunito' => 'Nunito:wght@400;600;700',
                'roboto-condensed' => 'Roboto+Condensed:wght@400;700',
                'oswald' => 'Oswald:wght@500;700',
                'rajdhani' => 'Rajdhani:wght@500;600;700',
                'exo2' => 'Exo+2:wght@500;700',
                'jetbrains-mono' => 'JetBrains+Mono:wght@400;600',
                'fira-code' => 'Fira+Code:wght@400;600',
                'source-code' => 'Source+Code+Pro:wght@400;600',
            ];
            $families = [];
            foreach (array_unique([$ui, $brand, $code]) as $slug) {
                if (isset($fontMap[$slug])) {
                    $families[] = $fontMap[$slug];
                }
            }
            if (empty($families)) {
                return;
            }
            $url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $families) . '&display=swap';
            $this->emitStylesheet($url, true);
        } catch (\Throwable $e) {
        }
    }

    public function outputCustomHeaderCode(): void
    {
        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $code = $customizer->get('advanced', 'custom_head_code', '');
            if (trim((string) $code) === '') {
                $code = $customizer->get('advanced', 'custom_header_code', '');
            }
            if (!empty(trim((string) $code))) {
                echo "\n" . (string) $code . "\n";
            }
        } catch (\Throwable $e) {
        }
    }

    public function outputCustomFooterCode(): void
    {
        if ($this->footerCodeOutput) {
            return;
        }

        $this->footerCodeOutput = true;

        try {
            $code = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'custom_footer_code', '');
            if (!empty(trim((string) $code))) {
                echo "\n" . (string) $code . "\n";
            }

            $gaId = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'google_analytics_id', '');
            if (!empty(trim((string) $gaId)) && preg_match('/^G-[A-Z0-9]{6,}$/', trim((string) $gaId))) {
                $gaId = trim((string) $gaId);
                $analyticsLoaderFile = CMS_PHINIT_THEME_DIR . 'assets/js/analytics-loader.js';

                if (is_file($analyticsLoaderFile)) {
                    $analyticsLoaderUrl = $this->themeAssetUrl('assets/js/analytics-loader.js', filemtime($analyticsLoaderFile));
                    echo '<script src="' . htmlspecialchars($analyticsLoaderUrl, ENT_QUOTES, 'UTF-8') . '" data-ga-id="' . htmlspecialchars($gaId, ENT_QUOTES, 'UTF-8') . '" defer></script>' . "\n";
                }
            }
        } catch (\Throwable $e) {
        }
    }

    private function generatePhinitCSS(): string
    {
        $c = \CMS\Services\ThemeCustomizer::instance();
        $css = "/* CMS Phinit – Customizer CSS */\n:root {\n";

        $colorMap = [
            'primary_color' => '--primary-color',
            'primary_dark' => '--primary-dark',
            'primary_mid' => '--primary-mid',
            'primary_light' => '--primary-light',
            'accent_color' => '--accent-color',
            'accent_hover' => '--accent-hover',
            'accent_blue' => '--accent-blue',
            'accent_blue2' => '--accent-blue2',
            'accent_teal' => '--accent-teal',
            'accent_teal_light' => '--accent-teal-light',
            'bg_header1' => '--bg-header1',
            'bg_header2' => '--bg-header2',
            'bg_header3' => '--bg-header3',
            'bg_primary' => '--bg-primary',
            'bg_secondary' => '--bg-secondary',
            'bg_dark' => '--bg-dark',
            'text_primary' => '--text-primary',
            'text_secondary' => '--text-secondary',
            'text_muted' => '--text-muted',
            'text_nav' => '--text-nav',
            'text_nav_member' => '--text-nav-member',
            'text_nav_member_hover' => '--text-nav-member-hover',
            'text_nav_main' => '--text-nav-main',
            'text_nav_main_hover' => '--text-nav-main-hover',
            'text_nav_quicklinks' => '--text-nav-quicklinks',
            'text_nav_quicklinks_hover' => '--text-nav-quicklinks-hover',
            'text_nav_dropdown' => '--text-nav-dropdown',
            'text_nav_dropdown_hover' => '--text-nav-dropdown-hover',
            'text_nav_mobile_hover' => '--text-nav-mobile-hover',
            'text_nav_footer_hover' => '--text-nav-footer-hover',
            'text_nav_footer_bottom_hover' => '--text-nav-footer-bottom-hover',
            'text_nav_network_hover' => '--text-nav-network-hover',
            'text_nav_member_sidebar_hover' => '--text-nav-member-sidebar-hover',
            'page_edge_tint_color' => '--page-edge-overlay-color',
            'logo_suffix_color' => '--logo-suffix-color',
            'border_light' => '--border-color',
            'footer_bg' => '--footer-bg',
            'footer_bottom_bg' => '--footer-bottom-bg',
            'footer_border' => '--footer-border',
            'success_color' => '--success-color',
            'error_color' => '--error-color',
            'progress_bar_start' => '--progress-bar-start',
            'progress_bar_end' => '--progress-bar-end',
        ];
        foreach ($colorMap as $key => $var) {
            $val = $c->get('colors', $key, '');
            if (!empty($val) && $val !== '') {
                $css .= "    {$var}: {$val};\n";
            }
        }

        $fontMapSlug = [
            'barlow' => "'Barlow', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'barlow-condensed' => "'Barlow Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'inter' => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'space-grotesk' => "'Space Grotesk', 'Sora', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'sora' => "'Sora', 'Space Grotesk', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'roboto' => "'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'open-sans' => "'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'lato' => "'Lato', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'montserrat' => "'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'poppins' => "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'source-sans' => "'Source Sans 3', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'nunito' => "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'roboto-condensed' => "'Roboto Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'oswald' => "'Oswald', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'rajdhani' => "'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'exo2' => "'Exo 2', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'jetbrains-mono' => "'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace",
            'fira-code' => "'Fira Code', 'JetBrains Mono', monospace",
            'source-code' => "'Source Code Pro', 'Fira Code', monospace",
            'cascadia' => "'Cascadia Code', 'JetBrains Mono', monospace",
            'system' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'system-mono' => "'Cascadia Code', 'Consolas', 'Courier New', monospace",
        ];

        $uiFont = $c->get('typography', 'font_family_ui', 'inter');
        if (!empty($uiFont) && isset($fontMapSlug[$uiFont])) {
            $css .= "    --font-ui: {$fontMapSlug[$uiFont]};\n";
        }
        $brandFont = $c->get('typography', 'font_family_brand', 'space-grotesk');
        if (!empty($brandFont) && isset($fontMapSlug[$brandFont])) {
            $css .= "    --font-brand: {$fontMapSlug[$brandFont]};\n";
        }
        $codeFont = $c->get('typography', 'font_family_code', 'jetbrains-mono');
        if (!empty($codeFont) && isset($fontMapSlug[$codeFont])) {
            $css .= "    --font-code: {$fontMapSlug[$codeFont]};\n";
        }

        $typoNumMap = [
            'font_size_base' => ['--fs-base', 'px'],
            'font_size_post' => ['--fs-post', 'px'],
            'line_height_base' => ['--lh-base', ''],
            'line_height_post' => ['--lh-post', ''],
        ];
        foreach ($typoNumMap as $key => $info) {
            $val = $c->get('typography', $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$info[0]}: {$val}{$info[1]};\n";
            }
        }
        $fwHead = $c->get('typography', 'font_weight_heading', '');
        if (!empty($fwHead)) {
            $css .= "    --fw-heading: {$fwHead};\n";
        }
        $fwNav = $c->get('typography', 'font_weight_nav', '');
        if (!empty($fwNav)) {
            $css .= "    --fw-nav: {$fwNav};\n";
        }

        $layoutMap = [
            'container_width' => ['--container-max', 'px'],
            'sidebar_width' => ['--sidebar-width', 'px'],
            'border_radius' => ['--radius-sm', 'px'],
            'border_radius_md' => ['--radius', 'px'],
            'spacing_header_content' => ['--spacing-header-content', 'px'],
            'spacing_content_footer' => ['--spacing-content-footer', 'px'],
            'content_gap' => ['--content-gap', 'px'],
            'spacing_sections' => ['--spacing-sections', 'px'],
        ];
        foreach ($layoutMap as $key => $info) {
            $val = $c->get('layout', $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$info[0]}: {$val}{$info[1]};\n";
            }
        }
        $pageEdgeOpacity = $c->get('layout', 'page_edge_tint_opacity', '');
        if ($pageEdgeOpacity !== '' && $pageEdgeOpacity !== null) {
            $css .= "    --page-edge-overlay-opacity: {$pageEdgeOpacity};\n";
        }
        $homeHeaderSpacing = $c->get('homepage', 'home_header_content_spacing', '');
        if ($homeHeaderSpacing !== '' && $homeHeaderSpacing !== null) {
            $css .= "    --home-spacing-header-content: {$homeHeaderSpacing}px;\n";
        }
        $sidebarPos = $c->get('layout', 'sidebar_position', '');
        if (!empty($sidebarPos)) {
            $css .= "    --sidebar-position: {$sidebarPos};\n";
        }

        $logoAccent = $c->get('header', 'logo_accent_color', '');
        if (!empty($logoAccent)) {
            $css .= "    --logo-accent: {$logoAccent};\n";
        }
        $logoHeight = filter_var($c->get('header', 'logo_max_height', ''), FILTER_VALIDATE_INT);
        if (is_int($logoHeight) && $logoHeight > 0) {
            $logoHeight = max(16, min(180, $logoHeight));
            $headerMainHeight = max(56, $logoHeight + 20);
            $headerScrolledHeight = max(34, (int) round($headerMainHeight * 0.75));
            $logoScrolledHeight = min($logoHeight, max(18, $headerScrolledHeight - 12));

            $css .= "    --logo-max-height: {$logoHeight}px;\n";
            $css .= "    --header-h: {$headerMainHeight}px;\n";
            $css .= "    --header-scrolled-h: {$headerScrolledHeight}px;\n";
            $css .= "    --logo-scrolled-max-height: {$logoScrolledHeight}px;\n";
        }
        $memberBarH = $c->get('header', 'member_bar_height', '');
        if (!empty($memberBarH)) {
            $css .= "    --member-bar-h: {$memberBarH}px;\n";
        }
        $mainNavH = $c->get('header', 'main_nav_height', '');
        if (!empty($mainNavH)) {
            $css .= "    --main-nav-height: {$mainNavH}px;\n";
            $css .= "    --main-menu-h: {$mainNavH}px;\n";
        }
        $subBarH = $c->get('header', 'sub_bar_height', '');
        if (!empty($subBarH)) {
            $css .= "    --sub-bar-height: {$subBarH}px;\n";
            $css .= "    --quicklinks-h: {$subBarH}px;\n";
        }

        $headerMenuVarMap = [
            'member_bar_font_size' => ['header', '--member-bar-link-size', 'px'],
            'member_bar_item_spacing' => ['header', '--member-bar-link-gap', 'px'],
            'logo_title_font_size' => ['header', '--logo-title-font-size', 'px'],
            'main_nav_font_size' => ['header', '--main-nav-link-size', 'px'],
            'main_nav_item_spacing' => ['header', '--main-nav-link-space', 'px'],
            'dropdown_nav_font_size' => ['header', '--dropdown-link-size', 'px'],
            'dropdown_nav_item_spacing' => ['header', '--dropdown-link-space', 'px'],
            'quicklinks_font_size' => ['header', '--quicklinks-link-size', 'px'],
            'quicklinks_item_spacing' => ['header', '--quicklinks-link-space', 'px'],
            'mobile_menu_font_size' => ['header', '--mobile-menu-link-size', 'px'],
            'mobile_menu_item_spacing' => ['header', '--mobile-menu-link-space', 'px'],
            'footer_menu_font_size' => ['footer', '--footer-menu-link-size', 'px'],
            'footer_menu_item_spacing' => ['footer', '--footer-menu-link-gap', 'px'],
            'footer_bottom_font_size' => ['footer', '--footer-bottom-link-size', 'px'],
            'footer_bottom_item_spacing' => ['footer', '--footer-bottom-link-gap', 'px'],
            'network_bar_font_size' => ['footer', '--network-bar-link-size', 'px'],
            'network_bar_item_spacing' => ['footer', '--network-bar-link-gap', 'px'],
            'sidebar_menu_font_size' => ['memberdashboard', '--member-sidebar-link-size', 'px'],
            'sidebar_menu_item_spacing' => ['memberdashboard', '--member-sidebar-link-gap', 'px'],
        ];
        foreach ($headerMenuVarMap as $key => [$category, $varName, $unit]) {
            $val = $c->get($category, $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$varName}: {$val}{$unit};\n";
            }
        }

        $heroH = $c->get('posts', 'post_hero_height', '');
        $heroW = $c->get('posts', 'post_hero_width', '');
        if (!empty($heroH)) {
            $css .= "    --post-hero-h: {$heroH}px;\n";
        }
        if (!empty($heroW)) {
            $css .= "    --post-hero-w: {$heroW}px;\n";
        }

        $pageHeroW = $c->get('pages', 'page_hero_width', '');
        $pageHeroH = $c->get('pages', 'page_hero_height', '');
        $pageHeroFitMode = (string) $c->get('pages', 'page_hero_fit_mode', 'contain');
        if (!empty($pageHeroW)) {
            $css .= "    --page-hero-w: {$pageHeroW}px;\n";
        }
        if (!empty($pageHeroH)) {
            $css .= "    --page-hero-h: {$pageHeroH}px;\n";
        }
        if (in_array($pageHeroFitMode, ['contain', 'cover'], true)) {
            $css .= "    --page-hero-fit: {$pageHeroFitMode};\n";
        }

        $css .= "}\n";
        $css .= "\nbody {\n";
        $css .= "    font-family: var(--font-ui, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);\n";
        $css .= "    font-size: var(--fs-base, 14.5px);\n";
        $css .= "    line-height: var(--lh-base, 1.75);\n";
        $css .= "    background: var(--bg-secondary);\n";
        $css .= "    color: var(--text-primary);\n";
        $css .= "}\n";
        $css .= "h1, h2, h3, h4, h5, h6, .section-label, .home-featured-banner__title, .post-card-title, .article-body h4, .site-logo, .main-nav a, .sub-nav a {\n";
        $css .= "    font-family: var(--font-brand, 'Space Grotesk', 'Sora', 'Inter', sans-serif);\n";
        $css .= "}\n";
        $css .= "h1, h2, h3 { font-weight: var(--fw-heading, 700); }\n";
        $css .= ".main-nav a, .sub-nav a { font-weight: var(--fw-nav, 600); }\n";
        $css .= "code, pre, .inline-code, .code-block {\n";
        $css .= "    font-family: var(--font-code);\n";
        $css .= "}\n";
        $css .= ".post-body {\n";
        $css .= "    font-size: var(--fs-post, 15.5px);\n";
        $css .= "    line-height: var(--lh-post, 1.8);\n";
        $css .= "}\n";
        $css .= ".container { max-width: var(--container-max, 1060px); }\n";
        $css .= ".member-bar { background: var(--bg-header1); min-height: var(--member-bar-h, 36px); }\n";
        $css .= ".hdr-bar-main { background: var(--bg-header2); min-height: var(--header-h, 56px); }\n";
        $css .= ".main-menu-bar { background: var(--bg-header2); min-height: var(--main-menu-h, var(--main-nav-height, 51px)); }\n";
        $css .= ".quicklinks-bar { background: var(--bg-header3); min-height: var(--sub-bar-height, 30px); }\n";
        $css .= ".main-nav a { color: var(--text-nav-main, var(--text-nav, rgba(255,255,255,.82))); }\n";
        $css .= ".member-bar__link { color: var(--text-nav-member, rgba(255,255,255,.72)); }\n";
        $css .= ".member-bar__greeting { color: var(--text-nav-member, rgba(255,255,255,.7)); }\n";
        $css .= ".sub-nav a { color: var(--text-nav-quicklinks, var(--text-secondary)); }\n";
        $css .= ".main-nav .dropdown a { color: var(--text-nav-dropdown, rgba(226,232,240,.92)); }\n";
        $css .= ".main-nav__toggle { font-weight: var(--fw-nav, 600); }\n";
        $css .= ".site-footer { background: var(--footer-bg); border-top: 3px solid var(--footer-border); }\n";
        $css .= ".footer-bottom { background: var(--footer-bottom-bg); }\n";
        $css .= ".site-logo .logo-accent { color: var(--logo-accent, var(--accent-teal-light)); }\n";
        $css .= ".site-logo .logo-icon { background: var(--logo-accent, var(--accent-teal)); }\n";
        $css .= ".site-logo .logo-suffix { color: var(--logo-suffix-color, var(--accent-color)); }\n";
        $css .= ".site-logo img { max-height: var(--logo-max-height, 28px); }\n";
        $css .= "#scroll-progress { background: linear-gradient(90deg, var(--progress-bar-start, #2d7dd2), var(--progress-bar-end, #e8a838)); }\n";

        if (!empty($heroW)) {
            $w = max(60, (int) $heroW);
            $css .= ".post-hero-img { flex: 0 0 {$w}px !important; width: {$w}px !important; }\n";
        }
        if (!empty($heroH)) {
            $h = max(80, (int) $heroH);
            $css .= ".post-hero-img { min-height: {$h}px; max-height: {$h}px; }\n";
        }

        $thumbW = $c->get('homepage', 'article_thumb_width', '');
        $thumbH = $c->get('homepage', 'article_thumb_height', '');
        if (!empty($thumbW) || !empty($thumbH)) {
            $w = !empty($thumbW) ? max(60, (int) $thumbW) : 162;
            $h = !empty($thumbH) ? max(60, (int) $thumbH) : 215;
            $css .= ".article-thumb, .article-thumb-placeholder { flex: 0 0 {$w}px !important; width: {$w}px !important; height: {$h}px !important; }\n";
            $css .= ".article-thumb img { width: {$w}px !important; height: {$h}px !important; }\n";
            $css .= "@media (max-width: 768px) { .article-thumb, .article-thumb-placeholder { flex: none !important; width: 100% !important; height: 161px !important; max-height: 161px !important; min-height: 0 !important; aspect-ratio: auto !important; } .article-thumb img { width: 100% !important; height: 100% !important; max-height: 161px !important; } }\n";
            $css .= "@media (max-width: 480px) { .article-thumb, .article-thumb-placeholder { height: 161px !important; max-height: 161px !important; aspect-ratio: auto !important; } }\n";
        }

        $articleTitleFs = (int) ($c->get('typography', 'article_title_fontsize', 16) ?: 16);
        $css .= ".article-body h4 { font-size: {$articleTitleFs}px !important; }\n";

        $tileTitleFs = (int) ($c->get('typography', 'tile_title_fontsize', 15) ?: 15);
        $css .= ".post-card-title { font-size: {$tileTitleFs}px !important; }\n";

        $postTitleFs = (int) ($c->get('posts', 'post_title_fontsize', 36) ?: 36);
        $postTitleFs = max(30, min(56, $postTitleFs));
        $postTitleMinFs = max(24, min($postTitleFs - 4, (int) round($postTitleFs * 0.82)));
        $css .= ".post-title { font-size: clamp({$postTitleMinFs}px, 3.2vw, {$postTitleFs}px) !important; }\n";

        $pageTitleFs = (int) ($c->get('pages', 'page_title_fontsize', 36) ?: 36);
        $pageTitleFs = max(30, min(56, $pageTitleFs));
        $pageTitleMinFs = max(24, min($pageTitleFs - 4, (int) round($pageTitleFs * 0.82)));
        $css .= ".page-header-block h1 { font-size: clamp({$pageTitleMinFs}px, 3vw, {$pageTitleFs}px) !important; }\n";

        if (!empty($pageHeroW)) {
            $pageWidth = max(80, (int) $pageHeroW);
            $css .= ".page-hero-img { flex: 0 0 {$pageWidth}px !important; width: {$pageWidth}px !important; }\n";
        }
        if (!empty($pageHeroH)) {
            $pageHeight = max(100, (int) $pageHeroH);
            $css .= ".page-hero-img { height: {$pageHeight}px !important; min-height: {$pageHeight}px !important; max-height: {$pageHeight}px !important; }\n";
        }

        $excerptFs = (int) ($c->get('typography', 'article_excerpt_fontsize', 13) ?: 13);
        $css .= ".article-body p { font-size: {$excerptFs}px !important; display: block !important; -webkit-line-clamp: unset !important; overflow: visible !important; }\n";
        $css .= "@media (max-width: 768px) { .article-body p { display: -webkit-box !important; -webkit-box-orient: vertical !important; -webkit-line-clamp: 3 !important; line-clamp: 3 !important; overflow: hidden !important; font-size: var(--fs-sm) !important; line-height: 1.6 !important; } }\n";
        $css .= "@media (max-width: 480px) { .article-body p { font-size: .82rem !important; line-height: 1.55 !important; } }\n";

        $tileExcFs = (int) ($c->get('typography', 'tile_excerpt_fontsize', 12) ?: 12);
        $css .= ".post-card-excerpt { font-size: {$tileExcFs}px !important; }\n";

        return $css;
    }

    public function outputCustomStyles(): void
    {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $custom = $c->get('advanced', 'custom_css', '');
            if (!empty(trim((string) $custom))) {
                echo '<style id="cms-phinit-custom-css">' . "\n";
                echo strip_tags((string) $custom);
                echo "\n</style>\n";
            }
        } catch (\Throwable $e) {
        }
    }

    private function getHomepageLeadImageUrl(): string
    {
        if ($this->homepageLeadImageCache !== null) {
            return $this->homepageLeadImageCache;
        }

        $this->homepageLeadImageCache = '';

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $contentLocale = function_exists('phinit_get_request_content_locale')
                ? phinit_get_request_content_locale()
                : 'de';
            $localization = class_exists('CMS\\Services\\ContentLocalizationService')
                ? \CMS\Services\ContentLocalizationService::getInstance()
                : null;
            $localeCondition = function_exists('phinit_build_homepage_post_locale_condition')
                ? phinit_build_homepage_post_locale_condition($contentLocale, $localization)
                : '';
            $row = $db->get_row(
                                "SELECT p.featured_image
                                 FROM {$prefix}posts p
                                 WHERE " . phinit_post_publication_where('p') . "
                                     AND p.featured_image IS NOT NULL
                                     AND p.featured_image != ''
                   {$localeCondition}
                                 ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
                 LIMIT 1"
            );

            $this->homepageLeadImageCache = function_exists('phinit_normalize_public_media_url')
                ? phinit_normalize_public_media_url((string) ($row->featured_image ?? ''), true)
                : trim((string) ($row->featured_image ?? ''));
        } catch (\Throwable) {
            $this->homepageLeadImageCache = '';
        }

        return $this->homepageLeadImageCache;
    }
}
