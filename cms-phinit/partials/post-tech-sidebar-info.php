<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$techOs = isset($techOs) ? (string) $techOs : '';
$techVersion = isset($techVersion) ? (string) $techVersion : '';
$techTested = isset($techTested) ? (string) $techTested : '';

if ($techOs === '' && $techVersion === '') {
    return;
}
?>
<div class="toc tech-sidebar-info">
    <div class="toc-title">🖥️ Umgebung</div>
    <ul class="toc-list toc-list--plain">
        <?php if ($techOs !== ''): ?><li><strong>OS:</strong> <?php echo htmlspecialchars($techOs, ENT_QUOTES); ?></li><?php endif; ?>
        <?php if ($techVersion !== ''): ?><li><strong>Version:</strong> <code class="inline-code"><?php echo htmlspecialchars($techVersion, ENT_QUOTES); ?></code></li><?php endif; ?>
        <?php if ($techTested !== ''): ?><li><strong>Getestet:</strong> <?php echo htmlspecialchars(date('M Y', strtotime($techTested)), ENT_QUOTES); ?></li><?php endif; ?>
    </ul>
</div>
