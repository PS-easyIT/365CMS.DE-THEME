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
