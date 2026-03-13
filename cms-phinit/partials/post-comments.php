<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$showComments = isset($showComments) ? (bool) $showComments : false;
$commentsHeader = isset($commentsHeader) ? (string) $commentsHeader : '💬 Kommentare';
$comments = isset($comments) && is_array($comments) ? $comments : [];
$commentError = isset($commentError) ? trim((string) $commentError) : '';
$commentSuccess = isset($commentSuccess) ? trim((string) $commentSuccess) : '';
$commentFormHeader = isset($commentFormHeader) ? (string) $commentFormHeader : 'Kommentar hinterlassen';
$csrfToken = isset($csrfToken) ? (string) $csrfToken : '';
$post = isset($post) && is_array($post) ? $post : [];

if (!$showComments) {
    return;
}
?>
<section class="comments-section" id="comments">
    <h2 class="comments-title"><?php echo htmlspecialchars($commentsHeader, ENT_QUOTES); ?></h2>

    <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-item">
            <div class="comment-avatar" aria-hidden="true">
                <?php echo htmlspecialchars(strtoupper(substr((string) ($comment['author'] ?? 'A'), 0, 1)), ENT_QUOTES); ?>
            </div>
            <div class="comment-body-wrap">
                <div class="comment-author-line">
                    <span class="comment-author"><?php echo htmlspecialchars((string) ($comment['author'] ?? ''), ENT_QUOTES); ?></span>
                    <span class="comment-date"><?php echo htmlspecialchars(date('j. F Y', strtotime((string) ($comment['post_date'] ?? 'now'))), ENT_QUOTES); ?></span>
                </div>
                <p class="comment-text"><?php echo htmlspecialchars((string) ($comment['content'] ?? ''), ENT_QUOTES); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="comment-empty-state">Noch keine Kommentare. Sei der Erste!</p>
    <?php endif; ?>

    <?php if ($commentError !== ''): ?>
    <div class="alert-box alert-box--error">
        ❌ <?php echo htmlspecialchars($commentError, ENT_QUOTES); ?>
    </div>
    <?php endif; ?>

    <?php if ($commentSuccess !== ''): ?>
    <div class="alert-box alert-box--success">
        <?php echo htmlspecialchars($commentSuccess, ENT_QUOTES); ?>
    </div>
    <?php endif; ?>

    <div class="comment-form-wrap">
        <h4><?php echo htmlspecialchars($commentFormHeader, ENT_QUOTES); ?></h4>
        <form method="POST" action="#comments" novalidate>
            <input type="hidden" name="submit_comment" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="post_id" value="<?php echo (int) ($post['id'] ?? 0); ?>">

            <div class="form-group form-group--spaced">
                <label for="comment_text">Kommentar <span class="field-required">*</span></label>
                <textarea id="comment_text" name="comment_text" class="form-control" required placeholder="Dein Kommentar …" rows="4"></textarea>
                <small class="form-helper-text">E-Mail Adresse wird nicht veröffentlicht.</small>
            </div>

            <div class="form-row form-row--spaced">
                <div class="form-group">
                    <label for="comment_name">Name <span class="field-required">*</span></label>
                    <input type="text" id="comment_name" name="comment_name" class="form-control" required placeholder="Dein Name">
                </div>
                <div class="form-group">
                    <label for="comment_email">E-Mail <span class="field-required">*</span></label>
                    <input type="email" id="comment_email" name="comment_email" class="form-control" required placeholder="dein@email.de">
                </div>
            </div>

            <div class="form-group form-group--honeypot">
                <label for="comment_hp" class="visually-hidden-field">Dieses Feld leer lassen</label>
                <input type="text" id="comment_hp" name="comment_hp" value="" tabindex="-1" autocomplete="off" class="visually-hidden-field" aria-hidden="true">
            </div>

            <button type="submit" class="btn btn-primary">Kommentar abschicken</button>
        </form>
    </div>
</section>
