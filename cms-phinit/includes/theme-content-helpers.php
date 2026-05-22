<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('phinit_sanitize_renderable_content')) {
    /**
     * Sanitisiert bereits gerendertes HTML mit dem zentralen Core-Purifier.
     * Heading-IDs und Performance-Attribute werden bewusst erst danach ergänzt.
     */
    function phinit_sanitize_renderable_content(string $html, string $profile = 'default'): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        try {
            if (class_exists('\\CMS\\Services\\PurifierService')) {
                return (string) \CMS\Services\PurifierService::getInstance()->purify($html, $profile);
            }
        } catch (\Throwable) {
            // Fällt bewusst auf die WordPress-Kompat-Sanitizer zurück.
        }

        if (function_exists('wp_kses_post')) {
            return (string) wp_kses_post($html);
        }

        $sanitized = strip_tags(
            $html,
            '<p><a><strong><b><em><i><u><ul><ol><li><br><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img><table><thead><tbody><tfoot><tr><th><td><hr><span><div><figure><figcaption><dl><dt><dd><sub><sup><abbr><mark><del><ins><details><summary><video><source><audio>'
        );

        $sanitized = preg_replace('/\s+on[a-z0-9_-]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $sanitized) ?? $sanitized;
        $sanitized = preg_replace('/\s+(href|src|xlink:href)\s*=\s*(["\'])\s*(?:javascript|data:text\/html)\s*:[^"\']*\2/i', ' $1="#"', $sanitized) ?? $sanitized;

        return $sanitized;
    }
}

if (!function_exists('phinit_prepare_renderable_content')) {
    /**
     * Bereitet gespeicherten Seiten-/Beitragsinhalt für das Frontend auf.
     */
    function phinit_prepare_renderable_content(string $content, string $type = 'page', int $id = 0): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }

        try {
            return phinit_enhance_content_images((string) \CMS\Router::instance()->prepareRenderableContent($content, $type, $id));
        } catch (\Throwable) {
            try {
                $rendered = \CMS\Services\EditorService::getInstance()->renderContent($content);

                if (class_exists('\\CMS\\Services\\SiteTableService')) {
                    $rendered = \CMS\Services\SiteTableService::getInstance()->replaceShortcodes($rendered);
                }

                return phinit_enhance_content_images($rendered);
            } catch (\Throwable) {
                return phinit_enhance_content_images($content);
            }
        }
    }
}

if (!function_exists('phinit_get_html_attribute')) {
    /**
     * Liest ein HTML-Attribut aus einem Tag.
     */
    function phinit_get_html_attribute(string $tag, string $attribute): string
    {
        $attribute = preg_quote($attribute, '/');

        if (preg_match('/\s' . $attribute . '\s*=\s*(["\'])(.*?)\1/isu', $tag, $match) !== 1) {
            return '';
        }

        return html_entity_decode((string) ($match[2] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('phinit_set_html_attribute')) {
    /**
     * Setzt oder ersetzt ein HTML-Attribut in einem Tag.
     */
    function phinit_set_html_attribute(string $tag, string $attribute, string $value): string
    {
        $attribute = strtolower(trim($attribute));
        if ($tag === '' || $attribute === '' || preg_match('/^[a-z][a-z0-9:-]*$/i', $attribute) !== 1) {
            return $tag;
        }

        $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $pattern = '/\s' . preg_quote($attribute, '/') . '\s*=\s*(["\'])(.*?)\1/isu';
        if (preg_match($pattern, $tag) === 1) {
            $updated = preg_replace_callback(
                $pattern,
                static fn(): string => ' ' . $attribute . '="' . $escapedValue . '"',
                $tag,
                1
            );

            return is_string($updated) ? $updated : $tag;
        }

        $closing = str_ends_with($tag, '/>') ? '/>' : '>';
        $baseTag = substr($tag, 0, -strlen($closing));

        return rtrim($baseTag) . ' ' . $attribute . '="' . $escapedValue . '"' . $closing;
    }
}

if (!function_exists('phinit_append_html_class')) {
    /**
     * Ergänzt eine CSS-Klasse an einem HTML-Tag, ohne bestehende Klassen zu verlieren.
     */
    function phinit_append_html_class(string $tag, string $className): string
    {
        $className = trim($className);
        if ($tag === '' || $className === '') {
            return $tag;
        }

        $currentClass = phinit_get_html_attribute($tag, 'class');
        $classes = preg_split('/\s+/', trim($currentClass)) ?: [];
        $classes = array_values(array_filter(array_map('trim', $classes), static fn(string $class): bool => $class !== ''));

        if (!in_array($className, $classes, true)) {
            $classes[] = $className;
        }

        return phinit_set_html_attribute($tag, 'class', implode(' ', $classes));
    }
}

if (!function_exists('phinit_normalize_content_image_url')) {
    /**
     * Normalisiert Content-Bild-URLs bevorzugt auf direkte öffentliche Upload-URLs.
     */
    function phinit_normalize_content_image_url(string $url): string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '' || preg_match('#^(?:data|blob|cid):#i', $url) === 1) {
            return $url;
        }

        if (function_exists('phinit_normalize_public_media_url')) {
            $normalized = phinit_normalize_public_media_url($url, true, defined('SITE_URL') ? (string) SITE_URL : null);
            if ($normalized !== '') {
                return $normalized;
            }
        }

        return function_exists('phinit_safe_public_media_url')
            ? phinit_safe_public_media_url($url, defined('SITE_URL') ? (string) SITE_URL : null)
            : $url;
    }
}

if (!function_exists('phinit_normalize_content_image_srcset')) {
    /**
     * Normalisiert alle URL-Kandidaten eines srcset-Attributs.
     */
    function phinit_normalize_content_image_srcset(string $srcset): string
    {
        $candidates = array_filter(array_map('trim', explode(',', $srcset)), static fn(string $candidate): bool => $candidate !== '');
        if ($candidates === []) {
            return '';
        }

        $normalizedCandidates = [];
        foreach ($candidates as $candidate) {
            if (preg_match('/^(\S+)(\s+.+)?$/u', $candidate, $match) !== 1) {
                continue;
            }

            $url = phinit_normalize_content_image_url((string) ($match[1] ?? ''));
            if ($url === '') {
                continue;
            }

            $descriptor = trim((string) ($match[2] ?? ''));
            $normalizedCandidates[] = $descriptor !== '' ? $url . ' ' . $descriptor : $url;
        }

        return implode(', ', $normalizedCandidates);
    }
}

if (!function_exists('phinit_enhance_content_images')) {
    /**
     * Ergänzt Inhaltsbilder standardmäßig um Loading-/Priority-Attribute.
     * Das erste Inhaltsbild wird bevorzugt behandelt, um Above-the-fold-/LCP-Bilder
     * nicht versehentlich zu verlangsamen.
     */
    function phinit_enhance_content_images(string $html): string
    {
        if (trim($html) === '' || stripos($html, '<img') === false) {
            return $html;
        }

        $imageIndex = 0;

        $enhanced = preg_replace_callback(
            '/<img\b[^>]*>/i',
            static function (array $matches) use (&$imageIndex): string {
                $tag = (string) ($matches[0] ?? '');
                if ($tag === '') {
                    return $tag;
                }

                $imageIndex++;
                $isFirstImage = $imageIndex === 1;
                $lazyLoadingEnabled = phinit_is_image_lazy_loading_enabled();

                $src = phinit_get_html_attribute($tag, 'src');
                if ($src !== '') {
                    $normalizedSrc = phinit_normalize_content_image_url($src);
                    if ($normalizedSrc !== '') {
                        $tag = phinit_set_html_attribute($tag, 'src', $normalizedSrc);
                    }
                }

                $srcset = phinit_get_html_attribute($tag, 'srcset');
                if ($srcset !== '') {
                    $normalizedSrcset = phinit_normalize_content_image_srcset($srcset);
                    if ($normalizedSrcset !== '') {
                        $tag = phinit_set_html_attribute($tag, 'srcset', $normalizedSrcset);
                    }
                }

                if (preg_match('/\ssizes\s*=\s*["\'][^"\']*["\']/i', $tag) !== 1) {
                    $tag = phinit_set_html_attribute($tag, 'sizes', '(max-width: 768px) calc(100vw - 40px), min(100vw, 860px)');
                }

                $tag = phinit_append_html_class($tag, 'phinit-content-image');

                $closing = str_ends_with($tag, '/>') ? '/>' : '>';
                $baseTag = substr($tag, 0, -strlen($closing));
                $attrs = [];

                if (preg_match('/\sloading\s*=\s*["\'][^"\']*["\']/i', $tag) !== 1) {
                    if ($isFirstImage) {
                        $attrs[] = 'loading="eager"';
                    } elseif ($lazyLoadingEnabled) {
                        $attrs[] = 'loading="lazy"';
                    }
                }

                if ($isFirstImage && preg_match('/\sfetchpriority\s*=\s*["\'][^"\']*["\']/i', $tag) !== 1) {
                    $attrs[] = 'fetchpriority="high"';
                }

                if (preg_match('/\sdecoding\s*=\s*["\'][^"\']*["\']/i', $tag) !== 1) {
                    $attrs[] = 'decoding="async"';
                }

                $hasWidth = preg_match('/\swidth\s*=\s*["\'][^"\']*["\']/i', $tag) === 1;
                $hasHeight = preg_match('/\sheight\s*=\s*["\'][^"\']*["\']/i', $tag) === 1;

                if ((!$hasWidth || !$hasHeight) && preg_match('/\ssrc\s*=\s*["\']([^"\']+)["\']/i', $tag, $srcMatch) === 1) {
                    $dimensions = phinit_get_image_dimensions((string) ($srcMatch[1] ?? ''));
                    if (is_array($dimensions)) {
                        if (!$hasWidth && !empty($dimensions['width'])) {
                            $attrs[] = 'width="' . max(1, (int) $dimensions['width']) . '"';
                        }

                        if (!$hasHeight && !empty($dimensions['height'])) {
                            $attrs[] = 'height="' . max(1, (int) $dimensions['height']) . '"';
                        }
                    }
                }

                if ($attrs === []) {
                    return $tag;
                }

                return rtrim($baseTag) . ' ' . implode(' ', $attrs) . $closing;
            },
            $html
        );

        return is_string($enhanced) ? $enhanced : $html;
    }
}

if (!function_exists('phinit_slugify_heading')) {
    /**
     * Erzeugt einen stabilen Slug aus einem Heading-Text.
     */
    function phinit_slugify_heading(string $text): string
    {
        $displayText = phinit_display_text($text);
        $slug = function_exists('mb_strtolower')
            ? mb_strtolower($displayText, 'UTF-8')
            : strtolower($displayText);
        $slug = preg_replace('/[äÄ]/u', 'ae', $slug);
        $slug = preg_replace('/[öÖ]/u', 'oe', $slug);
        $slug = preg_replace('/[üÜ]/u', 'ue', $slug);
        $slug = preg_replace('/ß/u', 'ss', $slug);
        $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug ?? '');
        $slug = trim((string)$slug, '-');

        return $slug !== '' ? $slug : 'heading';
    }
}

if (!function_exists('phinit_with_heading_ids')) {
    /**
     * Ergänzt fehlende IDs an Überschriften und liefert optional die TOC-Daten.
     *
     * @param array<int,int> $levels
     * @return array{html:string,toc:array<int,array{level:int,id:string,text:string}>}
     */
    function phinit_with_heading_ids(string $html, array $levels = [2, 3, 4, 5, 6]): array
    {
        if (trim($html) === '') {
            return ['html' => '', 'toc' => []];
        }

        $levelPattern = implode('', array_map(static fn(int $level): string => (string)$level, $levels));
        if ($levelPattern === '') {
            return ['html' => $html, 'toc' => []];
        }

        $usedIds = [];
        $toc = [];

        $makeUniqueId = static function (string $preferred, string $text) use (&$usedIds): string {
            $baseId = trim($preferred);
            if ($baseId === '' || preg_match('/^[A-Za-z][A-Za-z0-9_.:-]*$/', $baseId) !== 1) {
                $baseId = phinit_slugify_heading($text);
            }

            $id = $baseId !== '' ? $baseId : 'heading';
            $suffix = 2;
            while (in_array($id, $usedIds, true)) {
                $id = $baseId . '-' . $suffix;
                $suffix++;
            }

            return $id;
        };

        $htmlWithIds = preg_replace_callback(
            '/<h([' . preg_quote($levelPattern, '/') . '])([^>]*)>(.*?)<\/h\1>/isu',
            static function (array $matches) use (&$usedIds, &$toc, $makeUniqueId): string {
                $level = (int)($matches[1] ?? 2);
                $attrs = (string)($matches[2] ?? '');
                $inner = (string)($matches[3] ?? '');
                $text  = phinit_display_text(strip_tags($inner));

                if ($text === '') {
                    return $matches[0];
                }

                $preferredId = '';
                if (preg_match('/\bid\s*=\s*(["\'])(.*?)\1/i', $attrs, $idMatch) === 1) {
                    $preferredId = html_entity_decode(trim((string)($idMatch[2] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }

                $id = $makeUniqueId($preferredId, $text);
                $openingTag = phinit_set_html_attribute('<h' . $level . $attrs . '>', 'id', $id);

                $usedIds[] = $id;
                $toc[] = ['level' => $level, 'id' => $id, 'text' => $text];

                return $openingTag . $inner . '</h' . $level . '>';
            },
            $html
        );

        return [
            'html' => is_string($htmlWithIds) ? $htmlWithIds : $html,
            'toc'  => $toc,
        ];
    }
}

if (!function_exists('phinit_build_toc_tree')) {
    /**
     * Wandelt flache TOC-Einträge in eine hierarchische Baumstruktur um.
     *
     * @param array<int,array{level?:int,id?:string,text?:string}> $items
     * @return array<int,array{level:int,id:string,text:string,children:array<int,array{level:int,id:string,text:string,children:array}>}>
     */
    function phinit_build_toc_tree(array $items): array
    {
        $tree = [];
        $stack = [];

        foreach ($items as $item) {
            $level = max(1, (int) ($item['level'] ?? 2));
            $id = trim((string) ($item['id'] ?? ''));
            $text = trim((string) ($item['text'] ?? ''));

            if ($id === '' || $text === '') {
                continue;
            }

            $node = [
                'level' => $level,
                'id' => $id,
                'text' => $text,
                'children' => [],
            ];

            while ($stack !== [] && $level <= (int) ($stack[count($stack) - 1]['level'] ?? 0)) {
                array_pop($stack);
            }

            if ($stack === []) {
                $tree[] = $node;
                $nodeIndex = array_key_last($tree);
                if ($nodeIndex !== null) {
                    $stack[] = [
                        'level' => $level,
                        'node' => &$tree[$nodeIndex],
                    ];
                }
                continue;
            }

            $parentIndex = count($stack) - 1;
            $parentNode = &$stack[$parentIndex]['node'];
            $parentNode['children'][] = $node;
            $nodeIndex = array_key_last($parentNode['children']);

            if ($nodeIndex !== null) {
                $stack[] = [
                    'level' => $level,
                    'node' => &$parentNode['children'][$nodeIndex],
                ];
            }
        }

        return $tree;
    }
}

if (!function_exists('phinit_has_visible_content')) {
    /**
     * Prüft, ob HTML-Inhalt sichtbar renderbaren Content enthält.
     */
    function phinit_has_visible_content(string $html): bool
    {
        $plainText = trim((string)(preg_replace('/\s+/u', ' ', phinit_display_text(strip_tags($html))) ?? ''));
        if ($plainText !== '') {
            return true;
        }

        return preg_match('/<(img|iframe|video|audio|table|ul|ol|blockquote|pre|figure|details|hr)\b/i', $html) === 1;
    }
}

if (!function_exists('phinit_render_sanitized_content')) {
    /**
     * Rendert HTML-Inhalte ausschließlich über den zentralen Purifier-Vertrag.
     */
    function phinit_render_sanitized_content(string $html, string $profile = 'default'): void
    {
        if (trim($html) === '') {
            return;
        }

        $safeHtml = phinit_sanitize_renderable_content($html, $profile);
        if ($safeHtml === '') {
            return;
        }

        $headingData = phinit_with_heading_ids($safeHtml, [2, 3, 4, 5, 6]);
        echo $headingData['html'];
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
            } catch (\Throwable) {
            }
            if ($wpm <= 0) {
                $wpm = 220;
            }
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

        $extractFromMalformedEditorJs = static function (string $raw): string {
            $parts = [];

            if (preg_match_all('/"text"\s*:\s*"((?:\\.|[^"\\])*)"/u', $raw, $matches)) {
                foreach ($matches[1] as $value) {
                    $decoded = json_decode('"' . $value . '"');
                    if (is_string($decoded) && trim($decoded) !== '') {
                        $parts[] = $decoded;
                    }
                }
            }

            if (preg_match_all('/"caption"\s*:\s*"((?:\\.|[^"\\])*)"/u', $raw, $matches)) {
                foreach ($matches[1] as $value) {
                    $decoded = json_decode('"' . $value . '"');
                    if (is_string($decoded) && trim($decoded) !== '') {
                        $parts[] = $decoded;
                    }
                }
            }

            if (preg_match_all('/"items"\s*:\s*\[(.*?)\]/us', $raw, $itemGroups)) {
                foreach ($itemGroups[1] as $group) {
                    if (preg_match_all('/"((?:\\.|[^"\\])*)"/u', $group, $itemMatches)) {
                        foreach ($itemMatches[1] as $value) {
                            $decoded = json_decode('"' . $value . '"');
                            if (is_string($decoded) && trim($decoded) !== '') {
                                $parts[] = $decoded;
                            }
                        }
                    }
                }
            }

            $text = trim(html_entity_decode(strip_tags(implode(' ', $parts)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            return preg_replace('/\s+/u', ' ', $text) ?? '';
        };

        $decoded = json_decode($content, true);
        if (is_array($decoded) && isset($decoded['blocks']) && is_array($decoded['blocks'])) {
            $html = '';
            if (class_exists('\\CMS\\Services\\EditorJsRenderer')) {
                try {
                    $html = \CMS\Services\EditorJsRenderer::getInstance()->render($decoded);
                } catch (\Throwable) {
                }
            }
            if ($html !== '') {
                $content = $html;
            } else {
                $parts = [];
                foreach ($decoded['blocks'] as $block) {
                    if (!is_array($block)) {
                        continue;
                    }
                    $data = $block['data'] ?? null;
                    if (!is_array($data)) {
                        continue;
                    }
                    foreach (['text', 'caption', 'message', 'title'] as $key) {
                        if (!empty($data[$key]) && is_string($data[$key])) {
                            $parts[] = $data[$key];
                        }
                    }
                    if (!empty($data['items']) && is_array($data['items'])) {
                        foreach ($data['items'] as $item) {
                            if (is_string($item) && trim($item) !== '') {
                                $parts[] = $item;
                            }
                        }
                    }
                }
                $content = implode(' ', $parts);
            }
        } elseif (str_contains($content, '"blocks"') && (str_starts_with($content, '{') || str_starts_with($content, '['))) {
            $recovered = $extractFromMalformedEditorJs($content);
            if ($recovered !== '') {
                $content = $recovered;
            }
        }

        $text = trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        return preg_replace('/\s+/u', ' ', $text) ?? '';
    }
}
