<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! current_user_can( 'install_plugins' ) ) {
	exit;
}

delete_option( 'logora_shortname' );
delete_option( 'logora_prefix_path' );
delete_option( 'logora_enable_sso' );
delete_option( 'logora_insert_shortcode' );
delete_option( 'logora_secret_key' );