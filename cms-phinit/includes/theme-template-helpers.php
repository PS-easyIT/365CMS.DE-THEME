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
        $normalizedPart = trim(str_replace('\\', '/', $part));
        if ($normalizedPart === '' || str_contains($normalizedPart, '..') || preg_match('#^[a-z0-9/_-]+$#i', $normalizedPart) !== 1) {
            return;
        }

        $themeRoot = realpath(CMS_PHINIT_THEME_DIR);
        if ($themeRoot === false) {
            return;
        }

        $file = $themeRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPart) . '.php';
        $resolvedFile = realpath($file);
        if ($resolvedFile === false || !is_file($resolvedFile)) {
            return;
        }

        $normalizedThemeRoot = rtrim(str_replace('\\', '/', $themeRoot), '/');
        $normalizedResolvedFile = str_replace('\\', '/', $resolvedFile);
        if (!str_starts_with($normalizedResolvedFile, $normalizedThemeRoot . '/')) {
            return;
        }

        $render = static function (string $__phinitFile, array $__phinitVars): void {
            foreach ($__phinitVars as $__phinitKey => $__phinitValue) {
                if (!is_string($__phinitKey) || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $__phinitKey) !== 1) {
                    continue;
                }

                if (in_array($__phinitKey, ['__phinitFile', '__phinitVars', 'render'], true)) {
                    continue;
                }

                ${$__phinitKey} = $__phinitValue;
            }

            include $__phinitFile;
        };

        $render($resolvedFile, $vars);
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

if (!function_exists('theme_csrf_token')) {
    function theme_csrf_token(string $action = 'form'): string
    {
        try {
            return \CMS\Security::instance()->generateToken($action);
        } catch (\Throwable) {
            return '';
        }
    }
}

if (!function_exists('theme_csrf_field')) {
    function theme_csrf_field(string $action = 'form'): void
    {
        $token = theme_csrf_token($action);
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
}

if (!function_exists('theme_get_flash')) {
    function theme_get_flash(string $type = 'error'): string
    {
        $key = $type === 'success' ? 'success' : 'error';
        $message = trim((string) ($_SESSION[$key] ?? ''));
        unset($_SESSION[$key]);

        return $message;
    }
}

if (!function_exists('theme_logged_in_redirect_path')) {
    function theme_logged_in_redirect_path(): string
    {
        try {
            return \CMS\Auth::instance()->isAdmin() ? '/admin' : '/member';
        } catch (\Throwable) {
            return '/member';
        }
    }
}

if (!function_exists('theme_account_path')) {
    function theme_account_path(): string
    {
        try {
            return \CMS\Auth::instance()->isAdmin() ? '/admin' : '/member/dashboard';
        } catch (\Throwable) {
            return '/member/dashboard';
        }
    }
}

if (!function_exists('theme_auth_url')) {
    function theme_auth_url(string $page = 'login', array $query = [], ?string $locale = null): string
    {
        $resolvedLocale = $locale ?? (function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de');

        try {
            if (class_exists('\CMS\Services\CmsAuthPageService')) {
                return \CMS\Services\CmsAuthPageService::getInstance()->getPublicUrl($page, $resolvedLocale, $query);
            }
        } catch (\Throwable) {
        }

        $fallbackPath = match (strtolower(trim($page))) {
            'register' => '/cms-register',
            'forgot-password' => '/cms-password-forgot',
            default => '/cms-login',
        };
        $url = function_exists('phinit_localized_href')
            ? phinit_localized_href($fallbackPath, $resolvedLocale, (string) SITE_URL)
            : rtrim((string) SITE_URL, '/') . $fallbackPath;

        if ($query === []) {
            return $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}

if (!function_exists('theme_login_url')) {
    function theme_login_url(?string $redirect = null, ?string $locale = null): string
    {
        $redirect = trim((string) $redirect);
        return theme_auth_url('login', $redirect !== '' ? ['redirect' => $redirect] : [], $locale);
    }
}

if (!function_exists('theme_register_url')) {
    function theme_register_url(?string $locale = null): string
    {
        return theme_auth_url('register', [], $locale);
    }
}

if (!function_exists('theme_forgot_password_url')) {
    function theme_forgot_password_url(?string $locale = null, array $query = []): string
    {
        return theme_auth_url('forgot-password', $query, $locale);
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

if (!function_exists('phinit_input_string')) {
    /**
     * Liest einen skalaren Request-Wert arraysicher aus und begrenzt optional die Länge.
     *
     * @param array<string,mixed> $source
     */
    function phinit_input_string(array $source, string $key, string $default = '', int $maxLength = 500): string
    {
        $value = $source[$key] ?? null;
        if ($value === null || is_array($value) || is_object($value)) {
            return $default;
        }

        $stringValue = trim((string) $value);
        if ($maxLength > 0 && mb_strlen($stringValue, 'UTF-8') > $maxLength) {
            return mb_substr($stringValue, 0, $maxLength, 'UTF-8');
        }

        return $stringValue;
    }
}

if (!function_exists('phinit_input_int')) {
    /**
     * Liest einen Integer-Request-Wert arraysicher aus und klemmt ihn optional ein.
     *
     * @param array<string,mixed> $source
     */
    function phinit_input_int(array $source, string $key, int $default = 0, ?int $min = null, ?int $max = null): int
    {
        $value = $source[$key] ?? null;
        if ($value === null || is_array($value) || is_object($value)) {
            $number = $default;
        } else {
            $number = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['default' => $default]]);
            $number = is_int($number) ? $number : $default;
        }

        if ($min !== null) {
            $number = max($min, $number);
        }

        if ($max !== null) {
            $number = min($max, $number);
        }

        return $number;
    }
}

if (!function_exists('phinit_input_int_list')) {
    /**
     * Normalisiert einen skalaren oder Array-Request-Wert zu eindeutigen positiven Integern.
     *
     * @param array<string,mixed> $source
     * @return list<int>
     */
    function phinit_input_int_list(array $source, string $key, int $min = 1, ?int $max = null): array
    {
        $value = $source[$key] ?? [];
        $values = is_array($value) ? $value : [$value];
        $normalized = [];

        foreach ($values as $entry) {
            if (is_array($entry) || is_object($entry)) {
                continue;
            }

            $number = filter_var($entry, FILTER_VALIDATE_INT);
            if (!is_int($number) || $number < $min || ($max !== null && $number > $max)) {
                continue;
            }

            $normalized[] = $number;
        }

        return array_values(array_unique($normalized));
    }
}

if (!function_exists('phinit_safe_public_url')) {
    /**
     * Validiert öffentliche URL-Werte für Frontend-Links mit Scheme-Allowlist.
     */
    function phinit_safe_public_url(?string $value, ?string $siteUrl = null, array $allowedSchemes = ['http', 'https', 'mailto']): string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, '/')) {
            if (str_starts_with($url, '//')) {
                return '';
            }

            return $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if ($scheme === '' || !in_array($scheme, $allowedSchemes, true)) {
            return '';
        }

        if ($scheme === 'mailto') {
            $address = preg_replace('/^mailto:/i', '', $url) ?? '';
            return filter_var($address, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $address : '';
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
    }
}

if (!function_exists('phinit_social_platform_icon')) {
    /**
     * Erkennt die Social-Media-Plattform anhand des Hosts einer URL und liefert
     * ein passendes Emoji-Icon plus sprechendes Label für Frontend-Ausgaben.
     *
     * @return array{icon:string,label:string}
     */
    function phinit_social_platform_icon(string $url): array
    {
        $host = strtolower((string) parse_url(trim($url), PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host ?? '') ?? '';

        $map = [
            'linkedin.com' => ['icon' => '💼', 'label' => 'LinkedIn'],
            'x.com' => ['icon' => '✕', 'label' => 'X (Twitter)'],
            'twitter.com' => ['icon' => '✕', 'label' => 'X (Twitter)'],
            'facebook.com' => ['icon' => '📘', 'label' => 'Facebook'],
            'instagram.com' => ['icon' => '📷', 'label' => 'Instagram'],
            'youtube.com' => ['icon' => '▶️', 'label' => 'YouTube'],
            'youtu.be' => ['icon' => '▶️', 'label' => 'YouTube'],
            'github.com' => ['icon' => '🐙', 'label' => 'GitHub'],
            'gitlab.com' => ['icon' => '🦊', 'label' => 'GitLab'],
            'xing.com' => ['icon' => '👔', 'label' => 'Xing'],
            'tiktok.com' => ['icon' => '🎵', 'label' => 'TikTok'],
            'threads.net' => ['icon' => '🧵', 'label' => 'Threads'],
            'bsky.app' => ['icon' => '🦋', 'label' => 'Bluesky'],
            'pinterest.com' => ['icon' => '📌', 'label' => 'Pinterest'],
            'discord.com' => ['icon' => '🎮', 'label' => 'Discord'],
            'discord.gg' => ['icon' => '🎮', 'label' => 'Discord'],
            't.me' => ['icon' => '✈️', 'label' => 'Telegram'],
            'telegram.org' => ['icon' => '✈️', 'label' => 'Telegram'],
            'mastodon.social' => ['icon' => '🐘', 'label' => 'Mastodon'],
        ];

        if ($host !== '' && isset($map[$host])) {
            return $map[$host];
        }

        if ($host !== '' && str_contains($host, 'mastodon')) {
            return ['icon' => '🐘', 'label' => 'Mastodon'];
        }

        return ['icon' => '🔗', 'label' => 'Profil-Link'];
    }
}

if (!function_exists('phinit_safe_public_media_url')) {
    /**
     * Normalisiert öffentliche Medien-URLs für Frontend-Bilder/Backgrounds.
     * Erlaubt absolute HTTP(S)-URLs, root-relative Pfade und einfache relative Upload-/Asset-Pfade.
     */
    function phinit_safe_public_media_url(?string $value, ?string $siteUrl = null): string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return '';
        }

        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $url) === 1) {
            return '';
        }

        $siteBase = rtrim((string) ($siteUrl ?? (defined('SITE_URL') ? SITE_URL : '')), '/');
        $normalizedUrl = str_replace('\\', '/', $url);

        if (str_starts_with($normalizedUrl, '//')) {
            return '';
        }

        if (preg_match('#^https?://#i', $normalizedUrl) === 1) {
            return filter_var($normalizedUrl, FILTER_VALIDATE_URL) ? $normalizedUrl : '';
        }

        if (str_starts_with($normalizedUrl, '/')) {
            return $siteBase !== '' ? $siteBase . $normalizedUrl : $normalizedUrl;
        }

        $relativePath = preg_replace('#^(?:\./)+#', '', $normalizedUrl) ?? '';
        $relativePath = ltrim($relativePath, '/');

        if ($relativePath === '' || str_contains($relativePath, '..')) {
            return '';
        }

        if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $relativePath) === 1) {
            return '';
        }

        return $siteBase !== '' ? $siteBase . '/' . $relativePath : '/' . $relativePath;
    }
}

if (!function_exists('phinit_extract_upload_relative_path')) {
    /**
     * Extrahiert aus einer Medienreferenz den relativen Pfad innerhalb von UPLOAD_PATH.
     */
    function phinit_extract_upload_relative_path(?string $value): string
    {
        $rawValue = trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($rawValue === '' || preg_match('/^[A-Za-z]:[\\\/]/', $rawValue) === 1) {
            return '';
        }

        $url = str_replace('\\', '/', $rawValue);
        if (str_starts_with($url, '//')) {
            return '';
        }

        $siteBase = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        $uploadBase = rtrim((string) (defined('UPLOAD_URL') ? UPLOAD_URL : ''), '/');
        $relativePath = '';

        if (preg_match('#^https?://#i', $url) === 1) {
            $urlPath = (string) (parse_url($url, PHP_URL_PATH) ?? '');
            if (str_ends_with($urlPath, '/media-file') || $urlPath === '/media-file') {
                $query = (string) (parse_url($url, PHP_URL_QUERY) ?? '');
                parse_str($query, $params);
                $relativePath = (string) ($params['path'] ?? '');
            } elseif ($uploadBase !== '' && str_starts_with($url, $uploadBase . '/')) {
                $relativePath = ltrim(substr($url, strlen($uploadBase)), '/');
            } elseif ($siteBase !== '' && str_starts_with($url, $siteBase . '/uploads/')) {
                $relativePath = ltrim(substr($url, strlen($siteBase . '/uploads/')), '/');
            }
        } elseif (str_starts_with($url, '/media-file')) {
            $query = (string) (parse_url($url, PHP_URL_QUERY) ?? '');
            parse_str($query, $params);
            $relativePath = (string) ($params['path'] ?? '');
        } elseif (str_starts_with($url, '/uploads/')) {
            $relativePath = ltrim(substr($url, strlen('/uploads/')), '/');
        } elseif (!str_starts_with($url, '/') && preg_match('#^[a-z][a-z0-9+.-]*:#i', $url) !== 1) {
            $relativePath = $url;
        }

        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '') {
            return '';
        }

        $relativePath = implode('/', array_map(static fn(string $segment): string => rawurldecode($segment), explode('/', $relativePath)));
        $relativePath = trim((string) preg_replace('#/+#', '/', $relativePath), '/');

        if ($relativePath === '' || str_contains($relativePath, '..') || preg_match('#^[a-z][a-z0-9+.-]*:#i', $relativePath) === 1) {
            return '';
        }

        return $relativePath;
    }
}

if (!function_exists('phinit_upload_path_can_be_served_directly')) {
    /**
     * Prüft konservativ, ob ein Upload-Bild direkt aus /uploads ausgeliefert werden darf.
     */
    function phinit_upload_path_can_be_served_directly(string $relativePath, string $absolutePath): bool
    {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '' || $absolutePath === '' || !is_file($absolutePath)) {
            return false;
        }

        if ($relativePath === 'member' || str_starts_with($relativePath, 'member/')) {
            return false;
        }

        foreach (explode('/', $relativePath) as $segment) {
            if ($segment !== '' && str_starts_with($segment, '.')) {
                return false;
            }
        }

        $extension = strtolower((string) pathinfo($relativePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['avif', 'bmp', 'gif', 'ico', 'jpg', 'jpeg', 'png', 'webp'], true)) {
            return false;
        }

        if (!is_readable($absolutePath)) {
            return false;
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            return true;
        }

        $permissions = @fileperms($absolutePath);
        if (!is_int($permissions)) {
            return false;
        }

        if (($permissions & 0004) !== 0) {
            return true;
        }

        $publicPermissions = ($permissions & 0777) | 0644;
        if (@chmod($absolutePath, $publicPermissions)) {
            clearstatcache(true, $absolutePath);
            $updatedPermissions = @fileperms($absolutePath);

            return is_int($updatedPermissions) && ($updatedPermissions & 0004) !== 0;
        }

        return false;
    }
}

if (!function_exists('phinit_build_direct_upload_media_url')) {
    /**
     * Baut eine cache- und webserverfreundliche direkte /uploads-URL.
     */
    function phinit_build_direct_upload_media_url(string $relativePath, ?string $siteUrl = null): string
    {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '' || str_contains($relativePath, '..')) {
            return '';
        }

        $baseUrl = rtrim((string) (defined('UPLOAD_URL') ? UPLOAD_URL : ''), '/');
        if ($baseUrl === '') {
            $siteBase = rtrim((string) ($siteUrl ?? (defined('SITE_URL') ? SITE_URL : '')), '/');
            $baseUrl = $siteBase !== '' ? $siteBase . '/uploads' : '/uploads';
        }

        $segments = array_map(static fn(string $segment): string => rawurlencode($segment), explode('/', $relativePath));

        return $baseUrl . '/' . implode('/', $segments);
    }
}

if (!function_exists('phinit_prefer_direct_public_upload_url')) {
    /**
     * Liefert für öffentliche Upload-Bilder bevorzugt direkte /uploads-URLs.
     */
    function phinit_prefer_direct_public_upload_url(?string $value, ?string $siteUrl = null): string
    {
        $relativePath = phinit_extract_upload_relative_path($value);
        if ($relativePath === '' || !defined('UPLOAD_PATH')) {
            return '';
        }

        $absolutePath = rtrim((string) UPLOAD_PATH, "\\/") . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (!phinit_upload_path_can_be_served_directly($relativePath, $absolutePath)) {
            return '';
        }

        return phinit_build_direct_upload_media_url($relativePath, $siteUrl);
    }
}

if (!function_exists('phinit_normalize_public_media_url')) {
    /**
     * Konvertiert öffentliche Medienreferenzen in frontend-taugliche Delivery-URLs.
     * Öffentliche Upload-Bilder laufen bevorzugt direkt über /uploads. Private,
     * versteckte oder nicht sicher direkt lesbare Dateien bleiben bei /media-file.
     */
    function phinit_normalize_public_media_url(?string $value, bool $preferInline = true, ?string $siteUrl = null): string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return '';
        }

        $directUploadUrl = phinit_prefer_direct_public_upload_url($url, $siteUrl);
        if ($directUploadUrl !== '') {
            return phinit_safe_public_media_url($directUploadUrl, $siteUrl);
        }

        try {
            if (class_exists('\\CMS\\Services\\MediaDeliveryService')) {
                $delivery = \CMS\Services\MediaDeliveryService::getInstance();
                $normalizedUrl = $delivery->normalizeUrl($url, $preferInline);
                $directUploadUrl = phinit_prefer_direct_public_upload_url($normalizedUrl, $siteUrl);
                if ($directUploadUrl !== '') {
                    return phinit_safe_public_media_url($directUploadUrl, $siteUrl);
                }

                $normalizedRelativePath = phinit_extract_upload_relative_path($normalizedUrl);
                $url = $normalizedUrl;

                if ($normalizedRelativePath !== '') {
                    if (method_exists($delivery, 'buildDeliveryUrl')) {
                        $url = $delivery->buildDeliveryUrl($normalizedRelativePath, $preferInline ? 'inline' : 'attachment');
                    } elseif (method_exists($delivery, 'normalizeAdminVisibleUrl')) {
                        $url = $delivery->normalizeAdminVisibleUrl($normalizedUrl);
                    }
                }

                if (str_contains($url, '/media-file') && method_exists($delivery, 'normalizeAdminVisibleUrl')) {
                    $url = $delivery->normalizeAdminVisibleUrl($url);
                }
            }
        } catch (\Throwable) {
        }

        return phinit_safe_public_media_url($url, $siteUrl);
    }
}

if (!function_exists('phinit_build_post_url')) {
    /**
     * Erzeugt die kanonische Beitrags-URL anhand der aktiven Permalink-Struktur.
     *
     * @param array<string, mixed>|object $post
     */
    function phinit_build_post_url(array|object $post, ?string $locale = null): string
    {
        $resolvedLocale = trim((string) ($locale ?? ''));
        if ($resolvedLocale === '' && function_exists('phinit_get_current_locale')) {
            $resolvedLocale = (string) phinit_get_current_locale();
        }
        if ($resolvedLocale === '') {
            $resolvedLocale = 'de';
        }

        try {
            if (class_exists('CMS\\Services\\PermalinkService')) {
                return \CMS\Services\PermalinkService::getInstance()->buildPostUrl($post, $resolvedLocale);
            }
        } catch (\Throwable) {
        }

        $readField = static function (array|object $value, string $field): string {
            if (is_array($value)) {
                return trim((string) ($value[$field] ?? ''));
            }

            return trim((string) ($value->{$field} ?? ''));
        };

        $slug = '';
        if ($resolvedLocale !== 'de') {
            $slug = $readField($post, 'slug_' . $resolvedLocale);
        }
        if ($slug === '') {
            $slug = $readField($post, 'slug');
        }
        if ($slug === '') {
            $slug = $readField($post, 'slug_en');
        }

        $siteUrl = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');

        return $siteUrl . '/blog/' . rawurlencode(trim($slug, '/'));
    }
}

if (!function_exists('phinit_get_post_template_definitions')) {
    /**
     * @return array<string,array<string,mixed>>
     */
    function phinit_get_post_template_definitions(): array
    {
        static $definitions = null;

        if (is_array($definitions)) {
            return $definitions;
        }

        $definitions = [];
        $themeJson = rtrim((string) (defined('CMS_PHINIT_THEME_DIR') ? CMS_PHINIT_THEME_DIR : __DIR__ . '/../'), "\\/") . DIRECTORY_SEPARATOR . 'theme.json';

        try {
            if (is_file($themeJson)) {
                $decoded = json_decode((string) file_get_contents($themeJson), true, 512, JSON_THROW_ON_ERROR);
                $templates = is_array($decoded['post_templates'] ?? null) ? $decoded['post_templates'] : [];

                foreach ($templates as $template) {
                    if (!is_array($template)) {
                        continue;
                    }

                    $id = strtolower(trim((string) ($template['id'] ?? '')));
                    $id = preg_replace('/[^a-z0-9_-]/', '', $id) ?? '';
                    if ($id === '') {
                        continue;
                    }

                    $definitions[$id] = $template;
                    $definitions[$id]['id'] = $id;
                }
            }
        } catch (\Throwable) {
            $definitions = [];
        }

        if ($definitions === []) {
            $definitions['default'] = [
                'id' => 'default',
                'label' => 'Standard',
                'file' => 'post.php',
                'meta_fields' => [],
            ];
        }

        return $definitions;
    }
}

if (!function_exists('phinit_read_post_field')) {
    function phinit_read_post_field(array|object $post, string $field, mixed $default = ''): mixed
    {
        return is_array($post) ? ($post[$field] ?? $default) : ($post->{$field} ?? $default);
    }
}

if (!function_exists('phinit_decode_post_template_meta')) {
    /**
     * @return array<string,mixed>
     */
    function phinit_decode_post_template_meta(array|object $post): array
    {
        $existingMeta = phinit_read_post_field($post, 'meta', null);
        if (is_array($existingMeta)) {
            return $existingMeta;
        }

        $rawJson = trim((string) phinit_read_post_field($post, 'post_meta_json', ''));
        if ($rawJson === '') {
            return [];
        }

        try {
            $decoded = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR);

            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable) {
            return [];
        }
    }
}

if (!function_exists('phinit_resolve_post_template')) {
    /**
     * @return array<string,mixed>
     */
    function phinit_resolve_post_template(array|object $post): array
    {
        $templates = phinit_get_post_template_definitions();
        $templateId = strtolower(trim((string) phinit_read_post_field($post, 'post_template', 'default')));
        $templateId = preg_replace('/[^a-z0-9_-]/', '', $templateId) ?? 'default';

        return $templates[$templateId] ?? ($templates['default'] ?? reset($templates));
    }
}

if (!function_exists('phinit_resolve_post_template_file')) {
    function phinit_resolve_post_template_file(array|object $post): string
    {
        $template = phinit_resolve_post_template($post);
        $file = trim((string) ($template['file'] ?? 'post.php'));
        $file = str_replace(['\\', '/'], '', $file);

        if ($file === '' || !str_ends_with($file, '.php') || $file === 'blog-single.php') {
            $file = 'post.php';
        }

        $path = rtrim((string) (defined('CMS_PHINIT_THEME_DIR') ? CMS_PHINIT_THEME_DIR : __DIR__ . '/../'), "\\/") . DIRECTORY_SEPARATOR . $file;

        return is_file($path) ? $file : 'post.php';
    }
}

if (!function_exists('phinit_build_post_template_meta_items')) {
    /**
     * @return array{title:string,template_id:string,items:array<int,array{key:string,label:string,value:mixed,type:string,url:string,display:string}>}
     */
    function phinit_build_post_template_meta_items(array|object $post): array
    {
        $template = phinit_resolve_post_template($post);
        $metaFields = is_array($template['meta_fields'] ?? null) ? $template['meta_fields'] : [];
        $meta = phinit_decode_post_template_meta($post);
        $items = [];

        foreach ($metaFields as $key => $field) {
            if (!is_array($field)) {
                continue;
            }

            $fieldKey = strtolower(trim((string) $key));
            $fieldKey = preg_replace('/[^a-z0-9_-]/', '', $fieldKey) ?? '';
            if ($fieldKey === '' || !array_key_exists($fieldKey, $meta)) {
                continue;
            }

            $type = strtolower(trim((string) ($field['type'] ?? 'text')));
            $label = trim((string) ($field['label'] ?? $fieldKey));
            $display = strtolower(trim((string) ($field['display'] ?? '')));
            $value = $meta[$fieldKey];
            $url = '';

            if (is_array($value)) {
                $value = array_values(array_filter(array_map(static fn(mixed $entry): string => trim((string) $entry), $value), static fn(string $entry): bool => $entry !== ''));
                if ($value === []) {
                    continue;
                }
                $type = 'array';
            } else {
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }
            }

            if ($type === 'url') {
                $url = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $value, defined('SITE_URL') ? (string) SITE_URL : null, ['http', 'https']) : (string) $value;
                if ($url === '') {
                    continue;
                }
            }

            if ($type === 'date' && is_string($value) && function_exists('phinit_format_date')) {
                $value = phinit_format_date($value, 'numeric');
            }

            $items[] = [
                'key' => $fieldKey,
                'label' => $label,
                'value' => $value,
                'type' => $type,
                'url' => $url,
                'display' => $display,
            ];
        }

        return [
            'title' => trim((string) ($template['card_label'] ?? $template['label'] ?? 'Zusatzinfos')),
            'template_id' => trim((string) ($template['id'] ?? '')),
            'items' => $items,
        ];
    }
}

if (!function_exists('phinit_image_loading_attributes')) {
    /**
     * Prüft, ob browserbasiertes Image-Lazy-Loading per Customizer aktiv ist.
     */
    function phinit_is_image_lazy_loading_enabled(): bool
    {
        static $lazyLoadingEnabled = null;

        if (is_bool($lazyLoadingEnabled)) {
            return $lazyLoadingEnabled;
        }

        $lazyLoadingEnabled = true;

        try {
            $setting = phinit_customizer_value('performance', 'lazyload_images', true);
            $lazyLoadingEnabled = filter_var($setting, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($lazyLoadingEnabled === null) {
                $lazyLoadingEnabled = true;
            }
        } catch (\Throwable) {
            $lazyLoadingEnabled = true;
        }

        return $lazyLoadingEnabled;
    }
}

if (!function_exists('phinit_image_loading_attributes')) {
    /**
     * Liefert standardisierte Loading-/Priority-Attribute für Theme-Bilder.
     */
    function phinit_image_loading_attributes(bool $aboveTheFold = false, bool $highPriority = true, bool $lowPriority = false): string
    {
        if ($aboveTheFold) {
            return $highPriority
                ? 'loading="eager" fetchpriority="high" decoding="async"'
                : 'loading="eager" decoding="async"';
        }

        if ($lowPriority) {
            return 'fetchpriority="low" decoding="async"';
        }

        if (!phinit_is_image_lazy_loading_enabled()) {
            return 'decoding="async"';
        }

        return 'loading="lazy" decoding="async"';
    }
}

if (!function_exists('phinit_get_local_image_path')) {
    /**
     * Löst öffentliche Bild-Referenzen auf einen lokalen Dateipfad auf, wenn möglich.
     */
    function phinit_get_local_image_path(?string $reference): string
    {
        $value = trim(html_entity_decode((string) $reference, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($value === '' || str_starts_with($value, 'data:') || str_starts_with($value, '//')) {
            return '';
        }

        $basePath = rtrim((string) ABSPATH, "\\/");
        $siteUrl = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        $uploadUrl = rtrim((string) (defined('UPLOAD_URL') ? UPLOAD_URL : ''), '/');
        $uploadPath = rtrim((string) (defined('UPLOAD_PATH') ? UPLOAD_PATH : ''), "\\/");
        $themeUrl = rtrim((string) (defined('CMS_PHINIT_THEME_URL') ? CMS_PHINIT_THEME_URL : ''), '/');
        $themeDir = rtrim((string) (defined('CMS_PHINIT_THEME_DIR') ? CMS_PHINIT_THEME_DIR : ''), "\\/");
        $candidates = [];

        $appendAbsoluteCandidate = static function (array &$paths, string $candidate): void {
            $candidate = trim($candidate);
            if ($candidate === '') {
                return;
            }

            $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $candidate);
            $paths[] = $normalized;
        };

        $appendRelativeCandidate = static function (array &$paths, string $root, string $relativePath): void {
            $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
            if ($root === '' || $relativePath === '' || str_contains($relativePath, '..')) {
                return;
            }

            $paths[] = rtrim($root, "\\/") . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        };

        $appendFromSitePath = static function (array &$paths, string $path) use ($appendRelativeCandidate, $basePath): void {
            $path = trim((string) parse_url($path, PHP_URL_PATH));
            if ($path === '') {
                return;
            }

            $appendRelativeCandidate($paths, $basePath, $path);
        };

        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $value) === 1) {
            $appendAbsoluteCandidate($candidates, $value);
        } elseif (preg_match('#^https?://#i', $value) === 1) {
            if ($siteUrl !== '' && str_starts_with($value, $siteUrl . '/')) {
                $appendFromSitePath($candidates, $value);
            }

            if ($uploadUrl !== '' && str_starts_with($value, $uploadUrl . '/')) {
                $relativeUploadPath = ltrim(substr($value, strlen($uploadUrl)), '/');
                $appendRelativeCandidate($candidates, $uploadPath !== '' ? $uploadPath : $basePath, $relativeUploadPath);
            }

            if ($themeUrl !== '' && str_starts_with($value, $themeUrl . '/')) {
                $relativeThemePath = ltrim(substr($value, strlen($themeUrl)), '/');
                $appendRelativeCandidate($candidates, $themeDir, $relativeThemePath);
            }

            if (preg_match('#/media-file(?:$|\?)#', $value) === 1) {
                $query = (string) parse_url($value, PHP_URL_QUERY);
                parse_str($query, $params);
                $mediaPath = trim(str_replace('\\', '/', (string) ($params['path'] ?? '')), '/');
                $appendRelativeCandidate($candidates, $basePath, $mediaPath);
                $appendRelativeCandidate($candidates, $uploadPath !== '' ? $uploadPath : $basePath, $mediaPath);
            }
        } elseif (str_starts_with($value, '/media-file')) {
            $query = (string) parse_url($value, PHP_URL_QUERY);
            parse_str($query, $params);
            $mediaPath = trim(str_replace('\\', '/', (string) ($params['path'] ?? '')), '/');
            $appendRelativeCandidate($candidates, $basePath, $mediaPath);
            $appendRelativeCandidate($candidates, $uploadPath !== '' ? $uploadPath : $basePath, $mediaPath);
        } elseif (str_starts_with($value, '/')) {
            $appendFromSitePath($candidates, $value);
        } else {
            $appendRelativeCandidate($candidates, $uploadPath !== '' ? $uploadPath : $basePath, $value);
            $appendRelativeCandidate($candidates, $themeDir, $value);
            $appendRelativeCandidate($candidates, $basePath, $value);
        }

        foreach (array_values(array_unique($candidates)) as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
    }
}

if (!function_exists('phinit_get_image_dimensions')) {
    /**
     * Ermittelt Bilddimensionen für lokale Bilder mit kleinem Request-Cache.
     *
     * @return array{width:int,height:int}|null
     */
    function phinit_get_image_dimensions(?string $reference): ?array
    {
        static $dimensionCache = [];

        $cacheKey = trim((string) $reference);
        if ($cacheKey === '') {
            return null;
        }

        if (array_key_exists($cacheKey, $dimensionCache)) {
            return $dimensionCache[$cacheKey];
        }

        $filePath = phinit_get_local_image_path($cacheKey);
        if ($filePath === '') {
            $dimensionCache[$cacheKey] = null;
            return null;
        }

        $size = @getimagesize($filePath);
        if (!is_array($size) || empty($size[0]) || empty($size[1])) {
            $dimensionCache[$cacheKey] = null;
            return null;
        }

        $dimensionCache[$cacheKey] = [
            'width' => max(1, (int) $size[0]),
            'height' => max(1, (int) $size[1]),
        ];

        return $dimensionCache[$cacheKey];
    }
}

if (!function_exists('phinit_local_path_to_public_url')) {
    /**
     * Übersetzt einen lokalen Dateipfad zurück in eine öffentliche URL, wenn die Datei
     * innerhalb von Uploads, Theme-Verzeichnis oder CMS-Root liegt.
     */
    function phinit_local_path_to_public_url(?string $path): string
    {
        $resolvedPath = realpath(trim((string) $path));
        if ($resolvedPath === false || $resolvedPath === '') {
            return '';
        }

        $normalizedPath = str_replace('\\', '/', $resolvedPath);
        $roots = [
            [realpath((string) UPLOAD_PATH) ?: '', rtrim((string) UPLOAD_URL, '/')],
            [realpath((string) CMS_PHINIT_THEME_DIR) ?: '', rtrim((string) CMS_PHINIT_THEME_URL, '/')],
            [realpath((string) ABSPATH) ?: '', rtrim((string) SITE_URL, '/')],
        ];

        foreach ($roots as [$rootPath, $baseUrl]) {
            $normalizedRoot = str_replace('\\', '/', (string) $rootPath);
            if ($normalizedRoot === '' || $baseUrl === '') {
                continue;
            }

            if ($normalizedPath !== $normalizedRoot && !str_starts_with($normalizedPath, $normalizedRoot . '/')) {
                continue;
            }

            $relativePath = ltrim(substr($normalizedPath, strlen($normalizedRoot)), '/');
            if ($relativePath === '') {
                return $baseUrl;
            }

            $segments = array_map(static fn(string $segment): string => rawurlencode($segment), explode('/', $relativePath));

            return $baseUrl . '/' . implode('/', $segments);
        }

        return '';
    }
}

if (!function_exists('phinit_public_image_url_with_mtime')) {
    /**
     * Ergänzt lokale Bild-URLs um einen filemtime-basierten Cachebuster.
     */
    function phinit_public_image_url_with_mtime(string $url, string $path): string
    {
        $url = trim($url);
        if ($url === '' || $path === '' || !is_file($path)) {
            return $url;
        }

        clearstatcache(true, $path);
        $mtime = (int) (filemtime($path) ?: 0);
        if ($mtime < 1) {
            return $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $mtime;
    }
}

if (!function_exists('phinit_image_variant_is_fresh')) {
    /**
     * Prüft, ob ein generiertes Bildderivat neuer oder gleich alt wie das Original ist.
     */
    function phinit_image_variant_is_fresh(string $variantPath, string $sourcePath): bool
    {
        if ($variantPath === '' || $sourcePath === '' || !is_file($variantPath) || !is_file($sourcePath)) {
            return false;
        }

        clearstatcache(true, $variantPath);
        clearstatcache(true, $sourcePath);

        $variantTime = (int) (filemtime($variantPath) ?: 0);
        $sourceTime = (int) (filemtime($sourcePath) ?: 0);

        return $variantTime > 0 && $sourceTime > 0 && $variantTime >= $sourceTime;
    }
}

if (!function_exists('phinit_get_picture_sources')) {
    /**
     * Liefert bevorzugte Bildquellen inkl. optionalem lokal generiertem WebP-Fallback.
     * Bestehende JPG/PNG-Dateien bleiben dabei als Fallback erhalten.
     *
     * @return array{url:string,avif_url:string,webp_url:string,width:int,height:int}
     */
    function phinit_get_picture_sources(?string $reference, ?string $siteUrl = null, int $fallbackWidth = 0, int $fallbackHeight = 0): array
    {
        $normalizedUrl = phinit_normalize_public_media_url($reference, false, $siteUrl);
        $dimensions = phinit_get_image_dimensions($reference);

        $result = [
            'url' => $normalizedUrl,
            'avif_url' => '',
            'webp_url' => '',
            'width' => max(0, (int) ($dimensions['width'] ?? $fallbackWidth)),
            'height' => max(0, (int) ($dimensions['height'] ?? $fallbackHeight)),
        ];

        if ($normalizedUrl === '') {
            return $result;
        }

        $sourcePath = phinit_get_local_image_path($reference);
        if ($sourcePath === '' || !is_file($sourcePath)) {
            return $result;
        }

        $result['url'] = phinit_public_image_url_with_mtime($result['url'], $sourcePath);

        $extension = strtolower((string) pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif'], true) || !class_exists('\CMS\Services\ImageService')) {
            return $result;
        }

        if ($extension === 'avif') {
            $result['avif_url'] = $result['url'];
            return $result;
        }

        try {
            $imageService = \CMS\Services\ImageService::getInstance();
            $imageInfo = $imageService->getInfo();

            if (!$imageService->isAvailable()) {
                return $result;
            }

            $sourceSize = (int) (filesize($sourcePath) ?: 0);

            if (!empty($imageInfo['avif_support'])) {
                $avifPath = preg_replace('/\.[a-z0-9]+$/i', '.avif', $sourcePath);
                if (is_string($avifPath) && $avifPath !== '' && $avifPath !== $sourcePath) {
                    if (!phinit_image_variant_is_fresh($avifPath, $sourcePath)) {
                        $generatedAvifPath = $imageService->convertToAvif($sourcePath, 62, 6);
                        if (is_string($generatedAvifPath) && is_file($generatedAvifPath)) {
                            $avifPath = $generatedAvifPath;
                        }
                    }

                    if (phinit_image_variant_is_fresh($avifPath, $sourcePath)) {
                        $avifSize = (int) (filesize($avifPath) ?: 0);
                        if ($avifSize > 0 && ($sourceSize === 0 || $avifSize < $sourceSize)) {
                            $avifUrl = phinit_local_path_to_public_url($avifPath);
                            if ($avifUrl !== '') {
                                $result['avif_url'] = function_exists('phinit_normalize_public_media_url')
                                    ? phinit_normalize_public_media_url($avifUrl, true, $siteUrl)
                                    : phinit_safe_public_media_url($avifUrl, $siteUrl);
                                $result['avif_url'] = phinit_public_image_url_with_mtime($result['avif_url'], $avifPath);
                            }
                        }
                    }
                }
            }

            if (empty($imageInfo['webp_support']) || !in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                return $result;
            }

            $webpPath = preg_replace('/\.[a-z0-9]+$/i', '.webp', $sourcePath);
            if (!is_string($webpPath) || $webpPath === '' || $webpPath === $sourcePath) {
                return $result;
            }

            if (!phinit_image_variant_is_fresh($webpPath, $sourcePath)) {
                $generatedPath = $imageService->convertToWebP($sourcePath, 78, false);
                if (!is_string($generatedPath) || !is_file($generatedPath)) {
                    return $result;
                }

                $webpPath = $generatedPath;
            }

            if (!phinit_image_variant_is_fresh($webpPath, $sourcePath)) {
                return $result;
            }

            $webpSize = (int) (filesize($webpPath) ?: 0);
            if ($sourceSize > 0 && $webpSize > 0 && $webpSize >= $sourceSize) {
                return $result;
            }

            $webpUrl = phinit_local_path_to_public_url($webpPath);
            if ($webpUrl === '') {
                return $result;
            }

            $result['webp_url'] = function_exists('phinit_normalize_public_media_url')
                ? phinit_normalize_public_media_url($webpUrl, true, $siteUrl)
                : phinit_safe_public_media_url($webpUrl, $siteUrl);
            $result['webp_url'] = phinit_public_image_url_with_mtime($result['webp_url'], $webpPath);
        } catch (\Throwable) {
            return $result;
        }

        return $result;
    }
}

if (!function_exists('phinit_get_thumbnail_picture_sources')) {
    /**
     * Liefert für kleine UI-Bilder bevorzugt eine lokal generierte Thumbnail-Variante
     * und fällt bei Problemen sauber auf die Standard-Bildquellen zurück.
     *
     * @return array{url:string,avif_url:string,webp_url:string,width:int,height:int}
     */
    function phinit_get_thumbnail_picture_sources(
        ?string $reference,
        ?string $siteUrl = null,
        int $width = 0,
        int $height = 0,
        string $mode = 'crop'
    ): array {
        $width = max(0, $width);
        $height = max(0, $height);
        $resolvedMode = in_array($mode, ['crop', 'contain'], true) ? $mode : 'crop';
        $fallback = phinit_get_picture_sources($reference, $siteUrl, $width, $height);

        if ($width < 1 || $height < 1 || !class_exists('\CMS\Services\ImageService')) {
            return $fallback;
        }

        $sourcePath = phinit_get_local_image_path($reference);
        if ($sourcePath === '' || !is_file($sourcePath)) {
            return $fallback;
        }

        $sourceDimensions = phinit_get_image_dimensions($reference);
        if (
            is_array($sourceDimensions)
            && (int) ($sourceDimensions['width'] ?? 0) > 0
            && (int) ($sourceDimensions['height'] ?? 0) > 0
            && (int) ($sourceDimensions['width'] ?? 0) <= $width
            && (int) ($sourceDimensions['height'] ?? 0) <= $height
        ) {
            return $fallback;
        }

        try {
            $imageService = \CMS\Services\ImageService::getInstance();
            if (!$imageService->isAvailable()) {
                return $fallback;
            }

            $pathInfo = pathinfo($sourcePath);
            $extension = strtolower((string) ($pathInfo['extension'] ?? ''));
            if ($extension === '') {
                return $fallback;
            }

            $variantPath = (string) ($pathInfo['dirname'] ?? '');
            $variantPath .= '/';
            $variantPath .= (string) ($pathInfo['filename'] ?? 'image');
            $variantPath .= '-' . $resolvedMode . '-' . $width . 'x' . $height . '.' . $extension;

            if (!phinit_image_variant_is_fresh($variantPath, $sourcePath)) {
                $generatedPath = $resolvedMode === 'contain'
                    ? $imageService->resize($sourcePath, $width, $height, $variantPath, 80)
                    : $imageService->createThumbnail($sourcePath, $width, $height, $variantPath, 80);

                if (!is_string($generatedPath) || !is_file($generatedPath)) {
                    return $fallback;
                }

                $variantPath = $generatedPath;
            }

            if (!phinit_image_variant_is_fresh($variantPath, $sourcePath)) {
                return $fallback;
            }

            $sourceSize = (int) (filesize($sourcePath) ?: 0);
            $variantSize = (int) (filesize($variantPath) ?: 0);
            if ($sourceSize > 0 && $variantSize > 0 && $variantSize >= $sourceSize) {
                return $fallback;
            }

            $variantUrl = phinit_local_path_to_public_url($variantPath);
            if ($variantUrl === '') {
                return $fallback;
            }

            $variantSources = phinit_get_picture_sources($variantUrl, $siteUrl, $width, $height);
            $variantSources['width'] = $width;
            $variantSources['height'] = $height;

            return $variantSources;
        } catch (\Throwable) {
            return $fallback;
        }
    }
}

if (!function_exists('phinit_image_dimension_attributes')) {
    /**
     * Liefert width-/height-Attribute für Bilder. Nutzt lokale Dateimaße oder optionale Fallbacks.
     */
    function phinit_image_dimension_attributes(?string $reference, int $fallbackWidth = 0, int $fallbackHeight = 0): string
    {
        $dimensions = phinit_get_image_dimensions($reference);
        $width = max(0, (int) ($dimensions['width'] ?? $fallbackWidth));
        $height = max(0, (int) ($dimensions['height'] ?? $fallbackHeight));

        if ($width < 1 || $height < 1) {
            return '';
        }

        return 'width="' . $width . '" height="' . $height . '"';
    }
}

if (!function_exists('phinit_current_request_uri')) {
    function phinit_current_request_uri(): string
    {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $requestUri = preg_replace('/[\x00-\x1F\x7F]/', '', $requestUri) ?? '/';
        if ($requestUri === '') {
            return '/';
        }

        $requestUri = str_starts_with($requestUri, '/') ? $requestUri : '/' . ltrim($requestUri, '/');

        return mb_substr($requestUri, 0, 4096, 'UTF-8');
    }
}

if (!function_exists('phinit_current_request_path')) {
    function phinit_current_request_path(): string
    {
        $path = (string) (parse_url(phinit_current_request_uri(), PHP_URL_PATH) ?? '/');
        return $path !== '' ? $path : '/';
    }
}

if (!function_exists('phinit_current_request_query')) {
    function phinit_current_request_query(): string
    {
        $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
        $query = preg_replace('/[\x00-\x1F\x7F]/', '', $query) ?? '';

        return mb_substr(trim($query), 0, 2048, 'UTF-8');
    }
}

if (!function_exists('phinit_request_method')) {
    function phinit_request_method(): string
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $method = preg_replace('/[^A-Z]/', '', $method) ?? 'GET';

        return $method !== '' ? $method : 'GET';
    }
}

if (!function_exists('phinit_current_host')) {
    function phinit_current_host(): string
    {
        $host = strtolower(trim((string) ($_SERVER['HTTP_HOST'] ?? ''), '.'));
        $host = preg_replace('/[^a-z0-9.:-]/', '', $host) ?? '';

        return mb_substr($host, 0, 255, 'UTF-8');
    }
}

if (!function_exists('phinit_get_recent_public_posts')) {
    /**
     * Liefert kompakte öffentliche Beitragsvorschläge für UI-Templates.
     *
     * @return list<array<string,mixed>>
     */
    function phinit_get_recent_public_posts(int $limit = 3): array
    {
        $limit = max(1, min(12, $limit));

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $rows = $db->get_results(
                "SELECT p.title, p.slug, p.slug_en, p.featured_image, p.published_at, p.created_at, c.name AS category_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                 WHERE " . phinit_post_publication_where('p') . "
                 ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
                 LIMIT {$limit}"
            ) ?: [];
        } catch (\Throwable) {
            return [];
        }

        return array_values(array_map(static fn(object|array $row): array => (array) $row, $rows));
    }
}

if (!function_exists('phinit_get_unread_notification_count')) {
    function phinit_get_unread_notification_count(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }

        try {
            $db = \CMS\Database::instance();

            return (int) ($db->get_var(
                "SELECT COUNT(*) FROM {$db->prefix()}notifications WHERE user_id = ? AND is_read = 0",
                [$userId]
            ) ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }
}

if (!function_exists('phinit_is_cookie_consent_enabled_by_cms')) {
    function phinit_is_cookie_consent_enabled_by_cms(): bool
    {
        try {
            if (class_exists('\\CMS\\Services\\SettingsService')) {
                return \CMS\Services\SettingsService::getInstance()->getBool('privacy', 'cookie_consent_enabled', false);
            }
        } catch (\Throwable) {
        }

        try {
            $db = \CMS\Database::instance();
            $value = $db->get_var(
                "SELECT option_value FROM {$db->prefix()}settings WHERE option_name = ? LIMIT 1",
                ['cookie_consent_enabled']
            );

            return (string) $value === '1';
        } catch (\Throwable) {
            return false;
        }
    }
}

if (!function_exists('phinit_resolve_request_context')) {
    /**
     * @return array{base_uri:string,locale:string,is_localized:bool}
     */
    function phinit_resolve_request_context(?string $requestPath = null): array
    {
        $path = trim((string) ($requestPath ?? phinit_current_request_path()));
        $path = $path !== '' ? $path : '/';
        $defaultLocale = function_exists('phinit_default_locale') ? phinit_default_locale() : 'de';

        $fallback = [
            'base_uri' => $path,
            'locale' => $defaultLocale,
            'is_localized' => false,
        ];

        try {
            $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($path);
            $baseUri = trim((string) ($context['base_uri'] ?? $path));
            $locale = strtolower(trim((string) ($context['locale'] ?? $defaultLocale)));

            return [
                'base_uri' => $baseUri !== '' ? $baseUri : '/',
                'locale' => $locale !== '' ? $locale : $defaultLocale,
                'is_localized' => !empty($context['is_localized']),
            ];
        } catch (\Throwable) {
            $detectedLocale = function_exists('phinit_detect_locale_from_path')
                ? phinit_detect_locale_from_path($path, $defaultLocale)
                : $defaultLocale;
            $normalizedPath = '/' . ltrim((string) (parse_url($path, PHP_URL_PATH) ?? $path), '/');
            $normalizedPath = preg_replace('#/+#', '/', $normalizedPath) ?? '/';
            $baseUri = $normalizedPath;

            if ($detectedLocale === 'en' && preg_match('#^/en(?:/|$)#i', $normalizedPath) === 1) {
                $baseUri = (string) preg_replace('#^/en(?=/|$)#i', '', $normalizedPath);
                $baseUri = $baseUri !== '' ? $baseUri : '/';
            }

            return [
                'base_uri' => $baseUri,
                'locale' => $detectedLocale,
                'is_localized' => $detectedLocale !== $defaultLocale,
            ];
        }
    }
}

if (!function_exists('phinit_get_request_base_path')) {
    function phinit_get_request_base_path(?string $requestPath = null): string
    {
        $context = phinit_resolve_request_context($requestPath);
        $basePath = trim((string) ($context['base_uri'] ?? '/'));

        return $basePath !== '' ? $basePath : '/';
    }
}

if (!function_exists('phinit_localize_page_payload')) {
    /**
     * @param array<string, mixed> $page
     * @return array<string, mixed>
     */
    function phinit_localize_page_payload(array $page, string $locale): array
    {
        $resolvedLocale = strtolower(trim($locale));
        if ($resolvedLocale === '') {
            $resolvedLocale = 'de';
        }

        try {
            if (class_exists('CMS\\Services\\ContentLocalizationService')) {
                return \CMS\Services\ContentLocalizationService::getInstance()->localizePage($page, $resolvedLocale);
            }
        } catch (\Throwable) {
        }

        if ($resolvedLocale !== 'de') {
            $localizedSlug = trim((string) ($page['slug_' . $resolvedLocale] ?? ''));
            if ($localizedSlug !== '') {
                $page['slug'] = $localizedSlug;
            }

            foreach (['title', 'content', 'excerpt'] as $field) {
                $localizedValue = trim((string) ($page[$field . '_' . $resolvedLocale] ?? ''));
                if ($localizedValue !== '') {
                    $page[$field] = $localizedValue;
                }
            }
        }

        return $page;
    }
}

if (!function_exists('phinit_get_page_by_request_path')) {
    /**
     * @return array<string, mixed>|null
     */
    function phinit_get_page_by_request_path(?string $requestPath = null, ?string $locale = null, bool $localize = true): ?array
    {
        $context = phinit_resolve_request_context($requestPath);
        $resolvedLocale = strtolower(trim((string) ($locale ?? ($context['locale'] ?? 'de'))));
        $resolvedLocale = $resolvedLocale !== '' ? $resolvedLocale : 'de';
        $slug = trim((string) ($context['base_uri'] ?? '/'), '/');

        if ($slug === '' || str_contains($slug, '/')) {
            return null;
        }

        try {
            $page = \CMS\PageManager::instance()->getPageBySlug($slug, $resolvedLocale);
        } catch (\Throwable) {
            return null;
        }

        if (is_object($page)) {
            $page = (array) $page;
        }

        if (!is_array($page) || $page === []) {
            return null;
        }

        return $localize ? phinit_localize_page_payload($page, $resolvedLocale) : $page;
    }
}

if (!function_exists('phinit_get_current_locale')) {
    function phinit_get_current_locale(): string
    {
        static $locale = null;

        if (is_string($locale) && $locale !== '') {
            return $locale;
        }

        $locale = function_exists('phinit_default_locale') ? phinit_default_locale() : 'de';

        try {
            $context = phinit_resolve_request_context(phinit_current_request_path());
            $resolvedLocale = strtolower(trim((string) ($context['locale'] ?? $locale)));
            if ($resolvedLocale !== '') {
                $locale = $resolvedLocale;
            }
        } catch (\Throwable) {
        }

        return $locale;
    }
}

if (!function_exists('phinit_default_locale')) {
    function phinit_default_locale(): string
    {
        static $defaultLocale = null;

        if (is_string($defaultLocale) && $defaultLocale !== '') {
            return $defaultLocale;
        }

        $defaultLocale = 'de';

        try {
            $configuredLocale = strtolower(trim((string) phinit_customizer_value('language', 'default_locale', 'de')));
            if (in_array($configuredLocale, ['de', 'en'], true)) {
                $defaultLocale = $configuredLocale;
            }
        } catch (\Throwable) {
        }

        return $defaultLocale;
    }
}

if (!function_exists('phinit_detect_locale_from_path')) {
    function phinit_detect_locale_from_path(string $path, string $fallback = 'de'): string
    {
        $normalizedPath = '/' . ltrim((string) (parse_url($path, PHP_URL_PATH) ?? $path), '/');
        $normalizedPath = preg_replace('#/+#', '/', $normalizedPath) ?? '/';

        if (preg_match('#^/en(?:/|$)#i', $normalizedPath) === 1) {
            return 'en';
        }

        return in_array($fallback, ['de', 'en'], true) ? $fallback : 'de';
    }
}

if (!function_exists('phinit_page_visibility_where')) {
    function phinit_page_visibility_where(string $alias = ''): string
    {
        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';

        if (function_exists('theme_is_logged_in') && theme_is_logged_in()) {
            return "({$prefix}status = 'published' OR {$prefix}status = 'private')";
        }

        return $prefix . "status = 'published'";
    }
}

if (!function_exists('phinit_post_publication_where')) {
    function phinit_post_publication_where(string $alias = ''): string
    {
        if (function_exists('cms_post_publication_where')) {
            return cms_post_publication_where($alias);
        }

        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';

        return $prefix . "status = 'published'"
            . ' AND (' . $prefix . 'published_at IS NULL OR ' . $prefix . 'published_at <= NOW())';
    }
}

if (!function_exists('phinit_is_english_locale')) {
    function phinit_is_english_locale(?string $locale = null): bool
    {
        return strtolower(trim((string) ($locale ?? phinit_get_current_locale()))) === 'en';
    }
}

if (!function_exists('phinit_localize_menu_label')) {
    function phinit_localize_menu_label(string $label, ?string $locale = null): string
    {
        $resolvedLocale = strtolower(trim((string) ($locale ?? phinit_get_current_locale())));
        $normalized = trim($label);
        if ($normalized === '' || $resolvedLocale !== 'en') {
            return $normalized;
        }

        if (str_contains($normalized, '|')) {
            $parts = array_map(static fn(string $part): string => trim($part), explode('|', $normalized, 2));
            if (count($parts) === 2 && $parts[1] !== '') {
                return $parts[1];
            }
        }

        $dictionary = [
            'Startseite' => 'Home',
            'Datenschutz' => 'Privacy',
            'Datenschutz & DSGVO' => 'Privacy & GDPR',
            'Über mich' => 'About me',
            'Kontakt' => 'Contact',
            'News' => 'News',
            'IT-News' => 'IT news',
            'Grundlagen' => 'Basics',
            'Glossar' => 'Glossary',
            'Rechtliches' => 'Legal',
            'Themen' => 'Topics',
            'Seiten' => 'Pages',
            'Impressum' => 'Imprint',
            'Schnelllinks' => 'Quick links',
            'Quicklinks' => 'Quick links',
            'Alle Beiträge' => 'All posts',
            'Mehr erfahren' => 'Learn more',
            'RSS-Feed' => 'RSS feed',
            'AGB' => 'Terms',
        ];

        return $dictionary[$normalized] ?? $normalized;
    }
}

if (!function_exists('phinit_localize_menu_items')) {
    /**
     * @param array<int,mixed> $items
     * @return array<int,mixed>
     */
    function phinit_localize_menu_items(array $items, ?string $locale = null): array
    {
        $resolvedLocale = strtolower(trim((string) ($locale ?? phinit_get_current_locale())));
        if (!in_array($resolvedLocale, ['de', 'en'], true)) {
            $resolvedLocale = 'de';
        }

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));
            $localizedLabelKey = 'label_' . $resolvedLocale;
            if (isset($item[$localizedLabelKey]) && trim((string) $item[$localizedLabelKey]) !== '') {
                $item['label'] = trim((string) $item[$localizedLabelKey]);
            } elseif ($label !== '') {
                $item['label'] = phinit_localize_menu_label($label, $resolvedLocale);
            }

            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = phinit_localize_menu_items($item['children'], $resolvedLocale);
            }

            $items[$index] = $item;
        }

        return $items;
    }
}

if (!function_exists('phinit_get_menu_for_locale')) {
    /**
     * @return array<int,mixed>
     */
    function phinit_get_menu_for_locale(string $baseSlug, ?string $locale = null): array
    {
        $resolvedLocale = strtolower(trim((string) ($locale ?? phinit_get_current_locale())));
        if (!in_array($resolvedLocale, ['de', 'en'], true)) {
            $resolvedLocale = 'de';
        }

        $candidates = $resolvedLocale === 'en'
            ? [$baseSlug . '_en', $baseSlug]
            : [$baseSlug];

        foreach ($candidates as $candidate) {
            try {
                $items = \CMS\ThemeManager::instance()->getMenu($candidate);
            } catch (\Throwable) {
                $items = [];
            }

            if (is_array($items) && $items !== []) {
                return phinit_localize_menu_items($items, $resolvedLocale);
            }
        }

        return [];
    }
}

if (!function_exists('phinit_localized_path')) {
    function phinit_localized_path(string $path, ?string $locale = null): string
    {
        $resolvedLocale = trim((string) ($locale ?? phinit_get_current_locale()));
        $resolvedPath = function_exists('cms_rewrite_archive_path')
            ? cms_rewrite_archive_path($path, $resolvedLocale)
            : $path;

        try {
            return \CMS\Services\ContentLocalizationService::getInstance()->buildLocalizedPath($resolvedPath, $resolvedLocale);
        } catch (\Throwable) {
            return $resolvedPath;
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
            $archiveAwareUrl = function_exists('cms_rewrite_archive_path')
                ? cms_rewrite_archive_path($trimmedUrl, $resolvedLocale)
                : $trimmedUrl;

            $currentHost = phinit_current_host();
            if ($currentHost !== '' && str_contains($currentHost, ':')) {
                $currentHost = explode(':', $currentHost, 2)[0];
            }

            $siteHost = strtolower(trim((string) (parse_url($siteBase, PHP_URL_HOST) ?? '')));
            $preferRelativeInternalUrls = $currentHost !== ''
                && $siteHost !== ''
                && $currentHost !== $siteHost;

            if (preg_match('#^https?://#i', $trimmedUrl) === 1) {
                if ($siteBase === '' || !str_starts_with($trimmedUrl, $siteBase)) {
                    return $trimmedUrl;
                }

                $path = (string) (parse_url($archiveAwareUrl, PHP_URL_PATH) ?? '/');
                $query = (string) (parse_url($trimmedUrl, PHP_URL_QUERY) ?? '');
                $localizedPath = $localization->buildLocalizedPath($path, $resolvedLocale) . ($query !== '' ? '?' . $query : '');

                if ($preferRelativeInternalUrls || $siteBase === '') {
                    return $localizedPath;
                }

                return $siteBase . $localizedPath;
            }

            if (!str_starts_with($trimmedUrl, '/')) {
                return $trimmedUrl;
            }

            $localizedPath = $localization->buildLocalizedPath($archiveAwareUrl, $resolvedLocale);

            if ($preferRelativeInternalUrls || $siteBase === '') {
                return $localizedPath;
            }

            return $siteBase . $localizedPath;
        } catch (\Throwable) {
            if (str_starts_with($trimmedUrl, '/')) {
                $currentHost = phinit_current_host();
                if ($currentHost !== '' && str_contains($currentHost, ':')) {
                    $currentHost = explode(':', $currentHost, 2)[0];
                }

                $siteHost = strtolower(trim((string) (parse_url($siteBase, PHP_URL_HOST) ?? '')));
                if ($siteBase !== '' && ($currentHost === '' || $siteHost === '' || $currentHost === $siteHost)) {
                    return $siteBase . $trimmedUrl;
                }

                return $trimmedUrl;
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
                'updated_badge_label' => '[UPDATE]',
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
                'skip_to_content' => 'Zum Inhalt springen',
                'switch_language' => 'Sprache wechseln',
                'quicklinks' => 'Quicklinks',
                'header_controls' => 'Header-Steuerung',
                'editor_language' => 'Editor-Sprache',
                'close' => 'Schließen',
                'back_to_top' => 'Zum Seitenanfang',
                'learn_more' => 'Mehr erfahren',
                'consent_accept' => 'Einwilligen',
                'consent_decline' => 'Ablehnen',
                'toc_title' => 'Inhaltsverzeichnis',
                'related_articles' => 'Ähnliche Artikel',
                'follow_us' => 'Folge uns',
                'tags' => 'Tags',
                'sidebar' => 'Seitenleiste',
                'article_toc_navigation' => 'Inhaltsverzeichnis des Artikels',
                'share' => 'Teilen',
                'comments' => 'Kommentare',
                'leave_comment' => 'Kommentar hinterlassen',
                'page_navigation' => 'Seitennavigation',
                'menu_location_primary' => 'Hauptnavigation',
                'menu_location_primary_en' => 'Hauptnavigation (Englisch)',
                'menu_location_quicklinks' => 'Quicklinks (Sub-Navigation)',
                'menu_location_quicklinks_en' => 'Quicklinks (Englisch)',
                'menu_location_footer_topics' => 'Footer – Themen',
                'menu_location_footer_topics_en' => 'Footer – Themen (Englisch)',
                'menu_location_footer_pages' => 'Footer – Seiten',
                'menu_location_footer_pages_en' => 'Footer – Seiten (Englisch)',
                'menu_location_footer_legal' => 'Footer – Rechtliches',
                'menu_location_footer_legal_en' => 'Footer – Rechtliches (Englisch)',
                'theme_editor_pretitle' => 'Theme-Editor',
                'theme_customizer_title' => 'Theme Customizer – CMS Phinit',
                'theme_editor_live_preview' => 'Live-Vorschau',
                'theme_editor_save' => 'Speichern',
                'theme_editor_reset_tab' => 'Tab zurücksetzen',
                'theme_editor_unsaved_changes' => 'Ungespeicherte Änderungen',
                'theme_editor_save_shortcut' => 'Strg+S zum Speichern',
                'theme_editor_confirm_change_title' => 'Änderung bestätigen',
                'theme_editor_confirm_continue' => 'Möchtest du fortfahren?',
                'theme_editor_cancel' => 'Abbrechen',
                'theme_editor_continue' => 'Fortfahren',
                'theme_editor_export_import' => 'Export / Import',
                'theme_editor_export' => 'Exportieren',
                'theme_editor_import' => 'Importieren',
                'theme_editor_advanced_import_ack' => 'Import mit enthaltenem Custom-Code bewusst freigeben',
                'theme_editor_menu_entries_title' => 'Menü-Einträge',
                'theme_editor_menu_entries_note' => 'Menü-Einträge (Hauptmenü, Quicklinks, Footer-Menüs) werden im Menü-Editor verwaltet – hier nur Aussehen (Höhen, Farben, Sichtbarkeit).',
                'theme_editor_open_menu_editor' => 'Menü-Editor öffnen',
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
                'updated_badge_label' => '[UPDATED]',
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
                'skip_to_content' => 'Skip to content',
                'switch_language' => 'Switch language',
                'quicklinks' => 'Quick links',
                'header_controls' => 'Header controls',
                'editor_language' => 'Editor language',
                'close' => 'Close',
                'back_to_top' => 'Back to top',
                'learn_more' => 'Learn more',
                'consent_accept' => 'Accept',
                'consent_decline' => 'Decline',
                'toc_title' => 'Table of contents',
                'related_articles' => 'Related articles',
                'follow_us' => 'Follow us',
                'tags' => 'Tags',
                'sidebar' => 'Sidebar',
                'article_toc_navigation' => 'Table of contents for this article',
                'share' => 'Share',
                'comments' => 'Comments',
                'leave_comment' => 'Leave a comment',
                'page_navigation' => 'Page navigation',
                'menu_location_primary' => 'Main navigation',
                'menu_location_primary_en' => 'Main navigation (English)',
                'menu_location_quicklinks' => 'Quick links (sub-navigation)',
                'menu_location_quicklinks_en' => 'Quick links (English)',
                'menu_location_footer_topics' => 'Footer - Topics',
                'menu_location_footer_topics_en' => 'Footer - Topics (English)',
                'menu_location_footer_pages' => 'Footer - Pages',
                'menu_location_footer_pages_en' => 'Footer - Pages (English)',
                'menu_location_footer_legal' => 'Footer - Legal',
                'menu_location_footer_legal_en' => 'Footer - Legal (English)',
                'theme_editor_pretitle' => 'Theme editor',
                'theme_customizer_title' => 'Theme customizer - CMS Phinit',
                'theme_editor_live_preview' => 'Live preview',
                'theme_editor_save' => 'Save',
                'theme_editor_reset_tab' => 'Reset tab',
                'theme_editor_unsaved_changes' => 'Unsaved changes',
                'theme_editor_save_shortcut' => 'Ctrl+S to save',
                'theme_editor_confirm_change_title' => 'Confirm change',
                'theme_editor_confirm_continue' => 'Do you want to continue?',
                'theme_editor_cancel' => 'Cancel',
                'theme_editor_continue' => 'Continue',
                'theme_editor_export_import' => 'Export / Import',
                'theme_editor_export' => 'Export',
                'theme_editor_import' => 'Import',
                'theme_editor_advanced_import_ack' => 'Allow importing included custom code',
                'theme_editor_menu_entries_title' => 'Menu entries',
                'theme_editor_menu_entries_note' => 'Menu entries (main menu, quick links, footer menus) are managed in the menu editor - only appearance (heights, colors, visibility) is configured here.',
                'theme_editor_open_menu_editor' => 'Open menu editor',
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

if (!function_exists('phinit_customizer_value')) {
    function phinit_customizer_value(string $category, string $key, mixed $default = '', ?string $locale = null): mixed
    {
        static $customizer = null;
        static $resolved = false;

        if (!$resolved) {
            $resolved = true;
            try {
                $customizer = \CMS\Services\ThemeCustomizer::instance();
            } catch (\Throwable) {
                $customizer = null;
            }
        }

        if (!$customizer) {
            return $default;
        }

        try {
            $supportedLocales = ['de', 'en'];
            $configuredDefaultLocale = function_exists('phinit_default_locale')
                ? strtolower(trim((string) phinit_default_locale()))
                : 'de';
            $defaultLocale = in_array($configuredDefaultLocale, $supportedLocales, true) ? $configuredDefaultLocale : 'de';

            $resolvedLocale = strtolower(trim((string) ($locale ?? (function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : $defaultLocale))));
            if (!in_array($resolvedLocale, $supportedLocales, true)) {
                $resolvedLocale = $defaultLocale;
            }

            if ($resolvedLocale === $defaultLocale) {
                return $customizer->get($category, $key, $default);
            }

            $localizedValueKey = '__locale_' . $resolvedLocale . '__' . $key;
            $localizedStateKey = '__locale_' . $resolvedLocale . '__set__' . $key;
            $isCustomizedRaw = $customizer->get($category, $localizedStateKey, null);
            $isCustomized = filter_var($isCustomizedRaw, FILTER_VALIDATE_BOOLEAN);

            if (!$isCustomized) {
                return $customizer->get($category, $key, $default);
            }

            $fallback = $customizer->get($category, $key, $default);
            return $customizer->get($category, $localizedValueKey, $fallback);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('phinit_customizer_bool')) {
    function phinit_customizer_bool(string $category, string $key, bool $default = true): bool
    {
        return filter_var(phinit_customizer_value($category, $key, $default), FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('phinit_customizer_locale_proxy')) {
    function phinit_customizer_locale_proxy(object $customizer, ?string $locale = null): object
    {
        $resolvedLocale = strtolower(trim((string) ($locale ?? (function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de'))));
        if (!in_array($resolvedLocale, ['de', 'en'], true)) {
            $resolvedLocale = 'de';
        }

        return new class($customizer, $resolvedLocale) {
            public function __construct(
                private readonly object $inner,
                private readonly string $locale
            ) {
            }

            public function get(string $category, string $key, mixed $default = ''): mixed
            {
                return phinit_customizer_value($category, $key, $default, $this->locale);
            }

            public function __call(string $method, array $arguments): mixed
            {
                return $this->inner->{$method}(...$arguments);
            }
        };
    }
}

if (!function_exists('phinit_author_service_hub_href')) {
    function phinit_author_service_hub_href(?string $configuredUrl = null, ?string $locale = null, ?string $siteUrl = null): string
    {
        $candidate = trim((string) ($configuredUrl ?? ''));
        if ($candidate === '') {
            $candidate = '/it-dienstleistungen';
        }

        $safeUrl = function_exists('phinit_safe_public_url')
            ? phinit_safe_public_url($candidate, $siteUrl, ['http', 'https'])
            : $candidate;

        if ($safeUrl === '') {
            return '';
        }

        return function_exists('phinit_localized_href')
            ? phinit_localized_href($safeUrl, $locale, $siteUrl)
            : $safeUrl;
    }
}

if (!function_exists('phinit_build_author_box_context')) {
    /**
     * Lädt das im Member-Profil gepflegte Avatarbild eines Autors für Frontend-Autorboxen.
     */
    function phinit_resolve_author_profile_avatar_url(int $authorId, ?string $siteUrl = null): string
    {
        if ($authorId <= 0) {
            return '';
        }

        try {
            $db = \CMS\Database::instance();
            $prefix = method_exists($db, 'getPrefix') ? $db->getPrefix() : $db->prefix();
            $avatar = $db->get_var(
                "SELECT meta_value
                 FROM {$prefix}user_meta
                 WHERE user_id = ? AND meta_key IN ('avatar', 'avatar_url', 'profile_avatar')
                 ORDER BY FIELD(meta_key, 'avatar', 'avatar_url', 'profile_avatar')
                 LIMIT 1",
                [$authorId]
            );
        } catch (\Throwable) {
            $avatar = '';
        }

        $avatar = trim((string) $avatar);
        if ($avatar === '') {
            return '';
        }

        return function_exists('phinit_normalize_public_media_url')
            ? phinit_normalize_public_media_url($avatar, false, $siteUrl)
            : (function_exists('phinit_safe_public_url') ? phinit_safe_public_url($avatar, $siteUrl, ['http', 'https']) : $avatar);
    }

    /**
     * Baut die Daten für die wiederverwendbare Autorbox auf Beitrags- und Seitendetails.
     *
     * @param array<string,mixed> $options
     * @return array<string,mixed>
     */
    function phinit_build_author_box_context(array $options): array
    {
        $category = trim((string) ($options['category'] ?? 'posts')) ?: 'posts';
        $siteUrl = (string) ($options['site_url'] ?? (defined('SITE_URL') ? SITE_URL : ''));
        $locale = trim((string) ($options['locale'] ?? (function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de'))) ?: 'de';

        $showKey = (string) ($options['show_key'] ?? 'show_author_box');
        $nameKey = (string) ($options['name_key'] ?? 'author_name');
        $bioKey = (string) ($options['bio_key'] ?? 'author_bio');
        $avatarKey = (string) ($options['avatar_key'] ?? 'author_avatar_url');
        $eyebrowKey = (string) ($options['eyebrow_key'] ?? 'author_eyebrow');
        $aboutLabelKey = (string) ($options['about_label_key'] ?? 'author_about_label');
        $aboutWidthKey = (string) ($options['about_width_key'] ?? 'author_about_width');
        $showServiceKey = (string) ($options['show_service_key'] ?? 'show_author_service_hub');
        $serviceUrlKey = (string) ($options['service_url_key'] ?? 'author_service_hub_url');
        $serviceLabelKey = (string) ($options['service_label_key'] ?? 'author_service_hub_label');
        $serviceTextKey = (string) ($options['service_text_key'] ?? 'author_service_hub_text');

        $showAuthorBox = phinit_customizer_bool($category, $showKey, (bool) ($options['show_default'] ?? true));
        $showService = phinit_customizer_bool($category, $showServiceKey, (bool) ($options['show_service_default'] ?? true));

        $entityName = trim((string) ($options['entity_name'] ?? ''));
        $configuredName = trim((string) phinit_customizer_value($category, $nameKey, (string) ($options['default_name'] ?? '')));
        $authorName = $configuredName !== '' ? $configuredName : $entityName;

        $authorBio = trim((string) phinit_customizer_value($category, $bioKey, (string) ($options['default_bio'] ?? '')));
        $authorAvatar = trim((string) phinit_customizer_value($category, $avatarKey, (string) ($options['default_avatar'] ?? '')));
        if ($authorAvatar === '' && isset($options['author_id'])) {
            $authorAvatar = phinit_resolve_author_profile_avatar_url((int) $options['author_id'], $siteUrl);
        }
        $authorAvatarUrl = function_exists('phinit_normalize_public_media_url')
            ? phinit_normalize_public_media_url($authorAvatar, true, $siteUrl)
            : (function_exists('phinit_safe_public_url') ? phinit_safe_public_url($authorAvatar, $siteUrl, ['http', 'https']) : $authorAvatar);
        $authorAboutWidth = (int) phinit_customizer_value($category, $aboutWidthKey, (int) ($options['default_about_width'] ?? 60));
        if (!in_array($authorAboutWidth, [50, 60, 75], true)) {
            $authorAboutWidth = 60;
        }

        $serviceHubUrl = '';
        if ($showService) {
            $serviceHubUrl = phinit_author_service_hub_href(
                (string) phinit_customizer_value($category, $serviceUrlKey, (string) ($options['default_service_url'] ?? '/it-dienstleistungen')),
                $locale,
                $siteUrl
            );
        }

        $serviceHubLabel = trim((string) phinit_customizer_value($category, $serviceLabelKey, (string) ($options['default_service_label'] ?? 'Dienstleistungen ansehen')));
        $serviceHubText = trim((string) phinit_customizer_value($category, $serviceTextKey, (string) ($options['default_service_text'] ?? 'Du suchst Unterstützung bei Microsoft 365, 365CMS oder IT-Automatisierung? Hier findest du passende Leistungen und Einstiegspakete.')));
        $hasAuthorContent = $authorName !== '' || $authorBio !== '' || $authorAvatarUrl !== '';
        $hasServiceContent = $serviceHubUrl !== '' && ($serviceHubLabel !== '' || $serviceHubText !== '');

        $resolvedAuthorLink = function_exists('phinit_resolve_post_author_link')
            ? phinit_resolve_post_author_link(
                ['author_id' => $options['author_id'] ?? 0, 'author_display_url' => $options['author_display_url'] ?? ''],
                $locale,
                $siteUrl
            )
            : ['url' => '', 'isExternal' => false];
        $resolvedAuthorUrl = $resolvedAuthorLink['url'] !== '' ? $resolvedAuthorLink['url'] : trim((string) ($options['author_url'] ?? ''));

        return [
            'show' => $showAuthorBox && ($hasAuthorContent || $hasServiceContent),
            'authorName' => $authorName,
            'authorBio' => $authorBio,
            'authorAvatarUrl' => $authorAvatarUrl,
            'authorUrl' => $resolvedAuthorUrl,
            'authorUrlIsExternal' => $resolvedAuthorLink['isExternal'],
            'authorEyebrow' => trim((string) phinit_customizer_value($category, $eyebrowKey, (string) ($options['default_eyebrow'] ?? 'Autor'))),
            'authorAboutLabel' => trim((string) phinit_customizer_value($category, $aboutLabelKey, (string) ($options['default_about_label'] ?? 'Über mich'))),
            'authorAboutWidth' => $authorAboutWidth,
            'authorBoxModifierClass' => trim((string) ($options['authorBoxModifierClass'] ?? '')),
            'serviceHubUrl' => $serviceHubUrl,
            'serviceHubLabel' => $serviceHubLabel,
            'serviceHubText' => $serviceHubText,
        ];
    }
}

if (!function_exists('phinit_resolve_post_author_link')) {
    /**
     * Ermittelt Ziel-URL und Ziel-Attribut für den (ggf. alternativen) Autorennamen eines Beitrags.
     * Ist eine externe Autoren-Website hinterlegt (`author_display_url`), wird auf diese statt auf die
     * interne Autoren-Info-Seite verlinkt – und im neuen Tab geöffnet.
     *
     * @param array<string,mixed> $post
     * @return array{url:string,isExternal:bool}
     */
    function phinit_resolve_post_author_link(array $post, string $currentLocale, string $siteUrl): array
    {
        $displayUrl = trim((string) ($post['author_display_url'] ?? ''));
        if ($displayUrl !== '' && preg_match('#^https?://#i', $displayUrl) === 1) {
            return ['url' => $displayUrl, 'isExternal' => true];
        }

        $authorId = (int) ($post['author_id'] ?? 0);
        if ($authorId <= 0) {
            return ['url' => '', 'isExternal' => false];
        }

        $internalUrl = function_exists('phinit_localized_href')
            ? phinit_localized_href('/author/user-' . $authorId, $currentLocale, $siteUrl)
            : rtrim($siteUrl, '/') . '/author/user-' . $authorId;

        return ['url' => $internalUrl, 'isExternal' => false];
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

        $extractId = static function (array|object|null $entity): int {
            if (is_array($entity)) {
                return max(
                    0,
                    (int) ($entity['id'] ?? 0),
                    (int) ($entity['post_id'] ?? 0),
                    (int) ($entity['page_id'] ?? 0),
                    (int) ($entity['hub_id'] ?? 0)
                );
            }

            if (is_object($entity)) {
                return max(
                    0,
                    (int) ($entity->id ?? 0),
                    (int) ($entity->post_id ?? 0),
                    (int) ($entity->page_id ?? 0),
                    (int) ($entity->hub_id ?? 0)
                );
            }

            return 0;
        };

        $readField = static function (array|object|null $entity, string $field): string {
            if (is_array($entity)) {
                return trim((string) ($entity[$field] ?? ''));
            }

            if (is_object($entity)) {
                return trim((string) ($entity->{$field} ?? ''));
            }

            return '';
        };

        $currentPost = $GLOBALS['post'] ?? null;
        if (is_array($currentPost) || is_object($currentPost)) {
            $postId = $extractId($currentPost);
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

        $currentPage = $GLOBALS['page'] ?? null;
        if (is_array($currentPage) || is_object($currentPage)) {
            $pageId = $extractId($currentPage);
            $contentType = strtolower($readField($currentPage, 'content_type'));

            if ($pageId > 0 && in_array($contentType, ['hub', 'hubsite', 'hub_site'], true)) {
                return [
                    'show' => true,
                    'url' => rtrim((string) SITE_URL, '/') . '/admin/hub-sites?action=edit&id=' . $pageId,
                    'label' => phinit_t('edit_hubsite'),
                    'entity' => 'hub',
                    'entityId' => $pageId,
                ];
            }

            if ($pageId > 0) {
                return [
                    'show' => true,
                    'url' => rtrim((string) SITE_URL, '/') . '/admin/pages?action=edit&id=' . $pageId,
                    'label' => phinit_t('edit_page'),
                    'entity' => 'page',
                    'entityId' => $pageId,
                ];
            }
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
            $host = phinit_current_host();
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
                    "SELECT id FROM {$db->getPrefix()}posts WHERE slug = ? AND " . phinit_post_publication_where() . " LIMIT 1",
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
            $page = \CMS\PageManager::instance()->getPageBySlug($slug, $resolvedLocale !== '' ? $resolvedLocale : 'de');
            if (is_object($page)) {
                $page = (array) $page;
            }

            $pageId = $extractId(is_array($page) ? $page : null);
            if ($pageId > 0) {
                $contentType = strtolower($readField(is_array($page) ? $page : null, 'content_type'));
                if (in_array($contentType, ['hub', 'hubsite', 'hub_site'], true)) {
                    return [
                        'show' => true,
                        'url' => rtrim((string) SITE_URL, '/') . '/admin/hub-sites?action=edit&id=' . $pageId,
                        'label' => phinit_t('edit_hubsite'),
                        'entity' => 'hub',
                        'entityId' => $pageId,
                    ];
                }

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
            $url = phinit_safe_public_url((string) ($item['url'] ?? ''), defined('SITE_URL') ? (string) SITE_URL : null, ['http', 'https']);
            $title = trim((string) ($item['title'] ?? ''));
            $featuredImage = phinit_normalize_public_media_url((string) ($item['featured_image'] ?? ''), true, defined('SITE_URL') ? (string) SITE_URL : null);

            if ($contentId <= 0 || $contentType !== 'page' || $url === '' || $title === '') {
                continue;
            }

            $normalized[] = [
                'content_type' => 'page',
                'content_id' => $contentId,
                'title' => $title,
                'url' => $url,
                'excerpt' => trim((string) ($item['excerpt'] ?? '')),
                'featured_image' => $featuredImage,
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

        $payload = [];

        foreach ($favorites as $favorite) {
            if (!is_array($favorite)) {
                continue;
            }

            $contentId = (int) ($favorite['content_id'] ?? 0);
            $title = trim((string) ($favorite['title'] ?? ''));
            $url = phinit_safe_public_url((string) ($favorite['url'] ?? ''), defined('SITE_URL') ? (string) SITE_URL : null, ['http', 'https']);

            if ($contentId <= 0 || $title === '' || $url === '') {
                continue;
            }

            $payload[] = [
                'content_type' => 'page',
                'content_id' => $contentId,
                'title' => $title,
                'url' => $url,
                'excerpt' => trim((string) ($favorite['excerpt'] ?? '')),
                'featured_image' => phinit_normalize_public_media_url((string) ($favorite['featured_image'] ?? ''), true, defined('SITE_URL') ? (string) SITE_URL : null),
                'badge' => trim((string) ($favorite['badge'] ?? 'Seite')),
                'created_at' => trim((string) ($favorite['created_at'] ?? '')),
            ];
        }

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
        if (phinit_request_method() !== 'POST') {
            return;
        }

        if (phinit_input_string($_POST, 'phinit_toggle_favorite', '', 1) !== '1') {
            return;
        }

        $contentType = phinit_input_string($_POST, 'favorite_content_type', 'post', 20);
        if (!in_array($contentType, ['post', 'page'], true)) {
            return;
        }

        $contentId = phinit_input_int($_POST, 'favorite_content_id', 0, 1);
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
            if (!\CMS\Security::instance()->verifyPersistentToken(phinit_input_string($_POST, 'favorite_csrf_token', '', 128), $tokenAction)) {
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
                $existingCount = count($pageFavorites);
                $pageFavorites = array_values(array_filter(
                    $pageFavorites,
                    static fn(array $favorite): bool => (int) ($favorite['content_id'] ?? 0) !== $contentId
                ));

                $exists = count($pageFavorites) !== $existingCount;
                if (!$exists) {
                    $favoriteUrl = phinit_safe_public_url($requestPath !== '' ? $requestPath : '/', defined('SITE_URL') ? (string) SITE_URL : null, ['http', 'https']);
                    $pageFavorites[] = [
                        'content_type' => 'page',
                        'content_id' => $contentId,
                        'title' => phinit_input_string($_POST, 'favorite_title', 'Seite', 255),
                        'url' => $favoriteUrl !== '' ? $favoriteUrl : '/',
                        'excerpt' => phinit_input_string($_POST, 'favorite_excerpt', '', 1000),
                        'featured_image' => phinit_normalize_public_media_url(phinit_input_string($_POST, 'favorite_featured_image', '', 1024), true, defined('SITE_URL') ? (string) SITE_URL : null),
                        'badge' => phinit_input_string($_POST, 'favorite_badge', 'Seite', 80),
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
        $loginUrl = theme_login_url($requestUri);
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
