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
$_c3IsRepo = (string) ($_c3Style ?? '') === 'repo';
$_c3IsGold = (string) ($_c3Style ?? '') === 'gold';
$_c3Classes = 'info-card' . ($_c3IsRepo ? ' info-card--repo' : ($_c3IsGold ? ' info-card--gold' : ''));
$_c3FullUrl = str_starts_with((string) ($_c3LinkUrl ?? ''), 'http') ? (string) $_c3LinkUrl : $siteUrl . (string) ($_c3LinkUrl ?? '');
$_c3IsExternal = str_starts_with((string) ($_c3LinkUrl ?? ''), 'http');
?>
<section class="content-section home-section home-section--info" data-anim data-anim-delay="1">
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
            <?php if ($_c3IsRepo): ?>
            <div class="info-card-repo-icon" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="22" height="22" fill="currentColor"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
            </div>
            <?php endif; ?>
            <h3><?php echo htmlspecialchars((string) ($_c3Title ?? ''), ENT_QUOTES); ?></h3>
            <?php if (!empty($_c3Text)): ?>
            <p><?php echo htmlspecialchars((string) $_c3Text, ENT_QUOTES); ?></p>
            <?php endif; ?>
            <?php if (!empty($_c3Badge)): ?>
            <span class="repo-badge repo-badge--inline"><?php echo htmlspecialchars((string) $_c3Badge, ENT_QUOTES); ?></span>
            <?php endif; ?>
            <?php if ($_c3FullUrl !== '' && !empty($_c3LinkText)): ?>
            <a href="<?php echo htmlspecialchars($_c3FullUrl, ENT_QUOTES); ?>"
               class="btn btn-sm info-card-cta <?php echo $_c3IsRepo ? 'btn-accent' : 'btn-outline'; ?>"
               <?php echo $_c3IsExternal ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <?php echo htmlspecialchars((string) $_c3LinkText, ENT_QUOTES); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>