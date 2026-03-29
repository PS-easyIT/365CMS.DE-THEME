<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$activeSocial = isset($activeSocial) && is_array($activeSocial) ? $activeSocial : [];
$socialHeader = isset($socialHeader) ? (string) $socialHeader : 'Folge mir';
$czGet = isset($czGet) && $czGet instanceof Closure ? $czGet : null;
$siteUrl = defined('SITE_URL') ? SITE_URL : '';

if (empty($activeSocial) || $czGet === null) {
    return;
}
?>
<div class="toc">
    <div class="toc-title">🌐 <?php echo htmlspecialchars($socialHeader, ENT_QUOTES); ?></div>
    <ul class="toc-list social-sidebar-list" role="list">
        <?php foreach ($activeSocial as $s): ?>
        <?php $socialUrl = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $czGet('social', (string) ($s['key'] ?? ''), ''), $siteUrl, ['http', 'https']) : (string) $czGet('social', (string) ($s['key'] ?? ''), ''); ?>
        <?php if ($socialUrl === ''): continue; endif; ?>
        <li>
            <a href="<?php echo htmlspecialchars($socialUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer">
                <span class="social-icon"><?php echo htmlspecialchars((string) ($s['icon'] ?? ''), ENT_QUOTES); ?></span> <?php echo htmlspecialchars((string) ($s['label'] ?? ''), ENT_QUOTES); ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
