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
     * Initialize the class and set its properties.
     *
     * @since    1.0
     * @access   public
     * @param    string $logora       The name of this plugin.
     * @param    string $version      The version of this plugin.
     */
    public function __construct( $logora, $version ) {
        $this->logora = $logora;
        $this->version = $version;
    }

    /**
     *
     * Loads Logora Debate Module page template
     * 
     * @since      1.0
     * @access     public
     * @return     object Template
     */
    public static function load_template( $template ) {
        if ( is_page( 'logora-app-page' ) ) {
            $page_template = dirname( __FILE__ ) . '/page-logora-app-page.php';
            return $page_template;
        }
        return $template;
    }
    
    /**
     *
     * Hide admin bar on Logora Debate page
     * 
     * @since     1.0
     * @access    public
     * @return    boolean    false if page is the Logora Debate page, true otherwise
     */
    public function show_admin_bar() {
        if ( is_page( 'logora-app-page' ) ) {
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
    
    /**
     * Removes all scripts on the Logora Debate page.
     *
     * @since      1.0
     * @access     public
     * @return     None
     */
    public function dequeue_all_scripts(){
        if ( is_page( 'logora-app-page' ) ) {
            global $wp_scripts;
            $scripts = $wp_scripts->registered;
            foreach ( $scripts as $script ){
                wp_dequeue_script($script->handle);
            }
        }
    }
    
    /**
     * Removes all stylesheets on the Logora Debate page.
     *
     * @since     1.0
     * @access    public
     * @return    None
     */
    public function dequeue_all_styles(){
        if ( is_page( 'logora-app-page' ) ) {
            global $wp_styles;
            $wp_styles->queue = array();
        }    
    }
    
    /**
     * Adds rewriting rules for the Logora Debate Module
     *
     * @since   1.0
     * @access  public
     * @return  None
     */
    public function add_rewrite_rules()
    {
        $prefix_path = get_option('logora_prefix_path', "");
        if(!empty($prefix_path)) {
            add_rewrite_rule(
                '^'. $prefix_path .'[\S]*$',
                'index.php?pagename=logora-app-page',
                'top'
            );
        } else {
            add_rewrite_rule(
                '^debat/[a-zA-Z0-9-]{0,100}$',
                'index.php?pagename=logora-app-page',
                'top'
            );
            add_rewrite_rule(
                '^user/[a-zA-Z0-9-]{0,100}$',
                'index.php?pagename=logora-app-page',
                'top'
            );
            add_rewrite_rule(
                '^search/?$',
                'index.php?pagename=logora-app-page',
                'top'
            );
            add_rewrite_rule(
                '^debats/?$',
                'index.php?pagename=logora-app-page',
                'top'
            );
        }
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
            'shortname' => get_option('logora_shortname', ''),
            'auth' => array('login_url' => wp_http_validate_url(wp_login_url()),
                            'registration_url' => wp_http_validate_url(wp_registration_url())),
            'provider' => array('name' => get_bloginfo('name'), 'url' => get_site_url()),
            'ga_tracking_id' => get_option('logora_ga_tracking_id'),
            'hideHeaders' => false,
		);
        
        if(is_user_logged_in()) {
            $logora_utils = new Logora_Utils();
            $remote_auth = $logora_utils->get_sso_auth();
            $embed_vars['remote_auth'] = $remote_auth;
        }
        
        return $embed_vars;
	}
}
