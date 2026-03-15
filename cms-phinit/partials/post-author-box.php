<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$authorName = trim((string) ($authorName ?? ''));
$authorBio = trim((string) ($authorBio ?? ''));
$authorAvatarUrl = trim((string) ($authorAvatarUrl ?? ''));
$authorUrl = trim((string) ($authorUrl ?? ''));

if ($authorName === '' && $authorBio === '' && $authorAvatarUrl === '') {
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
?>
<section class="post-author-box" aria-label="Autorinnen- und Autorenbox">
    <?php if ($authorAvatarUrl !== ''): ?>
    <img class="post-author-box__avatar"
         src="<?php echo htmlspecialchars($authorAvatarUrl, ENT_QUOTES); ?>"
         alt="<?php echo htmlspecialchars($authorName !== '' ? $authorName : 'Autor', ENT_QUOTES); ?>"
         <?php echo phinit_image_loading_attributes(); ?>>
    <?php else: ?>
    <div class="post-author-box__avatar-placeholder" aria-hidden="true"><?php echo htmlspecialchars($authorInitials, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <div class="post-author-box__body">
        <span class="post-author-box__eyebrow">Autor</span>
        <h3 class="post-author-box__name">
            <?php if ($authorUrl !== ''): ?>
            <a class="post-author-box__name-link" href="<?php echo htmlspecialchars($authorUrl, ENT_QUOTES); ?>"><?php echo phinit_escape_text($authorName !== '' ? $authorName : 'Autor'); ?></a>
            <?php else: ?>
            <?php echo phinit_escape_text($authorName !== '' ? $authorName : 'Autor'); ?>
            <?php endif; ?>
        </h3>
        <?php if ($authorBio !== ''): ?>
        <p class="post-author-box__bio"><?php echo phinit_escape_text($authorBio); ?></p>
        <?php endif; ?>
    </div>
</section>