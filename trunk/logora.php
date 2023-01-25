<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package   Logora
 * @license   GPL-2.0
 * @since     1.0.0
 * @link      https://logora.fr
 *
 * @wordpress-plugin
 * Plugin Name:       Logora
 * Plugin URI:        https://logora.fr
 * Description:       Logora helps publishers create an engaged debate community.
 * Version:           1.2.0
 * Author:            Logora
 * Author URI:        https://logora.fr
 * Text Domain:       logora
 * License:           GPL-2.0
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

$LOGORAVERSION = '1.2.0';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'LOGORA_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation (but not during updates).
 */
function activate_logora() {
	if ( version_compare( phpversion(), '5.4', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'Logora requires PHP version 5.4 or higher. Plugin was deactivated.' );
	}
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-logora-activator.php';
	Logora_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-logora-deactivator.php
 */
function deactivate_logora() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-logora-deactivator.php';
	Logora_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_logora' );
register_deactivation_hook( __FILE__, 'deactivate_logora' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-logora.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_logora() {
	global $LOGORAVERSION;

	$plugin = new Logora( $LOGORAVERSION );
	$plugin->run();

}
run_logora();