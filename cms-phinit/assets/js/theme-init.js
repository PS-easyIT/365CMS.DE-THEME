/**
 * CMS Phinit Theme – Early Head Init
 * Läuft bewusst früh im <head>, um Dark-Mode-Flicker zu vermeiden.
 */
(function () {
    'use strict';

    try {
        var storageKey = 'phinit_theme';
        var legacyStorageKeys = ['cms365-theme', 'cms-phinit-theme'];
        var storedTheme = localStorage.getItem(storageKey);

        if (storedTheme !== 'dark' && storedTheme !== 'light') {
            storedTheme = null;
            for (var index = 0; index < legacyStorageKeys.length; index++) {
                var legacyTheme = localStorage.getItem(legacyStorageKeys[index]);
                if (legacyTheme === 'dark' || legacyTheme === 'light') {
                    storedTheme = legacyTheme;
                    localStorage.setItem(storageKey, legacyTheme);
                    break;
                }
            }
        }

        if (storedTheme === 'dark') {
            document.documentElement.classList.add('dark-mode');
            document.documentElement.classList.remove('light-mode');
        } else if (storedTheme === 'light') {
            document.documentElement.classList.remove('dark-mode');
            document.documentElement.classList.add('light-mode');
        }
    } catch (error) {
        // Storage kann im Privacy-Kontext gesperrt sein – dann still abbrechen.
    }
})();