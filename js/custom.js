/**
 * Nispaksha Awaj — Custom JavaScript (Ratopati Style)
 *
 * @package Nispaksha_Child
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initSearchToggle();
        initStickyNav();
        initMobileMenu();
        initBackToTop();
    });

    // Dark Mode Toggle
    function initDarkMode() {
        const toggle = document.getElementById('dark-mode-toggle');
        if (!toggle) return;

        const body = document.body;
        const icon = document.getElementById('dark-mode-icon');
        const text = document.getElementById('dark-mode-text');

        const savedMode = localStorage.getItem('nispaksha-dark-mode');
        if (savedMode === 'true') {
            body.classList.add('dark-mode');
            if (icon) icon.textContent = '☀️';
            if (text) text.textContent = 'लाइट';
        }

        toggle.addEventListener('click', function () {
            const isDark = body.classList.toggle('dark-mode');
            localStorage.setItem('nispaksha-dark-mode', isDark);
            if (icon) icon.textContent = isDark ? '☀️' : '🌙';
            if (text) text.textContent = isDark ? 'लाइट' : 'डार्क';
        });
    }

    // Search Toggle Overlay
    function initSearchToggle() {
        const btn = document.getElementById('search-toggle');
        const overlay = document.getElementById('search-overlay');
        if (!btn || !overlay) return;

        btn.addEventListener('click', function () {
            overlay.classList.toggle('is-open');
            const input = overlay.querySelector('input');
            if (input && overlay.classList.contains('is-open')) {
                input.focus();
            }
        });
    }

    // Sticky Nav
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

    // Mobile Menu Drawer
    function initMobileMenu() {
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const closeBtn = document.getElementById('mobile-menu-close');
        const menu = document.getElementById('mobile-menu');
        const backdrop = document.getElementById('mobile-backdrop');

        if (!toggleBtn || !menu) return;

        function openMenu() {
            menu.classList.add('is-open');
            if (backdrop) backdrop.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            menu.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-visible');
            document.body.style.overflow = '';
        }

        toggleBtn.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (backdrop) backdrop.addEventListener('click', closeMenu);
    }

    // Back to top
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

})();
