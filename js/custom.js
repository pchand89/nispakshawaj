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

})();
