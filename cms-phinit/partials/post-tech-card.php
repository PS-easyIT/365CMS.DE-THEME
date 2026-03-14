<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$showTechCard = isset($showTechCard) ? (bool) $showTechCard : true;
$hasTechData = isset($hasTechData) ? (bool) $hasTechData : false;
$techCardHeader = isset($techCardHeader) ? (string) $techCardHeader : 'Technische Details';
$diffInfo = isset($diffInfo) && is_array($diffInfo) ? $diffInfo : null;
$techOs = isset($techOs) ? (string) $techOs : '';
$techVersion = isset($techVersion) ? (string) $techVersion : '';
$techTested = isset($techTested) ? (string) $techTested : '';
$readTime = isset($readTime) ? (int) $readTime : 0;
$techPrereqs = isset($techPrereqs) && is_array($techPrereqs) ? $techPrereqs : [];

if (!$showTechCard || !$hasTechData) {
    return;
}
?>
<div class="tech-card" data-anim data-anim-delay="1" role="complementary" aria-label="Technische Informationen">
    <div class="tech-card__header">
        <span class="tech-card__icon" aria-hidden="true">⚙️</span>
        <span class="tech-card__title"><?php echo htmlspecialchars($techCardHeader, ENT_QUOTES); ?></span>
        <?php if ($diffInfo): ?>
        <span class="badge tech-card__diff <?php echo htmlspecialchars((string) ($diffInfo['class'] ?? ''), ENT_QUOTES); ?>">
            <?php echo htmlspecialchars((string) ($diffInfo['label'] ?? ''), ENT_QUOTES); ?>
        </span>
        <?php endif; ?>
    </div>
    <dl class="tech-card__grid">
        <?php if ($techOs !== ''): ?>
        <div class="tech-card__item">
            <dt>Betriebssystem</dt>
            <dd><?php echo htmlspecialchars($techOs, ENT_QUOTES); ?></dd>
        </div>
        <?php endif; ?>
        <?php if ($techVersion !== ''): ?>
        <div class="tech-card__item">
            <dt>Version</dt>
            <dd><code class="inline-code"><?php echo htmlspecialchars($techVersion, ENT_QUOTES); ?></code></dd>
        </div>
        <?php endif; ?>
        <?php if ($techTested !== ''): ?>
        <div class="tech-card__item">
            <dt>Zuletzt getestet</dt>
            <dd><?php echo htmlspecialchars(date('F Y', strtotime($techTested)), ENT_QUOTES); ?></dd>
        </div>
        <?php endif; ?>
        <?php if ($readTime > 0): ?>
        <div class="tech-card__item">
            <dt>Lesezeit</dt>
            <dd><?php echo $readTime; ?> Min.</dd>
        </div>
        <?php endif; ?>
    </dl>
    <?php if (!empty($techPrereqs)): ?>
    <div class="tech-card__prereqs">
        <strong>⚠️ Voraussetzungen:</strong>
        <ul>
            <?php foreach ($techPrereqs as $prereq): ?>
            <li><?php echo htmlspecialchars((string) $prereq, ENT_QUOTES); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
