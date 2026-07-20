/**
 * Nispaksha Awaj — Custom JS (Pixel-Perfect Ratopati Style)
 *
 * @package Nispaksha_Child
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initStickyNav();
        initBackToTop();
        initMobileNav();
        initSearchOverlay();
    });

    function initDarkMode() {
        const toggle = document.getElementById('dark-mode-toggle');
        if (!toggle) return;

        const body = document.body;
        const icon = document.getElementById('dark-mode-icon');
        const text = document.getElementById('dark-mode-text');

        const savedMode = localStorage.getItem('rp-dark-mode');
        if (savedMode === 'true') {
            body.classList.add('dark');
            if (icon) icon.textContent = '☀️';
            if (text) text.textContent = 'लाइट';
        }

        toggle.addEventListener('click', function () {
            const isDark = body.classList.toggle('dark');
            localStorage.setItem('rp-dark-mode', isDark);
            if (icon) icon.textContent = isDark ? '☀️' : '🌙';
            if (text) text.textContent = isDark ? 'लाइट' : 'डार्क';
        });
    }

    function initStickyNav() {
        const nav = document.getElementById('site-navigation');
        if (!nav) return;

        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    if (window.pageYOffset > 180) {
                        nav.classList.add('is-sticky');
                    } else {
                        nav.classList.remove('is-sticky');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;

        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 400) {
                btn.classList.add('is-visible');
            } else {
                btn.classList.remove('is-visible');
            }
        });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initMobileNav() {
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu = document.querySelector('.rp-nav__menu');
        const backdrop = document.getElementById('nav-backdrop');
        if (!toggle || !menu) return;

        function closeMenu() {
            toggle.classList.remove('is-active');
            menu.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('rp-no-scroll');
        }

        function openMenu() {
            toggle.classList.add('is-active');
            menu.classList.add('is-open');
            if (backdrop) backdrop.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('rp-no-scroll');
        }

        toggle.setAttribute('aria-expanded', 'false');
        toggle.addEventListener('click', function () {
            if (menu.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        if (backdrop) {
            backdrop.addEventListener('click', closeMenu);
        }

        // Close the drawer once a nav link is clicked (mobile only).
        menu.addEventListener('click', function (event) {
            if (event.target.closest('a') && window.innerWidth <= 768) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeMenu();
        });
    }

    function initSearchOverlay() {
        const toggle = document.getElementById('search-toggle');
        const overlay = document.getElementById('rp-search-overlay');
        const closeBtn = document.getElementById('search-close');
        if (!toggle || !overlay) return;

        function openOverlay() {
            overlay.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
            const input = overlay.querySelector('input[type="search"]');
            if (input) window.setTimeout(function () { input.focus(); }, 50);
        }

        function closeOverlay() {
            overlay.classList.remove('is-active');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', openOverlay);
        if (closeBtn) closeBtn.addEventListener('click', closeOverlay);

        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) closeOverlay();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeOverlay();
        });
    }

})();
