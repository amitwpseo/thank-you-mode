<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 * Removes only this plugin's settings - pages, posts and media are never touched.
 *
 * @package ThankYouMode
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'tym_settings' );
