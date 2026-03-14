<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

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

if (!function_exists('phinit_display_text')) {
    function phinit_display_text(?string $text): string
    {
        return trim(html_entity_decode((string)$text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}

if (!function_exists('phinit_escape_text')) {
    function phinit_escape_text(?string $text): string
    {
        return htmlspecialchars(phinit_display_text($text), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('phinit_image_loading_attributes')) {
    /**
     * Liefert standardisierte Loading-/Priority-Attribute für Theme-Bilder.
     */
    function phinit_image_loading_attributes(bool $aboveTheFold = false): string
    {
        if ($aboveTheFold) {
            return 'loading="eager" fetchpriority="high" decoding="async"';
        }

        return 'loading="lazy" decoding="async"';
    }
}

if (!function_exists('phinit_current_request_uri')) {
    function phinit_current_request_uri(): string
    {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        if ($requestUri === '') {
            return '/';
        }

        return str_starts_with($requestUri, '/') ? $requestUri : '/' . ltrim($requestUri, '/');
    }
}

if (!function_exists('phinit_current_request_path')) {
    function phinit_current_request_path(): string
    {
        $path = (string) (parse_url(phinit_current_request_uri(), PHP_URL_PATH) ?? '/');
        return $path !== '' ? $path : '/';
    }
}

if (!function_exists('phinit_get_current_locale')) {
    function phinit_get_current_locale(): string
    {
        static $locale = null;

        if (is_string($locale) && $locale !== '') {
            return $locale;
        }

        $locale = 'de';

        try {
            $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext(phinit_current_request_path());
            $resolvedLocale = strtolower(trim((string) ($context['locale'] ?? 'de')));
            if ($resolvedLocale !== '') {
                $locale = $resolvedLocale;
            }
        } catch (\Throwable) {
        }

        return $locale;
    }
}

if (!function_exists('phinit_is_english_locale')) {
    function phinit_is_english_locale(?string $locale = null): bool
    {
        return strtolower(trim((string) ($locale ?? phinit_get_current_locale()))) === 'en';
    }
}

if (!function_exists('phinit_localized_path')) {
    function phinit_localized_path(string $path, ?string $locale = null): string
    {
        $resolvedLocale = trim((string) ($locale ?? phinit_get_current_locale()));

        try {
            return \CMS\Services\ContentLocalizationService::getInstance()->buildLocalizedPath($path, $resolvedLocale);
        } catch (\Throwable) {
            return $path;
        }
    }
}

if (!function_exists('phinit_localized_href')) {
    function phinit_localized_href(string $url, ?string $locale = null, ?string $siteUrl = null): string
    {
        $resolvedLocale = trim((string) ($locale ?? phinit_get_current_locale()));
        $siteBase = rtrim((string) ($siteUrl ?? (defined('SITE_URL') ? SITE_URL : '')), '/');
        $trimmedUrl = trim($url);

        if ($trimmedUrl === '' || $trimmedUrl === '#') {
            return $trimmedUrl;
        }

        try {
            $localization = \CMS\Services\ContentLocalizationService::getInstance();

            if (preg_match('#^https?://#i', $trimmedUrl) === 1) {
                if ($siteBase === '' || !str_starts_with($trimmedUrl, $siteBase)) {
                    return $trimmedUrl;
                }

                $path = (string) (parse_url($trimmedUrl, PHP_URL_PATH) ?? '/');
                $query = (string) (parse_url($trimmedUrl, PHP_URL_QUERY) ?? '');

                return $siteBase . $localization->buildLocalizedPath($path, $resolvedLocale) . ($query !== '' ? '?' . $query : '');
            }

            if (!str_starts_with($trimmedUrl, '/')) {
                return $trimmedUrl;
            }

            return $siteBase . $localization->buildLocalizedPath($trimmedUrl, $resolvedLocale);
        } catch (\Throwable) {
            if (str_starts_with($trimmedUrl, '/') && $siteBase !== '') {
                return $siteBase . $trimmedUrl;
            }

            return $trimmedUrl;
        }
    }
}

if (!function_exists('phinit_translation_catalog')) {
    function phinit_translation_catalog(): array
    {
        return [
            'de' => [
                'continue_reading' => '… Weiter lesen →',
                'read_time_short' => '⏱ {minutes} Min.',
                'read_time_aria' => 'Lesezeit {minutes} Minuten',
                'updated_label' => 'Aktualisiert:',
                'comment_count_one' => '{count} Kommentar',
                'comment_count_other' => '{count} Kommentare',
                'article_navigation' => 'Artikel-Navigation',
                'previous_post' => '← Vorheriger Beitrag',
                'next_post' => 'Nächster Beitrag →',
                'archive_navigation' => 'Archiv-Seitennavigation',
                'previous_page' => '← Zurück',
                'previous_page_aria' => 'Vorherige Seite',
                'next_page' => 'Weiter →',
                'next_page_aria' => 'Nächste Seite',
                'home' => 'Startseite',
                'search_posts_placeholder' => 'Beiträge durchsuchen…',
                'search_submit' => 'Suchen',
                'search_term_input' => 'Suchbegriff eingeben',
                'search_start' => 'Suche starten',
                'search_results_for' => 'Suchergebnisse für',
                'hits' => '{count} Treffer',
                'reset' => 'Zurücksetzen',
                'no_results' => 'Keine Ergebnisse',
                'no_results_for' => 'Für „{query}“ wurden leider keine passenden Inhalte gefunden. Versuche einen allgemeineren Begriff oder nimm den Typ-Filter zurück.',
                'search_tip_1' => 'Nutze kürzere oder allgemeinere Begriffe.',
                'search_tip_2' => 'Teste ohne Typ-Filter, falls einer aktiv ist.',
                'search_tip_3' => 'Prüfe alternative Schreibweisen oder Synonyme.',
                'new_search' => 'Neue Suche starten',
                'go_to_blog' => 'Zum Blog',
                'search_intro_title' => 'Suche starten',
                'search_intro_text' => 'Gib einen Suchbegriff ein, um Artikel, Seiten und weitere Inhalte in einer kompakten Ergebnisübersicht zu finden.',
                'member_navigation' => 'Member-Navigation',
                'dashboard' => 'Dashboard',
                'profile' => 'Profil',
                'notifications' => 'Benachrichtigungen',
                'favorites' => 'Favoriten',
                'security' => 'Sicherheit',
                'rss_subscribe' => 'RSS-Feed abonnieren',
                'logout' => 'Logout',
                'logout_title' => 'Abmelden',
                'site_home_aria' => '{site} – Startseite',
                'main_navigation' => 'Hauptnavigation',
                'submenu_open_for' => 'Untermenü für {label} öffnen',
                'darkmode_toggle' => 'Dark Mode umschalten',
                'account' => 'Mein Konto',
                'login' => 'Login',
                'menu_open' => 'Menü öffnen',
                'mobile_navigation' => 'Mobile Navigation',
                'mobile_search' => 'Mobilsuche',
                'switch_language' => 'Sprache wechseln',
                'quicklinks' => 'Quicklinks',
                'back_to_top' => 'Zum Seitenanfang',
                'learn_more' => 'Mehr erfahren',
                'consent_accept' => 'Einwilligen',
                'consent_decline' => 'Ablehnen',
                'edit' => 'Bearbeiten',
                'edit_hubsite' => 'Diese HubSite bearbeiten',
                'edit_post' => 'Diesen Beitrag bearbeiten',
                'edit_page' => 'Diese Seite bearbeiten',
                'favorite' => 'Favorit',
                'favorited' => 'Gespeichert',
                'favorite_add' => 'Zu Favoriten hinzufügen',
                'favorite_remove' => 'Aus Favoriten entfernen',
                'authors' => 'Autorinnen & Autoren',
                'public_profile_default' => 'Öffentliche Profilangaben dieses Accounts, freigegeben über den Datenschutz-Bereich im Member-Dashboard.',
                'public_profile_data' => 'Öffentliche Profilangaben',
                'no_public_profile_data' => 'Für diese Author-Seite wurden aktuell keine zusätzlichen Profilfelder freigegeben.',
                'published_posts' => 'Veröffentlichte Beiträge',
                'author_posts' => 'Beiträge dieses Autors',
                'read_article' => 'Artikel lesen →',
                'author_nav' => 'Seitennavigation Author-Seite',
                'author_no_posts' => '{name} hat aktuell noch keine veröffentlichten Beiträge.',
            ],
            'en' => [
                'continue_reading' => '… Continue reading →',
                'read_time_short' => '⏱ {minutes} min read',
                'read_time_aria' => 'Reading time {minutes} minutes',
                'updated_label' => 'Updated:',
                'comment_count_one' => '{count} comment',
                'comment_count_other' => '{count} comments',
                'article_navigation' => 'Article navigation',
                'previous_post' => '← Previous post',
                'next_post' => 'Next post →',
                'archive_navigation' => 'Archive page navigation',
                'previous_page' => '← Back',
                'previous_page_aria' => 'Previous page',
                'next_page' => 'Next →',
                'next_page_aria' => 'Next page',
                'home' => 'Home',
                'search_posts_placeholder' => 'Search posts…',
                'search_submit' => 'Search',
                'search_term_input' => 'Enter search term',
                'search_start' => 'Start search',
                'search_results_for' => 'Search results for',
                'hits' => '{count} hits',
                'reset' => 'Reset',
                'no_results' => 'No results',
                'no_results_for' => 'Unfortunately, no matching content was found for “{query}”. Try a broader term or remove the type filter.',
                'search_tip_1' => 'Use shorter or more general terms.',
                'search_tip_2' => 'Try again without a type filter if one is active.',
                'search_tip_3' => 'Check alternative spellings or synonyms.',
                'new_search' => 'Start a new search',
                'go_to_blog' => 'Go to the blog',
                'search_intro_title' => 'Start searching',
                'search_intro_text' => 'Enter a search term to find posts, pages, and other content in a compact results overview.',
                'member_navigation' => 'Member navigation',
                'dashboard' => 'Dashboard',
                'profile' => 'Profile',
                'notifications' => 'Notifications',
                'favorites' => 'Favorites',
                'security' => 'Security',
                'rss_subscribe' => 'Subscribe to RSS feed',
                'logout' => 'Logout',
                'logout_title' => 'Sign out',
                'site_home_aria' => '{site} – Home',
                'main_navigation' => 'Main navigation',
                'submenu_open_for' => 'Open submenu for {label}',
                'darkmode_toggle' => 'Toggle dark mode',
                'account' => 'My account',
                'login' => 'Login',
                'menu_open' => 'Open menu',
                'mobile_navigation' => 'Mobile navigation',
                'mobile_search' => 'Mobile search',
                'switch_language' => 'Switch language',
                'quicklinks' => 'Quick links',
                'back_to_top' => 'Back to top',
                'learn_more' => 'Learn more',
                'consent_accept' => 'Accept',
                'consent_decline' => 'Decline',
                'edit' => 'Edit',
                'edit_hubsite' => 'Edit this hub site',
                'edit_post' => 'Edit this post',
                'edit_page' => 'Edit this page',
                'favorite' => 'Favorite',
                'favorited' => 'Saved',
                'favorite_add' => 'Add to favorites',
                'favorite_remove' => 'Remove from favorites',
                'authors' => 'Authors',
                'public_profile_default' => 'Public profile details for this account, shared via the privacy area in the member dashboard.',
                'public_profile_data' => 'Public profile details',
                'no_public_profile_data' => 'No additional profile fields are currently shared for this author page.',
                'published_posts' => 'Published posts',
                'author_posts' => 'Posts by this author',
                'read_article' => 'Read article →',
                'author_nav' => 'Author page navigation',
                'author_no_posts' => '{name} has not published any posts yet.',
            ],
        ];
    }
}

if (!function_exists('phinit_t')) {
    function phinit_t(string $key, array $replacements = [], ?string $locale = null): string
    {
        $catalog = phinit_translation_catalog();
        $resolvedLocale = strtolower(trim((string) ($locale ?? phinit_get_current_locale())));
        $messages = $catalog[$resolvedLocale] ?? $catalog['de'];
        $message = $messages[$key] ?? ($catalog['de'][$key] ?? $key);

        if ($replacements !== []) {
            $replacePairs = [];
            foreach ($replacements as $replaceKey => $replaceValue) {
                $replacePairs['{' . $replaceKey . '}'] = (string) $replaceValue;
            }
            $message = strtr($message, $replacePairs);
        }

        return $message;
    }
}

if (!function_exists('phinit_comment_count_text')) {
    function phinit_comment_count_text(int $count, ?string $locale = null): string
    {
        $key = $count === 1 ? 'comment_count_one' : 'comment_count_other';
        return phinit_t($key, ['count' => $count], $locale);
    }
}

if (!function_exists('phinit_format_date')) {
    function phinit_format_date(?string $dateValue, string $style = 'long', ?string $locale = null): string
    {
        $rawValue = trim((string) $dateValue);
        if ($rawValue === '') {
            return '';
        }

        $timestamp = strtotime($rawValue);
        if ($timestamp === false) {
            return '';
        }

        $resolvedLocale = strtolower(trim((string) ($locale ?? phinit_get_current_locale())));
        $intlLocale = $resolvedLocale === 'en' ? 'en_US' : 'de_DE';
        $pattern = $style === 'numeric'
            ? ($resolvedLocale === 'en' ? 'MM/dd/yyyy' : 'dd.MM.yyyy')
            : ($resolvedLocale === 'en' ? 'MMMM d, yyyy' : 'd. MMMM yyyy');

        if (class_exists('IntlDateFormatter')) {
            $formatter = new \IntlDateFormatter(
                $intlLocale,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                date_default_timezone_get(),
                \IntlDateFormatter::GREGORIAN,
                $pattern
            );

            $formatted = $formatter->format($timestamp);
            if ($formatted !== false) {
                return (string) $formatted;
            }
        }

        $fallbackPattern = $style === 'numeric'
            ? ($resolvedLocale === 'en' ? 'm/d/Y' : 'd.m.Y')
            : ($resolvedLocale === 'en' ? 'F j, Y' : 'd.m.Y');

        return date($fallbackPattern, $timestamp);
    }
}

if (!function_exists('phinit_get_member_edit_link')) {
    /**
     * @return array{show:bool,url:string,label:string,entity:string,entityId:int}
     */
    function phinit_get_member_edit_link(?string $requestPath = null, ?string $locale = null): array
    {
        $default = [
            'show' => false,
            'url' => '',
            'label' => '',
            'entity' => '',
            'entityId' => 0,
        ];

        try {
            $auth = \CMS\Auth::instance();
            if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
                return $default;
            }
        } catch (\Throwable) {
            return $default;
        }

        $path = trim((string) ($requestPath ?? phinit_current_request_path()));
        $path = $path !== '' ? $path : '/';
        $resolvedLocale = trim((string) ($locale ?? 'de'));

        try {
            $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($path);
            $path = trim((string) ($context['base_uri'] ?? $path));
            $resolvedLocale = trim((string) ($context['locale'] ?? $resolvedLocale));
        } catch (\Throwable) {
        }

        $path = $path !== '' ? $path : '/';

        try {
            $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
            if ($path === '/' && $host !== '') {
                $hubPage = \CMS\Services\SiteTableService::getInstance()->getHubPageByDomain($host, $resolvedLocale !== '' ? $resolvedLocale : 'de');
                if (is_array($hubPage) && (int) ($hubPage['id'] ?? 0) > 0) {
                    return [
                        'show' => true,
                        'url' => rtrim((string) SITE_URL, '/') . '/admin/hub-sites?action=edit&id=' . (int) $hubPage['id'],
                        'label' => phinit_t('edit_hubsite'),
                        'entity' => 'hub',
                        'entityId' => (int) $hubPage['id'],
                    ];
                }
            }
        } catch (\Throwable) {
        }

        try {
            $postSlug = \CMS\Services\PermalinkService::getInstance()->extractPostSlugFromPath($path);
            if ($postSlug !== null && $postSlug !== '') {
                $db = \CMS\Database::instance();
                $postId = (int) ($db->get_var(
                    "SELECT id FROM {$db->getPrefix()}posts WHERE slug = ? AND status = 'published' LIMIT 1",
                    [$postSlug]
                ) ?: 0);

                if ($postId > 0) {
                    return [
                        'show' => true,
                        'url' => rtrim((string) SITE_URL, '/') . '/admin/posts?action=edit&id=' . $postId,
                        'label' => phinit_t('edit_post'),
                        'entity' => 'post',
                        'entityId' => $postId,
                    ];
                }
            }
        } catch (\Throwable) {
        }

        $slug = trim($path, '/');
        if ($slug === '' || str_contains($slug, '/')) {
            return $default;
        }

        try {
            $hubPage = \CMS\Services\SiteTableService::getInstance()->getHubPageBySlug($slug, $resolvedLocale !== '' ? $resolvedLocale : 'de');
            if (is_array($hubPage) && (int) ($hubPage['id'] ?? 0) > 0) {
                return [
                    'show' => true,
                    'url' => rtrim((string) SITE_URL, '/') . '/admin/hub-sites?action=edit&id=' . (int) $hubPage['id'],
                    'label' => phinit_t('edit_hubsite'),
                    'entity' => 'hub',
                    'entityId' => (int) $hubPage['id'],
                ];
            }
        } catch (\Throwable) {
        }

        try {
            $db = \CMS\Database::instance();
            $pageRow = $db->get_row(
                "SELECT id FROM {$db->getPrefix()}pages WHERE slug = ? AND status = 'published' LIMIT 1",
                [$slug]
            );
            $pageId = (int) ($pageRow->id ?? 0);

            if ($pageId > 0) {
                return [
                    'show' => true,
                    'url' => rtrim((string) SITE_URL, '/') . '/admin/pages?action=edit&id=' . $pageId,
                    'label' => phinit_t('edit_page'),
                    'entity' => 'page',
                    'entityId' => $pageId,
                ];
            }
        } catch (\Throwable) {
        }

        return $default;
    }
}

if (!function_exists('phinit_page_favorites_meta_key')) {
    function phinit_page_favorites_meta_key(): string
    {
        return 'phinit_page_favorites';
    }
}

if (!function_exists('phinit_get_page_favorites_for_user')) {
    /**
     * @return array<int,array<string,mixed>>
     */
    function phinit_get_page_favorites_for_user(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        try {
            $db = \CMS\Database::instance();
            $row = $db->get_row(
                "SELECT meta_value FROM {$db->getPrefix()}user_meta WHERE user_id = ? AND meta_key = ? LIMIT 1",
                [$userId, phinit_page_favorites_meta_key()]
            );
        } catch (\Throwable) {
            return [];
        }

        $items = \CMS\Json::decodeArray($row->meta_value ?? null, []);
        $normalized = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $contentId = (int) ($item['content_id'] ?? 0);
            $contentType = (string) ($item['content_type'] ?? 'page');
            $url = trim((string) ($item['url'] ?? ''));
            $title = trim((string) ($item['title'] ?? ''));

            if ($contentId <= 0 || $contentType !== 'page' || $url === '' || $title === '') {
                continue;
            }

            $normalized[] = [
                'content_type' => 'page',
                'content_id' => $contentId,
                'title' => $title,
                'url' => $url,
                'excerpt' => trim((string) ($item['excerpt'] ?? '')),
                'featured_image' => trim((string) ($item['featured_image'] ?? '')),
                'badge' => trim((string) ($item['badge'] ?? 'Seite')),
                'created_at' => trim((string) ($item['created_at'] ?? '')),
            ];
        }

        return $normalized;
    }
}

if (!function_exists('phinit_store_page_favorites_for_user')) {
    /**
     * @param array<int,array<string,mixed>> $favorites
     */
    function phinit_store_page_favorites_for_user(int $userId, array $favorites): void
    {
        if ($userId <= 0) {
            return;
        }

        $payload = array_values(array_map(static function (array $favorite): array {
            return [
                'content_type' => 'page',
                'content_id' => (int) ($favorite['content_id'] ?? 0),
                'title' => trim((string) ($favorite['title'] ?? '')),
                'url' => trim((string) ($favorite['url'] ?? '')),
                'excerpt' => trim((string) ($favorite['excerpt'] ?? '')),
                'featured_image' => trim((string) ($favorite['featured_image'] ?? '')),
                'badge' => trim((string) ($favorite['badge'] ?? 'Seite')),
                'created_at' => trim((string) ($favorite['created_at'] ?? '')),
            ];
        }, $favorites));

        try {
            $db = \CMS\Database::instance();
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $db->execute(
                "INSERT INTO {$db->getPrefix()}user_meta (user_id, meta_key, meta_value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)",
                [$userId, phinit_page_favorites_meta_key(), $json !== false ? $json : '[]']
            );
        } catch (\Throwable) {
        }
    }
}

if (!function_exists('phinit_handle_favorite_toggle_request')) {
    function phinit_handle_favorite_toggle_request(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }

        if ((string) ($_POST['phinit_toggle_favorite'] ?? '') !== '1') {
            return;
        }

        $contentType = (string) ($_POST['favorite_content_type'] ?? 'post');
        if (!in_array($contentType, ['post', 'page'], true)) {
            return;
        }

        $contentId = (int) ($_POST['favorite_content_id'] ?? 0);
        if ($contentId <= 0) {
            return;
        }

        try {
            $auth = \CMS\Auth::instance();
            if (!$auth->isLoggedIn()) {
                return;
            }

            $currentUser = $auth->getCurrentUser();
            $userId = (int) ($currentUser->id ?? 0);
            if ($userId <= 0) {
                return;
            }

            $tokenAction = 'phinit_favorite_' . $contentType . '_' . $contentId;
            if (!\CMS\Security::instance()->verifyPersistentToken((string) ($_POST['favorite_csrf_token'] ?? ''), $tokenAction)) {
                return;
            }

            $db = \CMS\Database::instance();

            if ($contentType === 'post') {
                $exists = (int) ($db->get_var(
                    "SELECT COUNT(*) FROM {$db->getPrefix()}favorites WHERE user_id = ? AND post_id = ?",
                    [$userId, $contentId]
                ) ?: 0) > 0;

                if ($exists) {
                    $db->execute(
                        "DELETE FROM {$db->getPrefix()}favorites WHERE user_id = ? AND post_id = ?",
                        [$userId, $contentId]
                    );
                } else {
                    $db->execute(
                        "INSERT IGNORE INTO {$db->getPrefix()}favorites (user_id, post_id) VALUES (?, ?)",
                        [$userId, $contentId]
                    );
                }
            } else {
                $requestPath = phinit_current_request_path();
                $pageFavorites = phinit_get_page_favorites_for_user($userId);
                $pageFavorites = array_values(array_filter(
                    $pageFavorites,
                    static fn(array $favorite): bool => (int) ($favorite['content_id'] ?? 0) !== $contentId
                ));

                $exists = count($pageFavorites) !== count(phinit_get_page_favorites_for_user($userId));
                if (!$exists) {
                    $pageFavorites[] = [
                        'content_type' => 'page',
                        'content_id' => $contentId,
                        'title' => trim((string) ($_POST['favorite_title'] ?? 'Seite')),
                        'url' => $requestPath !== '' ? $requestPath : '/',
                        'excerpt' => trim((string) ($_POST['favorite_excerpt'] ?? '')),
                        'featured_image' => trim((string) ($_POST['favorite_featured_image'] ?? '')),
                        'badge' => trim((string) ($_POST['favorite_badge'] ?? 'Seite')),
                        'created_at' => date('c'),
                    ];
                }

                phinit_store_page_favorites_for_user($userId, $pageFavorites);
            }

            $redirectUri = phinit_current_request_uri();
            header('Location: ' . rtrim((string) SITE_URL, '/') . $redirectUri);
            exit;
        } catch (\Throwable) {
            return;
        }
    }
}

if (!function_exists('phinit_get_favorite_control')) {
    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    function phinit_get_favorite_control(string $contentType, int $contentId, array $payload = []): array
    {
        $contentType = in_array($contentType, ['post', 'page'], true) ? $contentType : 'post';
        $requestUri = phinit_current_request_uri();
        $requestPath = phinit_current_request_path();
        $loginUrl = rtrim((string) SITE_URL, '/') . '/login?redirect=' . urlencode($requestUri);
        $title = trim((string) ($payload['title'] ?? 'Eintrag'));
        $url = trim((string) ($payload['url'] ?? ($requestPath !== '' ? $requestPath : '/')));
        $excerpt = trim((string) ($payload['excerpt'] ?? ''));
        $featuredImage = trim((string) ($payload['featured_image'] ?? ''));
        $badge = trim((string) ($payload['badge'] ?? ($contentType === 'page' ? 'Seite' : 'Beitrag')));

        $state = [
            'show' => $contentId > 0,
            'contentType' => $contentType,
            'contentId' => $contentId,
            'isLoggedIn' => false,
            'isFavorited' => false,
            'loginUrl' => $loginUrl,
            'csrfToken' => '',
            'payload' => [
                'title' => $title,
                'url' => $url,
                'excerpt' => $excerpt,
                'featured_image' => $featuredImage,
                'badge' => $badge,
            ],
            'label' => phinit_t('favorite'),
            'title' => phinit_t('favorite_add'),
            'action' => 'add',
        ];

        if ($contentId <= 0) {
            return $state;
        }

        try {
            $auth = \CMS\Auth::instance();
            $state['isLoggedIn'] = $auth->isLoggedIn();
            if (!$state['isLoggedIn']) {
                return $state;
            }

            $currentUser = $auth->getCurrentUser();
            $userId = (int) ($currentUser->id ?? 0);
            if ($userId <= 0) {
                return $state;
            }

            $db = \CMS\Database::instance();

            if ($contentType === 'post') {
                $state['isFavorited'] = (int) ($db->get_var(
                    "SELECT COUNT(*) FROM {$db->getPrefix()}favorites WHERE user_id = ? AND post_id = ?",
                    [$userId, $contentId]
                ) ?: 0) > 0;
            } else {
                foreach (phinit_get_page_favorites_for_user($userId) as $favorite) {
                    if ((int) ($favorite['content_id'] ?? 0) === $contentId) {
                        $state['isFavorited'] = true;
                        break;
                    }
                }
            }

            $state['csrfToken'] = \CMS\Security::instance()->generateToken('phinit_favorite_' . $contentType . '_' . $contentId);
        } catch (\Throwable) {
            return $state;
        }

        $state['label'] = $state['isFavorited'] ? phinit_t('favorited') : phinit_t('favorite');
        $state['title'] = $state['isFavorited'] ? phinit_t('favorite_remove') : phinit_t('favorite_add');
        $state['action'] = $state['isFavorited'] ? 'remove' : 'add';

        return $state;
    }
}

if (!function_exists('phinit_render_favorite_button')) {
    /**
     * @param array<string,mixed> $favoriteControl
     */
    function phinit_render_favorite_button(array $favoriteControl): string
    {
        if (!($favoriteControl['show'] ?? false)) {
            return '';
        }

        if (!($favoriteControl['isLoggedIn'] ?? false)) {
            return '';
        }

        $isActive = (bool) ($favoriteControl['isFavorited'] ?? false);
        $class = 'content-favorite' . ($isActive ? ' content-favorite--active' : '');
        $icon = $isActive ? '★' : '☆';
        $label = htmlspecialchars((string) ($favoriteControl['label'] ?? 'Favorit'), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars((string) ($favoriteControl['title'] ?? 'Favorit'), ENT_QUOTES, 'UTF-8');

        $contentType = htmlspecialchars((string) ($favoriteControl['contentType'] ?? 'post'), ENT_QUOTES, 'UTF-8');
        $contentId = (int) ($favoriteControl['contentId'] ?? 0);
        $csrfToken = htmlspecialchars((string) ($favoriteControl['csrfToken'] ?? ''), ENT_QUOTES, 'UTF-8');
        $favoriteTitle = htmlspecialchars((string) ($favoriteControl['payload']['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $favoriteExcerpt = htmlspecialchars((string) ($favoriteControl['payload']['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8');
        $favoriteImage = htmlspecialchars((string) ($favoriteControl['payload']['featured_image'] ?? ''), ENT_QUOTES, 'UTF-8');
        $favoriteBadge = htmlspecialchars((string) ($favoriteControl['payload']['badge'] ?? ''), ENT_QUOTES, 'UTF-8');
        $pressed = $isActive ? 'true' : 'false';

        return '<form method="post" class="content-favorite-form"><input type="hidden" name="phinit_toggle_favorite" value="1"><input type="hidden" name="favorite_content_type" value="' . $contentType . '"><input type="hidden" name="favorite_content_id" value="' . $contentId . '"><input type="hidden" name="favorite_csrf_token" value="' . $csrfToken . '"><input type="hidden" name="favorite_title" value="' . $favoriteTitle . '"><input type="hidden" name="favorite_excerpt" value="' . $favoriteExcerpt . '"><input type="hidden" name="favorite_featured_image" value="' . $favoriteImage . '"><input type="hidden" name="favorite_badge" value="' . $favoriteBadge . '"><button type="submit" class="' . $class . '" aria-pressed="' . $pressed . '" aria-label="' . $title . '" title="' . $title . '"><span class="content-favorite__icon" aria-hidden="true">' . $icon . '</span><span class="content-favorite__label">' . $label . '</span></button></form>';
    }
}

if (!function_exists('phinit_render_member_flash')) {
    /**
     * @param array<string,mixed>|null $flash
     */
    function phinit_render_member_flash(?array $flash): string
    {
        if (!is_array($flash) || $flash === []) {
            return '';
        }

        $type = trim((string) ($flash['type'] ?? 'info'));
        $message = trim((string) ($flash['message'] ?? ''));
        $payload = is_array($flash['payload'] ?? null) ? $flash['payload'] : [];
        $backupCodes = is_array($payload['backup_codes'] ?? null) ? $payload['backup_codes'] : [];

        if ($message === '' && $backupCodes === []) {
            return '';
        }

        $classMap = [
            'success' => 'member-alert-success',
            'danger' => 'member-alert-error',
            'error' => 'member-alert-error',
            'warning' => 'member-alert-warning',
            'info' => 'member-alert-info',
        ];

        $class = $classMap[$type] ?? 'member-alert-info';
        $html = '<div class="member-alert ' . $class . '">';

        if ($message !== '') {
            $html .= '<p class="member-alert__message">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        if ($backupCodes !== []) {
            $html .= '<div class="member-backup-codes"><strong>Backup-Codes</strong><div class="member-backup-codes__grid">';
            foreach ($backupCodes as $code) {
                $html .= '<code>' . htmlspecialchars((string) $code, ENT_QUOTES, 'UTF-8') . '</code>';
            }
            $html .= '</div><p class="member-backup-codes__hint">Bitte speichere diese Codes sicher. Sie werden nur jetzt vollständig angezeigt.</p></div>';
        }

        $html .= '</div>';

        return $html;
    }
}
