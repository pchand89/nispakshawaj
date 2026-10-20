/**
 * Festival greeting splash. Shown on every page load, never to bots or
 * visitors landing from a search engine (Google's intrusive interstitial
 * guidelines). `?na-splash=1` forces it for previewing.
 *
 * @package Maglist_Child
 */
( function () {
	'use strict';

	var template = document.getElementById( 'na-splash-template' );
	if ( ! template || ! ( 'content' in template ) ) {
		return;
	}

	var params = new URLSearchParams( window.location.search );
	var forced = params.get( 'na-splash' ) === '1';

	if ( ! forced && ! shouldShow() ) {
		return;
	}

	function shouldShow() {
		if ( params.get( 'na-splash' ) === '0' ) {
			return false;
		}
		if ( Date.now() > Number( template.getAttribute( 'data-ends' ) ) ) {
			return false;
		}
		if ( /bot|crawl|spider|slurp|lighthouse|headless|facebookexternalhit|whatsapp|telegram/i.test( navigator.userAgent ) ) {
			return false;
		}
		return ! /(^|\.)(google|bing|yahoo|duckduckgo|yandex|baidu)\./i.test( referrerHost() );
	}

	function referrerHost() {
		try {
			return document.referrer ? new URL( document.referrer ).hostname : '';
		} catch ( e ) {
			return '';
		}
	}

	var splash;
	var lastFocus;

	function open() {
		document.body.appendChild( template.content.cloneNode( true ) );
		splash = document.body.querySelector( '.na-splash' );
		lastFocus = document.activeElement;

		document.documentElement.classList.add( 'na-splash-open' );
		splash.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-na-splash-close]' ) ) {
				close();
			}
		} );
		splash.querySelector( '.na-splash__timer' ).addEventListener( 'animationend', close );
		document.addEventListener( 'keydown', onKeydown );

		window.requestAnimationFrame( function () {
			splash.classList.add( 'is-open' );
			splash.querySelector( '.na-splash__card' ).focus( { preventScroll: true } );
		} );
	}

	function close() {
		if ( ! splash || splash.classList.contains( 'is-leaving' ) ) {
			return;
		}
		splash.classList.add( 'is-leaving' );
		document.removeEventListener( 'keydown', onKeydown );
		document.documentElement.classList.remove( 'na-splash-open' );

		window.setTimeout( function () {
			splash.remove();
			if ( lastFocus && lastFocus.focus ) {
				lastFocus.focus( { preventScroll: true } );
			}
		}, 320 );
	}

	function onKeydown( event ) {
		if ( event.key === 'Escape' ) {
			close();
			return;
		}
		if ( event.key !== 'Tab' ) {
			return;
		}
		splash.classList.add( 'is-paused' );
		var focusable = splash.querySelectorAll( 'button' );
		var first = focusable[ 0 ];
		var last = focusable[ focusable.length - 1 ];
		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			window.setTimeout( open, 400 );
		} );
	} else {
		window.setTimeout( open, 400 );
	}
}() );
