<?php
/**
 * Member Profil – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'member/includes/bootstrap.php';

$controller->handleProfileRequest();

$currentUser = $controller->getCurrentUser();
$siteUrl = SITE_URL;
$activePage = 'profile';
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
$memberService = \CMS\Services\MemberService::getInstance();
$userMeta = $memberService->getUserMeta($controller->getUserId());
$profileCompletion = $controller->getProfileCompletion();
$memberDisplayName = trim((string) ($currentUser->display_name ?? ''));
$flash = $controller->consumeFlash();

include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>👤 Mein Profil</h1>
            <p>Pflege Kontodaten, Profilbild, Anzeigename und zusätzliche Angaben für deinen Member-Bereich.</p>
        </div>

        <?php echo phinit_render_member_flash($flash); ?>

        <form method="POST">
            <input type="hidden" name="action" value="profile_save">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('profile_save'), ENT_QUOTES); ?>">

            <div class="member-grid-2" data-anim data-anim-delay="1">

                <!-- Persönliche Daten -->
                <div class="member-card">
                    <div class="member-card-header"><h3>🏷️ Persönliche Daten</h3></div>
                    <div class="member-form-group">
                        <label class="member-label" for="display_name">Anzeigename</label>
                        <input type="text" id="display_name" name="display_name" class="member-input"
                               value="<?php echo htmlspecialchars($memberDisplayName, ENT_QUOTES); ?>" placeholder="Wie dein Name öffentlich erscheinen soll">
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="email">E-Mail <span class="req">*</span></label>
                        <input type="email" id="email" name="email" class="member-input"
                               value="<?php echo htmlspecialchars($currentUser->email ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="member-form-row">
                        <div class="member-form-group">
                            <label class="member-label" for="first_name">Vorname</label>
                            <input type="text" id="first_name" name="first_name" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['first_name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="member-form-group">
                            <label class="member-label" for="last_name">Nachname</label>
                            <input type="text" id="last_name" name="last_name" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['last_name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <div class="member-form-row">
                        <div class="member-form-group">
                            <label class="member-label" for="birth_date">Geburtsdatum</label>
                            <input type="date" id="birth_date" name="birth_date" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['birth_date'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="member-form-group">
                            <label class="member-label" for="phone">Telefon</label>
                            <input type="text" id="phone" name="phone" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['phone'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                </div>

                <!-- Erweitert -->
                <div class="member-card">
                    <div class="member-card-header"><h3>🖼️ Profilbild & Preview</h3></div>
                    <div class="member-profile-summary">
                        <div class="member-avatar member-avatar--xl">
                            <?php if (!empty($userMeta['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars((string) $userMeta['avatar'], ENT_QUOTES); ?>"
                                 alt="<?php echo htmlspecialchars($controller->getDisplayName(), ENT_QUOTES); ?>"
                                 class="member-avatar__image"
                                 loading="lazy"
                                 width="88"
                                 height="88">
                            <?php else: ?>
                            <?php echo htmlspecialchars($controller->getInitials(), ENT_QUOTES); ?>
                            <?php endif; ?>
                        </div>
                        <div class="member-profile-summary__content">
                            <strong><?php echo htmlspecialchars($controller->getDisplayName(), ENT_QUOTES); ?></strong>
                            <span><?php echo htmlspecialchars((string) ($currentUser->username ?? ''), ENT_QUOTES); ?></span>
                            <span><?php echo htmlspecialchars((string) ($currentUser->email ?? ''), ENT_QUOTES); ?></span>
                        </div>
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="avatar">Profilbild (URL)</label>
                        <input type="url" id="avatar" name="avatar" class="member-input" placeholder="https://..."
                               value="<?php echo htmlspecialchars($userMeta['avatar'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="social">Social / Profil-Link</label>
                        <input type="url" id="social" name="social" class="member-input" placeholder="https://linkedin.com/in/..."
                               value="<?php echo htmlspecialchars($userMeta['social'] ?? '', ENT_QUOTES); ?>">
                    </div>
                </div>

            </div>

            <div class="member-grid-2" data-anim data-anim-delay="1.5">
                <div class="member-card">
                    <div class="member-card-header"><h3>🌐 Beruf & Kontakt</h3></div>
                    <div class="member-form-row">
                        <div class="member-form-group">
                            <label class="member-label" for="company">Unternehmen</label>
                            <input type="text" id="company" name="company" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['company'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="member-form-group">
                            <label class="member-label" for="position">Position</label>
                            <input type="text" id="position" name="position" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['position'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="website">Website</label>
                        <input type="url" id="website" name="website" class="member-input" placeholder="https://"
                               value="<?php echo htmlspecialchars($userMeta['website'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="location">Ort</label>
                        <input type="text" id="location" name="location" class="member-input"
                               value="<?php echo htmlspecialchars($userMeta['location'] ?? '', ENT_QUOTES); ?>">
                    </div>
                </div>

                <div class="member-card">
                    <div class="member-card-header"><h3>📝 Über dich</h3></div>
                    <div class="member-form-group">
                        <label class="member-label" for="bio">Über mich</label>
                        <textarea id="bio" name="bio" class="member-input member-textarea"
                                  rows="5" placeholder="Kurze Beschreibung …"><?php echo htmlspecialchars($userMeta['bio'] ?? '', ENT_QUOTES); ?></textarea>
                    </div>
                    <div class="member-form-info">
                        <p>📅 Mitglied seit: <strong><?php echo date('d.m.Y', strtotime($currentUser->created_at ?? 'now')); ?></strong></p>
                        <p>🔑 Rolle: <strong><?php echo htmlspecialchars(ucfirst($currentUser->role ?? 'member')); ?></strong></p>
                        <p>📊 Profil vollständig: <strong><?php echo (int) ($profileCompletion['percentage'] ?? 0); ?>%</strong></p>
                    </div>
                </div>
            </div>

            <div class="member-actions" data-anim data-anim-delay="2">
                <button type="submit" class="btn btn-primary">💾 Profil speichern</button>
            </div>

        </form>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
