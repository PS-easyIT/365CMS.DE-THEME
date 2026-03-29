/**
 * Navigation - Mobile Menu & Search Overlay
 *
 * @package IT_Expert_Network_Theme
 */

(function () {
    'use strict';

    function syncBodyScrollLock() {
        const shouldLock = Boolean(document.querySelector('.mobile-menu-drawer.is-open, .search-overlay.is-active, .directory-filters.is-open'));
        document.body.classList.toggle('body-scroll-locked', shouldLock);
    }

    function getFocusableElements(container) {
        if (!container) {
            return [];
        }

        return Array.from(container.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'));
    }

    function focusFirstElement(container) {
        const focusableElements = getFocusableElements(container);
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
            return;
        }

        if (typeof container.focus === 'function') {
            container.focus();
        }
    }

    function trapFocus(container, event) {
        const focusableElements = getFocusableElements(container);
        if (focusableElements.length === 0) {
            event.preventDefault();
            if (typeof container.focus === 'function') {
                container.focus();
            }
            return;
        }

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        const activeElement = document.activeElement;

        if (event.shiftKey && (activeElement === firstElement || activeElement === container)) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    }

    function init() {
        let lastMenuFocus = null;
        let lastSearchFocus = null;

        const header = document.getElementById('masthead');
        if (header) {
            window.addEventListener('scroll', function () {
                header.classList.toggle('scrolled', window.scrollY > 80);
            }, { passive: true });
        }

        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileDrawer = document.getElementById('mobileMenuDrawer');
        const mobileOverlay = document.getElementById('mobileMenuOverlay');

        const profileToggle = document.getElementById('profileToggle');
        const profileMenu = document.getElementById('profileMenu');

        const searchToggle = document.getElementById('searchToggle');
        const searchOverlay = document.getElementById('searchOverlay');
        const searchClose = document.getElementById('searchOverlayClose');

        function openMobileMenu() {
            if (!mobileDrawer || !mobileOverlay || !mobileToggle) {
                return;
            }

            lastMenuFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            mobileDrawer.classList.add('is-open');
            mobileOverlay.classList.add('is-open');
            mobileToggle.classList.add('is-active');
            mobileToggle.setAttribute('aria-expanded', 'true');
            mobileDrawer.setAttribute('aria-hidden', 'false');
            syncBodyScrollLock();

            window.requestAnimationFrame(function () {
                focusFirstElement(mobileDrawer);
            });
        }

        function closeMobileMenu() {
            if (!mobileDrawer || !mobileOverlay || !mobileToggle) {
                return;
            }

            mobileDrawer.classList.remove('is-open');
            mobileOverlay.classList.remove('is-open');
            mobileToggle.classList.remove('is-active');
            mobileToggle.setAttribute('aria-expanded', 'false');
            mobileDrawer.setAttribute('aria-hidden', 'true');
            syncBodyScrollLock();

            if (lastMenuFocus && typeof lastMenuFocus.focus === 'function') {
                lastMenuFocus.focus();
            } else {
                mobileToggle.focus();
            }
        }

        function openProfileDropdown() {
            if (!profileMenu || !profileToggle) {
                return;
            }

            profileMenu.classList.add('is-open');
            profileMenu.setAttribute('aria-hidden', 'false');
            profileToggle.setAttribute('aria-expanded', 'true');
        }

        function closeProfileDropdown() {
            if (!profileMenu || !profileToggle) {
                return;
            }

            profileMenu.classList.remove('is-open');
            profileMenu.setAttribute('aria-hidden', 'true');
            profileToggle.setAttribute('aria-expanded', 'false');
        }

        function openSearchOverlay() {
            if (!searchOverlay) {
                return;
            }

            lastSearchFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            searchOverlay.classList.add('is-active');
            searchOverlay.setAttribute('aria-hidden', 'false');
            if (searchToggle) {
                searchToggle.setAttribute('aria-expanded', 'true');
            }
            syncBodyScrollLock();

            window.requestAnimationFrame(function () {
                const input = searchOverlay.querySelector('input[type="search"]');
                if (input) {
                    input.focus();
                    return;
                }

                focusFirstElement(searchOverlay);
            });
        }

        function closeSearchOverlay() {
            if (!searchOverlay) {
                return;
            }

            searchOverlay.classList.remove('is-active');
            searchOverlay.setAttribute('aria-hidden', 'true');
            if (searchToggle) {
                searchToggle.setAttribute('aria-expanded', 'false');
            }
            syncBodyScrollLock();

            if (lastSearchFocus && typeof lastSearchFocus.focus === 'function') {
                lastSearchFocus.focus();
            } else if (searchToggle) {
                searchToggle.focus();
            }
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function () {
                if (mobileDrawer && mobileDrawer.classList.contains('is-open')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });
        }

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileMenu);
        }

        if (profileToggle) {
            profileToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                if (profileMenu && profileMenu.classList.contains('is-open')) {
                    closeProfileDropdown();
                } else {
                    openProfileDropdown();
                }
            });
        }

        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                closeProfileDropdown();
            }
        });

        if (searchToggle) {
            searchToggle.addEventListener('click', openSearchOverlay);
        }

        if (searchClose) {
            searchClose.addEventListener('click', closeSearchOverlay);
        }

        if (searchOverlay) {
            searchOverlay.addEventListener('click', function (event) {
                if (event.target === searchOverlay) {
                    closeSearchOverlay();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMobileMenu();
                closeSearchOverlay();
                closeProfileDropdown();
                return;
            }

            if (event.key === 'Tab') {
                if (searchOverlay && searchOverlay.classList.contains('is-active')) {
                    trapFocus(searchOverlay, event);
                    return;
                }

                if (mobileDrawer && mobileDrawer.classList.contains('is-open')) {
                    trapFocus(mobileDrawer, event);
                }
            }
        });

        initFilterDrawer();
    }

    function initFilterDrawer() {
        const toggleBtn = document.getElementById('mobileFilterToggle');
        const overlay = document.getElementById('mobileFilterOverlay');
        const filters = document.getElementById('directoryFilters');
        const closeBtn = document.getElementById('filterDrawerClose');
        const applyBtn = document.getElementById('filterDrawerApply');

        if (!toggleBtn || !filters) {
            return;
        }

        function openDrawer() {
            filters.classList.add('is-open');
            if (overlay) {
                overlay.classList.add('is-visible');
            }
            document.body.classList.add('filter-drawer-open');
            toggleBtn.setAttribute('aria-expanded', 'true');

            if (typeof filters.focus === 'function') {
                filters.focus();
            }
            syncBodyScrollLock();
        }

        function closeDrawer() {
            filters.classList.remove('is-open');
            if (overlay) {
                overlay.classList.remove('is-visible');
            }
            document.body.classList.remove('filter-drawer-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleBtn.focus();
            syncBodyScrollLock();
        }

        toggleBtn.addEventListener('click', function () {
            if (filters.classList.contains('is-open')) {
                closeDrawer();
            } else {
                openDrawer();
            }
        });

        if (overlay) {
            overlay.addEventListener('click', closeDrawer);
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', closeDrawer);
        }
        if (applyBtn) {
            applyBtn.addEventListener('click', closeDrawer);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && filters.classList.contains('is-open')) {
                closeDrawer();
            }
        });

        if (window.matchMedia) {
            const drawerMediaQuery = window.matchMedia('(min-width:1024px)');
            const onDrawerBreakpointChange = function (mediaQuery) {
                if (mediaQuery.matches) {
                    closeDrawer();
                }
            };

            if (typeof drawerMediaQuery.addEventListener === 'function') {
                drawerMediaQuery.addEventListener('change', onDrawerBreakpointChange);
            } else if (typeof drawerMediaQuery.addListener === 'function') {
                drawerMediaQuery.addListener(onDrawerBreakpointChange);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

}());
