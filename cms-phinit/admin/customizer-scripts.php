<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$localFontCssMap = [];
$preferLocalFonts = false;

try {
    $db = \CMS\Database::instance();
    $row = $db->get_row(
        "SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = 'privacy_use_local_fonts' LIMIT 1"
    );
    $preferLocalFonts = in_array(strtolower(trim((string)($row->option_value ?? ''))), ['1', 'true', 'yes', 'on'], true);

    $aliasToThemeSlug = [
        'source-sans-3' => 'source-sans',
        'source-code-pro' => 'source-code',
        'exo-2' => 'exo2',
    ];

    $fonts = $db->get_results(
        "SELECT slug, css_path FROM {$db->getPrefix()}custom_fonts WHERE css_path IS NOT NULL AND css_path != ''"
    ) ?: [];

    foreach ($fonts as $font) {
        $slug = strtolower(trim((string)($font->slug ?? '')));
        $cssPath = trim((string)($font->css_path ?? ''));
        if ($slug === '' || $cssPath === '') {
            continue;
        }

        $cssFile = ABSPATH . ltrim($cssPath, '/');
        if (!is_file($cssFile)) {
            continue;
        }

        $url = rtrim(SITE_URL, '/') . '/' . ltrim($cssPath, '/');
        $localFontCssMap[$slug] = $url;

        if (isset($aliasToThemeSlug[$slug])) {
            $localFontCssMap[$aliasToThemeSlug[$slug]] = $url;
        }
    }
} catch (\Throwable $e) {
    $localFontCssMap = [];
}

$config = [
    'preferLocalFonts' => $preferLocalFonts,
    'localFontCssMap' => $localFontCssMap,
    'siteOrigin' => rtrim(SITE_URL, '/') . '/',
];

$customizerAdminScriptUrl = '';
if (defined('CMS_PHINIT_THEME_DIR') && defined('CMS_PHINIT_THEME_URL')) {
    $customizerAdminScriptFile = CMS_PHINIT_THEME_DIR . 'assets/js/customizer-admin.js';
    if (is_file($customizerAdminScriptFile)) {
        $customizerAdminScriptUrl = CMS_PHINIT_THEME_URL . 'assets/js/customizer-admin.js?v=' . rawurlencode((string) filemtime($customizerAdminScriptFile));
    }
}
?>
<script type="application/json" id="phinit-customizer-config"><?php
echo json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?></script>
<?php if ($customizerAdminScriptUrl !== ''): ?>
<script src="<?php echo htmlspecialchars($customizerAdminScriptUrl, ENT_QUOTES, 'UTF-8'); ?>" defer></script>
<?php endif; ?>
