/**
 * Show a Romanized slug in the permalink field as the title is typed.
 * Published posts are left alone. A reporter-typed ASCII slug is kept.
 */
( function ( wp ) {
	if ( ! wp || ! wp.data || ! wp.apiFetch ) {
		return;
	}

	var lastTitle = null;
	var lastAutoSlug = '';
	var initialized = false;
	var timer = null;
	var requestId = 0;

	function isNonAscii( value ) {
		return /[^\x00-\x7F]/.test( value || '' );
	}

	function canReplace( slug ) {
		if ( ! slug ) {
			return true;
		}
		if ( isNonAscii( slug ) || /%[0-9a-f]{2}/i.test( slug ) || /^[0-9]+$/.test( slug ) ) {
			return true;
		}
		return slug === lastAutoSlug;
	}

	function isLocked( editor ) {
		var post = editor.getCurrentPost();
		if ( ! post ) {
			return true;
		}
		if ( editor.getCurrentPostType() !== 'post' ) {
			return true;
		}
		if ( editor.isCurrentPostPublished && editor.isCurrentPostPublished() ) {
			return true;
		}
		if ( [ 'publish', 'future', 'private' ].indexOf( post.status ) !== -1 && post.slug ) {
			return true;
		}
		return false;
	}

	function applySlug( title, slug ) {
		var editor = wp.data.select( 'core/editor' );
		if ( ! editor || isLocked( editor ) ) {
			return;
		}
		if ( ( editor.getEditedPostAttribute( 'title' ) || '' ) !== title ) {
			return;
		}
		if ( ! canReplace( editor.getEditedPostAttribute( 'slug' ) || '' ) ) {
			return;
		}
		lastAutoSlug = slug;
		if ( ( editor.getEditedPostAttribute( 'slug' ) || '' ) !== slug ) {
			wp.data.dispatch( 'core/editor' ).editPost( { slug: slug } );
		}
	}

	function requestSlug( title ) {
		var id = ++requestId;
		wp.apiFetch( {
			path: '/maglist-child/v1/romanize-slug?title=' + encodeURIComponent( title ),
		} ).then( function ( res ) {
			if ( id !== requestId ) {
				return;
			}
			var slug = res && res.slug ? res.slug : '';
			if ( slug ) {
				applySlug( title, slug );
			}
		} ).catch( function () {} );
	}

	function maybeUpdate() {
		var editor = wp.data.select( 'core/editor' );
		if ( ! editor || ! editor.getEditedPostAttribute ) {
			return;
		}
		if ( isLocked( editor ) ) {
			return;
		}

		var title = editor.getEditedPostAttribute( 'title' ) || '';
		var slug = editor.getEditedPostAttribute( 'slug' ) || '';

		if ( ! initialized ) {
			initialized = true;
			if ( slug && ! isNonAscii( slug ) ) {
				lastAutoSlug = slug;
			}
		}

		if ( title === lastTitle ) {
			return;
		}
		lastTitle = title;

		if ( ! title || title === 'Auto Draft' ) {
			return;
		}
		if ( ! canReplace( slug ) ) {
			return;
		}

		clearTimeout( timer );
		timer = setTimeout( function () {
			requestSlug( title );
		}, 220 );
	}

	wp.data.subscribe( maybeUpdate );
} )( window.wp );
