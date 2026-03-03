<?php
/**
 * Blog-Einzelartikel – CMS Phinit Theme
 *
 * Der Router registriert die Route /blog/:slug und ruft
 * ThemeManager::render('blog-single', ['post' => $post]) auf.
 * Der bereits geladene $post (stdClass) wird per extract() zum Template
 * übergeben und in post.php zu einem Array normalisiert.
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

// $post (stdClass) wurde von ThemeManager::render() via extract() gesetzt.
// post.php normalisiert es zu einem Array und übernimmt das komplette Rendering.
include __DIR__ . '/post.php';
