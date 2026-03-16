<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;

if (empty($_showInfoGrid)) {
    return;
}

$_c1Href = !empty($_c1LinkUrl) ? (str_starts_with((string) $_c1LinkUrl, 'http') ? (string) $_c1LinkUrl : $siteUrl . (string) $_c1LinkUrl) : '';
$_c2Href = !empty($_c2LinkUrl) ? (str_starts_with((string) $_c2LinkUrl, 'http') ? (string) $_c2LinkUrl : $siteUrl . (string) $_c2LinkUrl) : '';
$_c3Style = (string) ($_c3Style ?? '');
$_c3IsProjects = $_c3Style === 'repo' || $_c3Style === 'projects';
$_c3IsGold = (string) ($_c3Style ?? '') === 'gold';
$_c3Classes = 'info-card' . ($_c3IsProjects ? ' info-card--projects' : ($_c3IsGold ? ' info-card--gold' : ''));

$_resolveCardUrl = static function (string $url) use ($siteUrl): string {
    $url = trim($url);

    if ($url === '') {
        return '';
    }

    return str_starts_with($url, 'http') ? $url : $siteUrl . $url;
};

$_c3ProjectLinks = [];
foreach ([
    ['text' => (string) ($_c3LinkText ?? ''), 'url' => (string) ($_c3LinkUrl ?? '')],
    ['text' => (string) ($_c3LinkText2 ?? ''), 'url' => (string) ($_c3LinkUrl2 ?? '')],
    ['text' => (string) ($_c3LinkText3 ?? ''), 'url' => (string) ($_c3LinkUrl3 ?? '')],
] as $_c3ProjectLink) {
    $_label = trim((string) ($_c3ProjectLink['text'] ?? ''));
    $_url = $_resolveCardUrl((string) ($_c3ProjectLink['url'] ?? ''));

    if ($_label === '' || $_url === '') {
        continue;
    }

    $_c3ProjectLinks[] = [
        'label' => $_label,
        'url' => $_url,
        'external' => str_starts_with((string) ($_c3ProjectLink['url'] ?? ''), 'http'),
    ];
}
?>
<section class="content-section home-section home-section--info">
    <div class="section-header">
        <span class="section-label section-label--dark">Themenbereiche</span>
    </div>
    <div class="info-grid<?php echo !empty($_showCard3) ? ' info-grid--cols-3' : ''; ?>">
        <div class="info-card<?php echo (string) ($_c1Style ?? '') === 'gold' ? ' info-card--gold' : ''; ?>">
            <h3><?php echo htmlspecialchars((string) ($_c1Title ?? ''), ENT_QUOTES); ?></h3>
            <?php if (!empty($_c1Text)): ?>
            <p><?php echo htmlspecialchars((string) $_c1Text, ENT_QUOTES); ?></p>
            <?php endif; ?>
            <?php if ($_c1Href !== '' && !empty($_c1LinkText)): ?>
            <a href="<?php echo htmlspecialchars($_c1Href, ENT_QUOTES); ?>" class="btn btn-outline btn-sm info-card-cta"><?php echo htmlspecialchars((string) $_c1LinkText, ENT_QUOTES); ?></a>
            <?php endif; ?>
        </div>
        <div class="info-card<?php echo (string) ($_c2Style ?? '') === 'gold' ? ' info-card--gold' : ''; ?>">
            <h3><?php echo htmlspecialchars((string) ($_c2Title ?? ''), ENT_QUOTES); ?></h3>
            <?php if (!empty($_c2Text)): ?>
            <p><?php echo htmlspecialchars((string) $_c2Text, ENT_QUOTES); ?></p>
            <?php endif; ?>
            <?php if ($_c2Href !== '' && !empty($_c2LinkText)): ?>
            <a href="<?php echo htmlspecialchars($_c2Href, ENT_QUOTES); ?>" class="btn btn-outline btn-sm info-card-cta"><?php echo htmlspecialchars((string) $_c2LinkText, ENT_QUOTES); ?></a>
            <?php endif; ?>
        </div>
        <?php if (!empty($_showCard3)): ?>
        <div class="<?php echo htmlspecialchars($_c3Classes, ENT_QUOTES); ?>">
            <?php if ($_c3IsProjects): ?>
            <div class="info-card-projects-head">
                <div class="info-card-projects-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7.5h18"></path><path d="M7 3.5h10a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z"></path><path d="M8 12h3"></path><path d="M13 12h3"></path><path d="M8 16h8"></path></svg>
                </div>
                <h3><?php echo htmlspecialchars((string) ($_c3Title ?? ''), ENT_QUOTES); ?></h3>
            </div>
            <?php else: ?>
            <h3><?php echo htmlspecialchars((string) ($_c3Title ?? ''), ENT_QUOTES); ?></h3>
            <?php endif; ?>
            <?php if (!empty($_c3Text)): ?>
            <p><?php echo htmlspecialchars((string) $_c3Text, ENT_QUOTES); ?></p>
            <?php endif; ?>
            <?php if ($_c3IsProjects && $_c3ProjectLinks !== []): ?>
            <div class="info-card-projects-footer">
                <?php if (!empty($_c3Badge)): ?>
                <span class="info-card-projects-badge"><?php echo htmlspecialchars((string) $_c3Badge, ENT_QUOTES); ?></span>
                <?php endif; ?>
                <div class="info-card-project-links">
                    <?php foreach ($_c3ProjectLinks as $_c3ProjectLink): ?>
                    <a href="<?php echo htmlspecialchars((string) $_c3ProjectLink['url'], ENT_QUOTES); ?>"
                       class="info-card-project-link"
                       <?php echo !empty($_c3ProjectLink['external']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo htmlspecialchars((string) $_c3ProjectLink['label'], ENT_QUOTES); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <?php if (!empty($_c3Badge)): ?>
            <span class="<?php echo $_c3IsProjects ? 'info-card-projects-badge' : 'repo-badge repo-badge--inline'; ?>"><?php echo htmlspecialchars((string) $_c3Badge, ENT_QUOTES); ?></span>
            <?php endif; ?>
            <?php if (!$_c3IsProjects && !empty($_c3LinkText) && !empty($_c3LinkUrl)): ?>
            <a href="<?php echo htmlspecialchars($_resolveCardUrl((string) $_c3LinkUrl), ENT_QUOTES); ?>"
               class="btn btn-sm info-card-cta btn-outline"
               <?php echo str_starts_with((string) $_c3LinkUrl, 'http') ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <?php echo htmlspecialchars((string) $_c3LinkText, ENT_QUOTES); ?>
            </a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>