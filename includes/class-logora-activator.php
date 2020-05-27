<?php
/**
 * Fired during plugin activation
 *
 * @link       https://logora.fr
 * @since      1.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Activator {

    /**
    * Activates plugin functionalities
    *
    * Long Description.
    *
    * @since    1.0
    */
    public static function activate() {
        add_option( 'logora_shortname', getenv("LOGORA_SHORTNAME") ? getenv("LOGORA_SHORTNAME") : '');
        add_option( 'logora_public_key', getenv("LOGORA_API_KEY") ? getenv("LOGORA_API_KEY") : '');
        add_option( 'logora_secret_key', getenv("LOGORA_API_SECRET") ? getenv("LOGORA_API_SECRET") : '');
        add_option( 'logora_prefix_path', getenv("LOGORA_PREFIX_PATH") ? getenv("LOGORA_PREFIX_PATH") : '');
        add_option( 'logora_login_url', wp_login_url());
        add_option( 'logora_registration_url', wp_registration_url());

        $this->add_logora_debate_page();
        $this->add_rewrite_rules();
        flush_rewrite_rules();
    }
    
    /**
     * Add page to include the Logora Debate Module
     *
     * @since 1.0.0
     *
     * @return None
     */
    private function add_logora_debate_page() {
        if (!post_exists('Logora App Page')) {
          // Create post/page object
          $logora_app_page = array(
              'post_title' => 'Logora App Page',
              'post_name' => LOGORA_APP_PAGE_SLUG,
              'post_content' => '',
              'post_status' => 'publish',
              'post_type' => 'page'
          );
          // Insert the post into the database
          $my_page = wp_insert_post( $logora_app_page );
          update_option(LOGORA_APP_PAGE_SLUG, $my_page);
        }
    }
    
    /**
     * Adds rewriting rules for the Logora Debate Module
     *
     * @since 1.0.0
     *
     * @return None
     */
    private function add_rewrite_rules()
    {
        $prefix_path = get_option('logora_prefix_path', "");
        if(!empty($prefix_path)) {
            add_rewrite_rule(
                '^'. $prefix_path .'[/[\S]*]?$',
                'index.php?pagename=logora-app-page',
                'top'
            );
            $prefix_path = $prefix_path . '/';
        }
        add_rewrite_rule(
            '^'. $prefix_path .'debat/[a-zA-Z0-9-]{0,100}$',
            'index.php?pagename=logora-app-page',
            'top'
        );
        add_rewrite_rule(
            '^'. $prefix_path .'user/[a-zA-Z0-9-]{0,100}$',
            'index.php?pagename=logora-app-page',
            'top'
        );
        add_rewrite_rule(
            '^'. $prefix_path .'search/?$',
            'index.php?pagename=logora-app-page',
            'top'
        );
        add_rewrite_rule(
            '^'. $prefix_path .'debats/?$',
            'index.php?pagename=logora-app-page',
            'top'
        );
    }

}