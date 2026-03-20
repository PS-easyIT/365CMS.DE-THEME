/**
 * CMS Phinit Theme – Consent-aware Google Analytics Loader
 */
(function () {
    'use strict';

    const currentScript = document.currentScript;
    const gaId = currentScript && currentScript.dataset ? (currentScript.dataset.gaId || '') : '';
    const isValidGaId = /^G-[A-Z0-9]{6,}$/.test(gaId);
    let analyticsInitialized = false;

    function isConsentAccepted() {
        try {
            return window.localStorage.getItem('cms-consent') === 'accepted';
        } catch (_) {
            return false;
        }
    }

    function ensureTrackingScript() {
        const source = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(gaId);
        const existing = document.querySelector('script[data-cms-ga-external="1"]');
        if (existing) {
            return;
        }

        const script = document.createElement('script');
        script.async = true;
        script.src = source;
        script.setAttribute('data-cms-ga-external', '1');
        document.head.appendChild(script);
    }

    function ensureAnalytics() {
        if (!isValidGaId || analyticsInitialized || !isConsentAccepted()) {
            return;
        }

        analyticsInitialized = true;
        ensureTrackingScript();

        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function gtag() {
            window.dataLayer.push(arguments);
        };

        window.gtag('js', new Date());
        window.gtag('config', gaId, { anonymize_ip: true });
    }

    if (isValidGaId) {
        ensureAnalytics();
        window.addEventListener('cms-cookie-consent-change', ensureAnalytics);
        window.addEventListener('storage', function (event) {
            if (event.key === 'cms-consent') {
                ensureAnalytics();
            }
        });
    }
})();