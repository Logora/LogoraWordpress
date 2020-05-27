<?php
/**
 * Define the Logora Debate Module
 *
 * @link       https://logora.fr
 * @since      1.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Define the Logora Debate Module
 *
 * Creates a page to include the Logora Debate Module, dequeues
 * unecessary styles and rewrites URLs to add the Logora Debate Module
 * routes.
 *
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Debate {
    /**
     * The ID of this plugin.
     *
     * @since    1.0
     * @access   private
     * @var      string $logora    The ID of this plugin.
     */
    private $logora;
    
    /**
     * The version of this plugin.
     *
     * @since    1.0
     * @access   private
     * @var      string $version    The current version of this plugin.
     */
    private $version;

    /**
     * The unique Logora website shortname.
     *
     * @since    1.0
     * @access   private
     * @var      string $shortname    The unique Logora website shortname.
     */
    private $shortname;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0
     * @param    string $logora       The name of this plugin.
     * @param    string $version      The version of this plugin.
     * @param    string $shortname    The configured Logora shortname.
     */
    public function __construct( $logora, $version, $shortname ) {
        $this->logora = $logora;
        $this->version = $version;
        $this->shortname = $shortname;
    }

    /**
     *
     * Loads Logora Debate Module page template
     * 
     * @since 1.0
     *
     * @return object Template
     */
    public static function load_template( $template ) {
        if ( is_page( LOGORA_APP_PAGE_SLUG ) ) {
            $page_template = dirname( __FILE__ ) . '/page-logora-app-page.php';
            return $page_template;
        }
        return $template;
    }
    
    public function show_admin_bar() {
        if ( is_page( LOGORA_APP_PAGE_SLUG ) ) {
            return false;
        }
        return true;
    }
    
    public function login_redirect($redirect_to, $request, $user) {
        $logora_redirect_key = 'logora_redirect';
        if($user && is_object( $user ) && is_a( $user, 'WP_User' )) {
            if(isset($_POST['redirect_to'])) {
                return  $_POST['redirect_to'];
            } else {
                return $redirect_to;
            }
        } else {
            if(isset($_GET[$logora_redirect_key])) {
                return $_GET[$logora_redirect_key];
            } else {
                return $redirect_to;
            }
        }
    }
    
    public function dequeue_all_scripts(){
        if ( is_page( LOGORA_APP_PAGE_SLUG ) ) {
            global $wp_scripts;
            $scripts = $wp_scripts->registered;
            foreach ( $scripts as $script ){
                wp_dequeue_script($script->handle);
            }
        }
    }
    
    public function dequeue_all_styles(){
        if ( is_page( LOGORA_APP_PAGE_SLUG ) ) {
            global $wp_styles;
            $wp_styles->queue = array();
        }    
    }
    
    public function get_user_object() {
        $current_user = wp_get_current_user();
        $first_name = $current_user->user_firstname;
        $last_name = $current_user->user_lastname;
        $user_name = $current_user->user_login;
        $user_image = get_avatar_url($current_user->ID, ['size' => '200']);
        if(empty($first_name) && empty($last_name) && !empty($user_name)) {
            $first_name = $user_name;
        }
        return array(
            "uid" => $current_user->ID,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $current_user->user_email,
            "image_url" => $user_image
        );
    }
    
    public function get_sso_auth($secret) {
        $data = self::get_user_object();
        $message = base64_encode(json_encode($data));
        $timestamp = time();
        $hmac = hash_hmac( 'sha1', $message . ' ' . $timestamp, $secret );
        return $message . ' ' . $hmac . ' ' . $timestamp;
    }
    
    /**
	 * Configuration variables for the Logora Debate Module
	 *
	 * @since     1.0
     * @access    public
     * @return    array            The embed configuration to localize the debate module script with.
	 */
	public static function embed_vars() {        
		$embed_vars = array(
            'shortname' => $this->shortname,
            'login_url' => get_option('logora_login_url', wp_login_url()),
            'registration_url' => get_option('logora_registration_url', wp_registration_url()),
            'provider' => array('name' => get_bloginfo('name'), 'url' => get_site_url()),
            'ga_tracking_id' => get_option('logora_ga_tracking_id');,
            'hideHeaders' => false,
		);
        
        if(is_user_logged_in()) {
            $api_secret = get_option('logora_secret_key');
            $remote_auth = self::get_sso_auth($api_secret);
            $embed_vars['remote_auth'] = $remote_auth;
        }
        
        return $embed_vars;
	}
}
