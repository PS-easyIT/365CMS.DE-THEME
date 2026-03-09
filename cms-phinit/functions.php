<?php
/**
 * Theme-Funktionen – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 * @version 1.0.0
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('CMS_PHINIT_THEME_VERSION', '1.4.0');
define('CMS_PHINIT_THEME_DIR',     THEME_PATH . 'cms-phinit/');
define('CMS_PHINIT_THEME_URL',     rtrim(\CMS\ThemeManager::instance()->getThemeUrl(), '/') . '/');

/**
 * Theme-Hauptklasse (Singleton)
 */
final class CMS_Phinit_Theme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        // Assets
        \CMS\Hooks::addAction('head', [$this, 'outputPreconnect'],       1);
        \CMS\Hooks::addAction('head', [$this, 'outputGoogleFonts'],      5);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'],         8);
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],         15);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'],    20);
        \CMS\Hooks::addAction('head', [$this, 'outputSchemaOrg'],       25);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomHeaderCode'], 99);

        // Scripts ans Ende des Body
        \CMS\Hooks::addAction('body_end', [$this, 'enqueueScripts'],        10);
        \CMS\Hooks::addAction('body_end', [$this, 'outputCustomFooterCode'], 99);

        // Breadcrumb (nach dem Header)
        \CMS\Hooks::addAction('after_header', [$this, 'outputBreadcrumb'], 5);

        // Menüpositionen
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Standardmenüs beim ersten Start anlegen
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus'], 20);

        // Body-Class für aktuelle Seite anreichern
        \CMS\Hooks::addFilter('body_class', [$this, 'bodyClass']);

        // Dynamischer Seitentitel
        \CMS\Hooks::addFilter('page_title', [$this, 'filterPageTitle']);
    }

    /* ── Assets ─────────────────────────────────────────────────── */

    public function enqueueStyles(): void
    {
        $cssFile = CMS_PHINIT_THEME_DIR . 'style.css';
        // Cache-Buster aus Customizer oder Datei-Timestamp
        $cbVersion = '';
        try {
            $cbVersion = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'cache_buster_css', '');
        } catch (\Throwable $e) {}
        $version = !empty(trim((string)$cbVersion)) ? $cbVersion : (file_exists($cssFile) ? filemtime($cssFile) : CMS_PHINIT_THEME_VERSION);
        echo '<link rel="stylesheet" href="' . CMS_PHINIT_THEME_URL . 'style.css?v=' . $version . '">' . "\n";

        // PhotoSwipe CSS (CMS-Asset, nur wenn per Customizer aktiv)
        $pswpEnabled = true;
        try {
            $pswpEnabled = filter_var(
                \CMS\Services\ThemeCustomizer::instance()->get('performance', 'enable_photoswipe', true),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (\Throwable $_e) {}
        $pswpCss = defined('ASSETS_PATH') ? ASSETS_PATH . 'photoswipe/photoswipe.css' : '';
        if ($pswpEnabled && !empty($pswpCss) && file_exists($pswpCss)) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars(SITE_URL . '/assets/photoswipe/photoswipe.css?' . filemtime($pswpCss), ENT_QUOTES) . '">' . "\n";
        }

        // Phinit-spezifisches CSS aus Customizer generieren
        // (überschreibt die generische generateCSS()-Methode, die andere Key-Namen erwartet)
        try {
            $css = $this->generatePhinitCSS();
            if (!empty(trim($css))) {
                echo '<style id="cms-phinit-customizer-css">' . "\n" . $css . "\n" . '</style>' . "\n";
            }
        } catch (\Throwable $e) {
            // Kein Customizer-CSS – kein Fehler
        }
    }

    public function enqueueScripts(): void
    {
        $jsFile  = CMS_PHINIT_THEME_DIR . 'assets/js/navigation.js';
        $version = file_exists($jsFile) ? filemtime($jsFile) : CMS_PHINIT_THEME_VERSION;
        echo '<script src="' . CMS_PHINIT_THEME_URL . 'assets/js/navigation.js?v=' . $version . '" defer></script>' . "\n";

        // PhotoSwipe Lightbox (CMS-Asset, nur wenn per Customizer aktiv)
        $pswpEnabled = true;
        try {
            $pswpEnabled = filter_var(
                \CMS\Services\ThemeCustomizer::instance()->get('performance', 'enable_photoswipe', true),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (\Throwable $_e) {}
        $pswpJs = defined('ASSETS_PATH') ? ASSETS_PATH . 'js/photoswipe-init.js' : '';
        if ($pswpEnabled && !empty($pswpJs) && file_exists($pswpJs)) {
            echo '<script type="module" src="' . htmlspecialchars(SITE_URL . '/assets/js/photoswipe-init.js?' . filemtime($pswpJs), ENT_QUOTES) . '"></script>' . "\n";
        }
    }

    /* ── Meta Tags, OG, Twitter-Card, Canonical ───────────────── */

    public function outputMetaTags(): void
    {
        $tm        = \CMS\ThemeManager::instance();
        $siteTitle = $tm->getSiteTitle() ?? '';
        $sitDesc   = $tm->getSiteDescription() ?? '';
        $siteUrl   = defined('SITE_URL') ? SITE_URL : '';
        $uri       = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

        $ogTitle   = $siteTitle;
        $ogDesc    = $sitDesc;
        $ogImg     = '';
        $ogType    = 'website';
        $canonical = $siteUrl . $uri;

        // SEO-Customizer-Einstellungen laden
        $cz = null;
        try { $cz = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable) {}

        $metaRobots    = $cz ? (string)$cz->get('seo', 'meta_robots',       'index,follow')        : 'index,follow';
        $canonicalSelf = $cz ? filter_var($cz->get('seo', 'canonical_self',  true), FILTER_VALIDATE_BOOLEAN) : true;
        $ogSiteName    = $cz ? (string)$cz->get('seo', 'og_site_name',       '')                   : '';
        $ogTypeDefault = $cz ? (string)$cz->get('seo', 'og_type_default',    'website')            : 'website';
        $twitterCard   = $cz ? (string)$cz->get('seo', 'twitter_card_type',  'summary_large_image') : 'summary_large_image';
        $noindexSearch = $cz ? filter_var($cz->get('seo', 'noindex_search',  true), FILTER_VALIDATE_BOOLEAN) : true;
        $noindex404    = $cz ? filter_var($cz->get('seo', 'noindex_404',     true), FILTER_VALIDATE_BOOLEAN) : true;

        $ogType = $ogTypeDefault;

        // Robots: Überschreibungen für Sonderseiten
        $httpCode = http_response_code();
        if ($noindex404 && $httpCode === 404) {
            $metaRobots = 'noindex,follow';
        } elseif ($noindexSearch && $uri === '/search') {
            $metaRobots = 'noindex,follow';
        }

        // Post-spezifische OG-Daten laden
        if (preg_match('#^/blog/([\w-]+)$#', $uri, $m)) {
            try {
                $db     = \CMS\Database::instance();
                $prefix = $db->prefix();
                $p = $db->get_row(
                    "SELECT title, excerpt, featured_image FROM {$prefix}posts WHERE slug = ? AND status = 'published' LIMIT 1",
                    [$m[1]]
                );
                if ($p) {
                    $p = is_object($p) ? (array)$p : (array)$p;
                    $ogTitle = ($p['title'] ?? '') . ' – ' . $siteTitle;
                    $ogDesc  = mb_substr(function_exists('phinit_excerpt_plain_text') ? phinit_excerpt_plain_text($p['excerpt'] ?? '') : strip_tags($p['excerpt'] ?? ''), 0, 200);
                    if (empty($ogDesc)) { $ogDesc = mb_substr($sitDesc, 0, 200); }
                    $ogImg   = $p['featured_image'] ?? '';
                    $ogType  = 'article';
                }
            } catch (\Throwable) {}
        }

        // Fallback OG-Image aus Customizer
        if (empty($ogImg) && $cz) {
            try { $ogImg = (string)$cz->get('advanced', 'og_default_image', ''); } catch (\Throwable) {}
        }

        // theme-color aus Customizer
        $themeColor = '#1e3a5f';
        try {
            if ($cz) {
                $tc = $cz->get('colors', 'primary_color', '#1e3a5f');
                if (!empty($tc)) { $themeColor = $tc; }
            }
        } catch (\Throwable) {}

        $ogSiteFinal = !empty($ogSiteName) ? $ogSiteName : $siteTitle;

        // Basis-Meta
        echo '<meta name="description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="robots" content="' . htmlspecialchars($metaRobots, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="theme-color" content="' . htmlspecialchars($themeColor, ENT_QUOTES) . '">' . "\n";
        if ($canonicalSelf) {
            echo '<link rel="canonical" href="' . htmlspecialchars($canonical, ENT_QUOTES) . '">' . "\n";
        }
        echo '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars($siteTitle, ENT_QUOTES) . ' RSS" href="' . htmlspecialchars($siteUrl . '/feed', ENT_QUOTES) . '">' . "\n";

        // Open Graph
        echo '<meta property="og:type" content="' . htmlspecialchars($ogType, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . htmlspecialchars($ogSiteFinal, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:title" content="' . htmlspecialchars($ogTitle, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:url" content="' . htmlspecialchars($canonical, ENT_QUOTES) . '">' . "\n";
        if (!empty($ogImg)) {
            echo '<meta property="og:image" content="' . htmlspecialchars($ogImg, ENT_QUOTES) . '">' . "\n";
        }

        // Twitter Card
        $twitterCardFinal = (!empty($ogImg) && $twitterCard === 'summary_large_image') ? 'summary_large_image' : $twitterCard;
        echo '<meta name="twitter:card" content="' . htmlspecialchars($twitterCardFinal, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . htmlspecialchars($ogTitle, ENT_QUOTES) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES) . '">' . "\n";
        if (!empty($ogImg)) {
            echo '<meta name="twitter:image" content="' . htmlspecialchars($ogImg, ENT_QUOTES) . '">' . "\n";
        }
    }

    /* ── Schema.org JSON-LD ─────────────────────────────────────── */

    public function outputSchemaOrg(): void
    {
        // structured_data Toggle
        try {
            if (!filter_var(\CMS\Services\ThemeCustomizer::instance()->get('seo', 'structured_data', true), FILTER_VALIDATE_BOOLEAN)) {
                return;
            }
        } catch (\Throwable) {}

        $tm        = \CMS\ThemeManager::instance();
        $siteTitle = $tm->getSiteTitle() ?? '';
        $siteUrl   = defined('SITE_URL') ? SITE_URL : '';
        $uri       = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

        // WebSite-Schema (auf jeder Seite)
        $webSite = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => $siteTitle,
            'url'      => $siteUrl,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => $siteUrl . '/search?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
        echo '<script type="application/ld+json">' . json_encode($webSite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

        // BlogPosting-Schema nur für Artikel
        if (!preg_match('#^/blog/([\w-]+)$#', $uri, $m)) { return; }
        try {
            $db     = \CMS\Database::instance();
            $prefix = $db->prefix();
            $p = $db->get_row(
                "SELECT p.*, u.display_name AS author_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}users u ON u.id = p.author_id
                 WHERE p.slug = ? AND p.status = 'published' LIMIT 1",
                [$m[1]]
            );
            if (!$p) { return; }
            $p  = is_object($p) ? (array)$p : (array)$p;
            $cz = null;
            try { $cz = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable) {}
            $authorName   = $p['author_name'] ?? ($cz ? (string)$cz->get('posts', 'author_name', '') : '');
            $authorAvatar = $cz ? (string)$cz->get('posts', 'author_avatar_url', '') : '';
            $orgImg       = $cz ? (string)$cz->get('advanced', 'og_default_image', '') : '';
            $publisher = ['@type' => 'Organization', 'name' => $siteTitle];
            if (!empty($orgImg)) {
                $publisher['logo'] = ['@type' => 'ImageObject', 'url' => $orgImg];
            }
            $bp = [
                '@context'      => 'https://schema.org',
                '@type'         => 'BlogPosting',
                'headline'      => $p['title'] ?? '',
                'description'   => mb_substr(strip_tags($p['excerpt'] ?? ''), 0, 200),
                'url'           => $siteUrl . '/blog/' . $m[1],
                'datePublished' => (string)($p['published_at'] ?? ''),
                'dateModified'  => !empty($p['updated_at']) ? (string)$p['updated_at'] : (string)($p['published_at'] ?? ''),
                'publisher'     => $publisher,
            ];
            if (!empty($authorName)) {
                $bp['author'] = ['@type' => 'Person', 'name' => $authorName];
                if (!empty($authorAvatar)) { $bp['author']['image'] = $authorAvatar; }
            }
            if (!empty($p['featured_image'])) { $bp['image'] = $p['featured_image']; }
            echo '<script type="application/ld+json">' . json_encode($bp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        } catch (\Throwable) {}
    }

    /* ── Breadcrumb-Navigation ──────────────────────────────────── */

    public function outputBreadcrumb(): void
    {
        // Customizer-Einstellungen prüfen
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $show = filter_var($c->get('layout', 'show_breadcrumb', true), FILTER_VALIDATE_BOOLEAN);
            if (!$show) { return; }
            $onPosts = filter_var($c->get('layout', 'breadcrumb_on_posts', true), FILTER_VALIDATE_BOOLEAN);
            $onPages = filter_var($c->get('layout', 'breadcrumb_on_pages', true), FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable) {
            $onPosts = true;
            $onPages = true;
        }

        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $uri     = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        if ($uri === '/' || $uri === '') { return; }

        // Kontext bestimmen und ggf. abbrechen
        $isPost = preg_match('#^/blog/[\w-]+$#', $uri);
        $isPage = !$isPost && $uri !== '/blog' && !str_starts_with($uri, '/kategorie/') && !str_starts_with($uri, '/member') && $uri !== '/search';
        if ($isPost && !$onPosts) { return; }
        if ($isPage && !$onPages) { return; }

        $crumbs = [['label' => 'Home', 'url' => $siteUrl . '/']];
        $title  = '';
        try {
            $db     = \CMS\Database::instance();
            $prefix = $db->prefix();
            if (preg_match('#^/blog/([\w-]+)$#', $uri, $m)) {
                $crumbs[] = ['label' => 'Blog', 'url' => $siteUrl . '/blog'];
                $t = $db->get_var("SELECT title FROM {$prefix}posts WHERE slug = ? LIMIT 1", [$m[1]]);
                $title = $t ? htmlspecialchars((string)$t, ENT_QUOTES) : htmlspecialchars($m[1], ENT_QUOTES);
            } elseif ($uri === '/blog') {
                $title = 'Blog';
            } elseif (preg_match('#^/kategorie/([\w-]+)$#', $uri, $m)) {
                $crumbs[] = ['label' => 'Blog', 'url' => $siteUrl . '/blog'];
                $title = htmlspecialchars(ucwords(str_replace('-', ' ', $m[1])), ENT_QUOTES);
            } elseif (str_starts_with($uri, '/member')) {
                $crumbs[] = ['label' => 'Member', 'url' => $siteUrl . '/member'];
                $memberLabels = [
                    '/member/profile'    => 'Profil',
                    '/member/favorites'  => 'Favoriten',
                    '/member/security'   => 'Sicherheit',
                    '/member/comments'   => 'Kommentare',
                    '/member/newsletter' => 'Newsletter',
                    '/member/feeds'      => 'Feed-Abos',
                    '/member/messages'   => 'Nachrichten',
                    '/member/forum'      => 'Forum',
                ];
                foreach ($memberLabels as $route => $label) {
                    if (str_starts_with($uri, $route)) { $title = $label; break; }
                }
                if (!$title && $uri !== '/member') { $title = 'Dashboard'; }
            } elseif ($uri === '/search') {
                $q = trim($_GET['q'] ?? '');
                $title = $q ? 'Suche: ' . htmlspecialchars($q, ENT_QUOTES) : 'Suche';
            } else {
                $slug = ltrim($uri, '/');
                if (!str_contains($slug, '/')) {
                    $t = $db->get_var("SELECT title FROM {$prefix}pages WHERE slug = ? AND status = 'published' LIMIT 1", [$slug]);
                    $title = $t ? htmlspecialchars((string)$t, ENT_QUOTES) : htmlspecialchars(ucwords(str_replace('-', ' ', $slug)), ENT_QUOTES);
                }
            }
        } catch (\Throwable) {}

        if (!$title) { return; }

        // JSON-LD BreadcrumbList (im Body ausgeben, wird von Google trotzdem verarbeitet)
        $ldItems = [];
        foreach ($crumbs as $i => $c) {
            $ldItems[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['label'], 'item' => $c['url']];
        }
        $ldItems[] = ['@type' => 'ListItem', 'position' => count($ldItems) + 1, 'name' => strip_tags($title), 'item' => $siteUrl . $uri];
        $bcSchema = true;
        try { $bcSchema = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('seo', 'breadcrumb_schema', true), FILTER_VALIDATE_BOOLEAN); } catch (\Throwable) {}
        if ($bcSchema) {
            echo '<script type="application/ld+json">' . json_encode(
                ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $ldItems],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . '</script>' . "\n";
        }

        // HTML Breadcrumb
        echo '<nav class="breadcrumb-nav" aria-label="Breadcrumb">' . "\n";
        echo '<div class="container"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">' . "\n";
        foreach ($crumbs as $i => $c) {
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . htmlspecialchars($c['url'], ENT_QUOTES) . '" itemprop="item"><span itemprop="name">' . htmlspecialchars($c['label'], ENT_QUOTES) . '</span></a>';
            echo '<meta itemprop="position" content="' . ($i + 1) . '">';
            echo '</li><li class="sep" aria-hidden="true">›</li>';
        }
        echo '<li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . $title . '</span>';
        echo '<meta itemprop="position" content="' . count($ldItems) . '">';
        echo '</li>' . "\n";
        echo '</ol></div>' . "\n";
        echo '</nav>' . "\n";
    }

    /* ── Hilfsmethode: Lokale Fonts aktiv? ─────────────────────── */

    private function isLocalFontsEnabled(): bool
    {
        try {
            $db  = \CMS\Database::instance();
            $row = $db->get_var(
                "SELECT option_value FROM {$db->prefix()}settings WHERE option_name = 'privacy_use_local_fonts'"
            );
            return ($row === '1');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /* ── Preconnect: nur bei Remote-Fonts ───────────────────────── */

    public function outputPreconnect(): void
    {
        $localFonts = $this->isLocalFontsEnabled();

        // DNS-Prefetch (kann auch ohne Remote-Fonts aktiv sein)
        $dnsPrefetch = true;
        try { $dnsPrefetch = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('performance', 'dns_prefetch', true), FILTER_VALIDATE_BOOLEAN); } catch (\Throwable) {}

        if (!$localFonts) {
            // Preconnect für Google Fonts
            $preconnectFonts = true;
            try { $preconnectFonts = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('performance', 'preconnect_fonts', true), FILTER_VALIDATE_BOOLEAN); } catch (\Throwable) {}
            if ($preconnectFonts) {
                echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
                echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
                if ($dnsPrefetch) {
                    echo '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' . "\n";
                    echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">' . "\n";
                }
            }
        }

        // Zusätzliche Preconnect + DNS-Prefetch URLs aus Customizer
        if ($dnsPrefetch) {
            try {
                $extra = (string)\CMS\Services\ThemeCustomizer::instance()->get('performance', 'preconnect_extra', '');
                foreach (array_filter(array_map('trim', explode("\n", $extra))) as $extraUrl) {
                    $safeUrl = filter_var($extraUrl, FILTER_VALIDATE_URL) ? htmlspecialchars($extraUrl, ENT_QUOTES) : '';
                    if (!empty($safeUrl)) {
                        echo '<link rel="dns-prefetch" href="' . $safeUrl . '">' . "\n";
                    }
                }
            } catch (\Throwable) {}
        }
    }

    /* ── Fonts laden: Local-First (Font Manager) oder Google ────── */

    public function outputGoogleFonts(): void
    {
        // 1. CMS Font Manager (On-Prem) hat absolute Priorität
        if ($this->isLocalFontsEnabled()) {
            $localCssPath = defined('ASSETS_PATH') ? ASSETS_PATH . 'css/local-fonts.css' : '';
            $localCssUrl  = defined('SITE_URL')    ? SITE_URL . '/assets/css/local-fonts.css' : '';
            if ($localCssPath && file_exists($localCssPath) && $localCssUrl) {
                $v = filemtime($localCssPath);
                echo '<link rel="stylesheet" href="' . htmlspecialchars($localCssUrl, ENT_QUOTES) . '?v=' . $v . '">' . "\n";
            }
            return; // Kein Google-Fonts-Request
        }

        // 2. Fallback: Google Fonts (nur wenn kein Local-Fonts-Flag gesetzt)
        try {
            $c     = \CMS\Services\ThemeCustomizer::instance();
            $ui    = $c->get('typography', 'font_family_ui',    'barlow');
            $brand = $c->get('typography', 'font_family_brand', 'barlow-condensed');
            $code  = $c->get('typography', 'font_family_code',  'jetbrains-mono');
            $fontMap = [
                'barlow'           => 'Barlow:wght@400;500;600;700',
                'barlow-condensed' => 'Barlow+Condensed:wght@500;600;700;800',
                'inter'            => 'Inter:wght@400;500;600;700',
                'roboto'           => 'Roboto:wght@400;500;700',
                'open-sans'        => 'Open+Sans:wght@400;600;700',
                'lato'             => 'Lato:wght@400;700',
                'montserrat'       => 'Montserrat:wght@400;600;700',
                'poppins'          => 'Poppins:wght@400;500;600;700',
                'source-sans'      => 'Source+Sans+3:wght@400;600;700',
                'nunito'           => 'Nunito:wght@400;600;700',
                'roboto-condensed' => 'Roboto+Condensed:wght@400;700',
                'oswald'           => 'Oswald:wght@500;700',
                'rajdhani'         => 'Rajdhani:wght@500;600;700',
                'exo2'             => 'Exo+2:wght@500;700',
                'jetbrains-mono'   => 'JetBrains+Mono:wght@400;600',
                'fira-code'        => 'Fira+Code:wght@400;600',
                'source-code'      => 'Source+Code+Pro:wght@400;600',
            ];
            $families = [];
            foreach (array_unique([$ui, $brand, $code]) as $slug) {
                if (isset($fontMap[$slug])) {
                    $families[] = $fontMap[$slug];
                }
            }
            if (empty($families)) { return; }
            $url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $families) . '&display=swap';
            echo '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . "\n";
        } catch (\Throwable $e) {}
    }

    /* ── Custom Head Code aus Customizer ───────────────────────── */

    public function outputCustomHeaderCode(): void
    {
        try {
            $code = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'custom_head_code', '');
            if (!empty(trim((string)$code))) {
                echo "\n" . (string)$code . "\n";
            }
        } catch (\Throwable $e) {}
    }

    /* ── Custom Footer Code aus Customizer ─────────────────────── */

    public function outputCustomFooterCode(): void
    {
        try {
            $code = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'custom_footer_code', '');
            if (!empty(trim((string)$code))) {
                echo "\n" . (string)$code . "\n";
            }
            // Google Analytics – nur laden wenn Consent gegeben (DSGVO)
            $gaId = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'google_analytics_id', '');
            if (!empty(trim((string)$gaId)) && preg_match('/^G-[A-Z0-9]{6,}$/', trim((string)$gaId))) {
                $gaId = htmlspecialchars(trim((string)$gaId), ENT_QUOTES);
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
        } catch (\Throwable $e) {}
    }

    /* ── Custom Styles aus Customizer ──────────────────────────── */

    /**
     * Phinit-spezifisches CSS aus Customizer generieren.
     * Mappt die Customizer-Keys (colors.primary_color, typography.font_family_ui, etc.)
     * auf die CSS Custom Properties in style.css.
     */
    private function generatePhinitCSS(): string
    {
        $c = \CMS\Services\ThemeCustomizer::instance();
        $css = "/* CMS Phinit – Customizer CSS */\n:root {\n";

        // ── Farben → CSS Custom Properties ──
        $colorMap = [
            'primary_color'      => '--primary-color',
            'primary_dark'       => '--primary-dark',
            'primary_mid'        => '--primary-mid',
            'primary_light'      => '--primary-light',
            'accent_color'       => '--accent-color',
            'accent_hover'       => '--accent-hover',
            'accent_blue'        => '--accent-blue',
            'accent_blue2'       => '--accent-blue2',
            'accent_teal'        => '--accent-teal',
            'accent_teal_light'  => '--accent-teal-light',
            'bg_header1'         => '--bg-header1',
            'bg_header2'         => '--bg-header2',
            'bg_header3'         => '--bg-header3',
            'bg_primary'         => '--bg-primary',
            'bg_secondary'       => '--bg-secondary',
            'bg_dark'            => '--bg-dark',
            'text_primary'       => '--text-primary',
            'text_secondary'     => '--text-secondary',
            'text_muted'         => '--text-muted',
            'text_nav'           => '--text-nav',
            'text_nav_member'    => '--text-nav-member',
            'text_nav_main'      => '--text-nav-main',
            'text_nav_quicklinks'=> '--text-nav-quicklinks',
            'text_nav_dropdown'  => '--text-nav-dropdown',
            'logo_suffix_color'  => '--logo-suffix-color',
            'border_light'       => '--border-color',
            'footer_bg'          => '--footer-bg',
            'footer_bottom_bg'   => '--footer-bottom-bg',
            'footer_border'      => '--footer-border',
            'success_color'      => '--success-color',
            'error_color'        => '--error-color',
            'progress_bar_start' => '--progress-bar-start',
            'progress_bar_end'   => '--progress-bar-end',
        ];
        foreach ($colorMap as $key => $var) {
            $val = $c->get('colors', $key, '');
            if (!empty($val) && $val !== '') {
                $css .= "    {$var}: {$val};\n";
            }
        }

        // ── Typografie ──
        $fontMapSlug = [
            'barlow'           => "'Barlow', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'barlow-condensed' => "'Barlow Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'inter'            => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'roboto'           => "'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'open-sans'        => "'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'lato'             => "'Lato', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'montserrat'       => "'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'poppins'          => "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'source-sans'      => "'Source Sans 3', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'nunito'           => "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'roboto-condensed' => "'Roboto Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'oswald'           => "'Oswald', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'rajdhani'         => "'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'exo2'             => "'Exo 2', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'jetbrains-mono'   => "'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace",
            'fira-code'        => "'Fira Code', 'JetBrains Mono', monospace",
            'source-code'      => "'Source Code Pro', 'Fira Code', monospace",
            'cascadia'         => "'Cascadia Code', 'JetBrains Mono', monospace",
            'system'           => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'system-mono'      => "'Cascadia Code', 'Consolas', 'Courier New', monospace",
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
            'font_size_base'      => ['--fs-base', 'px'],
            'font_size_post'      => ['--fs-post', 'px'],
            'line_height_base'    => ['--lh-base', ''],
            'line_height_post'    => ['--lh-post', ''],
        ];
        foreach ($typoNumMap as $key => $info) {
            $val = $c->get('typography', $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$info[0]}: {$val}{$info[1]};\n";
            }
        }
        $fwHead = $c->get('typography', 'font_weight_heading', '');
        if (!empty($fwHead)) { $css .= "    --fw-heading: {$fwHead};\n"; }
        $fwNav = $c->get('typography', 'font_weight_nav', '');
        if (!empty($fwNav)) { $css .= "    --fw-nav: {$fwNav};\n"; }

        // ── Layout ──
        $layoutMap = [
            'container_width'  => ['--container-max', 'px'],
            'sidebar_width'    => ['--sidebar-width', 'px'],
            'border_radius'    => ['--radius-sm', 'px'],
            'border_radius_md' => ['--radius', 'px'],
            'spacing_header_content' => ['--spacing-header-content', 'px'],
            'spacing_content_footer' => ['--spacing-content-footer', 'px'],
            'content_gap'            => ['--content-gap', 'px'],
            'spacing_sections'       => ['--spacing-sections', 'px'],
        ];
        foreach ($layoutMap as $key => $info) {
            $val = $c->get('layout', $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$info[0]}: {$val}{$info[1]};\n";
            }
        }
        // sidebar_position: String-Wert ohne Einheit
        $sidebarPos = $c->get('layout', 'sidebar_position', '');
        if (!empty($sidebarPos)) { $css .= "    --sidebar-position: {$sidebarPos};\n"; }

        // ── Header ──
        $logoAccent = $c->get('header', 'logo_accent_color', '');
        if (!empty($logoAccent)) { $css .= "    --logo-accent: {$logoAccent};\n"; }
        $logoHeight = $c->get('header', 'logo_max_height', '');
        if (!empty($logoHeight)) { $css .= "    --logo-max-height: {$logoHeight}px;\n"; }
        $memberBarH = $c->get('header', 'member_bar_height', '');
        if (!empty($memberBarH)) { $css .= "    --member-bar-h: {$memberBarH}px;\n"; }
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

        // ── Posts ──
        $heroH = $c->get('posts', 'post_hero_height', '');
        $heroW = $c->get('posts', 'post_hero_width', '');
        if (!empty($heroH)) { $css .= "    --post-hero-h: {$heroH}px;\n"; }
        if (!empty($heroW)) { $css .= "    --post-hero-w: {$heroW}px;\n"; }

        $css .= "}\n";

        // ── Element-spezifische Regeln ──
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

        // Beitragsbild-Dimensionen aus Customizer
        if (!empty($heroW)) {
            $w = max(60, (int)$heroW);
            $css .= ".post-hero-img { flex: 0 0 {$w}px !important; width: {$w}px !important; }\n";
        }
        if (!empty($heroH)) {
            $h = max(80, (int)$heroH);
            $css .= ".post-hero-img { min-height: {$h}px; max-height: {$h}px; }\n";
        }

        // Artikel-Thumbnail Dimensionen
        $thumbW = $c->get('homepage', 'article_thumb_width', '');
        $thumbH = $c->get('homepage', 'article_thumb_height', '');
        if (!empty($thumbW) || !empty($thumbH)) {
            $w = !empty($thumbW) ? max(60, (int)$thumbW) : 162;
            $h = !empty($thumbH) ? max(60, (int)$thumbH) : 215;
            $css .= ".article-thumb, .article-thumb-placeholder { flex: 0 0 {$w}px !important; width: {$w}px !important; height: {$h}px !important; }\n";
            $css .= ".article-thumb img { width: {$w}px !important; height: {$h}px !important; }\n";
        }

        // ── Titel-Schriftgrößen ──
        $articleTitleFs = (int)($c->get('typography', 'article_title_fontsize', 16) ?: 16);
        $css .= ".article-body h4 { font-size: {$articleTitleFs}px !important; }\n";

        $tileTitleFs = (int)($c->get('typography', 'tile_title_fontsize', 15) ?: 15);
        $css .= ".post-card-title { font-size: {$tileTitleFs}px !important; }\n";

        $postTitleFs = (int)($c->get('posts', 'post_title_fontsize', 28) ?: 28);
        $css .= ".post-title { font-size: {$postTitleFs}px !important; }\n";

        $pageTitleFs = (int)($c->get('pages', 'page_title_fontsize', 28) ?: 28);
        $css .= ".page-header-block h1 { font-size: {$pageTitleFs}px !important; }\n";

        // ── Excerpt-Schriftgrößen ──
        $excerptFs = (int)($c->get('typography', 'article_excerpt_fontsize', 13) ?: 13);
        $css .= ".article-body p { font-size: {$excerptFs}px !important; display: block !important; -webkit-line-clamp: unset !important; overflow: visible !important; }\n";

        $tileExcFs = (int)($c->get('typography', 'tile_excerpt_fontsize', 12) ?: 12);
        $css .= ".post-card-excerpt { font-size: {$tileExcFs}px !important; }\n";

        return $css;
    }

    public function outputCustomStyles(): void
    {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $custom = $c->get('advanced', 'custom_css', '');
            if (!empty(trim((string)$custom))) {
                echo '<style id="cms-phinit-custom-css">' . "\n";
                // Nur sicheres CSS ausgeben (kein Inline-Event-Handler möglich)
                echo strip_tags((string)$custom);
                echo "\n</style>\n";
            }
        } catch (\Throwable $e) {}
    }

    /* ── Menü-Positionen ────────────────────────────────────────── */

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',       'label' => 'Hauptnavigation'];
        $locations[] = ['slug' => 'quicklinks',    'label' => 'Quicklinks (Sub-Navigation)'];
        $locations[] = ['slug' => 'footer-topics', 'label' => 'Footer – Themen'];
        $locations[] = ['slug' => 'footer-pages',  'label' => 'Footer – Seiten'];
        $locations[] = ['slug' => 'footer',        'label' => 'Footer – Rechtliches'];
        return $locations;
    }

    /** Standard-Navigation beim Erststart anlegen */
    public function seedDefaultMenus(): void
    {
        try {
            $tm = \CMS\ThemeManager::instance();

            // Hauptnavigation
            if (empty($tm->getMenu('primary'))) {
                $tm->saveMenu('primary', [
                    ['label' => 'Startseite',    'url' => '/'],
                    ['label' => 'Linux / BASH',  'url' => '/linux'],
                    ['label' => 'PowerShell',    'url' => '/powershell', 'children' => [
                        ['label' => 'Grundlagen',   'url' => '/powershell/grundlagen'],
                        ['label' => 'Glossar',      'url' => '/powershell/glossar'],
                    ]],
                    ['label' => 'Microsoft 365', 'url' => '/microsoft-365', 'children' => [
                        ['label' => 'Microsoft 365 Admin', 'url' => '/microsoft-365/admin'],
                        ['label' => 'Exchange Online',     'url' => '/microsoft-365/exchange'],
                        ['label' => 'Teams & SharePoint',  'url' => '/microsoft-365/teams'],
                    ]],
                    ['label' => 'Datenschutz',   'url' => '/datenschutz'],
                    ['label' => 'News',          'url' => '/news'],
                ]);
            }

            // Quicklinks (Sub-Navigation)
            if (empty($tm->getMenu('quicklinks'))) {
                $tm->saveMenu('quicklinks', [
                    ['label' => 'Entra ID',    'url' => '/kategorie/entra-id'],
                    ['label' => 'Intune',      'url' => '/kategorie/intune'],
                    ['label' => 'Compliance',  'url' => '/kategorie/compliance'],
                    ['label' => 'Graph API',   'url' => '/kategorie/graph-api'],
                    ['label' => 'PowerShell',  'url' => '/kategorie/powershell'],
                    ['label' => 'Security',    'url' => '/kategorie/security'],
                    ['label' => 'Exchange',    'url' => '/kategorie/exchange'],
                ]);
            }

            // Footer – Themen
            if (empty($tm->getMenu('footer-topics'))) {
                $tm->saveMenu('footer-topics', [
                    ['label' => 'Linux & BASH',          'url' => '/linux'],
                    ['label' => 'PowerShell',            'url' => '/powershell'],
                    ['label' => 'Microsoft 365',         'url' => '/microsoft-365'],
                    ['label' => 'Intune & MDM',          'url' => '/intune'],
                    ['label' => 'Datenschutz & DSGVO',   'url' => '/datenschutz'],
                    ['label' => 'IT-News',               'url' => '/news'],
                ]);
            }

            // Footer – Seiten
            if (empty($tm->getMenu('footer-pages'))) {
                $tm->saveMenu('footer-pages', [
                    ['label' => 'Über mich', 'url' => '/ueber-uns'],
                    ['label' => 'Kontakt',   'url' => '/kontakt'],
                    ['label' => 'RSS-Feed',  'url' => '/feed'],
                ]);
            }

            // Footer – Rechtliches
            if (empty($tm->getMenu('footer'))) {
                $tm->saveMenu('footer', [
                    ['label' => 'Impressum',            'url' => '/impressum'],
                    ['label' => 'Datenschutzerklärung', 'url' => '/datenschutzerklaerung'],
                    ['label' => 'Disclaimer',           'url' => '/disclaimer'],
                    ['label' => 'Cookie-Policy',        'url' => '/cookie-policy'],
                ]);
            }

        } catch (\Throwable $e) {}
    }
    /* ── Dynamischer Seiten-/Post-Titel ────────────────────────────── */

    public function filterPageTitle(string $siteTitle): string
    {
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

        try {
            $db     = \CMS\Database::instance();
            $prefix = $db->prefix();

            // Blog-Einzelartikel: /blog/<slug>
            if (preg_match('#^/blog/([\w-]+)$#', $uri, $m)) {
                $title = $db->get_var(
                    "SELECT title FROM {$prefix}posts WHERE slug = ? AND status = 'published' LIMIT 1",
                    [$m[1]]
                );
                if ($title) return htmlspecialchars((string)$title, ENT_QUOTES) . ' – ' . $siteTitle;
            }

            // Seite: /<slug> (ausgenommen bekannte Routen)
            $skipRoutes = ['', '/', 'blog', 'login', 'register', 'logout', 'search', 'feed', 'member'];
            $slug = ltrim($uri, '/');
            if (!empty($slug) && !in_array($slug, $skipRoutes, true) && !str_contains($slug, '/')) {
                $title = $db->get_var(
                    "SELECT title FROM {$prefix}pages WHERE slug = ? AND status = 'published' LIMIT 1",
                    [$slug]
                );
                if ($title) return htmlspecialchars((string)$title, ENT_QUOTES) . ' – ' . $siteTitle;
            }

            // Kategorie-Seiten: /kategorie/<slug>
            if (preg_match('#^/kategorie/([\.\w-]+)$#', $uri, $m)) {
                $label = ucwords(str_replace('-', ' ', $m[1]));
                return $label . ' – ' . $siteTitle;
            }

            // Member-Bereich
            if (str_starts_with($uri, '/member')) {
                $memberTitles = [
                    '/member/dashboard'     => 'Dashboard',
                    '/member/profile'       => 'Mein Profil',
                    '/member/favorites'     => 'Favoriten',
                    '/member/comments'      => 'Meine Kommentare',
                    '/member/newsletter'    => 'Newsletter',
                    '/member/feeds'         => 'Feed-Abos',
                    '/member/forum'         => 'Forum',
                    '/member/security'      => 'Sicherheit',
                    '/member/messages'      => 'Nachrichten',
                    '/member/notifications' => 'Benachrichtigungen',
                ];
                foreach ($memberTitles as $route => $label) {
                    if (str_starts_with($uri, $route)) {
                        return $label . ' – ' . $siteTitle;
                    }
                }
                return 'Member-Bereich – ' . $siteTitle;
            }

            // Blog-Listing
            if ($uri === '/blog') return 'Blog – ' . $siteTitle;

            // Such-Ergebnisse
            if ($uri === '/search') {
                $q = trim($_GET['q'] ?? '');
                if ($q) return 'Suche: ' . htmlspecialchars($q, ENT_QUOTES) . ' – ' . $siteTitle;
                return 'Suche – ' . $siteTitle;
            }

        } catch (\Throwable $e) {}

        return $siteTitle . ' – IT-Blog &amp; Tutorials';
    }
    /* ── Body-Class ────────────────────────────────────────────── */

    public function bodyClass(string $classes): string
    {
        $add = [];
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        if ($uri === '/' || $uri === '') {
            $add[] = 'home';
        } else {
            $add[] = 'singular';
        }
        // Blog-Einzelbeitrag erkennen
        if (preg_match('#^/blog/.+#', $uri)) {
            $add[] = 'is-post';
        }
        // Member-Bereich
        if (str_starts_with($uri, '/member') || str_starts_with($uri, '/dashboard')) {
            $add[] = 'is-member';
        }
        return trim($classes . ' ' . implode(' ', $add));
    }
}

// Theme initialisieren
CMS_Phinit_Theme::instance();

/* ── Template-Helper ─────────────────────────────────────────── */

if (!function_exists('get_theme_part')) {
    /**
     * Theme-Partial laden (header.php, footer.php, …)
     */
    function get_theme_part(string $part, array $vars = []): void
    {
        $file = CMS_PHINIT_THEME_DIR . $part . '.php';
        if (!file_exists($file)) {
            return;
        }
        if (!empty($vars)) {
            extract($vars, EXTR_SKIP);
        }
        include $file;
    }
}

if (!function_exists('theme_is_logged_in')) {
    function theme_is_logged_in(): bool
    {
        try {
            return \CMS\Auth::instance()->isLoggedIn();
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('phinit_reading_time')) {
    /**
     * Lesezeit in Minuten schätzen
     */
    function phinit_reading_time(string $content, int $wpm = 0): int
    {
        if ($wpm <= 0) {
            try {
                $wpm = (int)\CMS\Services\ThemeCustomizer::instance()->get('posts', 'reading_time_wpm', 220);
            } catch (\Throwable) {}
            if ($wpm <= 0) { $wpm = 220; }
        }
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int)round($wordCount / $wpm));
    }
}

if (!function_exists('phinit_excerpt_plain_text')) {
    /**
     * Wandelt HTML oder Editor.js-JSON in reinen Klartext für Textauszüge um.
     * Nutzt EditorJsRenderer falls verfügbar, fällt auf Block-Extraktion zurück.
     */
    function phinit_excerpt_plain_text(string $content): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }
        $decoded = json_decode($content, true);
        if (is_array($decoded) && isset($decoded['blocks']) && is_array($decoded['blocks'])) {
            $html = '';
            if (class_exists('\\CMS\\Services\\EditorJsRenderer')) {
                try {
                    $html = \CMS\Services\EditorJsRenderer::getInstance()->render($decoded);
                } catch (\Throwable) {}
            }
            if ($html !== '') {
                $content = $html;
            } else {
                // Fallback: Text-Felder aus Blöcken extrahieren
                $parts = [];
                foreach ($decoded['blocks'] as $block) {
                    if (!is_array($block)) { continue; }
                    $data = $block['data'] ?? null;
                    if (!is_array($data)) { continue; }
                    foreach (['text', 'caption', 'message', 'title'] as $key) {
                        if (!empty($data[$key]) && is_string($data[$key])) {
                            $parts[] = $data[$key];
                        }
                    }
                    if (!empty($data['items']) && is_array($data['items'])) {
                        foreach ($data['items'] as $item) {
                            if (is_string($item) && trim($item) !== '') { $parts[] = $item; }
                        }
                    }
                }
                $content = implode(' ', $parts);
            }
        }
        $text = trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        return preg_replace('/\s+/u', ' ', $text) ?? '';
    }
}
