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
$commentTextValue = trim((string) ($_POST['comment'] ?? $_POST['comment_text'] ?? ''));
$commentNameValue = trim((string) ($_POST['author'] ?? $_POST['comment_name'] ?? ''));
$commentEmailValue = trim((string) ($_POST['email'] ?? $_POST['comment_email'] ?? ''));
$commentUser = class_exists('\\CMS\\Auth') && \CMS\Auth::isLoggedIn() ? \CMS\Auth::getCurrentUser() : null;
$commentUserName = trim((string) ($commentUser->display_name ?? $commentUser->username ?? ''));
$commentUserInitials = 'M';

if ($commentUserName !== '') {
    $commentUserNameParts = preg_split('/\s+/u', $commentUserName) ?: [];
    $commentUserInitials = '';
    foreach (array_slice($commentUserNameParts, 0, 2) as $commentUserNamePart) {
        $commentUserInitials .= mb_strtoupper((string) mb_substr((string) $commentUserNamePart, 0, 1), 'UTF-8');
    }
    $commentUserInitials = $commentUserInitials !== '' ? $commentUserInitials : 'M';
}

$commentSessionError = trim((string) ($_SESSION['error'] ?? ''));
$commentSessionSuccess = trim((string) ($_SESSION['success'] ?? ''));

if ($commentError === '' && $commentSessionError !== '') {
    $commentError = $commentSessionError;
}

if ($commentSuccess === '' && $commentSessionSuccess !== '') {
    $commentSuccess = $commentSessionSuccess;
}

if ($commentSessionError !== '' || $commentSessionSuccess !== '') {
    unset($_SESSION['error'], $_SESSION['success']);
}

$commentFormAction = rtrim((string) SITE_URL, '/') . '/comments/post';
$commentCount = count($comments);
$commentCountLabel = $commentCount === 1 ? '1 Kommentar' : $commentCount . ' Kommentare';
$commentAnonymousChecked = !empty($_POST['comment_anonymous']);
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$formatCommentDate = static function (?string $value, string $format = 'j. F Y'): string {
    $timestamp = strtotime((string) $value);

    return $timestamp !== false ? date($format, $timestamp) : '—';
};

if (!$showComments) {
    return;
}
?>
<section class="comments-section" id="comments">
    <div class="comments-section__meta" aria-label="Kommentarübersicht">
        <span class="comments-section__count"><?php echo htmlspecialchars($commentCountLabel, ENT_QUOTES); ?></span>
    </div>

    <?php if (!empty($comments)): ?>
        <div class="comments-list" role="list">
        <?php foreach ($comments as $comment): ?>
        <?php
            $commentDepth = max(0, (int) ($comment['depth'] ?? (!empty($comment['parent_id']) ? 1 : 0)));
            $commentClasses = 'comment-item';
            $commentUserId = (int) ($comment['user_id'] ?? 0);
            $commentIsAnonymous = !empty($comment['is_anonymous']);
            $commentAuthorUrl = ($commentUserId > 0 && !$commentIsAnonymous)
                ? (function_exists('phinit_localized_href') ? phinit_localized_href('/author/user-' . $commentUserId, $currentLocale, (string) SITE_URL) : rtrim((string) SITE_URL, '/') . '/author/user-' . $commentUserId)
                : '';
            if ($commentDepth > 0) {
                $commentClasses .= ' comment-item--reply';
            }
            if ($commentDepth > 1) {
                $commentClasses .= ' comment-item--reply-deep';
            }
        ?>
        <article class="<?php echo htmlspecialchars($commentClasses, ENT_QUOTES); ?>" role="listitem">
            <div class="comment-avatar" aria-hidden="true">
                <?php echo htmlspecialchars(strtoupper(substr((string) ($comment['author'] ?? 'A'), 0, 1)), ENT_QUOTES); ?>
            </div>
            <div class="comment-body-wrap">
                <div class="comment-author-line">
                    <?php if ($commentAuthorUrl !== ''): ?>
                    <a href="<?php echo htmlspecialchars($commentAuthorUrl, ENT_QUOTES); ?>" class="comment-author comment-author--link"><?php echo htmlspecialchars((string) ($comment['author'] ?? ''), ENT_QUOTES); ?></a>
                    <?php else: ?>
                    <span class="comment-author"><?php echo htmlspecialchars((string) ($comment['author'] ?? ''), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <span class="comment-date"><?php echo htmlspecialchars($formatCommentDate((string) ($comment['post_date'] ?? '')), ENT_QUOTES); ?></span>
                </div>
                <p class="comment-text"><?php echo htmlspecialchars((string) ($comment['content'] ?? ''), ENT_QUOTES); ?></p>
            </div>
        </article>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="comment-empty-state">
            <div class="comment-empty-state__icon" aria-hidden="true">✍</div>
            <div class="comment-empty-state__content">
                <strong>Noch keine Kommentare</strong>
                <p>Sei der Erste und starte die Diskussion mit einem hilfreichen Beitrag.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($commentError !== ''): ?>
    <div class="alert-box alert-box--error">
        <span class="alert-box__icon" aria-hidden="true">❌</span>
        <div class="alert-box__content"><?php echo htmlspecialchars($commentError, ENT_QUOTES); ?></div>
    </div>
    <?php endif; ?>

    <?php if ($commentSuccess !== ''): ?>
    <div class="alert-box alert-box--success">
        <span class="alert-box__icon" aria-hidden="true">✅</span>
        <div class="alert-box__content"><?php echo htmlspecialchars($commentSuccess, ENT_QUOTES); ?></div>
    </div>
    <?php endif; ?>

    <div class="comment-form-wrap">
        <div class="comment-form-wrap__header">
            <h4><?php echo htmlspecialchars($commentFormHeader, ENT_QUOTES); ?></h4>
            <div class="comment-form-wrap__notice" role="note">
                <span class="comment-form-wrap__notice-icon" aria-hidden="true">✦</span>
                <p class="comment-form-wrap__intro">Dein Beitrag wird vor der Veröffentlichung kurz geprüft — fachlich, respektvoll und auf den Punkt ist hier genau richtig.</p>
            </div>
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($commentFormAction, ENT_QUOTES); ?>" novalidate class="comment-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="post_id" value="<?php echo (int) ($post['id'] ?? 0); ?>">

            <div class="form-group form-group--spaced">
                <label for="comment">Kommentar <span class="field-required">*</span></label>
                <textarea id="comment" name="comment" class="form-control" required placeholder="Dein Kommentar …" rows="4"><?php echo htmlspecialchars($commentTextValue, ENT_QUOTES); ?></textarea>
                <small class="form-helper-text">E-Mail Adresse wird nicht veröffentlicht.</small>
            </div>

            <div class="form-row form-row--spaced">
                <?php if ($commentUser && !empty($commentUser->id)): ?>
                <div class="form-group form-group--fullwidth">
                    <label>Kommentar als angemeldetes Profil</label>
                    <div class="comment-profile-card">
                        <div class="comment-profile-card__status">Angemeldet & verifiziert</div>
                        <div class="comment-profile-card__main">
                            <div class="comment-profile-card__avatar" aria-hidden="true"><?php echo htmlspecialchars($commentUserInitials, ENT_QUOTES); ?></div>
                            <div class="comment-profile-card__content">
                                <strong class="comment-profile-card__name"><?php echo htmlspecialchars($commentUserName !== '' ? $commentUserName : 'Mitglied', ENT_QUOTES); ?></strong>
                                <?php if ($commentUserName !== ''): ?>
                                <span class="comment-profile-card__email"><?php echo htmlspecialchars($commentUserName, ENT_QUOTES); ?></span>
                                <?php endif; ?>
                                <span class="comment-profile-card__meta">Dein Kommentar wird automatisch mit deinem hinterlegten Mitgliederprofil eingereicht.</span>
                            </div>
                        </div>
                    </div>
                    <label class="comment-anonymous-toggle" for="comment_anonymous">
                        <input type="checkbox" id="comment_anonymous" name="comment_anonymous" value="1" <?php echo $commentAnonymousChecked ? 'checked' : ''; ?>>
                        <span class="comment-anonymous-toggle__text">
                            <strong>Anonym veröffentlichen</strong>
                            <small>Dein Konto bleibt intern zugeordnet, öffentlich erscheint der Kommentar nur als „Anonym“.</small>
                        </span>
                    </label>
                </div>
                <?php else: ?>
                <div class="form-group">
                    <label for="author">Name <span class="field-required">*</span></label>
                    <input type="text" id="author" name="author" class="form-control" required placeholder="Dein Name" value="<?php echo htmlspecialchars($commentNameValue, ENT_QUOTES); ?>">
                </div>
                <div class="form-group">
                    <label for="email">E-Mail <span class="field-required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="dein@email.de" value="<?php echo htmlspecialchars($commentEmailValue, ENT_QUOTES); ?>">
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group form-group--honeypot">
                <label for="comment_hp" class="visually-hidden-field">Dieses Feld leer lassen</label>
                <input type="text" id="comment_hp" name="comment_hp" value="" tabindex="-1" autocomplete="off" class="visually-hidden-field" aria-hidden="true">
            </div>

            <div class="comment-form__actions">
                <button type="submit" class="btn btn-primary comment-submit-btn">Kommentar abschicken</button>
            </div>
        </form>
    </div>
</section>
