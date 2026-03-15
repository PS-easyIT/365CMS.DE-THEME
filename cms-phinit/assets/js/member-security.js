/**
 * CMS Phinit Theme – Member Security Interactions
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    const boot = () => {
        initPasskeys();
        initBackupCodeCopy();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    function setFormFeedback(form, message) {
        if (!(form instanceof HTMLElement) || !message) {
            return;
        }

        let feedback = form.querySelector('[data-passkey-feedback]');
        if (!(feedback instanceof HTMLElement)) {
            feedback = document.createElement('p');
            feedback.className = 'member-form-hint';
            feedback.setAttribute('data-passkey-feedback', '1');
            form.appendChild(feedback);
        }

        feedback.textContent = message;
    }

    function toBase64Url(uint8Array) {
        let binary = '';
        uint8Array.forEach((byte) => {
            binary += String.fromCharCode(byte);
        });

        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
    }

    function toBase64UrlFromBufferSource(bufferSource) {
        if (!bufferSource) {
            return '';
        }

        const view = bufferSource instanceof Uint8Array
            ? bufferSource
            : new Uint8Array(bufferSource);

        return toBase64Url(view);
    }

    function fromBase64Url(value) {
        if (!value) {
            return new Uint8Array();
        }

        const normalized = value.replace(/-/g, '+').replace(/_/g, '/');
        const padded = normalized + '==='.slice((normalized.length + 3) % 4);
        const binary = atob(padded);
        const bytes = new Uint8Array(binary.length);

        for (let index = 0; index < binary.length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        return bytes;
    }

    function normalizePublicKeyOptions(options) {
        if (!options || typeof options !== 'object') {
            return options;
        }

        const publicKey = options.publicKey && typeof options.publicKey === 'object'
            ? options.publicKey
            : options;

        if (publicKey.challenge) {
            publicKey.challenge = fromBase64Url(publicKey.challenge);
        }

        if (publicKey.user && publicKey.user.id) {
            publicKey.user.id = fromBase64Url(publicKey.user.id);
        }

        if (Array.isArray(publicKey.excludeCredentials)) {
            publicKey.excludeCredentials = publicKey.excludeCredentials.map((credential) => {
                if (credential.id) {
                    credential.id = fromBase64Url(credential.id);
                }

                return credential;
            });
        }

        return publicKey;
    }

    function initPasskeys() {
        const form = document.querySelector('[data-passkey-form]');
        const trigger = document.querySelector('[data-passkey-register]');

        if (!form || !trigger || !window.PublicKeyCredential || !navigator.credentials || typeof navigator.credentials.create !== 'function') {
            return;
        }

        trigger.addEventListener('click', async () => {
            const optionsJson = form.getAttribute('data-passkey-options') || '{}';
            let options;

            try {
                options = JSON.parse(optionsJson);
            } catch (_error) {
                setFormFeedback(form, 'Die Passkey-Optionen konnten nicht gelesen werden.');
                return;
            }

            try {
                trigger.setAttribute('disabled', 'disabled');
                setFormFeedback(form, 'Passkey wird vorbereitet …');

                const credential = await navigator.credentials.create({
                    publicKey: normalizePublicKeyOptions(options)
                });

                if (!credential || !credential.response || !credential.response.clientDataJSON || !credential.response.attestationObject) {
                    setFormFeedback(form, 'Der Passkey konnte nicht erstellt werden.');
                    return;
                }

                form.querySelector('input[name="client_data_json"]').value = toBase64UrlFromBufferSource(credential.response.clientDataJSON);
                form.querySelector('input[name="attestation_object"]').value = toBase64UrlFromBufferSource(credential.response.attestationObject);
                form.submit();
            } catch (error) {
                setFormFeedback(form, error && error.message ? error.message : 'Passkey-Registrierung wurde abgebrochen.');
            } finally {
                trigger.removeAttribute('disabled');
            }
        });
    }

    function initBackupCodeCopy() {
        const codes = document.querySelector('.member-backup-codes');

        if (!codes || !navigator.clipboard) {
            return;
        }

        codes.addEventListener('click', () => {
            const text = Array.from(codes.querySelectorAll('code')).map((node) => node.textContent || '').join('\n');
            navigator.clipboard.writeText(text).catch(() => undefined);
        });
    }
})();