<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$page = isset($page) && is_array($page) ? $page : [];
$_pg_showHero = isset($_pg_showHero) ? (bool) $_pg_showHero : true;
$favoriteControl = isset($favoriteControl) && is_array($favoriteControl) ? $favoriteControl : [];
$hasHeroImage = !empty($page['featured_image']) && $_pg_showHero;
?>
<div class="page-header-block<?php echo $hasHeroImage ? ' page-header-block--with-image' : ''; ?>" data-anim>
    <?php echo phinit_render_favorite_button($favoriteControl); ?>
    <?php if ($hasHeroImage): ?>
    <img class="page-hero-img"
         src="<?php echo htmlspecialchars((string) $page['featured_image'], ENT_QUOTES); ?>"
         alt="<?php echo htmlspecialchars((string) ($page['title'] ?? ''), ENT_QUOTES); ?>"
         <?php echo phinit_image_loading_attributes(true); ?>>
    <?php endif; ?>
    <div class="page-header-body">
        <h1><?php echo htmlspecialchars((string) ($page['title'] ?? ''), ENT_QUOTES); ?></h1>
    </div>
</div>
