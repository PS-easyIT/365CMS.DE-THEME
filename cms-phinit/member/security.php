<?php
/**
 * Member Sicherheit – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$formatSecurityDate = static function (?string $value, string $format = 'd.m.Y H:i'): string {
    $timestamp = strtotime((string) $value);

    return $timestamp !== false ? date($format, $timestamp) : '–';
};

require_once ABSPATH . 'member/includes/bootstrap.php';

$controller->handleSecurityRequest();

$currentUser = $controller->getCurrentUser();
$siteUrl = SITE_URL;
$activePage = 'security';
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
$securityData = $controller->getSecurityPageData();
$security = is_array($securityData['security'] ?? null) ? $securityData['security'] : [];
$sessions = is_array($securityData['sessions'] ?? null) ? $securityData['sessions'] : [];
$credentials = is_array($securityData['credentials'] ?? null) ? $securityData['credentials'] : [];
$passkeyPayload = is_array($securityData['passkey_payload'] ?? null) ? $securityData['passkey_payload'] : ['available' => false, 'options_json' => '{}'];
$totpSetup = is_array($securityData['totp_setup'] ?? null) ? $securityData['totp_setup'] : null;
$totpQrUrl = trim((string) ($totpSetup['qr_data_uri'] ?? $totpSetup['qr_url'] ?? ''));
$totpSecret = trim((string) ($totpSetup['secret'] ?? ''));
$totpOtpUri = trim((string) ($totpSetup['otp_uri'] ?? ''));
$sessionCount = count($sessions);
$lastActivity = $sessionCount > 0 ? (string) (($sessions[0]->last_activity ?? $sessions[0]['last_activity'] ?? '')) : '';
$flash = $controller->consumeFlash();

include $themeDir . 'header.php';
?>
<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main" id="main-content">

        <section class="member-security-hero" data-anim>
            <div class="member-security-hero__content">
                <span class="member-security-hero__eyebrow">🔒 Sicherheitscenter</span>
                <h1>Sicherheit & Sitzungen</h1>
                <p>Verwalte Passwort, Zwei-Faktor-Authentifizierung, Backup-Codes, Passkeys und alle aktiven Sitzungen an einem Ort.</p>
            </div>
            <div class="member-security-hero__stats">
                <div class="member-security-stat">
                    <span class="member-security-stat__label">Aktive Sitzungen</span>
                    <strong><?php echo $sessionCount; ?></strong>
                </div>
                <div class="member-security-stat">
                    <span class="member-security-stat__label">Security Score</span>
                    <strong><?php echo (int) ($security['score'] ?? 0); ?>/100</strong>
                </div>
                <div class="member-security-stat">
                    <span class="member-security-stat__label">MFA Status</span>
                    <strong><?php echo !empty($securityData['totp_enabled']) ? 'Aktiv' : 'Inaktiv'; ?></strong>
                </div>
                <div class="member-security-stat">
                    <span class="member-security-stat__label">Backup-Codes</span>
                    <strong><?php echo (int) ($securityData['backup_count'] ?? 0); ?></strong>
                </div>
            </div>
        </section>

        <?php echo phinit_render_member_flash($flash); ?>

        <div class="member-grid-2 member-grid-2--security" data-anim data-anim-delay="1.5">
            <div class="member-card member-card--security-form">
                <div class="member-card-header">
                    <h3>🔑 Passwort ändern</h3>
                </div>

                <form method="POST" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security" class="member-security-form">
                    <input type="hidden" name="action" value="password_change">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_password'), ENT_QUOTES); ?>">

                    <div class="member-form-group">
                        <label for="current_password" class="member-label">Aktuelles Passwort <span class="req">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="member-input" autocomplete="current-password" required>
                    </div>

                    <div class="member-form-group">
                        <label for="new_password" class="member-label">Neues Passwort <span class="req">*</span></label>
                        <input type="password" id="new_password" name="new_password" class="member-input" autocomplete="new-password" minlength="12" required>
                        <p class="member-form-hint">Mindestens 12 Zeichen, Groß-/Kleinbuchstaben, Zahl und Sonderzeichen.</p>
                    </div>

                    <div class="member-form-group">
                        <label for="confirm_password" class="member-label">Neues Passwort wiederholen <span class="req">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="member-input" autocomplete="new-password" minlength="12" required>
                    </div>

                    <div class="member-actions member-actions--compact">
                        <button type="submit" class="btn btn-primary">🔒 Passwort ändern</button>
                    </div>
                </form>
            </div>

            <div class="member-card member-card--security-side">
                <div class="member-card-header">
                    <h3>🛡️ Schnellcheck</h3>
                </div>

                <ul class="member-security-checklist">
                    <?php foreach ((array) ($security['recommendations'] ?? []) as $recommendation): ?>
                    <li><?php echo htmlspecialchars((string) ($recommendation['text'] ?? ''), ENT_QUOTES); ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="member-security-note">
                    <strong>Hinweis:</strong>
                    <p><?php echo htmlspecialchars((string) ($security['score_message'] ?? 'Wenn dir ein Gerät unbekannt vorkommt, ändere sofort dein Passwort und überprüfe offene Browser-Sitzungen.'), ENT_QUOTES); ?></p>
                    <?php if ($lastActivity !== ''): ?>
                    <p class="member-security-note__meta">Letzte registrierte Aktivität: <?php echo htmlspecialchars($formatSecurityDate($lastActivity), ENT_QUOTES); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="member-grid-2 member-grid-2--security" data-anim data-anim-delay="1.8">
            <div class="member-card member-card--security-form">
                <div class="member-card-header">
                    <h3>📲 Zwei-Faktor-Authentifizierung</h3>
                    <span class="member-security-pill<?php echo !empty($securityData['totp_enabled']) ? ' is-active' : ''; ?>"><?php echo !empty($securityData['totp_enabled']) ? 'Aktiv' : 'Nicht aktiv'; ?></span>
                </div>

                <?php if ($totpSetup !== null): ?>
                <div class="member-totp-setup">
                    <div class="member-totp-setup__qr">
                        <?php if ($totpQrUrl !== ''): ?>
                        <img src="<?php echo htmlspecialchars($totpQrUrl, ENT_QUOTES); ?>" alt="TOTP QR-Code" <?php echo phinit_image_loading_attributes(true, false); ?>>
                        <?php else: ?>
                        <div class="member-totp-placeholder">QR-Code nicht verfügbar</div>
                        <?php endif; ?>
                    </div>
                    <div class="member-totp-setup__content">
                        <p>Scanne den QR-Code mit deiner Authenticator-App und bestätige danach den 6-stelligen Code.</p>
                        <?php if ($totpSecret !== ''): ?>
                        <p><strong>Manueller Schlüssel:</strong> <code><?php echo htmlspecialchars($totpSecret, ENT_QUOTES); ?></code></p>
                        <?php endif; ?>
                        <?php if ($totpOtpUri !== ''): ?>
                        <p class="member-form-hint"><?php echo htmlspecialchars($totpOtpUri, ENT_QUOTES); ?></p>
                        <?php endif; ?>
                        <form method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security" class="member-security-form member-security-form--inline">
                            <input type="hidden" name="action" value="totp_confirm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_mfa'), ENT_QUOTES); ?>">
                            <div class="member-form-group">
                                <label for="totp_code" class="member-label">Authenticator-Code</label>
                                <input type="text" id="totp_code" name="totp_code" class="member-input" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
                            </div>
                            <div class="member-actions member-actions--compact">
                                <button type="submit" class="btn btn-primary">MFA aktivieren</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <p class="member-security-copy">Schütze dein Konto zusätzlich mit einem Authenticator und sicheren Backup-Codes.</p>
                <div class="member-actions member-actions--row">
                    <?php if (empty($securityData['totp_enabled'])): ?>
                    <form method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security">
                        <input type="hidden" name="action" value="totp_start">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_mfa'), ENT_QUOTES); ?>">
                        <button type="submit" class="btn btn-primary">TOTP einrichten</button>
                    </form>
                    <?php else: ?>
                    <form method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security">
                        <input type="hidden" name="action" value="backup_generate">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_mfa'), ENT_QUOTES); ?>">
                        <button type="submit" class="btn btn-secondary">Backup-Codes erneuern</button>
                    </form>
                    <form method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security">
                        <input type="hidden" name="action" value="totp_disable">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_mfa'), ENT_QUOTES); ?>">
                        <button type="submit" class="btn btn-outline">MFA deaktivieren</button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="member-card member-card--security-side">
                <div class="member-card-header">
                    <h3>🔑 Passkeys</h3>
                    <span class="member-security-pill<?php echo !empty($passkeyPayload['available']) ? ' is-active' : ''; ?>"><?php echo !empty($passkeyPayload['available']) ? 'Verfügbar' : 'Nicht verfügbar'; ?></span>
                </div>
                <p class="member-security-copy">Registriere einen Hardware-Key oder Plattform-Passkey für schnellere und sicherere Anmeldungen.</p>

                <?php if (!empty($passkeyPayload['available'])): ?>
                <form method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security" data-passkey-form data-passkey-options='<?php echo htmlspecialchars((string) ($passkeyPayload['options_json'] ?? '{}'), ENT_QUOTES); ?>' class="member-security-form">
                    <input type="hidden" name="action" value="passkey_register">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_passkey'), ENT_QUOTES); ?>">
                    <input type="hidden" name="client_data_json" value="">
                    <input type="hidden" name="attestation_object" value="">
                    <div class="member-form-group">
                        <label for="credential_name" class="member-label">Bezeichnung</label>
                        <input type="text" id="credential_name" name="credential_name" class="member-input" value="Mein Gerät">
                    </div>
                    <div class="member-actions member-actions--compact">
                        <button type="button" class="btn btn-primary" data-passkey-register>Passkey registrieren</button>
                    </div>
                </form>
                <?php endif; ?>

                <?php if (!empty($credentials)): ?>
                <div class="member-passkey-list">
                    <?php foreach ($credentials as $credential): ?>
                    <?php
                    $credentialName = is_array($credential) ? (string) ($credential['name'] ?? 'Passkey') : (string) ($credential->name ?? 'Passkey');
                    $credentialCreatedAt = is_array($credential) ? (string) ($credential['created_at'] ?? '') : (string) ($credential->created_at ?? '');
                    $credentialRecordId = is_array($credential) ? (int) ($credential['id'] ?? 0) : (int) ($credential->id ?? 0);
                    ?>
                    <article class="member-passkey-item">
                        <div>
                            <strong><?php echo htmlspecialchars($credentialName, ENT_QUOTES); ?></strong>
                            <span><?php echo htmlspecialchars($credentialCreatedAt, ENT_QUOTES); ?></span>
                        </div>
                        <form method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security">
                            <input type="hidden" name="action" value="passkey_delete">
                            <input type="hidden" name="credential_id" value="<?php echo $credentialRecordId; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('security_passkey'), ENT_QUOTES); ?>">
                            <button type="submit" class="member-fav-remove-btn">Entfernen</button>
                        </form>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="member-empty"><p>📭 Noch keine Passkeys registriert.</p></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="member-card member-card--spaced" data-anim data-anim-delay="2">
            <div class="member-card-header">
                <h3>📱 Aktive Sitzungen</h3>
            </div>

            <?php if (!empty($sessions)): ?>
            <div class="member-session-list">
                <?php foreach ($sessions as $session):
                    $ua = (string) ($session['user_agent'] ?? 'Unbekanntes Gerät');
                    $uaLabel = mb_strlen($ua) > 70 ? mb_substr($ua, 0, 70) . '…' : $ua;
                ?>
                <article class="member-session-item">
                    <div class="member-session-item__main">
                        <strong><?php echo htmlspecialchars($uaLabel, ENT_QUOTES); ?></strong>
                        <span><?php echo htmlspecialchars((string) ($session['ip_address'] ?? '–'), ENT_QUOTES); ?></span>
                    </div>
                    <div class="member-session-item__meta">
                        <span>Erstellt: <?php echo htmlspecialchars(!empty($session['created_at']) ? $formatSecurityDate((string) $session['created_at']) : '–', ENT_QUOTES); ?></span>
                        <span>Zuletzt aktiv: <?php echo htmlspecialchars(!empty($session['last_activity']) ? $formatSecurityDate((string) $session['last_activity']) : '–', ENT_QUOTES); ?></span>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="member-empty">
                <p>📭 Zurzeit wurden keine zusätzlichen aktiven Sitzungen gefunden.</p>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include $themeDir . 'footer.php'; ?>
