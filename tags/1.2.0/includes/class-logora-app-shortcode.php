<?php
/**
 * Define the Logora Debate App Shortcode
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Define the Logora Debate App Shortcode
 *
 * Add shortcode and define shortcode related functions
 *
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_App_Shortcode {
     /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $logora    The ID of this plugin.
     */
    private $logora;
    
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version    The current version of this plugin.
     */
    private $version;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string $logora       The name of this plugin.
     * @param    string $version      The version of this plugin.
     */
    public function __construct( $logora, $version ) {
        $this->logora = $logora;
        $this->version = $version;
    }
    
    /**
     * Register app shortcode.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_shortcode() {
        add_shortcode( 'logora-app', array($this, 'shortcode') );
    }
    
    /**
     * Create configuration script to be inserted with the shortcode.
     * 
     * @since    1.0.0
     * @access   public
     * @return   string   A string containing the configuration object as javascript object.
     */
    public function logora_config_script($object_name, $object) {
        return '<script>
                var '. $object_name .' = '. json_encode($object) .';
            </script>';
    }
	
	/**
     * Adds rewriting rules for the Logora App Shortcode
     *
     * @since   1.0.0
     * @access  public
     * @return  None
     */
    public function add_rewrite_rules()
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
     * Generates the code embedded by the shortcode.
     *
     * @since    1.0.0
     * @access   public
     * @return   string    The code embedded by the Logora Debate App shortcode.
     */
	public function shortcode( $atts ) {    
		$object_name = 'logora_config';

		if(!get_option('logora_shortname')) {
			$shortcode_init = "<div>" . __( 'To finalize your installation, add your app name and credentials on the ', 'logora' ) . "<a href=" .
				admin_url( 'admin.php?page=logora' ) .
				">" . __( 'Logora settings page', 'logora' ) . "</a></div>";
			return $shortcode_init;
		}

		$embed_vars = array(
            'shortname' => get_option('logora_shortname'),
            'provider' => array('url' => get_site_url(), 'name' => get_bloginfo('name'))
		);

        $sso_enabled = get_option('logora_enable_sso', true);
        if($sso_enabled) {
            $auth = array(
                'type' => 'signature_jwt', 
                'login_url' => wp_http_validate_url(wp_login_url()),
                'registration_url' => wp_http_validate_url(wp_registration_url()),
                'redirectParameter' => 'redirect_to'
            );
            $embed_vars['auth'] = $auth;

            $logora_utils = new Logora_Utils();
            $remote_auth = $logora_utils->get_sso_auth();
            $embed_vars['remote_auth'] = $remote_auth;
        }

		$api_app_url = 'https://api.logora.fr/debat.js';
		$shortcode = "<div id='logora_app' data-object-id=\"".$object_name."\"></div>
                      ". self::logora_config_script($object_name, $embed_vars) ."
					  <script>
                        (function() {
                            var d = document, s = d.createElement('script');
                            s.src = '".$api_app_url."';
                            (d.head || d.body).appendChild(s);
                         })();
                      </script>";

        wp_enqueue_script( $this->logora . '_debate' );
		return $shortcode;
	}
}
