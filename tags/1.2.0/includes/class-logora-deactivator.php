<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Deactivator {

	/**
	 * Plugin deactivation function
	 *
	 * Upon deactivation, the plugin will delete the Logora App page and flush rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		if (post_exists('Logora App Page')) {
			$post_id = post_exists('Logora App Page');
			wp_delete_post($post_id, true);
		}
        
        flush_rewrite_rules();
	}

}