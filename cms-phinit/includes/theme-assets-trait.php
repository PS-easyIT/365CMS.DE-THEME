<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Assets_Trait
{
    private function getRequestPath(): string
    {
        $requestUri = (string) (strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/');

        try {
            $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($requestUri);
            $baseUri = (string) ($context['base_uri'] ?? $requestUri);
            return $baseUri !== '' ? $baseUri : '/';
        } catch (\Throwable $e) {
            return $requestUri;
        }
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

    private function themeAssetUrl(string $relativePath, string|int $version): string
    {
        return CMS_PHINIT_THEME_URL . ltrim($relativePath, '/') . '?v=' . rawurlencode((string) $version);
    }

    private function isAuthOrMemberRequest(string $path): bool
    {
        return in_array($path, ['/login', '/register'], true)
            || str_starts_with($path, '/member')
            || str_starts_with($path, '/dashboard');
    }

    private function isBlogListingRequest(string $path): bool
    {
        return $path === '/'
            || $path === '/blog'
            || str_starts_with($path, '/kategorie/')
            || str_starts_with($path, '/tag/')
            || str_starts_with($path, '/author/');
    }

    private function isPageExtrasRequest(string $path): bool
    {
        return $path === '/search'
            || $path === '/404'
            || $path === '/error'
            || http_response_code() === 404;
    }

    private function isPostRequest(string $path): bool
    {
        return preg_match('#^/blog/[^/]+$#', $path) === 1;
    }

    private function isHubSiteRequest(string $path): bool
    {
        if ($path === '/' || $this->isBlogListingRequest($path) || $this->isAuthOrMemberRequest($path) || $this->isPageExtrasRequest($path)) {
            return false;
        }

        if ($this->isPostRequest($path)) {
            return false;
        }

        $slug = trim($path, '/');
        if ($slug === '' || str_contains($slug, '/')) {
            return false;
        }

        try {
            $db = \CMS\Database::instance();
            $contentType = $db->get_var(
                "SELECT content_type FROM {$db->prefix()}pages WHERE slug = ? AND status = 'published' LIMIT 1",
                [$slug]
            );

            return (string) $contentType === 'hub';
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isRichContentRequest(string $path, bool $isHubSite): bool
    {
        if ($isHubSite || $this->isAuthOrMemberRequest($path) || $this->isPageExtrasRequest($path) || $this->isBlogListingRequest($path)) {
            return false;
        }

        return $this->isPostRequest($path) || ($path !== '/' && !str_contains(trim($path, '/'), '/'));
    }

    private function isTemplateStylesRequest(string $path, bool $isHubSite): bool
    {
        if ($isHubSite || $this->isAuthOrMemberRequest($path) || $this->isPageExtrasRequest($path) || $this->isBlogListingRequest($path)) {
            return false;
        }

        return $this->isPostRequest($path) || ($path !== '/' && !str_contains(trim($path, '/'), '/'));
    }

    public function enqueueStyles(): void
    {
        $requestPath = $this->getRequestPath();
        $isHubSiteRequest = $this->isHubSiteRequest($requestPath);
        $loadHomepageBlogCss = $this->isBlogListingRequest($requestPath);
        $loadMemberAuthCss = $this->isAuthOrMemberRequest($requestPath);
        $loadPageExtrasCss = $this->isPageExtrasRequest($requestPath);
        $loadRichContentCss = $this->isRichContentRequest($requestPath, $isHubSiteRequest);
        $loadTemplateCss = $this->isTemplateStylesRequest($requestPath, $isHubSiteRequest);

        $cssFile = CMS_PHINIT_THEME_DIR . 'style.css';
        $templateCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/templates.css';
        $memberAuthCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/member-auth.css';
        $hubSitesCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/hub-sites.css';
        $pageExtrasCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/page-extras.css';
        $richContentCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/rich-content.css';
        $homepageBlogCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/homepage-blog.css';
        $footerConsentCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/footer-consent.css';

        $cbVersion = '';
        try {
            $cbVersion = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'cache_buster_css', '');
        } catch (\Throwable $e) {
        }

        $version = !empty(trim((string) $cbVersion)) ? $cbVersion : (file_exists($cssFile) ? filemtime($cssFile) : CMS_PHINIT_THEME_VERSION);
        $this->emitStylesheet($this->themeAssetUrl('style.css', $version));

        if ($loadTemplateCss && file_exists($templateCssFile)) {
            $templateVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($templateCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/templates.css', $templateVersion));
        }

        if ($loadMemberAuthCss && file_exists($memberAuthCssFile)) {
            $memberAuthVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($memberAuthCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/member-auth.css', $memberAuthVersion));
        }

        if ($isHubSiteRequest && file_exists($hubSitesCssFile)) {
            $hubSitesVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($hubSitesCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/hub-sites.css', $hubSitesVersion));
        }

        if ($loadPageExtrasCss && file_exists($pageExtrasCssFile)) {
            $pageExtrasVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($pageExtrasCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/page-extras.css', $pageExtrasVersion));
        }

        if ($loadRichContentCss && file_exists($richContentCssFile)) {
            $richContentVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($richContentCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/rich-content.css', $richContentVersion));
        }

        if ($loadHomepageBlogCss && file_exists($homepageBlogCssFile)) {
            $homepageBlogVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($homepageBlogCssFile);
            $this->emitStylesheet($this->themeAssetUrl('assets/css/homepage-blog.css', $homepageBlogVersion));
        }

        if (file_exists($footerConsentCssFile)) {
            $footerConsentVersion = !empty(trim((string) $cbVersion)) ? $cbVersion : filemtime($footerConsentCssFile);
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

        $jsFile = CMS_PHINIT_THEME_DIR . 'assets/js/navigation.js';
        $version = file_exists($jsFile) ? filemtime($jsFile) : CMS_PHINIT_THEME_VERSION;
        echo '<script src="' . CMS_PHINIT_THEME_URL . 'assets/js/navigation.js?v=' . $version . '" defer></script>' . "\n";
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
                (string) $customizer->get('typography', 'font_family_ui', 'barlow'),
                (string) $customizer->get('typography', 'font_family_brand', 'barlow-condensed'),
                (string) $customizer->get('typography', 'font_family_code', 'jetbrains-mono'),
            ];
        } catch (\Throwable $e) {
            $requestedSlugs = ['barlow', 'barlow-condensed', 'jetbrains-mono'];
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

            $available = false;
            foreach ($candidates as $candidate) {
                if (isset($fontMap[$candidate])) {
                    $available = true;
                    break;
                }
            }

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

        $dnsPrefetch = true;
        try {
            $dnsPrefetch = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('performance', 'dns_prefetch', true), FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable) {
        }

        if (!$localFonts) {
            $preconnectFonts = true;
            try {
                $preconnectFonts = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('performance', 'preconnect_fonts', true), FILTER_VALIDATE_BOOLEAN);
            } catch (\Throwable) {
            }
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
                $extra = (string) \CMS\Services\ThemeCustomizer::instance()->get('performance', 'preconnect_extra', '');
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
                echo '<link rel="stylesheet" href="' . htmlspecialchars($localCssUrl, ENT_QUOTES) . '">' . "\n";
            }
            return;
        }

        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $ui = $c->get('typography', 'font_family_ui', 'barlow');
            $brand = $c->get('typography', 'font_family_brand', 'barlow-condensed');
            $code = $c->get('typography', 'font_family_code', 'jetbrains-mono');
            $fontMap = [
                'barlow' => 'Barlow:wght@400;500;600;700',
                'barlow-condensed' => 'Barlow+Condensed:wght@500;600;700;800',
                'inter' => 'Inter:wght@400;500;600;700',
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
            echo '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . "\n";
        } catch (\Throwable $e) {
        }
    }

    public function outputCustomHeaderCode(): void
    {
        try {
            $code = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'custom_head_code', '');
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
                $gaId = htmlspecialchars(trim((string) $gaId), ENT_QUOTES);
                echo "<script>\n";
                echo "(function(){\n";
                echo "  var consent = localStorage.getItem('cms-consent');\n";
                echo "  if (consent !== 'accepted') return;\n";
                echo "  var s = document.createElement('script');\n";
                echo "  s.async = true;\n";
                echo "  s.src = 'https://www.googletagmanager.com/gtag/js?id={$gaId}';\n";
                echo "  document.head.appendChild(s);\n";
                echo "  window.dataLayer = window.dataLayer || [];\n";
                echo "  function gtag(){dataLayer.push(arguments);}\n";
                echo "  gtag('js', new Date());\n";
                echo "  gtag('config', '{$gaId}', {anonymize_ip: true});\n";
                echo "})();\n";
                echo "</script>\n";
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
            'text_nav_main' => '--text-nav-main',
            'text_nav_quicklinks' => '--text-nav-quicklinks',
            'text_nav_dropdown' => '--text-nav-dropdown',
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

        $uiFont = $c->get('typography', 'font_family_ui', 'barlow');
        if (!empty($uiFont) && isset($fontMapSlug[$uiFont])) {
            $css .= "    --font-ui: {$fontMapSlug[$uiFont]};\n";
        }
        $brandFont = $c->get('typography', 'font_family_brand', 'barlow-condensed');
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
        $sidebarPos = $c->get('layout', 'sidebar_position', '');
        if (!empty($sidebarPos)) {
            $css .= "    --sidebar-position: {$sidebarPos};\n";
        }

        $logoAccent = $c->get('header', 'logo_accent_color', '');
        if (!empty($logoAccent)) {
            $css .= "    --logo-accent: {$logoAccent};\n";
        }
        $logoHeight = $c->get('header', 'logo_max_height', '');
        if (!empty($logoHeight)) {
            $css .= "    --logo-max-height: {$logoHeight}px;\n";
        }
        $memberBarH = $c->get('header', 'member_bar_height', '');
        if (!empty($memberBarH)) {
            $css .= "    --member-bar-h: {$memberBarH}px;\n";
        }
        $mainNavH = $c->get('header', 'main_nav_height', '');
        if (!empty($mainNavH)) {
            $css .= "    --main-nav-height: {$mainNavH}px;\n";
            $css .= "    --header-h: {$mainNavH}px;\n";
        }
        $subBarH = $c->get('header', 'sub_bar_height', '');
        if (!empty($subBarH)) {
            $css .= "    --sub-bar-height: {$subBarH}px;\n";
            $css .= "    --quicklinks-h: {$subBarH}px;\n";
        }

        $heroH = $c->get('posts', 'post_hero_height', '');
        $heroW = $c->get('posts', 'post_hero_width', '');
        if (!empty($heroH)) {
            $css .= "    --post-hero-h: {$heroH}px;\n";
        }
        if (!empty($heroW)) {
            $css .= "    --post-hero-w: {$heroW}px;\n";
        }

        $css .= "}\n";
        $css .= "\nbody {\n";
        $css .= "    font-family: var(--font-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);\n";
        $css .= "    font-size: var(--fs-base, 14.5px);\n";
        $css .= "    line-height: var(--lh-base, 1.55);\n";
        $css .= "    background: var(--bg-secondary);\n";
        $css .= "    color: var(--text-primary);\n";
        $css .= "}\n";
        $css .= "h1, h2, h3, h4, h5, h6, .site-logo, .main-nav a, .sub-nav a {\n";
        $css .= "    font-family: var(--font-brand);\n";
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
        $css .= ".hdr-bar-main { background: var(--bg-header2); min-height: var(--main-nav-height, 48px); }\n";
        $css .= ".quicklinks-bar { background: var(--bg-header3); min-height: var(--sub-bar-height, 30px); }\n";
        $css .= ".main-nav a { color: var(--text-nav-main, var(--text-nav, rgba(255,255,255,.82))); }\n";
        $css .= ".member-bar__link { color: var(--text-nav-member, rgba(255,255,255,.72)); }\n";
        $css .= ".member-bar__greeting { color: var(--text-nav-member, rgba(255,255,255,.7)); }\n";
        $css .= ".sub-nav a { color: var(--text-nav-quicklinks, var(--text-secondary)); }\n";
        $css .= ".main-nav .dropdown a { color: var(--text-nav-dropdown, rgba(255,255,255,.82)); }\n";
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
        }

        $articleTitleFs = (int) ($c->get('typography', 'article_title_fontsize', 16) ?: 16);
        $css .= ".article-body h4 { font-size: {$articleTitleFs}px !important; }\n";

        $tileTitleFs = (int) ($c->get('typography', 'tile_title_fontsize', 15) ?: 15);
        $css .= ".post-card-title { font-size: {$tileTitleFs}px !important; }\n";

        $postTitleFs = (int) ($c->get('posts', 'post_title_fontsize', 28) ?: 28);
        $css .= ".post-title { font-size: {$postTitleFs}px !important; }\n";

        $pageTitleFs = (int) ($c->get('pages', 'page_title_fontsize', 28) ?: 28);
        $css .= ".page-header-block h1 { font-size: {$pageTitleFs}px !important; }\n";

        $excerptFs = (int) ($c->get('typography', 'article_excerpt_fontsize', 13) ?: 13);
        $css .= ".article-body p { font-size: {$excerptFs}px !important; display: block !important; -webkit-line-clamp: unset !important; overflow: visible !important; }\n";

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
}
