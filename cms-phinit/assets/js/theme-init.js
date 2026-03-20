/**
 * CMS Phinit Theme – Early Head Init
 * Läuft bewusst früh im <head>, um Dark-Mode-Flicker zu vermeiden.
 */
(function () {
    'use strict';

    try {
        var storageKey = 'cms365-theme';
        var legacyStorageKey = 'cms-phinit-theme';
        var storedTheme = localStorage.getItem(storageKey);

        if (storedTheme === null) {
            storedTheme = localStorage.getItem(legacyStorageKey);
            if (storedTheme !== null) {
                localStorage.setItem(storageKey, storedTheme);
            }
        }

        if (storedTheme === 'dark') {
            document.documentElement.classList.add('dark-mode');
            document.addEventListener('DOMContentLoaded', function () {
                if (document.body) {
                    document.body.classList.add('dark-mode');
                }
            }, { once: true });
        }
    } catch (error) {
        // Storage kann im Privacy-Kontext gesperrt sein – dann still abbrechen.
    }
})();