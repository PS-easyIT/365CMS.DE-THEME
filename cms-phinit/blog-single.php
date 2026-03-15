<?php
/**
 * Blog-Einzelartikel – CMS Phinit Theme
 *
 * Der Router registriert die Route /blog/:slug und ruft
 * ThemeManager::render('blog-single', ['post' => $post]) auf.
 * Der bereits geladene $post (stdClass) wird über den validierten
 * Render-Scope des ThemeManagers an das Template übergeben und in
 * post.php zu einem Array normalisiert.
 *
 * Diese Datei dient als Einstiegspunkt und delegiert an post.php,
 * welches beide Aufrufarten (direkt & per Router) verarbeiten kann.
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// $post (stdClass) wird von ThemeManager::render() im lokalen Scope bereitgestellt.
// post.php normalisiert es zu einem Array und übernimmt das komplette Rendering.
include __DIR__ . '/post.php';
