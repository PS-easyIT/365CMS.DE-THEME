<?php
/**
 * Member Plugin Section – CMS Phinit Theme
 *
 * Rendert Plugin-Bereiche innerhalb des cms-phinit Member-Layouts,
 * statt den generischen 365CMS-Wrapper zu verwenden.
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'member/includes/bootstrap.php';

$currentUser = $controller->getCurrentUser();
$siteUrl = SITE_URL;
$activePage = (string) ($section['slug'] ?? '');
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
$pluginLabel = trim((string) ($section['label'] ?? 'Plugin-Bereich'));
$pluginIcon = trim((string) ($section['icon'] ?? '🔌'));
$pluginName = trim((string) ($section['plugin'] ?? 'plugin'));
$hidePluginPageTitle = in_array((string) ($section['slug'] ?? ''), ['m365-license', 'm365-license-special'], true);

include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main" id="main-content">
        <?php if (!$hidePluginPageTitle): ?>
        <section class="member-page-title" data-anim>
            <h1><?php echo htmlspecialchars(trim(($pluginIcon !== '' ? $pluginIcon . ' ' : '') . $pluginLabel), ENT_QUOTES); ?></h1>
            <p>Plugin-Bereich im geschützten CMS-PHINIT Memberdashboard.</p>
        </section>
        <?php endif; ?>

        <div class="member-card member-card--spaced" data-anim data-anim-delay="1">
            <div class="member-card-header">
                <h3><?php echo htmlspecialchars($pluginLabel, ENT_QUOTES); ?></h3>
                <span class="member-card-link"><?php echo htmlspecialchars($pluginName, ENT_QUOTES); ?></span>
            </div>

            <?php \CMS\Hooks::doAction('member_plugin_section_head', $section, $user, $params ?? []); ?>
            <?php
            $requestMethod = phinit_request_method();
            $pluginCallback = $requestMethod === 'POST' && is_callable($section['post_callback'] ?? null)
                ? $section['post_callback']
                : ($section['render_callback'] ?? null);

            if (is_callable($pluginCallback)) {
                call_user_func($pluginCallback, $user, $params ?? []);
            }
            ?>
        </div>
    </div>
</div>

<?php include $themeDir . 'footer.php'; ?>