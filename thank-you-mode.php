<?php
/**
 * Plugin Name:       Thank You Mode
 * Description:       One-click "Thank You / Stay Tuned" landing page for any WordPress site. Choose from 6 premium designs, customise colors, logo, text and background, schedule it, and hide the rest of the site from the public while you stay logged in. Perfect after events, launches, sales and seasonal closures. Nothing is deleted - deactivate to restore the site instantly.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            BLSS - Best Local SEO Services & Development
 * Author URI:        https://bestlocalseoservices.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       thank-you-mode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TYM_VERSION', '2.0.0' );
define( 'TYM_FILE', __FILE__ );
define( 'TYM_DIR', plugin_dir_path( __FILE__ ) );
define( 'TYM_URL', plugin_dir_url( __FILE__ ) );
define( 'TYM_OPTION', 'tym_settings' );

require_once TYM_DIR . 'includes/settings.php';
require_once TYM_DIR . 'includes/designs.php';
require_once TYM_DIR . 'includes/frontend.php';
if ( is_admin() ) {
	require_once TYM_DIR . 'includes/admin.php';
}

/**
 * On first activation: create default settings. If a previous private build
 * ("Post-Event Thank You Mode", option vtc_ty_settings) exists, carry its settings over.
 */
register_activation_hook( TYM_FILE, 'tym_activate' );
function tym_activate() {
	if ( false !== get_option( TYM_OPTION, false ) ) {
		return;
	}
	$settings = tym_defaults();
	$legacy   = get_option( 'vtc_ty_settings', false );
	if ( is_array( $legacy ) ) {
		$settings = array_merge( $settings, array_intersect_key( $legacy, $settings ) );
	}
	add_option( TYM_OPTION, $settings );
}
