/**
 * Reporter guidance above the Categories panel in the post editor.
 * Data (term IDs) comes from inc/editor-category-guide.php as naCategoryGuide.
 */
( function ( wp, cfg ) {
	if ( ! wp || ! wp.hooks || ! wp.element || ! wp.data || ! cfg ) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;

	function hasAny( selected, ids ) {
		return selected.some( function ( id ) {
			return ids.indexOf( id ) !== -1;
		} );
	}

	function Pill( ok, label ) {
		return el(
			'span',
			{ className: 'na-cat-guide__pill ' + ( ok ? 'is-ok' : 'is-missing' ) },
			( ok ? '✓ ' : '✗ ' ) + label
		);
	}

	function Guide() {
		var selected = wp.data.useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'categories' ) || [];
		}, [] );

		var hasPlace = hasAny( selected, cfg.placeIds );
		var hasTopic = hasAny( selected, cfg.topicIds );
		var hasUncat = hasAny( selected, cfg.uncatIds );

		return el(
			'div',
			{ className: 'na-cat-guide' },
			el( 'p', { className: 'na-cat-guide__title' }, 'संवाददाताका लागि नियम:' ),
			el(
				'p',
				{ className: 'na-cat-guide__rule' },
				'हरेक समाचारमा एउटा स्थान (सुदूरपश्चिमभित्रको जिल्ला जस्तै कञ्चनपुर, वा सुदूरपश्चिम, राष्ट्रिय, अन्तर्राष्ट्रिय) र एउटा विषय (राजनीति, समाज, खेलकुद आदि) अनिवार्य रूपमा छान्नुपर्नेछ।'
			),
			el(
				'ul',
				null,
				el( 'li', null, 'स्थान: कञ्चनपुर लगायत सबै जिल्ला "सुदूरपश्चिम" भित्र छन्। घटना भएको जिल्ला छान्नुहोस्; समाचार सुदूरपश्चिममा आफैं देखिन्छ, "सुदूरपश्चिम" छुट्टै टिक गर्नु पर्दैन। प्रदेशभरिको समाचार भए मात्र "सुदूरपश्चिम" छान्नुहोस्।' ),
				el( 'li', null, 'प्रदेश बाहिर नेपालभित्रको समाचार → "राष्ट्रिय", विदेशको समाचार → "अन्तर्राष्ट्रिय"।' ),
				el( 'li', null, 'विषय: मिल्ने उपविषय भए त्यही छान्नुहोस् (जस्तै स्वास्थ्यको समाचार → "स्वास्थ्य, विज्ञान र प्रविधि")। उपविषय छानेपछि मूल विषयमा पनि आफैं देखिन्छ।' ),
				el( 'li', null, 'खेलकुदको समाचारमा "मनोरञ्जन" र राजनीतिको समाचारमा "स्थानीय तह/विकास" नछान्नुहोस्।' ),
				el( 'li', null, '"Uncategorized" कहिल्यै नछान्नुहोस्।' )
			),
			el(
				'div',
				{ className: 'na-cat-guide__status' },
				Pill( hasPlace, 'स्थान' ),
				Pill( hasTopic, 'विषय' ),
				hasUncat ? Pill( false, 'Uncategorized हटाउनुहोस्' ) : null
			)
		);
	}

	wp.hooks.addFilter( 'editor.PostTaxonomyType', 'maglist-child/category-guide', function ( OriginalComponent ) {
		return function ( props ) {
			if ( props.slug !== 'category' ) {
				return el( OriginalComponent, props );
			}
			return el( Fragment, null, el( Guide ), el( OriginalComponent, props ) );
		};
	} );
} )( window.wp, window.naCategoryGuide );
