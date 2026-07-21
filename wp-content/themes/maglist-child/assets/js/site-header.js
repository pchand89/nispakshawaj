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
		initNavToggle();
		initMobileSubmenus();
		initSearchOverlay();
	} );

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
