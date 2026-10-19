<?php
/**
 * Front end: gate the site, render the Thank You page, lock down REST/XML-RPC.
 *
 * @package ThankYouMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Decide whether to serve the Thank You page for this request.
 */
add_action( 'template_redirect', 'tym_maybe_serve', 1 );
function tym_maybe_serve() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || is_robots() ) {
		return;
	}

	$o       = tym_get();
	$preview = tym_is_admin_preview();

	if ( ! $preview ) {
		if ( ! tym_is_active( $o ) ) {
			return;
		}
		if ( tym_visitor_bypasses( $o ) ) {
			return;
		}
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore
	$req  = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	$rel  = $req;
	if ( '' !== $home && 0 === strpos( $req, $home ) ) {
		$rel = trim( substr( $req, strlen( $home ) ), '/' );
	}
	$is_home = ( $req === $home );

	if ( ! $preview ) {
		// Paths that stay public (e.g. /privacy-policy/).
		foreach ( tym_lines( $o['allow_paths'] ) as $p ) {
			$p = trim( $p, '/ ' );
			if ( '' !== $p && ( $rel === $p || 0 === strpos( $rel, $p . '/' ) ) ) {
				return;
			}
		}
		// Everything else goes to the front page.
		if ( ! $is_home && 'redirect' === $o['other_urls'] ) {
			wp_redirect( home_url( '/' ), 302 ); // phpcs:ignore WordPress.Security.SafeRedirect
			exit;
		}
	}

	tym_render_page( $o, $preview );
	exit;
}

/**
 * Output the standalone Thank You page (no theme header, menu or footer).
 */
function tym_render_page( $o, $preview = false ) {
	$designs = tym_designs();

	$d          = isset( $designs[ (int) $o['design'] ] ) ? (int) $o['design'] : 1;
	$use_custom = true;
	// Admins can preview any design with ?tym_design=N (a different design ignores custom colours).
	if ( $preview && isset( $_GET['tym_design'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$want = (int) $_GET['tym_design']; // phpcs:ignore
		if ( isset( $designs[ $want ] ) ) {
			if ( $want !== $d ) {
				$use_custom = false;
			}
			$d = $want;
		}
	}

	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();

	if ( ! $preview && 503 === (int) $o['status'] ) {
		status_header( 503 );
		header( 'Retry-After: 3600' );
	} else {
		status_header( 200 );
	}
	header( 'Content-Type: text/html; charset=utf-8' );
	if ( ! empty( $o['noindex'] ) ) {
		header( 'X-Robots-Tag: noindex, nofollow', true );
	}

	$paras = array_values( array_filter( array_map( 'trim', preg_split( "/\r\n|\n|\r/", (string) $o['message'] ) ) ) );
	$c     = tym_colors( $d, $o, $use_custom );
	$vars  = ':root{--bg:' . $c[0] . ';--ac:' . $c[1] . ';--hi:' . $c[2] . '}';

	// Optional background image with a dark overlay.
	$has_bg = ! empty( $o['bg_image'] ) && $use_custom;
	$extra  = '';
	if ( $has_bg ) {
		$ov     = max( 0, min( 90, (int) $o['bg_overlay'] ) ) / 100;
		$url    = str_replace( array( '"', '\\', "\n", "\r" ), array( '%22', '%5C', '', '' ), esc_url_raw( $o['bg_image'] ) );
		$extra .= '.opt.has-bg{background-image:linear-gradient(rgba(0,0,0,' . $ov . '),rgba(0,0,0,' . $ov . ')),url("' . $url . '");background-size:cover;background-position:center}';
	}
	if ( ! empty( $o['custom_css'] ) ) {
		$extra .= "\n" . wp_strip_all_tags( $o['custom_css'] );
	}

	$body = tym_design_body( $d, $o, $paras );
	if ( $has_bg ) {
		$body = preg_replace( '/class="opt /', 'class="opt has-bg ', $body, 1 );
	}

	$font_url = 'https://fonts.googleapis.com/css2?family=' . $designs[ $d ]['font'] . '&family=Inter:wght@400;500;600&display=swap';
	?>
<!doctype html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( $o['page_title'] ); ?></title>
<meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( implode( ' ', $paras ) ) ); ?>">
<meta name="theme-color" content="<?php echo esc_attr( $c[0] ); ?>">
<?php if ( ! empty( $o['noindex'] ) ) : ?><meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<link rel="canonical" href="<?php echo esc_url( home_url( '/' ) ); ?>">
<?php wp_site_icon(); ?>
<?php if ( ! empty( $o['load_fonts'] ) ) : ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="<?php echo esc_attr( $font_url ); ?>" rel="stylesheet">
<?php endif; ?>
<style>
<?php echo $vars; // phpcs:ignore ?>

<?php echo tym_common_css(); // phpcs:ignore ?>

<?php echo tym_design_css( $d ); // phpcs:ignore ?>

<?php echo $extra; // phpcs:ignore ?>
</style>
</head>
<body>
<?php echo $body; // phpcs:ignore ?>
</body>
</html>
	<?php
}

/* ---------------------------------------------------------------
 * Lock down REST API and XML-RPC for the public while active.
 * ------------------------------------------------------------- */
add_filter( 'rest_authentication_errors', function ( $result ) {
	if ( ! empty( $result ) ) {
		return $result;
	}
	$o = tym_get();
	if ( empty( $o['block_rest'] ) || ! tym_is_active( $o ) || is_user_logged_in() || tym_ip_allowed( $o ) ) {
		return $result;
	}
	return new WP_Error( 'tym_closed', 'This site is currently closed to the public.', array( 'status' => 401 ) );
} );

add_filter( 'xmlrpc_enabled', function ( $enabled ) {
	$o = tym_get();
	return ( ! empty( $o['block_rest'] ) && tym_is_active( $o ) ) ? false : $enabled;
} );
