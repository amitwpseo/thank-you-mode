<?php
/**
 * Settings: defaults, getters, sanitising, access rules.
 *
 * @package ThankYouMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings. Everything is generic - nothing site specific.
 */
function tym_defaults() {
	$name = get_bloginfo( 'name' );
	return array(
		'enabled'         => 1,
		'design'          => 1,

		// Content.
		'logo'            => '',
		'eyebrow'         => $name,
		'headline'        => 'Thank You!',
		'message'         => "Thank you to everyone who joined us - attendees, partners, vendors, guests and supporters.\n\nStay tuned for future events. We can't wait to see you again!",
		'email'           => '',
		'contact_label'   => 'Questions?',
		'button_text'     => '',
		'button_url'      => '',
		'tagline'         => 'INSERT COIN FOR THE NEXT LEVEL',
		'stub_label'      => 'Admit One',
		'stub_text'       => 'See you next time',

		// Look.
		'color_bg'        => '',
		'color_ac'        => '',
		'color_hi'        => '',
		'bg_image'        => '',
		'bg_overlay'      => 55,

		// Rules.
		'start_at'        => '',
		'end_at'          => '',
		'status'          => 200,
		'other_urls'      => 'redirect',
		'allow_paths'     => '',
		'bypass_loggedin' => 0,
		'allow_ips'       => '',
		'block_rest'      => 1,
		'noindex'         => 0,

		// Advanced.
		'page_title'      => 'Thank You - ' . $name,
		'load_fonts'      => 1,
		'custom_css'      => '',
	);
}

/**
 * Saved settings merged over the defaults.
 */
function tym_get() {
	$saved = get_option( TYM_OPTION, array() );
	$o     = wp_parse_args( is_array( $saved ) ? $saved : array(), tym_defaults() );
	return apply_filters( 'tym_settings', $o );
}

/**
 * Effective [background, accent, highlight] for a design: custom colour if set, else the design default.
 */
function tym_colors( $d, $o, $use_custom = true ) {
	$designs = tym_designs();
	$def     = $designs[ $d ]['colors'];
	$keys    = array( 'color_bg', 'color_ac', 'color_hi' );
	$out     = array();
	foreach ( $keys as $i => $k ) {
		$c         = ( $use_custom && ! empty( $o[ $k ] ) ) ? sanitize_hex_color( $o[ $k ] ) : '';
		$out[ $i ] = $c ? $c : $def[ $i ];
	}
	return $out;
}

/**
 * Split a textarea value into trimmed, non-empty lines.
 */
function tym_lines( $text ) {
	$lines = preg_split( "/\r\n|\n|\r/", (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Convert a "Y-m-dTH:i" (datetime-local) value in the site's timezone to a timestamp. 0 = not set.
 */
function tym_ts( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return 0;
	}
	$dt = DateTime::createFromFormat( 'Y-m-d\TH:i', $value, wp_timezone() );
	return $dt ? $dt->getTimestamp() : 0;
}

/**
 * off | scheduled | live | ended
 */
function tym_state( $o = null ) {
	$o = null === $o ? tym_get() : $o;
	if ( empty( $o['enabled'] ) ) {
		return 'off';
	}
	$now   = time();
	$start = tym_ts( $o['start_at'] );
	$end   = tym_ts( $o['end_at'] );
	if ( $start && $now < $start ) {
		return 'scheduled';
	}
	if ( $end && $now >= $end ) {
		return 'ended';
	}
	return 'live';
}

/**
 * Is Thank You Mode switched on (and inside its schedule) right now, for the public?
 */
function tym_is_active( $o = null ) {
	$o = null === $o ? tym_get() : $o;
	return (bool) apply_filters( 'tym_is_active', 'live' === tym_state( $o ), $o );
}

/**
 * Visitor's IP as reported by the server. Behind a CDN/proxy this may be the proxy's address.
 */
function tym_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

function tym_ip_allowed( $o ) {
	$ip = tym_client_ip();
	return '' !== $ip && in_array( $ip, tym_lines( $o['allow_ips'] ), true );
}

/**
 * Does this visitor see the real site? (admins, optionally all logged-in users, allow-listed IPs)
 */
function tym_visitor_bypasses( $o ) {
	if ( is_user_logged_in() ) {
		if ( current_user_can( 'manage_options' ) || ! empty( $o['bypass_loggedin'] ) ) {
			return true;
		}
	}
	return tym_ip_allowed( $o );
}

/**
 * Admin design preview (?tym_preview=1) - works even when the mode is off.
 */
function tym_is_admin_preview() {
	return isset( $_GET['tym_preview'] ) && is_user_logged_in() && current_user_can( 'manage_options' ); // phpcs:ignore WordPress.Security.NonceVerification
}

/**
 * Sanitise settings on save.
 */
function tym_sanitize( $in ) {
	$d       = tym_defaults();
	$designs = tym_designs();
	$in      = is_array( $in ) ? $in : array();
	$out     = array();

	$out['enabled'] = empty( $in['enabled'] ) ? 0 : 1;
	$design         = isset( $in['design'] ) ? (int) $in['design'] : 1;
	$out['design']  = isset( $designs[ $design ] ) ? $design : 1;

	$out['logo']          = isset( $in['logo'] ) ? esc_url_raw( $in['logo'] ) : '';
	$out['eyebrow']       = isset( $in['eyebrow'] ) ? sanitize_text_field( $in['eyebrow'] ) : '';
	$out['headline']      = isset( $in['headline'] ) ? sanitize_text_field( $in['headline'] ) : $d['headline'];
	$out['message']       = isset( $in['message'] ) ? wp_kses_post( $in['message'] ) : $d['message'];
	$out['email']         = isset( $in['email'] ) ? sanitize_email( $in['email'] ) : '';
	$out['contact_label'] = isset( $in['contact_label'] ) ? sanitize_text_field( $in['contact_label'] ) : '';
	$out['button_text']   = isset( $in['button_text'] ) ? sanitize_text_field( $in['button_text'] ) : '';
	$out['button_url']    = isset( $in['button_url'] ) ? esc_url_raw( $in['button_url'] ) : '';
	$out['tagline']       = isset( $in['tagline'] ) ? sanitize_text_field( $in['tagline'] ) : '';
	$out['stub_label']    = isset( $in['stub_label'] ) ? sanitize_text_field( $in['stub_label'] ) : '';
	$out['stub_text']     = isset( $in['stub_text'] ) ? sanitize_text_field( $in['stub_text'] ) : '';

	foreach ( array( 'color_bg', 'color_ac', 'color_hi' ) as $k ) {
		$out[ $k ] = isset( $in[ $k ] ) ? (string) sanitize_hex_color( $in[ $k ] ) : '';
	}
	$out['bg_image']   = isset( $in['bg_image'] ) ? esc_url_raw( $in['bg_image'] ) : '';
	$overlay           = isset( $in['bg_overlay'] ) ? (int) $in['bg_overlay'] : (int) $d['bg_overlay'];
	$out['bg_overlay'] = max( 0, min( 90, $overlay ) );

	foreach ( array( 'start_at', 'end_at' ) as $k ) {
		$v         = isset( $in[ $k ] ) ? trim( (string) $in[ $k ] ) : '';
		$out[ $k ] = preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v ) ? $v : '';
	}
	$out['status']     = ( isset( $in['status'] ) && 503 === (int) $in['status'] ) ? 503 : 200;
	$out['other_urls'] = ( isset( $in['other_urls'] ) && 'serve' === $in['other_urls'] ) ? 'serve' : 'redirect';

	$paths = array();
	foreach ( tym_lines( isset( $in['allow_paths'] ) ? $in['allow_paths'] : '' ) as $line ) {
		$line = trim( sanitize_text_field( $line ), '/ ' );
		if ( '' !== $line ) {
			$paths[] = '/' . $line . '/';
		}
	}
	$out['allow_paths'] = implode( "\n", array_unique( $paths ) );

	$ips = array();
	foreach ( tym_lines( isset( $in['allow_ips'] ) ? $in['allow_ips'] : '' ) as $line ) {
		if ( false !== filter_var( $line, FILTER_VALIDATE_IP ) ) {
			$ips[] = $line;
		}
	}
	$out['allow_ips'] = implode( "\n", array_unique( $ips ) );

	$out['bypass_loggedin'] = empty( $in['bypass_loggedin'] ) ? 0 : 1;
	$out['block_rest']      = empty( $in['block_rest'] ) ? 0 : 1;
	$out['noindex']         = empty( $in['noindex'] ) ? 0 : 1;
	$out['load_fonts']      = empty( $in['load_fonts'] ) ? 0 : 1;

	$out['page_title'] = isset( $in['page_title'] ) ? sanitize_text_field( $in['page_title'] ) : $d['page_title'];
	$out['custom_css'] = isset( $in['custom_css'] ) ? wp_strip_all_tags( (string) $in['custom_css'] ) : '';

	return $out;
}
