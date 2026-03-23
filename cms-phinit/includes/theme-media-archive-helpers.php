<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('phinit_image_archive_page_slugs')) {
    /**
     * @return array<int, string>
     */
    function phinit_image_archive_page_slugs(): array
    {
        return [
            'image-archiv',
            'bilder-archiv',
            'bildarchiv',
            'media-archiv',
            'medien-archiv',
            'medien',
        ];
    }
}

if (!function_exists('phinit_is_image_archive_page')) {
    /**
     * @param array<string, mixed> $page
     */
    function phinit_is_image_archive_page(array $page): bool
    {
        $slug = strtolower(trim((string) ($page['slug'] ?? '')));
        $contentType = strtolower(trim((string) ($page['content_type'] ?? '')));

        return in_array($slug, phinit_image_archive_page_slugs(), true)
            || in_array($contentType, ['image_archive', 'media_archive'], true);
    }
}

if (!function_exists('phinit_build_image_archive_view_model')) {
    /**
     * @param array<string, mixed> $page
     * @return array<string, mixed>
     */
    function phinit_build_image_archive_view_model(array $page): array
    {
        static $cache = [];

        $cacheKey = strtolower(trim((string) ($page['slug'] ?? 'image-archiv')));
        if (isset($cache[$cacheKey]) && is_array($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $viewModel = [
            'groups' => [],
            'stats' => [
                'images' => 0,
                'cover_images' => 0,
                'content_images' => 0,
                'categories' => 0,
                'articles' => 0,
            ],
            'empty_message' => 'Aktuell wurden keine lokalen Medienbilder gefunden, die als Artikelbild oder Cover in veröffentlichten Beiträgen verwendet werden.',
        ];

        try {
            $mediaService = \CMS\Services\MediaService::getInstance();
            $categories = $mediaService->getCategories();
            $mediaFiles = phinit_media_archive_collect_files($mediaService);
            $fileIndex = phinit_media_archive_build_file_index($mediaFiles);

            if ($fileIndex === []) {
                $cache[$cacheKey] = $viewModel;
                return $viewModel;
            }

            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $posts = $db->get_results(
                "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.created_at,
                        c.name AS category_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                  WHERE " . phinit_post_publication_where('p') . "
                  ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC"
            ) ?: [];

            $usedArticles = [];
            $usageByFile = [];

            foreach ($posts as $postObject) {
                $post = is_object($postObject) ? (array) $postObject : (array) $postObject;
                $postId = (int) ($post['id'] ?? 0);
                if ($postId <= 0) {
                    continue;
                }

                $permalink = function_exists('phinit_build_post_url')
                    ? phinit_build_post_url($post, function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de')
                    : (rtrim((string) SITE_URL, '/') . '/blog/' . rawurlencode((string) ($post['slug'] ?? '')));
                $postTitle = trim((string) ($post['title'] ?? 'Artikel'));
                $publishedAt = trim((string) ($post['published_at'] ?? $post['created_at'] ?? ''));
                $categoryName = trim((string) ($post['category_name'] ?? ''));

                $registerUsage = static function (string $filePath, string $type, string $imageUrl) use (&$usageByFile, &$usedArticles, $postId, $postTitle, $permalink, $publishedAt, $categoryName): void {
                    if (!isset($usageByFile[$filePath])) {
                        $usageByFile[$filePath] = [
                            'types' => [],
                            'posts' => [],
                            'article_count' => 0,
                            'latest_date' => '',
                            'primary_alt' => '',
                        ];
                    }

                    $usageByFile[$filePath]['types'][$type] = true;
                    $usedArticles[$postId] = true;

                    if (!isset($usageByFile[$filePath]['posts'][$postId])) {
                        $usageByFile[$filePath]['posts'][$postId] = [
                            'post_id' => $postId,
                            'title' => $postTitle,
                            'url' => $permalink,
                            'published_at' => $publishedAt,
                            'category_name' => $categoryName,
                            'types' => [],
                        ];
                    }

                    $usageByFile[$filePath]['posts'][$postId]['types'][$type] = true;
                    $usageByFile[$filePath]['article_count'] = count($usageByFile[$filePath]['posts']);

                    if ($publishedAt !== '' && (($usageByFile[$filePath]['latest_date'] ?? '') === '' || strtotime($publishedAt) > strtotime((string) $usageByFile[$filePath]['latest_date']))) {
                        $usageByFile[$filePath]['latest_date'] = $publishedAt;
                    }

                    if (($usageByFile[$filePath]['primary_alt'] ?? '') === '') {
                        $usageByFile[$filePath]['primary_alt'] = $postTitle !== '' ? $postTitle : basename($imageUrl);
                    }
                };

                $featuredImage = trim((string) ($post['featured_image'] ?? ''));
                if ($featuredImage !== '') {
                    $relativePath = phinit_media_archive_normalize_image_reference($featuredImage);
                    if ($relativePath !== '' && isset($fileIndex[$relativePath])) {
                        $registerUsage($relativePath, 'cover', $featuredImage);
                    }
                }

                $content = trim((string) ($post['content'] ?? ''));
                if ($content === '') {
                    continue;
                }

                $renderedContent = phinit_prepare_renderable_content($content, 'post', $postId);
                foreach (phinit_media_archive_extract_image_sources($renderedContent) as $contentImage) {
                    $relativePath = phinit_media_archive_normalize_image_reference($contentImage);
                    if ($relativePath === '' || !isset($fileIndex[$relativePath])) {
                        continue;
                    }

                    $registerUsage($relativePath, 'content', $contentImage);
                }
            }

            if ($usageByFile === []) {
                $cache[$cacheKey] = $viewModel;
                return $viewModel;
            }

            $groups = [];
            foreach ($categories as $category) {
                if (!is_array($category)) {
                    continue;
                }

                $slug = trim((string) ($category['slug'] ?? ''));
                if ($slug === '') {
                    continue;
                }

                $groups[$slug] = [
                    'slug' => $slug,
                    'name' => trim((string) ($category['name'] ?? $slug)),
                    'is_system' => !empty($category['is_system']),
                    'items' => [],
                ];
            }

            $groups['_uncategorized'] = [
                'slug' => '_uncategorized',
                'name' => 'Ohne Medienkategorie',
                'is_system' => false,
                'items' => [],
            ];

            $coverCount = 0;
            $contentCount = 0;

            foreach ($usageByFile as $filePath => $usage) {
                if (!isset($fileIndex[$filePath])) {
                    continue;
                }

                $file = $fileIndex[$filePath];
                $categorySlug = trim((string) ($file['category'] ?? ''));
                if ($categorySlug === '' || !isset($groups[$categorySlug])) {
                    $categorySlug = '_uncategorized';
                }

                $postsForFile = array_values($usage['posts']);
                usort($postsForFile, static function (array $left, array $right): int {
                    return strtotime((string) ($right['published_at'] ?? '')) <=> strtotime((string) ($left['published_at'] ?? ''));
                });

                $usageTypes = array_keys(array_filter((array) ($usage['types'] ?? [])));
                sort($usageTypes);

                if (in_array('cover', $usageTypes, true)) {
                    $coverCount++;
                }
                if (in_array('content', $usageTypes, true)) {
                    $contentCount++;
                }

                $groups[$categorySlug]['items'][] = [
                    'file_path' => $filePath,
                    'file_name' => trim((string) ($file['name'] ?? basename($filePath))),
                    'image_url' => trim((string) ($file['preview_url'] ?? $file['url'] ?? '')),
                    'download_url' => trim((string) ($file['url'] ?? '')),
                    'category' => trim((string) ($groups[$categorySlug]['name'] ?? '')),
                    'usage_types' => $usageTypes,
                    'article_count' => (int) ($usage['article_count'] ?? count($postsForFile)),
                    'latest_date' => trim((string) ($usage['latest_date'] ?? '')),
                    'posts' => $postsForFile,
                    'alt' => trim((string) ($usage['primary_alt'] ?? '')) !== ''
                        ? trim((string) $usage['primary_alt'])
                        : trim((string) ($file['name'] ?? basename($filePath))),
                ];
            }

            foreach ($groups as $groupSlug => $group) {
                if (($group['items'] ?? []) === []) {
                    unset($groups[$groupSlug]);
                    continue;
                }

                usort($groups[$groupSlug]['items'], static function (array $left, array $right): int {
                    $dateCompare = strtotime((string) ($right['latest_date'] ?? '')) <=> strtotime((string) ($left['latest_date'] ?? ''));
                    if ($dateCompare !== 0) {
                        return $dateCompare;
                    }

                    return strcasecmp((string) ($left['file_name'] ?? ''), (string) ($right['file_name'] ?? ''));
                });

                $groups[$groupSlug]['item_count'] = count($groups[$groupSlug]['items']);
            }

            uasort($groups, static function (array $left, array $right): int {
                if (($left['slug'] ?? '') === '_uncategorized') {
                    return 1;
                }
                if (($right['slug'] ?? '') === '_uncategorized') {
                    return -1;
                }

                return strcasecmp((string) ($left['name'] ?? ''), (string) ($right['name'] ?? ''));
            });

            $viewModel['groups'] = array_values($groups);
            $viewModel['stats'] = [
                'images' => count($usageByFile),
                'cover_images' => $coverCount,
                'content_images' => $contentCount,
                'categories' => count($groups),
                'articles' => count($usedArticles),
            ];
        } catch (\Throwable) {
        }

        $cache[$cacheKey] = $viewModel;
        return $viewModel;
    }
}

if (!function_exists('phinit_media_archive_collect_files')) {
    /**
     * @return array<int, array<string, mixed>>
     */
    function phinit_media_archive_collect_files(\CMS\Services\MediaService $mediaService, string $path = ''): array
    {
        $items = $mediaService->getItems($path);
        if ($items instanceof \WP_Error || !is_array($items)) {
            return [];
        }

        $files = [];

        foreach ((array) ($items['files'] ?? []) as $file) {
            if (!is_array($file) || !phinit_media_archive_is_supported_image($file)) {
                continue;
            }

            $files[] = $file;
        }

        foreach ((array) ($items['folders'] ?? []) as $folder) {
            if (!is_array($folder)) {
                continue;
            }

            $folderPath = trim((string) ($folder['path'] ?? ''));
            if ($folderPath === '') {
                continue;
            }

            $files = array_merge($files, phinit_media_archive_collect_files($mediaService, $folderPath));
        }

        return $files;
    }
}

if (!function_exists('phinit_media_archive_is_supported_image')) {
    /**
     * @param array<string, mixed> $file
     */
    function phinit_media_archive_is_supported_image(array $file): bool
    {
        $mime = strtolower(trim((string) ($file['mime_type'] ?? '')));
        $extension = strtolower((string) pathinfo((string) ($file['path'] ?? ''), PATHINFO_EXTENSION));

        return in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/avif'], true)
            || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'avif'], true);
    }
}

if (!function_exists('phinit_media_archive_build_file_index')) {
    /**
     * @param array<int, array<string, mixed>> $files
     * @return array<string, array<string, mixed>>
     */
    function phinit_media_archive_build_file_index(array $files): array
    {
        $index = [];

        foreach ($files as $file) {
            if (!is_array($file)) {
                continue;
            }

            $path = trim(str_replace('\\', '/', (string) ($file['path'] ?? '')), '/');
            if ($path === '') {
                continue;
            }

            $index[$path] = $file;
        }

        return $index;
    }
}

if (!function_exists('phinit_media_archive_extract_image_sources')) {
    /**
     * @return array<int, string>
     */
    function phinit_media_archive_extract_image_sources(string $html): array
    {
        if (trim($html) === '' || stripos($html, '<img') === false) {
            return [];
        }

        if (!preg_match_all('/<img\b[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            return [];
        }

        $sources = array_map(static fn(string $src): string => trim(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8')), $matches[1]);
        $sources = array_values(array_unique(array_filter($sources, static fn(string $src): bool => $src !== '')));

        return $sources;
    }
}

if (!function_exists('phinit_media_archive_normalize_image_reference')) {
    function phinit_media_archive_normalize_image_reference(string $reference): string
    {
        $reference = trim(html_entity_decode($reference, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($reference === '') {
            return '';
        }

        $siteUrl = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        $uploadUrl = rtrim((string) (defined('UPLOAD_URL') ? UPLOAD_URL : ''), '/');

        if ($siteUrl !== '' && str_starts_with($reference, $siteUrl . '/media-file')) {
            $query = (string) parse_url($reference, PHP_URL_QUERY);
            parse_str($query, $params);
            return trim(str_replace('\\', '/', (string) ($params['path'] ?? '')), '/');
        }

        if ($uploadUrl !== '' && str_starts_with($reference, $uploadUrl . '/')) {
            $relative = ltrim(substr($reference, strlen($uploadUrl)), '/');
            return trim(implode('/', array_map('rawurldecode', explode('/', $relative))), '/');
        }

        if (preg_match('#^https?://#i', $reference) === 1) {
            $path = (string) parse_url($reference, PHP_URL_PATH);
            $uploadPath = (string) parse_url($uploadUrl, PHP_URL_PATH);
            if ($path !== '' && $uploadPath !== '' && str_starts_with($path, $uploadPath . '/')) {
                $relative = ltrim(substr($path, strlen($uploadPath)), '/');
                return trim(implode('/', array_map('rawurldecode', explode('/', $relative))), '/');
            }

            return '';
        }

        if (str_starts_with($reference, '/media-file')) {
            $query = (string) parse_url($reference, PHP_URL_QUERY);
            parse_str($query, $params);
            return trim(str_replace('\\', '/', (string) ($params['path'] ?? '')), '/');
        }

        if ($reference !== '' && !str_starts_with($reference, '/')) {
            return trim(str_replace('\\', '/', $reference), '/');
        }

        if ($reference !== '' && $uploadUrl !== '') {
            $uploadPath = (string) parse_url($uploadUrl, PHP_URL_PATH);
            if ($uploadPath !== '' && str_starts_with($reference, $uploadPath . '/')) {
                $relative = ltrim(substr($reference, strlen($uploadPath)), '/');
                return trim(implode('/', array_map('rawurldecode', explode('/', $relative))), '/');
            }
        }

        return '';
    }
}
