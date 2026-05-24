<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$authorName = trim((string) ($authorName ?? ''));
$authorBio = trim((string) ($authorBio ?? ''));
$authorAvatarUrl = trim((string) ($authorAvatarUrl ?? ''));
$authorUrl = trim((string) ($authorUrl ?? ''));
$authorEyebrow = trim((string) ($authorEyebrow ?? 'Autor'));
$authorAboutLabel = trim((string) ($authorAboutLabel ?? 'Über mich'));
$authorAboutWidth = (int) ($authorAboutWidth ?? 60);
$serviceHubUrl = trim((string) ($serviceHubUrl ?? ''));
$serviceHubLabel = trim((string) ($serviceHubLabel ?? 'Dienstleistungen ansehen'));
$serviceHubText = trim((string) ($serviceHubText ?? ''));
$hasServiceText = $serviceHubUrl !== '' && $serviceHubText !== '';
$hasServiceButton = $serviceHubUrl !== '' && $serviceHubLabel !== '';

if (!in_array($authorAboutWidth, [50, 60, 75], true)) {
    $authorAboutWidth = 60;
}

if ($authorName === '' && $authorBio === '' && $authorAvatarUrl === '' && !$hasServiceText && !$hasServiceButton) {
    return;
}

$authorInitials = '';
if ($authorName !== '') {
    $parts = preg_split('/\s+/u', $authorName) ?: [];
    foreach (array_slice($parts, 0, 2) as $part) {
        $authorInitials .= mb_strtoupper(mb_substr((string) $part, 0, 1));
    }
}

if ($authorInitials === '') {
    $authorInitials = 'A';
}

$serviceIsExternal = false;
if ($serviceHubUrl !== '' && preg_match('#^https?://#i', $serviceHubUrl) === 1) {
    $siteHost = strtolower((string) (parse_url((string) (defined('SITE_URL') ? SITE_URL : ''), PHP_URL_HOST) ?? ''));
    $serviceHost = strtolower((string) (parse_url($serviceHubUrl, PHP_URL_HOST) ?? ''));
    $serviceIsExternal = $serviceHost !== '' && $siteHost !== '' && $serviceHost !== $siteHost;
}
?>
<section class="post-author-box post-author-box--about-<?php echo (int) $authorAboutWidth; ?>" aria-label="Autorinnen- und Autorenbox">
    <div class="post-author-box__author">
    <?php if ($authorAvatarUrl !== ''): ?>
    <img class="post-author-box__avatar"
         src="<?php echo htmlspecialchars($authorAvatarUrl, ENT_QUOTES); ?>"
         alt="<?php echo htmlspecialchars($authorName !== '' ? $authorName : 'Autor', ENT_QUOTES); ?>"
         <?php echo phinit_image_loading_attributes(); ?>>
    <?php else: ?>
    <div class="post-author-box__avatar-placeholder" aria-hidden="true"><?php echo htmlspecialchars($authorInitials, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <div class="post-author-box__body">
        <?php if ($authorEyebrow !== ''): ?>
        <span class="post-author-box__eyebrow"><?php echo phinit_escape_text($authorEyebrow); ?></span>
        <?php endif; ?>
        <h3 class="post-author-box__name">
            <?php if ($authorUrl !== ''): ?>
            <a class="post-author-box__name-link" href="<?php echo htmlspecialchars($authorUrl, ENT_QUOTES); ?>"><?php echo phinit_escape_text($authorName !== '' ? $authorName : 'Autor'); ?></a>
            <?php else: ?>
            <?php echo phinit_escape_text($authorName !== '' ? $authorName : 'Autor'); ?>
            <?php endif; ?>
        </h3>
        <?php if ($authorBio !== ''): ?>
        <div class="post-author-box__about">
            <?php if ($authorAboutLabel !== ''): ?>
            <span class="post-author-box__content-label"><?php echo phinit_escape_text($authorAboutLabel); ?></span>
            <?php endif; ?>
            <p class="post-author-box__bio"><?php echo phinit_escape_text($authorBio); ?></p>
        </div>
        <?php endif; ?>
    </div>
    </div>

    <?php if ($hasServiceText || $hasServiceButton): ?>
    <div class="post-author-box__service" aria-label="Dienstleistungs-HubSite">
        <?php if ($hasServiceText): ?>
        <p class="post-author-box__service-text"><?php echo phinit_escape_text($serviceHubText); ?></p>
        <?php endif; ?>
        <?php if ($hasServiceButton): ?>
        <a class="post-author-box__service-link"
           href="<?php echo htmlspecialchars($serviceHubUrl, ENT_QUOTES); ?>"<?php echo $serviceIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo phinit_escape_text($serviceHubLabel); ?></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>