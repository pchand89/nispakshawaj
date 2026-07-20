/**
 * Minimal homepage enhancements for the Ratopati-style layout.
 * No external dependencies (jQuery not required) - only loaded on the
 * homepage / "Ratopati Home Layout" page template.
 *
 * @package Maglist_Child
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		initScrollFade();
		initFixedTab();
	} );

	/**
	 * Fade cards in as they scroll into view. No-JS visitors always see full
	 * content immediately (class is only added here, JS-only).
	 */
	function initScrollFade() {
		var cards = document.querySelectorAll(
			'.rp-breaking__item, .rp-lead-card, .rp-thumb-card'
		);

		if ( ! ( 'IntersectionObserver' in window ) || ! cards.length ) {
			return; // Graceful no-op on very old browsers; layout still works fine.
		}

		var observer = new IntersectionObserver(
			function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						obs.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.1 }
		);

		cards.forEach( function ( card ) {
			card.classList.add( 'ratopati-fade-init' );
			observer.observe( card );
		} );
	}

	/**
	 * Floating "ताजा / लोकप्रिय" panel: open/close + switch between its two tabs.
	 */
	function initFixedTab() {
		var widget = document.querySelector( '[data-maglist-child-fixed-tab]' );

		if ( ! widget ) {
			return;
		}

		widget.querySelectorAll( '.rp-fixed-tab__btn' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				setActiveTab( widget, btn.getAttribute( 'data-tab-target' ) );
				widget.classList.add( 'is-open' );
			} );
		} );

		var closeBtn = widget.querySelector( '.rp-fixed-tab__close' );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', function () {
				widget.classList.remove( 'is-open' );
			} );
		}

		widget.querySelectorAll( '.rp-fixed-tab__tab' ).forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				setActiveTab( widget, tab.getAttribute( 'data-tab-target' ) );
			} );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				widget.classList.remove( 'is-open' );
			}
		} );
	}

	/**
	 * Sync both the top pill buttons and the in-panel tab buttons/content to
	 * the given tab key ('taja' or 'lokpriya').
	 *
	 * @param {Element} widget Root .rp-fixed-tab element.
	 * @param {string}  key    Tab key to activate.
	 */
	function setActiveTab( widget, key ) {
		widget.querySelectorAll( '[data-tab-target]' ).forEach( function ( el ) {
			el.classList.toggle( 'is-active', el.getAttribute( 'data-tab-target' ) === key );
		} );
		widget.querySelectorAll( '[data-tab-content]' ).forEach( function ( el ) {
			el.classList.toggle( 'is-active', el.getAttribute( 'data-tab-content' ) === key );
		} );
	}
} )();
