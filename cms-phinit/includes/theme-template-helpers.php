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
                        'label' => 'Diese HubSite bearbeiten',
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
                        'label' => 'Diesen Beitrag bearbeiten',
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
                    'label' => 'Diese HubSite bearbeiten',
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
                    'label' => 'Diese Seite bearbeiten',
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
            'formGuardToken' => '',
            'label' => 'Favorit',
            'title' => 'Zu Favoriten hinzufügen',
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

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
                && (string) ($_POST['phinit_toggle_favorite'] ?? '') === '1'
                && (string) ($_POST['favorite_content_type'] ?? '') === $contentType
                && (int) ($_POST['favorite_content_id'] ?? 0) === $contentId
            ) {
                $tokenAction = 'phinit_favorite_' . $contentType . '_' . $contentId;
                if (\CMS\Security::instance()->verifyPersistentToken((string) ($_POST['favorite_csrf_token'] ?? ''), $tokenAction)) {
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
                                'title' => $title,
                                'url' => $url,
                                'excerpt' => $excerpt,
                                'featured_image' => $featuredImage,
                                'badge' => $badge,
                                'created_at' => date('c'),
                            ];
                        }

                        phinit_store_page_favorites_for_user($userId, $pageFavorites);
                    }

                    header('Location: ' . rtrim((string) SITE_URL, '/') . $requestUri);
                    exit;
                }

                $state['csrfToken'] = \CMS\Security::instance()->generateToken($tokenAction);
                $state['csrfError'] = true;
            }

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
            $state['formGuardToken'] = \CMS\Security::instance()->generateToken('form_guard');
        } catch (\Throwable) {
            return $state;
        }

        $state['label'] = $state['isFavorited'] ? 'Gespeichert' : 'Favorit';
        $state['title'] = $state['isFavorited'] ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen';
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

        $isActive = (bool) ($favoriteControl['isFavorited'] ?? false);
        $class = 'content-favorite' . ($isActive ? ' content-favorite--active' : '');
        $icon = $isActive ? '★' : '☆';
        $label = htmlspecialchars((string) ($favoriteControl['label'] ?? 'Favorit'), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars((string) ($favoriteControl['title'] ?? 'Favorit'), ENT_QUOTES, 'UTF-8');

        if (!($favoriteControl['isLoggedIn'] ?? false)) {
            $href = htmlspecialchars((string) ($favoriteControl['loginUrl'] ?? (SITE_URL . '/login')), ENT_QUOTES, 'UTF-8');
            return '<a href="' . $href . '" class="' . $class . ' content-favorite--link" aria-label="' . $title . '" title="' . $title . '"><span class="content-favorite__icon" aria-hidden="true">' . $icon . '</span><span class="content-favorite__label">' . $label . '</span></a>';
        }

        $contentType = htmlspecialchars((string) ($favoriteControl['contentType'] ?? 'post'), ENT_QUOTES, 'UTF-8');
        $contentId = (int) ($favoriteControl['contentId'] ?? 0);
        $csrfToken = htmlspecialchars((string) ($favoriteControl['csrfToken'] ?? ''), ENT_QUOTES, 'UTF-8');
        $formGuardToken = htmlspecialchars((string) ($favoriteControl['formGuardToken'] ?? ''), ENT_QUOTES, 'UTF-8');
        $pressed = $isActive ? 'true' : 'false';

        return '<form method="post" class="content-favorite-form"><input type="hidden" name="csrf_token" value="' . $formGuardToken . '"><input type="hidden" name="phinit_toggle_favorite" value="1"><input type="hidden" name="favorite_content_type" value="' . $contentType . '"><input type="hidden" name="favorite_content_id" value="' . $contentId . '"><input type="hidden" name="favorite_csrf_token" value="' . $csrfToken . '"><button type="submit" class="' . $class . '" aria-pressed="' . $pressed . '" aria-label="' . $title . '" title="' . $title . '"><span class="content-favorite__icon" aria-hidden="true">' . $icon . '</span><span class="content-favorite__label">' . $label . '</span></button></form>';
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
