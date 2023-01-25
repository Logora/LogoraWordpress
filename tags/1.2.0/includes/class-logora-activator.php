<?php
/**
 * Fired during plugin activation
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Activator {

    /**
    * Activates plugin functionalities
    *
    * @since    1.0.0
    */
    public static function activate() {
        add_option( 'logora_prefix_path', 'espace-debat');

        self::add_logora_debate_page();
		self::add_rewrite_rules();
        flush_rewrite_rules();
    }
	
	/**
     * Adds rewriting rules for the Logora App Shortcode
     *
     * @since   1.0.0
     * @access  public
     * @return  None
     */
    private static function add_rewrite_rules()
    {
        $prefix_path = get_option('logora_prefix_path');
        if(!empty($prefix_path)) {
            add_rewrite_rule(
                '^'. $prefix_path .'[\S]*$',
                'index.php?pagename=' . $prefix_path,
                'top'
            );
        }
    }
    
    /**
     * Add page to include the Logora Debate Module
     *
     * @since 1.0.0
     *
     * @return None
     */
    private static function add_logora_debate_page() {
        if (!post_exists('Logora App Page')) {
          // Create post/page object
          $logora_app_page = array(
              'post_title' => 'Logora App Page',
              'post_name' => get_option('logora_prefix_path'),
              'post_content' => '[logora-app]',
              'post_status' => 'publish',
              'comment_status' => 'closed',
              'post_type' => 'page'
          );
          // Insert the post into the database
          $post_id = wp_insert_post( $logora_app_page );
		  
		  $template_path = 'templates/template-logora-app.php'; 
		  update_post_meta($post_id, '_wp_page_template', $template_path);
        }
    }
}