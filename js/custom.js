/**
 * Nispaksha Awaj — Custom JavaScript
 *
 * Features:
 * - Dark mode toggle with localStorage persistence
 * - Sticky navigation on scroll
 * - Mobile menu toggle
 * - Back to top button
 * - Breaking news ticker speed adjustment
 *
 * @package Nispaksha_Child
 */

(function () {
    'use strict';

    // ============================================
    // DOM READY
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initStickyNav();
        initMobileMenu();
        initBackToTop();
        initTickerSpeed();
    });

    // ============================================
    // 1. DARK MODE
    // ============================================
    function initDarkMode() {
        const toggle = document.getElementById('dark-mode-toggle');
        if (!toggle) return;

        const body = document.body;
        const icon = toggle.querySelector('.nispaksha-dark-toggle__icon');
        const text = toggle.querySelector('.nispaksha-dark-toggle__text');

        // Check saved preference
        const savedMode = localStorage.getItem('nispaksha-dark-mode');
        if (savedMode === 'true') {
            body.classList.add('dark-mode');
            updateToggleUI(true);
        }

        toggle.addEventListener('click', function () {
            const isDark = body.classList.toggle('dark-mode');
            localStorage.setItem('nispaksha-dark-mode', isDark);
            updateToggleUI(isDark);
        });

        function updateToggleUI(isDark) {
            if (icon) icon.textContent = isDark ? '☀️' : '🌙';
            if (text) text.textContent = isDark ? 'लाइट मोड' : 'डार्क मोड';
        }
    }

    // ============================================
    // 2. STICKY NAVIGATION
    // ============================================
    function initStickyNav() {
        const nav = document.getElementById('site-navigation');
        if (!nav) return;

        const header = document.getElementById('masthead');
        const headerHeight = header ? header.offsetHeight + header.offsetTop : 150;
        let lastScroll = 0;
        let ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        function handleScroll() {
            const currentScroll = window.pageYOffset;

            if (currentScroll > headerHeight) {
                nav.classList.add('is-sticky');
                // Add padding to body to prevent content jump
                document.body.style.paddingTop = nav.offsetHeight + 'px';
            } else {
                nav.classList.remove('is-sticky');
                document.body.style.paddingTop = '0';
            }

            lastScroll = currentScroll;
        }
    }

    // ============================================
    // 3. MOBILE MENU
    // ============================================
    function initMobileMenu() {
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const closeBtn = document.getElementById('mobile-menu-close');
        const menu = document.getElementById('mobile-menu');
        const backdrop = document.getElementById('mobile-backdrop');

        if (!toggleBtn || !menu) return;

        function openMenu() {
            menu.classList.add('is-open');
            if (backdrop) backdrop.classList.add('is-visible');
            toggleBtn.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            menu.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-visible');
            toggleBtn.classList.remove('is-active');
            document.body.style.overflow = '';
        }

        toggleBtn.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (backdrop) backdrop.addEventListener('click', closeMenu);

        // Close on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && menu.classList.contains('is-open')) {
                closeMenu();
            }
        });
    }

    // ============================================
    // 4. BACK TO TOP
    // ============================================
    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;

        let ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    if (window.pageYOffset > 500) {
                        btn.classList.add('is-visible');
                    } else {
                        btn.classList.remove('is-visible');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        });

        btn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ============================================
    // 5. BREAKING NEWS TICKER SPEED
    // ============================================
    function initTickerSpeed() {
        const track = document.querySelector('.nispaksha-breaking__track');
        if (!track) return;

        // Calculate animation duration based on content width
        // More items = longer duration for readable speed
        const itemCount = track.querySelectorAll('.nispaksha-breaking__item').length;
        const duration = Math.max(30, itemCount * 5); // minimum 30s
        track.style.animationDuration = duration + 's';
    }

})();
