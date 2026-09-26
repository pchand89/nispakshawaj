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
		initNavToggle();
		// Before initNavFit, so items later moved into "थप" keep their handlers.
		initMobileSubmenus();
		initStickyLogo();
		initNavFit();
		initBackToTop();
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

	var NAV_FIT_MAX = 3;
	var navMore = null;
	var navObserver = null;

	/**
	 * Keep the desktop menu (plus the compact logo once scrolled) on one line:
	 * step through the tighter data-na-fit levels in site-header.css, and if
	 * even the tightest is too wide (long labels, e.g. after Google Translate)
	 * move trailing items into a "थप" (More) dropdown.
	 */
	function fitNav() {
		var nav = document.querySelector( '.na-nav' );
		var menu = nav && nav.querySelector( '.na-nav__menu' );

		if ( ! menu ) {
			return;
		}

		// Our own DOM moves must not re-trigger the translation observer.
		if ( navObserver ) {
			navObserver.disconnect();
		}

		var more = getNavMore( menu );
		restoreNavMore( menu, more );

		if ( window.innerWidth > 991 ) {
			packNav( nav, menu, more );
		} else {
			nav.removeAttribute( 'data-na-fit' );
		}

		if ( navObserver ) {
			navObserver.observe( menu, { childList: true, characterData: true, subtree: true } );
		}
	}

	function packNav( nav, menu, more ) {
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

		var list = more.querySelector( 'ul' );
		var movable = Array.prototype.filter.call( menu.children, isMovableNavItem );

		more.classList.add( 'is-active' );

		while ( movable.length && navContentWidth( menu, logo ) > available ) {
			list.insertBefore( movable.pop(), list.firstChild );
		}
	}

	function isMovableNavItem( item ) {
		return ! item.classList.contains( 'menu-item-home' ) &&
			! item.classList.contains( 'menu-item-gtranslate' ) &&
			! item.classList.contains( 'na-nav__more' );
	}

	function getNavMore( menu ) {
		if ( ! navMore ) {
			navMore = document.createElement( 'li' );
			navMore.className = 'menu-item menu-item-has-children na-nav__more';
			navMore.innerHTML = '<a href="#" aria-haspopup="true">थप <i class="fa fa-angle-down na-nav__caret" aria-hidden="true"></i></a><ul class="sub-menu"></ul>';
			navMore.firstElementChild.addEventListener( 'click', function ( event ) {
				event.preventDefault();
			} );

			// Keep the language switch last so it is never tucked away.
			menu.insertBefore( navMore, menu.querySelector( ':scope > .menu-item-gtranslate' ) );
		}

		return navMore;
	}

	function restoreNavMore( menu, more ) {
		var list = more.querySelector( 'ul' );

		while ( list.firstElementChild ) {
			menu.insertBefore( list.firstElementChild, more );
		}

		more.classList.remove( 'is-active' );
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
		var timer = 0;

		if ( window.MutationObserver ) {
			// Google Translate swaps the menu labels in place (both ways), so
			// re-pack once it has finished rewriting them.
			navObserver = new MutationObserver( function () {
				window.clearTimeout( timer );
				timer = window.setTimeout( fitNav, 100 );
			} );
		}

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
