<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'member/includes/bootstrap.php';

$controller->handlePrivacyRequest();

$siteUrl = SITE_URL;
$activePage = 'privacy';
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
$memberService = \CMS\Services\MemberService::getInstance();
$privacy = $memberService->getPrivacySettings($controller->getUserId());
$overview = $memberService->getDataOverview($controller->getUserId());
$publicProfileFields = $memberService->getPublicProfileFieldDefinitions();
$flash = $controller->consumeFlash();

include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>🔐 Datenschutz & Sichtbarkeit</h1>
            <p>Lege fest, welche Profilangaben auf deiner öffentlichen Autorenseite sichtbar sind und was nur intern bleibt.</p>
        </div>

        <?php echo phinit_render_member_flash($flash); ?>

        <form method="POST">
            <input type="hidden" name="action" value="privacy_save">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('privacy_action'), ENT_QUOTES); ?>">

            <div class="member-grid-2" data-anim data-anim-delay="1">
                <div class="member-card">
                    <div class="member-card-header"><h3>👁️ Sichtbarkeit</h3></div>

                    <div class="member-form-group">
                        <label class="member-label" for="profile_visibility">Öffentliche Profilseite sichtbar für</label>
                        <select id="profile_visibility" name="profile_visibility" class="member-input">
                            <?php foreach (['public' => 'Öffentlich', 'members' => 'Nur eingeloggte Mitglieder', 'private' => 'Niemand / nur ich'] as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" <?php echo (($privacy['profile_visibility'] ?? 'members') === $value) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="member-form-group">
                        <label class="member-label">Zusätzliche Freigaben</label>
                        <label class="member-switch-card" for="privacy_show_email">
                            <input type="checkbox" id="privacy_show_email" name="show_email" value="1" <?php echo !empty($privacy['show_email']) ? 'checked' : ''; ?>>
                            <span>
                                <strong>E-Mail sichtbar machen</strong>
                                <small>Nur aktivieren, wenn Besucher dich direkt über dein Profil kontaktieren dürfen.</small>
                            </span>
                        </label>
                        <label class="member-switch-card" for="privacy_show_activity">
                            <input type="checkbox" id="privacy_show_activity" name="show_activity" value="1" <?php echo !empty($privacy['show_activity']) ? 'checked' : ''; ?>>
                            <span>
                                <strong>Aktivität hervorheben</strong>
                                <small>Zeigt auf der Autorenseite deine veröffentlichten Beiträge deutlicher an.</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="member-card">
                    <div class="member-card-header"><h3>📊 Datenschutz-Überblick</h3></div>
                    <div class="member-form-info">
                        <p>🧾 Meta-Felder: <strong><?php echo (int) ($overview['stored_meta_fields'] ?? 0); ?></strong></p>
                        <p>🖥️ Aktive Sessions: <strong><?php echo (int) ($overview['sessions'] ?? 0); ?></strong></p>
                        <p>🔔 Benachrichtigungen: <strong><?php echo (int) ($overview['notifications'] ?? 0); ?></strong></p>
                    </div>
                    <div class="member-security-note member-privacy-note" role="note">
                        <strong>Nur deine Freigaben sind sichtbar</strong>
                        <p>Die Author-Seite zeigt ausschließlich Felder, die du hier aktiv auswählst. Nicht markierte Angaben bleiben unsichtbar.</p>
                    </div>
                </div>
            </div>

            <div class="member-card" data-anim data-anim-delay="1.5">
                <div class="member-card-header"><h3>🪪 Freigegebene Profilfelder</h3></div>
                <div class="member-grid-2">
                    <?php foreach ($publicProfileFields as $fieldKey => $fieldDefinition): ?>
                    <label class="member-switch-card" for="privacy_field_<?php echo htmlspecialchars((string) $fieldKey, ENT_QUOTES); ?>">
                        <input type="checkbox"
                               id="privacy_field_<?php echo htmlspecialchars((string) $fieldKey, ENT_QUOTES); ?>"
                               name="public_profile_fields[]"
                               value="<?php echo htmlspecialchars((string) $fieldKey, ENT_QUOTES); ?>"
                               <?php echo in_array($fieldKey, (array) ($privacy['public_profile_fields'] ?? []), true) ? 'checked' : ''; ?>>
                        <span>
                            <strong><?php echo htmlspecialchars((string) ($fieldDefinition['label'] ?? $fieldKey), ENT_QUOTES); ?></strong>
                            <small>Wird auf deiner öffentlichen Author-Seite angezeigt, sofern ausgefüllt.</small>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="member-grid-2" data-anim data-anim-delay="2">
                <div class="member-card">
                    <div class="member-card-header"><h3>📦 DSGVO-Aktionen</h3></div>
                    <div class="member-actions member-actions--compact">
                        <button type="submit" class="btn btn-primary">💾 Datenschutzeinstellungen speichern</button>
                    </div>
                    <div class="member-actions member-actions--row member-actions--compact">
                        <button type="submit" formaction="" name="action" value="privacy_export" class="btn btn-outline">📁 Meine Daten exportieren</button>
                        <button type="submit" formaction="" name="action" value="privacy_delete_request" class="btn btn-ghost">🗑️ Account-Löschung anfordern</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include $themeDir . 'footer.php';
