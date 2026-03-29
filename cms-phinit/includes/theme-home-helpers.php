<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Services\ThemeCustomizer;

/**
 * Liefert das View-Model für die Startseite inklusive robuster Fallback-Defaults.
 *
 * @return array<string, mixed>
 */
function phinit_get_homepage_view_model(): array
{
    $defaults = [
        '_showRepo' => true,
        '_repoTitle' => 'PS-easyIT Script-Repository',
        '_repoDesc' => '',
        '_repoBadge' => '25+ Repos',
        '_repoBtnText' => 'Zum GitHub →',
        '_repoBtnUrl' => '#',
        '_showList' => true,
        '_listLabel' => 'Aktuell',
        '_listCount' => 4,
        '_listLinkUrl' => '/blog',
        '_listThumbW' => 190,
        '_listThumbH' => 115,
        '_showExcerpt' => true,
        '_showMeta' => true,
        '_showBadge' => true,
        '_showMetaCat' => true,
        '_showMetaDate' => true,
        '_showMetaRT' => true,
        '_listExcLen' => 180,
        '_tileExcLen' => 160,
        '_showListSidebar' => false,
        '_listSidebarWidth' => 260,
        '_listSidebarTitle' => '',
        '_listSidebarContent' => '',
        '_sbShowProjects' => true,
        '_sbProj1Name' => '365CMS.DE',
        '_sbProj1Desc' => 'Das eigene CMS – modular & flexibel',
        '_sbProj1Url' => 'https://365cms.de',
        '_sbProj2Name' => '365NETWORK.DE',
        '_sbProj2Desc' => 'Business-Netzwerk-Plattform',
        '_sbProj2Url' => 'https://365network.de',
        '_sbShowStatus' => true,
        '_sbStatusLabel' => 'Dienst-Status',
        '_sbShowDownloads' => false,
        '_sbDownloadsLabel' => 'Downloads & Checklisten',
        '_sbDownloadsItems' => '',
        '_sbShowSocial' => true,
        '_sbSocialLabel' => 'Folge uns',
        '_sbSocialLinkedin' => '',
        '_sbSocialGithub' => '',
        '_sbSocialGitlab' => '',
        '_sbSocialTwitter' => '',
        '_sbSocialMastodon' => '',
        '_sbSocialRss' => '',
        '_sbSocialYoutube' => '',
        '_sbSocialXing' => '',
        '_sbLabelLinkedin' => 'LinkedIn',
        '_sbLabelGithub' => 'GitHub',
        '_sbLabelGitlab' => 'GitLab',
        '_sbLabelRss' => 'RSS Feed',
        '_sbShowIdentity' => true,
        '_sbIdentityLogoUrl' => '',
        '_sbIdentityTagline' => '',
        '_sbIdentityLinkUrl' => '/',
        '_sbProj1LogoUrl' => '',
        '_sbProj2LogoUrl' => '',
        '_sbStatusServices' => "Microsoft 365|https://status.office365.com|M365\nAzure|https://status.azure.com|AZ\nStarface|https://www.starface.com/support/|SF\nAnyDesk|https://status.anydesk.com|AD\nGitHub|https://githubstatus.com|GH\nCloudflare|https://www.cloudflarestatus.com|CF",
        '_sbShowNotice' => false,
        '_sbNoticeTitle' => '💡 Aktueller Hinweis',
        '_sbNoticeText' => '',
        '_sbNoticeUrl' => '',
        '_sbNoticeUrlText' => 'Mehr erfahren →',
        '_sbShowFeaturedPosts' => false,
        '_sbFeaturedPostsLabel' => '📌 Empfohlene Artikel',
        '_sbFeaturedBadgeStyle' => 'teal',
        '_sbFeaturedRotateSeconds' => 6,
        '_sbFeaturedTitleSize' => 12.5,
        '_sbFeaturedBadgeSize' => 10.0,
        '_sbFeaturedImageLayout' => 'auto',
        '_sbFeaturedPostId1' => 0,
        '_sbFeaturedPostId2' => 0,
        '_sbFeaturedPostId3' => 0,
        '_sbFeaturedPostId4' => 0,
        '_sbFeaturedPostId5' => 0,
        '_sbFeaturedPostId6' => 0,
        '_sbFeaturedCustomImage1' => '',
        '_sbFeaturedCustomImage2' => '',
        '_sbFeaturedCustomImage3' => '',
        '_sbFeaturedCustomImage4' => '',
        '_sbFeaturedCustomImage5' => '',
        '_sbFeaturedCustomImage6' => '',
        '_showInfoGrid' => true,
        '_c1Title' => '🖥️ Admin Anleitungen',
        '_c1Text' => '',
        '_c1LinkText' => 'Anleitungen →',
        '_c1LinkUrl' => '#',
        '_c1Style' => 'default',
        '_c2Title' => '🔒 DSGVO & Compliance',
        '_c2Text' => '',
        '_c2LinkText' => 'Compliance →',
        '_c2LinkUrl' => '#',
        '_c2Style' => 'gold',
        '_showCard3' => false,
        '_c3Title' => '🚀 Projekte',
        '_c3Text' => 'Wichtige Plattformen, Repositories und Tools im Schnellzugriff.',
        '_c3LinkText' => '365CMS.DE',
        '_c3LinkUrl' => 'https://365cms.de',
        '_c3LinkText2' => '365NETWORK.DE',
        '_c3LinkUrl2' => 'https://365network.de',
        '_c3LinkText3' => 'GitHub',
        '_c3LinkUrl3' => 'https://github.com/',
        '_c3Badge' => 'Projekte',
        '_c3Style' => 'repo',
        '_showTileGrid' => true,
        '_tileLabel' => 'Weitere Beiträge',
        '_tileCount' => 6,
        '_tileCols' => 3,
        '_showTileExc' => true,
        '_showTileCat' => true,
        '_showTileDate' => true,
        '_tileImageH' => 161,
        '_tileLinkUrl' => '/archiv',
        '_spRepo' => 32,
        '_spList' => 32,
        '_spInfo' => 32,
        '_spGrid' => 32,
        '_spRss' => 0,
        '_homeHeaderSpacing' => 15,
    ];

    try {
        $customizer = ThemeCustomizer::instance();
        $siteUrl = defined('SITE_URL') ? (string) SITE_URL : '';

        $featuredSectionTitle = $customizer->get('homepage', 'featured_section_title', null);
        if (!is_string($featuredSectionTitle) || trim($featuredSectionTitle) === '') {
            $featuredSectionTitle = $customizer->get('homepage', 'article_list_label', 'Aktuell');
        }

        $featuredPostsCount = $customizer->get('homepage', 'featured_posts_count', null);
        if (!is_numeric((string) $featuredPostsCount)) {
            $featuredPostsCount = $customizer->get('homepage', 'article_list_count', 4);
        }

        $showInfoCards = $customizer->get('homepage', 'show_info_cards', null);
        if ($showInfoCards === null || $showInfoCards === '') {
            $showInfoCards = $customizer->get('homepage', 'show_info_grid', true);
        }

        $gridSectionTitle = $customizer->get('homepage', 'grid_section_title', null);
        if (!is_string($gridSectionTitle) || trim($gridSectionTitle) === '') {
            $gridSectionTitle = $customizer->get('homepage', 'tile_grid_label', 'Weitere Beiträge');
        }

        $gridPostsPerPage = $customizer->get('homepage', 'grid_posts_per_page', null);
        if (!is_numeric((string) $gridPostsPerPage)) {
            $gridPostsPerPage = $customizer->get('homepage', 'tile_grid_count', 6);
        }

        return array_merge($defaults, [
            '_showRepo' => filter_var($customizer->get('homepage', 'show_repo_card', true), FILTER_VALIDATE_BOOLEAN),
            '_repoTitle' => $customizer->get('homepage', 'repo_card_title', 'PS-easyIT Script-Repository'),
            '_repoDesc' => $customizer->get('homepage', 'repo_card_description', ''),
            '_repoBadge' => $customizer->get('homepage', 'repo_card_badge', '25+ Repos'),
            '_repoBtnText' => $customizer->get('homepage', 'repo_card_btn_text', 'Zum GitHub →'),
            '_repoBtnUrl' => $customizer->get('homepage', 'repo_card_btn_url', 'https://github.com/'),
            '_showList' => filter_var($customizer->get('homepage', 'show_article_list', true), FILTER_VALIDATE_BOOLEAN),
            '_listLabel' => (string) $featuredSectionTitle,
            '_listCount' => max(1, (int) $featuredPostsCount),
            '_listLinkUrl' => $customizer->get('homepage', 'article_list_link_url', '/blog'),
            '_listThumbW' => max(80, (int) $customizer->get('homepage', 'article_thumb_width', 190)),
            '_listThumbH' => max(60, (int) $customizer->get('homepage', 'article_thumb_height', 115)),
            '_showExcerpt' => filter_var($customizer->get('homepage', 'show_article_excerpt', true), FILTER_VALIDATE_BOOLEAN),
            '_showMeta' => filter_var($customizer->get('homepage', 'show_article_meta', true), FILTER_VALIDATE_BOOLEAN),
            '_showBadge' => filter_var($customizer->get('homepage', 'show_article_badge', true), FILTER_VALIDATE_BOOLEAN),
            '_showMetaCat' => filter_var($customizer->get('homepage', 'show_meta_category', true), FILTER_VALIDATE_BOOLEAN),
            '_showMetaDate' => filter_var($customizer->get('homepage', 'show_meta_date', true), FILTER_VALIDATE_BOOLEAN),
            '_showMetaRT' => filter_var($customizer->get('homepage', 'show_meta_readtime', true), FILTER_VALIDATE_BOOLEAN),
            '_listExcLen' => max(60, (int) $customizer->get('typography', 'article_excerpt_length', 180)),
            '_tileExcLen' => max(40, (int) $customizer->get('typography', 'tile_excerpt_length', 160)),
            '_showListSidebar' => filter_var($customizer->get('homepage', 'show_list_sidebar', false), FILTER_VALIDATE_BOOLEAN),
            '_listSidebarWidth' => max(160, (int) $customizer->get('homepage', 'list_sidebar_width', 260)),
            '_listSidebarTitle' => $customizer->get('homepage', 'list_sidebar_title', ''),
            '_listSidebarContent' => $customizer->get('homepage', 'list_sidebar_content', ''),
            '_sbShowProjects' => filter_var($customizer->get('homepage', 'sidebar_show_projects', true), FILTER_VALIDATE_BOOLEAN),
            '_sbProj1Name' => $customizer->get('homepage', 'sidebar_project1_name', '365CMS.DE'),
            '_sbProj1Desc' => $customizer->get('homepage', 'sidebar_project1_desc', 'Das eigene CMS – modular & flexibel'),
            '_sbProj1Url' => function_exists('phinit_safe_public_url')
                ? phinit_safe_public_url((string) $customizer->get('homepage', 'sidebar_project1_url', 'https://365cms.de'), $siteUrl, ['http', 'https'])
                : $customizer->get('homepage', 'sidebar_project1_url', 'https://365cms.de'),
            '_sbProj2Name' => $customizer->get('homepage', 'sidebar_project2_name', '365NETWORK.DE'),
            '_sbProj2Desc' => $customizer->get('homepage', 'sidebar_project2_desc', 'Business-Netzwerk-Plattform'),
            '_sbProj2Url' => function_exists('phinit_safe_public_url')
                ? phinit_safe_public_url((string) $customizer->get('homepage', 'sidebar_project2_url', 'https://365network.de'), $siteUrl, ['http', 'https'])
                : $customizer->get('homepage', 'sidebar_project2_url', 'https://365network.de'),
            '_sbShowStatus' => filter_var($customizer->get('homepage', 'sidebar_show_status', true), FILTER_VALIDATE_BOOLEAN),
            '_sbStatusLabel' => $customizer->get('homepage', 'sidebar_status_label', 'Dienst-Status'),
            '_sbShowDownloads' => filter_var($customizer->get('homepage', 'sidebar_show_downloads', false), FILTER_VALIDATE_BOOLEAN),
            '_sbDownloadsLabel' => $customizer->get('homepage', 'sidebar_downloads_label', 'Downloads & Checklisten'),
            '_sbDownloadsItems' => $customizer->get('homepage', 'sidebar_downloads_items', ''),
            '_sbShowSocial' => filter_var($customizer->get('homepage', 'sidebar_show_social', true), FILTER_VALIDATE_BOOLEAN),
            '_sbSocialLabel' => $customizer->get('homepage', 'sidebar_social_label', 'Folge uns'),
            '_sbSocialLinkedin' => $customizer->get('social', 'social_linkedin', ''),
            '_sbSocialGithub' => $customizer->get('social', 'social_github', ''),
            '_sbSocialGitlab' => $customizer->get('social', 'social_gitlab', ''),
            '_sbSocialTwitter' => $customizer->get('social', 'social_twitter', ''),
            '_sbSocialMastodon' => $customizer->get('social', 'social_mastodon', ''),
            '_sbSocialRss' => $customizer->get('social', 'social_rss', ''),
            '_sbSocialYoutube' => $customizer->get('social', 'social_youtube', ''),
            '_sbSocialXing' => $customizer->get('social', 'social_xing', ''),
            '_sbLabelLinkedin' => $customizer->get('social', 'social_label_linkedin', 'LinkedIn'),
            '_sbLabelGithub' => $customizer->get('social', 'social_label_github', 'GitHub'),
            '_sbLabelGitlab' => $customizer->get('social', 'social_label_gitlab', 'GitLab'),
            '_sbLabelRss' => $customizer->get('social', 'social_label_rss', 'RSS Feed'),
            '_sbShowIdentity' => filter_var($customizer->get('homepage', 'sidebar_show_identity', true), FILTER_VALIDATE_BOOLEAN),
            '_sbIdentityLogoUrl' => function_exists('phinit_safe_public_media_url')
                ? phinit_safe_public_media_url((string) $customizer->get('homepage', 'sidebar_identity_logo_url', ''), $siteUrl)
                : $customizer->get('homepage', 'sidebar_identity_logo_url', ''),
            '_sbIdentityTagline' => $customizer->get('homepage', 'sidebar_identity_tagline', ''),
            '_sbIdentityLinkUrl' => function_exists('phinit_safe_public_url')
                ? (phinit_safe_public_url((string) $customizer->get('homepage', 'sidebar_identity_link_url', '/'), $siteUrl, ['http', 'https']) ?: '/')
                : $customizer->get('homepage', 'sidebar_identity_link_url', '/'),
            '_sbProj1LogoUrl' => function_exists('phinit_safe_public_media_url')
                ? phinit_safe_public_media_url((string) $customizer->get('homepage', 'sidebar_project1_logo_url', ''), $siteUrl)
                : $customizer->get('homepage', 'sidebar_project1_logo_url', ''),
            '_sbProj2LogoUrl' => function_exists('phinit_safe_public_media_url')
                ? phinit_safe_public_media_url((string) $customizer->get('homepage', 'sidebar_project2_logo_url', ''), $siteUrl)
                : $customizer->get('homepage', 'sidebar_project2_logo_url', ''),
            '_sbStatusServices' => $customizer->get(
                'homepage',
                'sidebar_status_services',
                $defaults['_sbStatusServices']
            ),
            '_sbShowNotice' => filter_var($customizer->get('homepage', 'sidebar_show_notice', false), FILTER_VALIDATE_BOOLEAN),
            '_sbNoticeTitle' => $customizer->get('homepage', 'sidebar_notice_title', '💡 Aktueller Hinweis'),
            '_sbNoticeText' => $customizer->get('homepage', 'sidebar_notice_text', ''),
            '_sbNoticeUrl' => $customizer->get('homepage', 'sidebar_notice_url', ''),
            '_sbNoticeUrlText' => $customizer->get('homepage', 'sidebar_notice_url_text', 'Mehr erfahren →'),
            '_sbShowFeaturedPosts' => filter_var($customizer->get('homepage', 'sidebar_show_featured_posts', false), FILTER_VALIDATE_BOOLEAN),
            '_sbFeaturedPostsLabel' => $customizer->get('homepage', 'sidebar_featured_posts_label', '📌 Empfohlene Artikel'),
            '_sbFeaturedBadgeStyle' => $customizer->get('homepage', 'sidebar_featured_badge_style', 'teal'),
            '_sbFeaturedRotateSeconds' => max(2, min(60, (int) $customizer->get('homepage', 'sidebar_featured_rotate_seconds', 6))),
            '_sbFeaturedTitleSize' => max(10, min(20, (float) $customizer->get('homepage', 'sidebar_featured_title_size', 12.5))),
            '_sbFeaturedBadgeSize' => max(8, min(18, (float) $customizer->get('homepage', 'sidebar_featured_badge_size', 10))),
            '_sbFeaturedImageLayout' => $customizer->get('homepage', 'sidebar_featured_image_layout', 'auto'),
            '_sbFeaturedPostId1' => (int) $customizer->get('homepage', 'sidebar_featured_post_1', 0),
            '_sbFeaturedPostId2' => (int) $customizer->get('homepage', 'sidebar_featured_post_2', 0),
            '_sbFeaturedPostId3' => (int) $customizer->get('homepage', 'sidebar_featured_post_3', 0),
            '_sbFeaturedPostId4' => (int) $customizer->get('homepage', 'sidebar_featured_post_4', 0),
            '_sbFeaturedPostId5' => (int) $customizer->get('homepage', 'sidebar_featured_post_5', 0),
            '_sbFeaturedPostId6' => (int) $customizer->get('homepage', 'sidebar_featured_post_6', 0),
            '_sbFeaturedCustomImage1' => $customizer->get('homepage', 'sidebar_featured_custom_image_1', ''),
            '_sbFeaturedCustomImage2' => $customizer->get('homepage', 'sidebar_featured_custom_image_2', ''),
            '_sbFeaturedCustomImage3' => $customizer->get('homepage', 'sidebar_featured_custom_image_3', ''),
            '_sbFeaturedCustomImage4' => $customizer->get('homepage', 'sidebar_featured_custom_image_4', ''),
            '_sbFeaturedCustomImage5' => $customizer->get('homepage', 'sidebar_featured_custom_image_5', ''),
            '_sbFeaturedCustomImage6' => $customizer->get('homepage', 'sidebar_featured_custom_image_6', ''),
            '_showInfoGrid' => filter_var($showInfoCards, FILTER_VALIDATE_BOOLEAN),
            '_c1Title' => $customizer->get('homepage', 'info_card1_title', '🖥️ Admin Anleitungen'),
            '_c1Text' => $customizer->get('homepage', 'info_card1_text', 'Schritt-für-Schritt-Tutorials für Microsoft 365 Administration.'),
            '_c1LinkText' => $customizer->get('homepage', 'info_card1_link_text', 'Alle Anleitungen ansehen →'),
            '_c1LinkUrl' => $customizer->get('homepage', 'info_card1_link_url', '/kategorie/anleitungen'),
            '_c1Style' => $customizer->get('homepage', 'info_card1_style', 'default'),
            '_c2Title' => $customizer->get('homepage', 'info_card2_title', '🔒 DSGVO & Compliance'),
            '_c2Text' => $customizer->get('homepage', 'info_card2_text', 'Konfigurationsanleitungen und Best Practices für Microsoft Purview.'),
            '_c2LinkText' => $customizer->get('homepage', 'info_card2_link_text', 'Compliance-Center →'),
            '_c2LinkUrl' => $customizer->get('homepage', 'info_card2_link_url', '/kategorie/compliance'),
            '_c2Style' => $customizer->get('homepage', 'info_card2_style', 'gold'),
            '_showCard3' => filter_var($customizer->get('homepage', 'show_info_card3', false), FILTER_VALIDATE_BOOLEAN),
            '_c3Title' => $customizer->get('homepage', 'info_card3_title', '🚀 Projekte'),
            '_c3Text' => $customizer->get('homepage', 'info_card3_text', 'Wichtige Plattformen, Repositories und Tools im Schnellzugriff.'),
            '_c3LinkText' => $customizer->get('homepage', 'info_card3_link_text', '365CMS.DE'),
            '_c3LinkUrl' => $customizer->get('homepage', 'info_card3_link_url', 'https://365cms.de'),
            '_c3LinkText2' => $customizer->get('homepage', 'info_card3_link_text_2', '365NETWORK.DE'),
            '_c3LinkUrl2' => $customizer->get('homepage', 'info_card3_link_url_2', 'https://365network.de'),
            '_c3LinkText3' => $customizer->get('homepage', 'info_card3_link_text_3', 'GitHub'),
            '_c3LinkUrl3' => $customizer->get('homepage', 'info_card3_link_url_3', 'https://github.com/'),
            '_c3Badge' => $customizer->get('homepage', 'info_card3_badge', 'Projekte'),
            '_c3Style' => $customizer->get('homepage', 'info_card3_style', 'repo'),
            '_showTileGrid' => filter_var($customizer->get('homepage', 'show_tile_grid', true), FILTER_VALIDATE_BOOLEAN),
            '_tileLabel' => (string) $gridSectionTitle,
            '_tileCount' => max(1, (int) $gridPostsPerPage),
            '_tileCols' => max(2, min(4, (int) $customizer->get('homepage', 'tile_grid_columns', 3))),
            '_showTileExc' => filter_var($customizer->get('homepage', 'show_tile_excerpt', true), FILTER_VALIDATE_BOOLEAN),
            '_showTileCat' => filter_var($customizer->get('homepage', 'show_tile_category', true), FILTER_VALIDATE_BOOLEAN),
            '_showTileDate' => filter_var($customizer->get('homepage', 'show_tile_date', true), FILTER_VALIDATE_BOOLEAN),
            '_tileImageH' => max(161, min(300, (int) $customizer->get('homepage', 'tile_grid_image_height', 161))),
            '_tileLinkUrl' => $customizer->get('homepage', 'tile_grid_link_url', '/archiv'),
            '_spRepo' => max(0, (int) $customizer->get('homepage', 'spacing_repo_card', 32)),
            '_spList' => max(0, (int) $customizer->get('homepage', 'spacing_article_list', 32)),
            '_spInfo' => max(0, (int) $customizer->get('homepage', 'spacing_info_cards', 32)),
            '_spGrid' => max(0, (int) $customizer->get('homepage', 'spacing_tile_grid', 32)),
            '_spRss' => max(0, (int) $customizer->get('homepage', 'spacing_rss_feeds', 0)),
            '_homeHeaderSpacing' => max(0, min(30, (int) $customizer->get('homepage', 'home_header_content_spacing', 15))),
        ]);
    } catch (\Throwable $_e) {
        return $defaults;
    }
}

/**
 * Ermittelt die aktuelle Content-Locale aus der Request-URI.
 */
function phinit_get_request_content_locale(): string
{
    try {
        $requestPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
        $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($requestPath);

        return (string) ($context['locale'] ?? 'de');
    } catch (\Throwable $_e) {
        return 'de';
    }
}

/**
 * Baut die SQL-Bedingung für sprachspezifisch verfügbare Beiträge.
 */
function phinit_build_homepage_post_locale_condition(string $locale, ?\CMS\Services\ContentLocalizationService $localization = null): string
{
    $localization ??= \CMS\Services\ContentLocalizationService::getInstance();
    $locale = $localization->normalizeLocale($locale);

    $baseContentCondition = "(CHAR_LENGTH(TRIM(COALESCE(p.content, ''))) > 0"
        . " OR CHAR_LENGTH(TRIM(COALESCE(p.excerpt, ''))) > 0"
        . " OR CHAR_LENGTH(TRIM(COALESCE(p.title, ''))) > 0)";

    $englishContentCondition = "(CHAR_LENGTH(TRIM(COALESCE(p.content_en, ''))) > 0"
        . " OR CHAR_LENGTH(TRIM(COALESCE(p.excerpt_en, ''))) > 0"
        . " OR CHAR_LENGTH(TRIM(COALESCE(p.title_en, ''))) > 0)";

    $legacyEnglishOnlyCondition = "(CHAR_LENGTH(TRIM(COALESCE(p.slug_en, ''))) > 0"
        . " AND NOT {$englishContentCondition})";

    if ($locale === '' || $locale === 'de') {
        return " AND {$baseContentCondition} AND NOT {$legacyEnglishOnlyCondition}";
    }

    if (!in_array($locale, $localization->getContentLocales(), true)) {
        return '';
    }

    $localizedContentCondition = "(CHAR_LENGTH(TRIM(COALESCE(p.content_{$locale}, ''))) > 0"
        . " OR CHAR_LENGTH(TRIM(COALESCE(p.excerpt_{$locale}, ''))) > 0"
        . " OR CHAR_LENGTH(TRIM(COALESCE(p.title_{$locale}, ''))) > 0)";

    if ($locale === 'en') {
        return " AND ({$localizedContentCondition} OR {$legacyEnglishOnlyCondition})";
    }

    return " AND {$localizedContentCondition}";
}

/**
 * Prüft, ob der aktuelle Nutzer private Beitrags-Teaser im Theme sehen darf.
 */
function phinit_can_view_private_featured_posts(): bool
{
    if (!class_exists('\\CMS\\Auth')) {
        return false;
    }

    try {
        return \CMS\Auth::instance()->isLoggedIn();
    } catch (\Throwable $_e) {
        return false;
    }
}

/**
 * Liefert die Sichtbarkeitsbedingung für manuell ausgewählte Sidebar-Featured-Posts.
 */
function phinit_featured_sidebar_post_where(string $alias = 'p'): string
{
    $publicWhere = phinit_post_publication_where($alias);
    if (!phinit_can_view_private_featured_posts()) {
        return $publicWhere;
    }

    $normalizedAlias = trim($alias);
    if ($normalizedAlias !== '') {
        $normalizedAlias = rtrim($normalizedAlias, '.') . '.';
    }

    return '(' . $publicWhere . " OR {$normalizedAlias}status = 'private')";
}

/**
 * Lokalisiert Home-Posts und ergänzt den kanonischen Permalink der aktuellen Content-Locale.
 *
 * @param list<array<string, mixed>> $posts
 * @return list<array<string, mixed>>
 */
function phinit_prepare_homepage_posts(array $posts, string $locale): array
{
    $prepared = [];

    try {
        $localization = \CMS\Services\ContentLocalizationService::getInstance();
        $permalinkService = \CMS\Services\PermalinkService::getInstance();

        foreach ($posts as $post) {
            $localizedPost = $localization->localizePost($post, $locale);
            $localizedPost['featured_image'] = function_exists('phinit_normalize_public_media_url')
                ? phinit_normalize_public_media_url((string) ($localizedPost['featured_image'] ?? ''), true)
                : (string) ($localizedPost['featured_image'] ?? '');
            if (!empty($localizedPost['custom_sidebar_image'])) {
                $localizedPost['custom_sidebar_image'] = function_exists('phinit_normalize_public_media_url')
                    ? phinit_normalize_public_media_url((string) $localizedPost['custom_sidebar_image'], true)
                    : (string) $localizedPost['custom_sidebar_image'];
            }
            $localizedPost['permalink'] = $permalinkService->buildPostUrl($localizedPost, $locale);
            $prepared[] = $localizedPost;
        }

        return $prepared;
    } catch (\Throwable $_e) {
        foreach ($posts as $post) {
            $post['featured_image'] = function_exists('phinit_normalize_public_media_url')
                ? phinit_normalize_public_media_url((string) ($post['featured_image'] ?? ''), true)
                : (string) ($post['featured_image'] ?? '');
            if (!empty($post['custom_sidebar_image'])) {
                $post['custom_sidebar_image'] = function_exists('phinit_normalize_public_media_url')
                    ? phinit_normalize_public_media_url((string) $post['custom_sidebar_image'], true)
                    : (string) $post['custom_sidebar_image'];
            }
            $post['permalink'] = function_exists('phinit_build_post_url')
                ? phinit_build_post_url($post, $locale)
                : (rtrim((string) SITE_URL, '/') . '/blog/' . (string) ($post['slug'] ?? ''));
            $prepared[] = $post;
        }

        return $prepared;
    }
}

/**
 * Lädt die Startseiten-Posts inklusive Grid-Paginierung und Sidebar-Featured-Posts.
 *
 * @param array<string, mixed> $viewModel
 * @return array<string, mixed>
 */
function phinit_get_homepage_posts_payload(array $viewModel): array
{
    $defaults = [
        'featuredPosts' => [],
        'gridPosts' => [],
        'sbFeaturedPosts' => [],
        'currentPage' => 1,
        'totalPages' => 1,
    ];

    try {
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();
        $contentLocale = phinit_get_request_content_locale();
        $localization = \CMS\Services\ContentLocalizationService::getInstance();
        $localeCondition = phinit_build_homepage_post_locale_condition($contentLocale, $localization);

        $_showList = !empty($viewModel['_showList']);
        $_showTileGrid = !empty($viewModel['_showTileGrid']);
        $_listCount = max(1, (int) ($viewModel['_listCount'] ?? 4));
        $_tileCount = max(1, (int) ($viewModel['_tileCount'] ?? 6));
        $_sbShowFeaturedPosts = !empty($viewModel['_sbShowFeaturedPosts']);

        $featuredRows = $_showList
            ? ($db->get_results(
                "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.created_at, p.views,
                    p.title_en, p.excerpt_en, p.content_en,
                    c.name AS category_name,
                    c.slug AS category_slug
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                 WHERE " . phinit_post_publication_where('p') . "
                 {$localeCondition}
                 ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
                 LIMIT " . (int) $_listCount
            ) ?: [])
            : [];
            $featuredPosts = phinit_prepare_homepage_posts(array_map(static fn($r) => (array) $r, $featuredRows), $contentLocale);

        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
            $totalPosts = (int) ($db->get_var("SELECT COUNT(*) FROM {$prefix}posts p WHERE " . phinit_post_publication_where('p') . "{$localeCondition}") ?: 0);
        $_gridAvail = max(0, $totalPosts - $_listCount);
        $totalPages = max(1, (int) ceil($_gridAvail / $_tileCount));
        $_gridOffset = $_listCount + (($currentPage - 1) * $_tileCount);

        $gridRows = $_showTileGrid
            ? ($db->get_results(
                "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.created_at,
                    p.title_en, p.excerpt_en, p.content_en,
                        c.name AS category_name,
                        c.slug AS category_slug
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                 WHERE " . phinit_post_publication_where('p') . "
                 {$localeCondition}
                 ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
                 LIMIT " . (int) $_tileCount . " OFFSET " . (int) $_gridOffset
            ) ?: [])
            : [];
            $gridPosts = phinit_prepare_homepage_posts(array_map(static fn($r) => (array) $r, $gridRows), $contentLocale);

        $sbFeaturedPosts = [];
        if ($_sbShowFeaturedPosts) {
            $_featuredSlots = [
                ['id' => (int) ($viewModel['_sbFeaturedPostId1'] ?? 0), 'custom_image' => trim((string) ($viewModel['_sbFeaturedCustomImage1'] ?? ''))],
                ['id' => (int) ($viewModel['_sbFeaturedPostId2'] ?? 0), 'custom_image' => trim((string) ($viewModel['_sbFeaturedCustomImage2'] ?? ''))],
                ['id' => (int) ($viewModel['_sbFeaturedPostId3'] ?? 0), 'custom_image' => trim((string) ($viewModel['_sbFeaturedCustomImage3'] ?? ''))],
                ['id' => (int) ($viewModel['_sbFeaturedPostId4'] ?? 0), 'custom_image' => trim((string) ($viewModel['_sbFeaturedCustomImage4'] ?? ''))],
                ['id' => (int) ($viewModel['_sbFeaturedPostId5'] ?? 0), 'custom_image' => trim((string) ($viewModel['_sbFeaturedCustomImage5'] ?? ''))],
                ['id' => (int) ($viewModel['_sbFeaturedPostId6'] ?? 0), 'custom_image' => trim((string) ($viewModel['_sbFeaturedCustomImage6'] ?? ''))],
            ];
            $_fpIds = array_values(array_map(
                static fn(array $_slot): int => (int) $_slot['id'],
                array_filter($_featuredSlots, static fn(array $_slot): bool => (int) ($_slot['id'] ?? 0) > 0)
            ));

            if ($_fpIds !== []) {
                $_fpIn = implode(',', array_map('intval', $_fpIds));
                $_fpRows = $db->get_results(
                        "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.created_at,
                            p.title_en, p.excerpt_en, p.content_en,
                            c.name AS category_name,
                            c.slug AS category_slug
                     FROM {$prefix}posts p
                     LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                     WHERE p.id IN ({$_fpIn}) AND " . phinit_featured_sidebar_post_where('p') . "{$localeCondition}"
                ) ?: [];

                $_fpMap = [];
                foreach (phinit_prepare_homepage_posts(array_map(static fn($r) => (array) $r, $_fpRows), $contentLocale) as $_fpPost) {
                    $_fpMap[(int) $_fpPost['id']] = $_fpPost;
                }

                foreach ($_featuredSlots as $_slot) {
                    $_slotId = (int) ($_slot['id'] ?? 0);
                    if ($_slotId <= 0 || !isset($_fpMap[$_slotId])) {
                        continue;
                    }

                    $_fpPost = $_fpMap[$_slotId];
                    $_customImage = trim((string) ($_slot['custom_image'] ?? ''));
                    if ($_customImage !== '') {
                        $_fpPost['custom_sidebar_image'] = $_customImage;
                        $_fpPost['featured_image'] = '';
                        $_fpPost['sidebar_image_source'] = 'custom';
                    } else {
                        $_fpPost['sidebar_image_source'] = 'featured';
                    }

                    $sbFeaturedPosts[] = $_fpPost;
                }
            }
        }

        return [
            'featuredPosts' => $featuredPosts,
            'gridPosts' => $gridPosts,
            'sbFeaturedPosts' => $sbFeaturedPosts,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'contentLocale' => $contentLocale,
        ];
    } catch (\Throwable $_e) {
        return $defaults;
    }
}

/**
 * Lädt die konfigurierten Homepage-Feed-Sektionen für cms-feed.
 *
 * @return list<array{channel: array<string, mixed>, items: array<int, mixed>}>
 */
function phinit_get_homepage_feed_sections(): array
{
    try {
        $customizer = ThemeCustomizer::instance();
        $showFeeds = filter_var($customizer->get('homepage', 'show_feed_section', true), FILTER_VALIDATE_BOOLEAN);
        $feed1Id = (int) $customizer->get('homepage', 'feed1_channel_id', 0);
        $feed2Id = (int) $customizer->get('homepage', 'feed2_channel_id', 0);
        $feed1Count = max(1, (int) $customizer->get('homepage', 'feed1_count', 5));
        $feed2Count = max(1, (int) $customizer->get('homepage', 'feed2_count', 5));
    } catch (
        \Throwable $_e
    ) {
        return [];
    }

    if (!$showFeeds
        || !\CMS\PluginManager::instance()->isPluginActive('cms-feed')
        || !class_exists('CMS_Feed_Database')
    ) {
        return [];
    }

    $hasFeedConsent = true;

    if (class_exists('CMS_Feed')) {
        $feedPlugin = \CMS_Feed::instance();
        if (method_exists($feedPlugin, 'has_public_feed_consent')) {
            $hasFeedConsent = $feedPlugin->has_public_feed_consent();
        }
    }

    if ($hasFeedConsent && class_exists('\\CMS\\Services\\CookieConsentService')) {
        $hasFeedConsent = \CMS\Services\CookieConsentService::getInstance()->hasConsentForService('cms_feed', 'external_media', true);
    }

    if (!$hasFeedConsent) {
        return [];
    }

    try {
        $feedDb = \CMS_Feed_Database::instance();
        $feedSections = [];
        $usedChannelIds = [];

        $appendFeedSection = static function (array &$sections, array &$usedIds, array $channel, int $feedCount, \CMS_Feed_Database $database): void {
            $channelId = (int)($channel['id'] ?? 0);
            if ($channelId <= 0 || in_array($channelId, $usedIds, true) || empty($channel['is_active'])) {
                return;
            }

            $items = (array)$database->get_items(['channel_id' => $channelId], 0, max(1, $feedCount));
            if ($items === []) {
                return;
            }

            $sections[] = [
                'channel' => $channel,
                'items' => $items,
            ];
            $usedIds[] = $channelId;
        };

        foreach ([[$feed1Id, $feed1Count], [$feed2Id, $feed2Count]] as [$feedId, $feedCount]) {
            if ($feedId <= 0) {
                continue;
            }

            $channel = $feedDb->get_channel($feedId);
            if (!$channel) {
                continue;
            }

            $appendFeedSection($feedSections, $usedChannelIds, (array)$channel, (int)$feedCount, $feedDb);
        }

        if (count($feedSections) < 2) {
            $fallbackCounts = [$feed1Count, $feed2Count];
            $fallbackChannels = array_values(array_filter(
                (array)$feedDb->get_channels(),
                static fn(array $channel): bool => !empty($channel['is_active'])
            ));

            foreach ($fallbackChannels as $index => $channel) {
                $count = $fallbackCounts[count($feedSections)] ?? end($fallbackCounts) ?: 5;
                $appendFeedSection($feedSections, $usedChannelIds, (array)$channel, (int)$count, $feedDb);

                if (count($feedSections) >= 2) {
                    break;
                }
            }
        }

        return $feedSections;
    } catch (\Throwable $_e) {
        return [];
    }
}
