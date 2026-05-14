<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('phinit_term_slug')) {
    function phinit_term_slug(string $value): string
    {
        $value = trim(mb_strtolower($value, 'UTF-8'));
        if ($value === '') {
            return '';
        }

        $value = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $value);
        $value = preg_replace('/[^a-z0-9]+/u', '-', $value) ?? '';

        return trim($value, '-');
    }
}

if (!function_exists('phinit_parse_post_tags')) {
    /**
     * @return array<int,array{name:string,slug:string}>
     */
    function phinit_parse_post_tags(?string $rawTags): array
    {
        $tags = [];

        foreach (array_filter(array_map('trim', explode(',', (string) $rawTags))) as $tagName) {
            $tags[] = [
                'name' => $tagName,
                'slug' => phinit_term_slug($tagName),
            ];
        }

        return $tags;
    }
}

if (!function_exists('phinit_get_public_authors_overview')) {
    /**
     * @return array<int,array<string,mixed>>
     */
    function phinit_get_public_authors_overview(): array
    {
        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $rows = $db->get_results(
                "SELECT stats.author_id,
                        stats.post_count,
                        stats.latest_post_at,
                        COALESCE(NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS display_name,
                        u.username,
                        MAX(CASE WHEN um.meta_key = 'bio' THEN um.meta_value ELSE NULL END) AS bio,
                        MAX(CASE WHEN um.meta_key IN ('avatar_url', 'avatar', 'profile_avatar') THEN um.meta_value ELSE NULL END) AS avatar_url,
                        MAX(CASE WHEN um.meta_key = 'website' THEN um.meta_value ELSE NULL END) AS website,
                        MAX(CASE WHEN um.meta_key = 'location' THEN um.meta_value ELSE NULL END) AS location
                 FROM (
                    SELECT p.author_id,
                           COUNT(*) AS post_count,
                           MAX(COALESCE(p.published_at, p.created_at)) AS latest_post_at
                    FROM {$prefix}posts p
                    WHERE " . phinit_post_publication_where('p') . " AND p.author_id IS NOT NULL AND p.author_id > 0
                    GROUP BY p.author_id
                 ) stats
                 LEFT JOIN {$prefix}users u ON u.id = stats.author_id
                 LEFT JOIN {$prefix}user_meta um ON um.user_id = stats.author_id
                    AND um.meta_key IN ('bio', 'avatar_url', 'avatar', 'profile_avatar', 'website', 'location')
                 GROUP BY stats.author_id, stats.post_count, stats.latest_post_at, u.display_name, u.username
                 ORDER BY stats.post_count DESC, stats.latest_post_at DESC"
            ) ?: [];
        } catch (\Throwable) {
            return [];
        }

        $authors = [];

        foreach ($rows as $row) {
            $author = (array) $row;
            $authorId = (int) ($author['author_id'] ?? 0);
            if ($authorId <= 0) {
                continue;
            }

            $details = [];
            $location = trim((string) ($author['location'] ?? ''));
            if ($location !== '') {
                $details[] = ['label' => 'Standort', 'value' => $location, 'type' => 'text'];
            }
            $website = trim((string) ($author['website'] ?? ''));
            if ($website !== '') {
                $details[] = ['label' => 'Website', 'value' => $website, 'type' => 'url'];
            }

            $authors[] = [
                'id' => $authorId,
                'display_name' => trim((string) ($author['display_name'] ?? 'Autor')),
                'username' => trim((string) ($author['username'] ?? '')),
                'bio' => trim((string) ($author['bio'] ?? '')),
                'avatar_url' => trim((string) ($author['avatar_url'] ?? '')),
                'profile_url' => '/author/user-' . $authorId,
                'details' => $details,
                'post_count' => (int) ($author['post_count'] ?? 0),
                'latest_post_at' => (string) ($author['latest_post_at'] ?? ''),
            ];
        }

        usort($authors, static function (array $left, array $right): int {
            $leftPosts = (int) ($left['post_count'] ?? 0);
            $rightPosts = (int) ($right['post_count'] ?? 0);

            if ($leftPosts !== $rightPosts) {
                return $rightPosts <=> $leftPosts;
            }

            return strcmp((string) ($left['display_name'] ?? ''), (string) ($right['display_name'] ?? ''));
        });

        return $authors;
    }
}

if (!function_exists('phinit_build_html_sitemap_view_model')) {
    /**
     * @return array<string,mixed>
     */
    function phinit_build_html_sitemap_view_model(): array
    {
        $siteUrl = rtrim((string) SITE_URL, '/');
        $currentLocale = phinit_get_current_locale();
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();
        $permalinkService = \CMS\Services\PermalinkService::getInstance();

        $pages = $db->get_results(
            "SELECT id, title, slug, updated_at, content_type
             FROM {$prefix}pages
             WHERE status = 'published'
             ORDER BY title ASC"
        ) ?: [];

        $pageLinks = [];
        foreach ($pages as $page) {
            $pageData = (array) $page;
            if (in_array((string) ($pageData['content_type'] ?? ''), ['cookie_consent'], true)) {
                continue;
            }

            $pageLinks[] = [
                'title' => trim((string) ($pageData['title'] ?? 'Seite')),
                'url' => phinit_localized_href('/' . ltrim((string) ($pageData['slug'] ?? ''), '/'), $currentLocale, $siteUrl),
                'meta' => (string) ($pageData['updated_at'] ?? ''),
            ];
        }

        $categories = $db->get_results(
            "SELECT c.name, c.slug,
                    COUNT(p.id) AS post_count
             FROM {$prefix}post_categories c
               INNER JOIN {$prefix}posts p ON p.category_id = c.id AND " . phinit_post_publication_where('p') . "
             GROUP BY c.id, c.name, c.slug
             ORDER BY c.name ASC"
        ) ?: [];

        $categoryLinks = array_map(static function (object $row) use ($currentLocale, $siteUrl): array {
            $slug = trim((string) ($row->slug ?? ''));
            return [
                'title' => trim((string) ($row->name ?? 'Kategorie')),
                'url' => phinit_localized_href('/kategorie/' . rawurlencode($slug), $currentLocale, $siteUrl),
                'count' => (int) ($row->post_count ?? 0),
            ];
        }, $categories);

        $tagCounts = [];
        $tagLabels = [];
        $tagRows = $db->get_results(
            "SELECT tags
             FROM {$prefix}posts
               WHERE " . phinit_post_publication_where() . " AND tags IS NOT NULL AND tags != ''"
        ) ?: [];

        foreach ($tagRows as $row) {
            foreach (phinit_parse_post_tags((string) ($row->tags ?? '')) as $tag) {
                $tagSlug = (string) ($tag['slug'] ?? '');
                if ($tagSlug === '') {
                    continue;
                }

                $tagCounts[$tagSlug] = ($tagCounts[$tagSlug] ?? 0) + 1;
                $tagLabels[$tagSlug] = (string) ($tag['name'] ?? $tagSlug);
            }
        }

        arsort($tagCounts);
        $tagLinks = [];
        foreach (array_slice(array_keys($tagCounts), 0, 30) as $tagSlug) {
            $tagLinks[] = [
                'title' => $tagLabels[$tagSlug] ?? $tagSlug,
                'url' => phinit_localized_href('/tag/' . rawurlencode($tagSlug), $currentLocale, $siteUrl),
                'count' => (int) ($tagCounts[$tagSlug] ?? 0),
            ];
        }

        $recentPosts = $db->get_results(
            "SELECT p.id, p.title, p.slug, p.published_at, p.created_at,
                    COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}users u ON u.id = p.author_id
               WHERE " . phinit_post_publication_where('p') . "
             ORDER BY COALESCE(p.published_at, p.created_at) DESC
             LIMIT 14"
        ) ?: [];

        $postLinks = array_map(static function (object $row) use ($permalinkService, $currentLocale, $siteUrl): array {
            $postData = (array) $row;
            return [
                'title' => trim((string) ($postData['title'] ?? 'Beitrag')),
                'url' => $siteUrl . $permalinkService->buildPostPath($postData, $currentLocale),
                'meta' => (string) ($postData['published_at'] ?? $postData['created_at'] ?? ''),
                'author' => trim((string) ($postData['author_name'] ?? '')),
            ];
        }, $recentPosts);

        $authors = array_map(static function (array $author) use ($currentLocale, $siteUrl): array {
            $profilePath = (string) ($author['profile_url'] ?? '/author/user-' . (int) ($author['id'] ?? 0));
            return [
                'title' => trim((string) ($author['display_name'] ?? 'Autor')),
                'url' => phinit_localized_href($profilePath, $currentLocale, $siteUrl),
                'count' => (int) ($author['post_count'] ?? 0),
            ];
        }, phinit_get_public_authors_overview());

        return [
            'quickLinks' => [
                ['title' => 'Startseite', 'url' => phinit_localized_href('/', $currentLocale, $siteUrl)],
                ['title' => 'Blog', 'url' => phinit_localized_href('/blog', $currentLocale, $siteUrl)],
                ['title' => 'Suche', 'url' => phinit_localized_href('/search', $currentLocale, $siteUrl)],
                ['title' => 'RSS-Feed', 'url' => phinit_localized_href('/feed', $currentLocale, $siteUrl)],
                ['title' => 'Kontakt', 'url' => phinit_localized_href('/contact', $currentLocale, $siteUrl)],
                ['title' => 'Alle Autoren', 'url' => phinit_localized_href('/autoren', $currentLocale, $siteUrl)],
            ],
            'pages' => $pageLinks,
            'categories' => $categoryLinks,
            'tags' => $tagLinks,
            'authors' => $authors,
            'recentPosts' => $postLinks,
        ];
    }
}

if (!function_exists('phinit_get_mastodon_share_url')) {
    function phinit_get_mastodon_share_url(string $url, string $title, ?string $profileUrl = null): string
    {
        $host = 'mastodon.social';
        $parsedHost = trim((string) parse_url((string) $profileUrl, PHP_URL_HOST));
        if ($parsedHost !== '') {
            $host = $parsedHost;
        }

        $shareText = trim($title . ' ' . $url);

        return 'https://' . $host . '/share?text=' . rawurlencode($shareText);
    }
}
