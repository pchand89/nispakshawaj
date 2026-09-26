/**
 * Sitewide header interactions: mobile nav toggle, mobile submenu
 * expand/collapse, and the fullscreen search overlay. No dependencies.
 *
 * @package Maglist_Child
 */
( function () {
	'use strict';

	function ready( fn ) {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	ready( function () {
		initDarkMode();
		initStickyLogo();
		initNavFit();
		initBackToTop();
		initNavToggle();
		initMobileSubmenus();
		initSearchOverlay();
	} );

	/**
	 * Flag <body> once the white logo header has scrolled out of view, so the
	 * sticky bars can reveal their compact logo (see site-header.css).
	 */
	function initStickyLogo() {
		var header = document.querySelector( '.na-header' );

		if ( ! header ) {
			return;
		}

		var threshold = 0;
		var ticking = false;

		function measure() {
			var rect = header.getBoundingClientRect();
			// Distance from the top of the document to the bottom of the header.
			threshold = rect.top + window.pageYOffset + rect.height;
		}

		function update() {
			ticking = false;
			var scrolled = window.pageYOffset > threshold;

			if ( scrolled !== document.body.classList.contains( 'na-scrolled' ) ) {
				document.body.classList.toggle( 'na-scrolled', scrolled );
				fitNav();
			}
		}

		measure();
		update();

		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					ticking = true;
					window.requestAnimationFrame( update );
				}
			},
			{ passive: true }
		);

		window.addEventListener( 'resize', function () {
			measure();
			update();
		} );
	}

	var NAV_FIT_MAX = 4;

	/**
	 * Keep the desktop menu (plus the compact logo once scrolled) on one line
	 * by stepping through the tighter data-na-fit levels in site-header.css
	 * until everything fits.
	 */
	function fitNav() {
		var nav = document.querySelector( '.na-nav' );
		var menu = nav && nav.querySelector( '.na-nav__menu' );

		if ( ! menu ) {
			return;
		}

		if ( window.innerWidth <= 991 ) {
			nav.removeAttribute( 'data-na-fit' );
			return;
		}

		var container = menu.parentElement;
		var style = window.getComputedStyle( container );
		var available = container.clientWidth - parseFloat( style.paddingLeft ) - parseFloat( style.paddingRight );
		var logo = document.body.classList.contains( 'na-scrolled' ) ? container.querySelector( '.na-sticky-logo--nav' ) : null;

		for ( var level = 0; level <= NAV_FIT_MAX; level++ ) {
			nav.setAttribute( 'data-na-fit', level );

			if ( navContentWidth( menu, logo ) <= available ) {
				return;
			}
		}
	}

	function navContentWidth( menu, logo ) {
		var width = 0;

		Array.prototype.forEach.call( menu.children, function ( item ) {
			width += item.getBoundingClientRect().width;
		} );

		if ( logo && logo.firstElementChild ) {
			// The logo box may still be animating open, so size it by its content.
			width += logo.firstElementChild.getBoundingClientRect().width + parseFloat( window.getComputedStyle( logo ).marginRight );
		}

		return width;
	}

	function initNavFit() {
		fitNav();

		window.addEventListener( 'resize', fitNav );
		// Web fonts and the logo image change the widths once they arrive.
		window.addEventListener( 'load', fitNav );

		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( fitNav );
		}
	}

	/**
	 * Back-to-top button; its visibility follows body.na-scrolled in CSS.
	 */
	function initBackToTop() {
		var button = document.querySelector( '[data-na-to-top]' );

		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
			window.scrollTo( { top: 0, behavior: reduceMotion ? 'auto' : 'smooth' } );
		} );
	}

	/**
	 * Persist light/dark preference on <html class="na-dark"> (also set early
	 * in header.php to avoid a flash of the wrong theme).
	 */
	function initDarkMode() {
		var toggle = document.querySelector( '[data-na-theme-toggle]' );

		if ( ! toggle ) {
			return;
		}

		var icon = toggle.querySelector( '[data-na-theme-icon]' );
		var label = toggle.querySelector( '[data-na-theme-label]' );
		var storageKey = 'na-dark-mode';

		function isDark() {
			return document.documentElement.classList.contains( 'na-dark' );
		}

		function apply( dark ) {
			document.documentElement.classList.toggle( 'na-dark', dark );
			document.body.classList.toggle( 'na-dark', dark );
			toggle.setAttribute( 'aria-pressed', dark ? 'true' : 'false' );

			if ( icon ) {
				icon.className = dark ? 'fa fa-sun-o' : 'fa fa-moon-o';
			}

			if ( label ) {
				label.textContent = dark ? 'लाइट' : 'डार्क';
			}

			try {
				localStorage.setItem( storageKey, dark ? 'true' : 'false' );
			} catch ( e ) {}
		}

		// Sync body class + button chrome with the early <html> class.
		apply( isDark() );

		toggle.addEventListener( 'click', function () {
			apply( ! isDark() );
		} );
	}

	function initNavToggle() {
		var toggle = document.querySelector( '[data-na-nav-toggle]' );

		if ( ! toggle ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var isOpen = document.body.classList.toggle( 'na-nav-open' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	}

	/**
	 * On mobile the nav is a vertical accordion: tapping a top-level link
	 * that has a submenu expands it in place instead of navigating away
	 * immediately (matches how most mobile news-site menus behave).
	 */
	function initMobileSubmenus() {
		var items = document.querySelectorAll( '.na-nav__menu > li' );

		items.forEach( function ( item ) {
			var submenu = item.querySelector( 'ul' );
			var link = item.querySelector( ':scope > a' );

			if ( ! submenu || ! link ) {
				return;
			}

			link.addEventListener( 'click', function ( event ) {
				if ( window.innerWidth > 991 ) {
					return; // Desktop: hover handles this, let the click navigate normally.
				}

				event.preventDefault();
				item.classList.toggle( 'na-nav__submenu-open' );
			} );
		} );
	}

	function initSearchOverlay() {
		var overlay = document.querySelector( '[data-na-search-overlay]' );

		if ( ! overlay ) {
			return;
		}

		document.addEventListener( 'click', function ( event ) {
			var trigger = event.target.closest( '[data-na-search-toggle]' );

			if ( ! trigger ) {
				return;
			}

			event.preventDefault();
			event.stopPropagation();

			var isOpen = document.body.classList.toggle( 'na-search-open' );
			document.body.style.overflow = isOpen ? 'hidden' : '';

			if ( isOpen ) {
				var input = overlay.querySelector( 'input[type="search"], input[type="text"], input[name="s"]' );
				if ( input ) {
					window.setTimeout( function () {
						input.focus();
					}, 50 );
				}
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				document.body.classList.remove( 'na-search-open' );
				document.body.style.overflow = '';
			}
		} );
	}
} )();
