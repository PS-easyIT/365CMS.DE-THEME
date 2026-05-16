/**
 * CMS Phinit Theme – Consent-aware Google Analytics Loader
 */
(function () {
    'use strict';

    const currentScript = document.currentScript;
    const gaId = currentScript && currentScript.dataset ? (currentScript.dataset.gaId || '') : '';
    const isValidGaId = /^G-[A-Z0-9]{6,}$/.test(gaId);
    let analyticsInitialized = false;
    let trackingScriptScheduled = false;

    function runWhenIdle(callback) {
        const run = () => {
            if (typeof window.requestIdleCallback === 'function') {
                window.requestIdleCallback(callback, { timeout: 2000 });
                return;
            }

            window.setTimeout(callback, 1200);
        };

        if (document.readyState === 'complete') {
            run();
            return;
        }

        window.addEventListener('load', run, { once: true });
    }

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

    function scheduleTrackingScript() {
        if (trackingScriptScheduled) {
            return;
        }

        trackingScriptScheduled = true;
        runWhenIdle(ensureTrackingScript);
    }

    function ensureAnalytics() {
        if (!isValidGaId || analyticsInitialized || !isConsentAccepted()) {
            return;
        }

        analyticsInitialized = true;

        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function gtag() {
            window.dataLayer.push(arguments);
        };

        window.gtag('js', new Date());
        window.gtag('config', gaId, { anonymize_ip: true });
        scheduleTrackingScript();
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