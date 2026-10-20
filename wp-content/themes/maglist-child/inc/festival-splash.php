<?php
/**
 * Festival greeting splash (बडा दशैं, तिहार तथा छठ) shown to readers on open.
 *
 * Pages are served from SpeedyCache, so the markup is printed for every
 * request inside the festival window and the browser decides whether to show
 * it (every page load, never for search-engine landings or bots). The markup
 * lives in a <template> so it is not rendered or indexed as page content.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Festival window and copy. Dates are Asia/Kathmandu calendar days, inclusive.
 *
 * @return array{start:string,end:string,festival:string,audience:string,title:string,kicker:string,signature:string,cta:string}
 */
function maglist_child_festival_splash_config() {
	return apply_filters(
		'maglist_child_festival_splash_config',
		array(
			'start'     => '2026-10-11',
			'end'       => '2026-11-15',
			'festival'  => 'बडा दशैं, तिहार तथा छठ पर्व २०८३ को शुभ अवसरमा',
			'audience'  => 'सम्पूर्ण पाठक वर्ग, विज्ञापनदाता, शुभचिन्तक तथा देश-विदेशमा रहनुहुने सम्पूर्ण नेपाली दाजुभाइ तथा दिदीबहिनीहरूमा',
			'title'     => 'हार्दिक मंगलमय शुभकामना',
			'kicker'    => 'शुभेच्छुक',
			'signature' => 'निश्पक्ष आवाज परिवार',
			'cta'       => 'समाचार पढ्नुहोस्',
		)
	);
}

/**
 * Customizer: on/off switch for the splash (Appearance → Customize → Festival Splash).
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function maglist_child_festival_splash_customize_register( $wp_customize ) {
	$config = maglist_child_festival_splash_config();

	$wp_customize->add_section(
		'maglist_child_festival_splash',
		array(
			'title'       => __( 'Festival Splash (शुभकामना)', 'maglist-child' ),
			'description' => sprintf(
				/* translators: 1: start date, 2: end date. */
				__( 'Festival greeting shown to readers on every page visit between %1$s and %2$s (Nepal time). Search-engine visitors and bots never see it. Saving clears the SpeedyCache page cache so the change goes live at once. Preview any page with ?na-splash=1.', 'maglist-child' ),
				$config['start'],
				$config['end']
			),
			'priority'    => 166,
		)
	);

	$wp_customize->add_setting(
		'maglist_child_festival_splash',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $value ) {
				return (bool) $value;
			},
		)
	);
	$wp_customize->add_control(
		'maglist_child_festival_splash',
		array(
			'label'   => __( 'Show festival greeting splash', 'maglist-child' ),
			'section' => 'maglist_child_festival_splash',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'maglist_child_festival_splash_customize_register' );

/**
 * Cached pages still carry the old on/off state, so purge them when it changes.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function maglist_child_festival_splash_purge_cache( $wp_customize ) {
	if ( ! array_key_exists( 'maglist_child_festival_splash', $wp_customize->unsanitized_post_values() ) ) {
		return;
	}
	if ( class_exists( '\SpeedyCache\Delete' ) ) {
		\SpeedyCache\Delete::run( array() );
	}
}
add_action( 'customize_save_after', 'maglist_child_festival_splash_purge_cache' );

/**
 * Whether the splash should be printed on this request.
 *
 * @return bool
 */
function maglist_child_festival_splash_active() {
	if ( ! get_theme_mod( 'maglist_child_festival_splash', true ) ) {
		return false;
	}
	if ( is_admin() || is_feed() || is_embed() || is_customize_preview() || wp_doing_ajax() ) {
		return false;
	}

	$config = maglist_child_festival_splash_config();
	$today  = wp_date( 'Y-m-d', null, new DateTimeZone( 'Asia/Kathmandu' ) );

	return $today >= $config['start'] && $today <= $config['end'];
}

/**
 * Enqueue splash CSS/JS only inside the festival window.
 */
function maglist_child_festival_splash_assets() {
	if ( ! maglist_child_festival_splash_active() ) {
		return;
	}

	wp_enqueue_style(
		'maglist-child-festival-splash',
		MAGLIST_CHILD_URI . '/assets/css/festival-splash.css',
		array( 'maglist-child-style' ),
		MAGLIST_CHILD_VERSION
	);

	wp_enqueue_script(
		'maglist-child-festival-splash',
		MAGLIST_CHILD_URI . '/assets/js/festival-splash.js',
		array(),
		MAGLIST_CHILD_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'maglist_child_festival_splash_assets', 30 );

/**
 * Inline artwork from assets/img/festival-splash/ (toran, cultural scene).
 *
 * @param string $name File name without extension.
 * @return string
 */
function maglist_child_festival_splash_art( $name ) {
	$file = MAGLIST_CHILD_DIR . '/assets/img/festival-splash/' . $name . '.svg';

	return is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}

/**
 * Nepali changa (diamond kite) with its string trailing off-screen.
 *
 * @param string $a Colour of the top-left / bottom-right panels.
 * @param string $b Colour of the other two panels.
 * @return string
 */
function maglist_child_festival_splash_kite( $a, $b ) {
	return '<svg viewBox="0 0 90 200" focusable="false">'
		. '<path d="M45,52 C40,110 24,150 -10,204" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="1.2"/>'
		. '<g class="na-splash__kite-body">'
		. sprintf( '<path d="M45,4 L45,45 L12,40 Z M45,45 L78,40 L45,86 Z" fill="%s"/>', esc_attr( $a ) )
		. sprintf( '<path d="M45,4 L78,40 L45,45 Z M45,45 L45,86 L12,40 Z" fill="%s"/>', esc_attr( $b ) )
		. '<path d="M45,4 L78,40 L45,86 L12,40 Z" fill="none" stroke="#fff" stroke-width="1.6" stroke-linejoin="round"/>'
		. '<path d="M45,4 V86 M14,40 Q45,22 76,40" fill="none" stroke="#7c2d12" stroke-width="1.4"/>'
		. sprintf( '<path d="M45,86 L36,100 H54 Z" fill="%s"/>', esc_attr( $a ) )
		. '</g></svg>';
}

/**
 * Firework burst (rays + dots) for the backdrop.
 *
 * @param string $color Burst colour.
 * @return string
 */
function maglist_child_festival_splash_burst( $color ) {
	$rays = '';
	for ( $a = 0; $a < 360; $a += 30 ) {
		$rays .= sprintf( '<path d="M0,-14 V-38" transform="rotate(%d)"/><circle cx="0" cy="-45" r="2.6" transform="rotate(%d)"/>', $a, $a + 15 );
	}

	return sprintf(
		'<svg viewBox="-50 -50 100 100" focusable="false"><g stroke="%1$s" fill="%1$s" stroke-width="3" stroke-linecap="round">%2$s</g></svg>',
		esc_attr( $color ),
		$rays
	);
}

/**
 * Falling marigold petals, spread deterministically so cached pages match.
 *
 * @return string
 */
function maglist_child_festival_splash_petals() {
	$colors = array( '#f97316', '#facc15', '#ef4444', '#fb923c', '#f59e0b' );
	$out    = '';

	for ( $i = 0; $i < 22; $i++ ) {
		$out .= sprintf(
			'<i class="na-splash__petal" style="--x:%d%%;--t:%.1fs;--d:-%.1fs;--c:%s;--s:%.2f"></i>',
			( $i * 37 + 7 ) % 100,
			7 + ( $i * 13 ) % 6,
			( $i * 7 ) % 11 + 0.3,
			$colors[ $i % 5 ],
			0.7 + ( ( $i * 11 ) % 6 ) / 10
		);
	}

	return $out;
}

/**
 * Print the splash template in the footer.
 */
function maglist_child_festival_splash_markup() {
	if ( ! maglist_child_festival_splash_active() ) {
		return;
	}

	$config  = maglist_child_festival_splash_config();
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$end     = new DateTimeImmutable( $config['end'] . ' 23:59:59', new DateTimeZone( 'Asia/Kathmandu' ) );
	?>
	<template id="na-splash-template" data-ends="<?php echo esc_attr( $end->getTimestamp() * 1000 ); ?>">
		<div class="na-splash" role="dialog" aria-modal="true" aria-labelledby="na-splash-title" aria-describedby="na-splash-message" data-nosnippet>
			<div class="na-splash__backdrop" data-na-splash-close></div>
			<div class="na-splash__sky" aria-hidden="true">
				<span class="na-splash__kite na-splash__kite--1"><?php echo maglist_child_festival_splash_kite( '#dc2626', '#facc15' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="na-splash__kite na-splash__kite--2"><?php echo maglist_child_festival_splash_kite( '#2563eb', '#f8fafc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="na-splash__kite na-splash__kite--3"><?php echo maglist_child_festival_splash_kite( '#16a34a', '#f97316' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="na-splash__burst na-splash__burst--1"><?php echo maglist_child_festival_splash_burst( '#facc15' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="na-splash__burst na-splash__burst--2"><?php echo maglist_child_festival_splash_burst( '#fb7185' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="na-splash__burst na-splash__burst--3"><?php echo maglist_child_festival_splash_burst( '#38bdf8' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="na-splash__burst na-splash__burst--4"><?php echo maglist_child_festival_splash_burst( '#f97316' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php echo maglist_child_festival_splash_petals(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="na-splash__card" tabindex="-1">
				<?php echo maglist_child_festival_splash_art( 'toran' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<button type="button" class="na-splash__close" data-na-splash-close aria-label="<?php esc_attr_e( 'बन्द गर्नुहोस्', 'maglist-child' ); ?>">&times;</button>

				<span class="na-splash__spark na-splash__spark--1" aria-hidden="true"></span>
				<span class="na-splash__spark na-splash__spark--2" aria-hidden="true"></span>
				<span class="na-splash__spark na-splash__spark--3" aria-hidden="true"></span>
				<span class="na-splash__spark na-splash__spark--4" aria-hidden="true"></span>

				<div class="na-splash__body">
					<div class="na-splash__logo">
						<?php
						if ( $logo_id ) {
							echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$logo_id,
								'medium',
								false,
								array(
									'class'   => 'no-lazyload skip-lazy',
									'alt'     => get_bloginfo( 'name' ),
									'loading' => 'eager',
								)
							);
						} else {
							echo esc_html( get_bloginfo( 'name' ) );
						}
						?>
					</div>

					<div id="na-splash-message">
						<p class="na-splash__festival"><?php echo esc_html( $config['festival'] ); ?></p>
						<p class="na-splash__audience"><?php echo esc_html( $config['audience'] ); ?></p>
						<h2 id="na-splash-title" class="na-splash__title"><?php echo esc_html( $config['title'] ); ?></h2>
					</div>

					<?php echo maglist_child_festival_splash_art( 'scene' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

					<p class="na-splash__kicker"><span><?php echo esc_html( $config['kicker'] ); ?></span></p>
					<p class="na-splash__signature"><?php echo esc_html( $config['signature'] ); ?></p>

					<button type="button" class="na-splash__cta" data-na-splash-close>
						<?php echo esc_html( $config['cta'] ); ?>
						<i class="fa fa-angle-right" aria-hidden="true"></i>
					</button>
				</div>

				<span class="na-splash__timer" aria-hidden="true"></span>
			</div>
		</div>
	</template>
	<?php
}
add_action( 'wp_footer', 'maglist_child_festival_splash_markup', 5 );
