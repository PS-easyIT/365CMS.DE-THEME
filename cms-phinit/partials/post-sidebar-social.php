<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$activeSocial = isset($activeSocial) && is_array($activeSocial) ? $activeSocial : [];
$socialHeader = isset($socialHeader) ? (string) $socialHeader : 'Folge mir';
$czGet = isset($czGet) && $czGet instanceof Closure ? $czGet : null;

if (empty($activeSocial) || $czGet === null) {
    return;
}
?>
<div class="toc">
    <div class="toc-title">🌐 <?php echo htmlspecialchars($socialHeader, ENT_QUOTES); ?></div>
    <ul class="toc-list social-sidebar-list" role="list">
        <?php foreach ($activeSocial as $s): ?>
        <li>
            <a href="<?php echo htmlspecialchars((string) $czGet('social', (string) ($s['key'] ?? ''), ''), ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer">
                <span class="social-icon"><?php echo htmlspecialchars((string) ($s['icon'] ?? ''), ENT_QUOTES); ?></span> <?php echo htmlspecialchars((string) ($s['label'] ?? ''), ENT_QUOTES); ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
