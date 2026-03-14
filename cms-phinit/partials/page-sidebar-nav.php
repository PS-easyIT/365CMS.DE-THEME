<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$_pgNavItems = isset($_pgNavItems) && is_array($_pgNavItems) ? $_pgNavItems : [];
?>
<div class="sidebar-widget page-sidebar-nav">
    <h4 class="page-sidebar-nav__title">Navigation</h4>
    <nav aria-label="Seitennavigation">
        <?php foreach ($_pgNavItems as $_ni): ?>
        <a href="<?php echo htmlspecialchars((string) ($_ni['url'] ?? '#'), ENT_QUOTES); ?>"
           class="page-sidebar-nav__link"><?php echo htmlspecialchars((string) ($_ni['label'] ?? ''), ENT_QUOTES); ?></a>
        <?php endforeach; ?>
    </nav>
</div>
