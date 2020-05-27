<?php
/**
 * Logora
 *
 *
 * @package   Logora
 * @author    Henry Boisgibault
 * @license   GPL-3.0
 * @link      https://logora.fr
 * @copyright Logora 2019
 */

/**
 * @package     Logora
 * @subpackage  Logora/admin
 * @author      Henry Boisgibault <henry@logora.fr>
 */
class Logora_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Plugin basename.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_basename = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

    /**
     * The option name
     *
     * @var string
     */
    private $option_name = 'logora_data';
     
    /** 
     * The security nonce 
     *
     * @var string 
     */
    private $_nonce = 'feedier_admin';
 
    /**
     * Returns the saved options data as an array
     *
     * @return array
     */
    private function getData() {
        return get_option($this->option_name, array());
    }

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->do_hooks();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		$plugin = Plugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->version = $plugin->get_plugin_version();

		$this->plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
	}


	/**
	 * Handle WP actions and filters.
	 *
	 * @since 	1.0.0
	 */
	private function do_hooks() {
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        	
        // Add callback when saving data
        add_action( 'wp_ajax_store_admin_data', array( $this, 'storeAdminData' ) );

		// Add plugin action link point to settings page
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug . '-style', plugins_url( 'assets/css/admin.css', dirname( __FILE__ ) ), array(), $this->version );
		}
	}

	/**
	 * Register and enqueue admin-specific javascript
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {

			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ), $this->version );

			wp_localize_script( $this->plugin_slug . '-admin-script', 'wpr_object', array(
				'api_nonce'   => wp_create_nonce( 'wp_rest' ),
				'api_url'	  => rest_url( $this->plugin_slug . '/v1/' ),
				)
			);
		}
        wp_enqueue_script('logora-admin', LOGORA_URL. '/assets/js/admin-save.js', array(), 1.0);
 
        $admin_options = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            '_nonce'   => wp_create_nonce( $this->_nonce ),
        );
     
        wp_localize_script('logora-admin', 'logora_exchanger', $admin_options);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Logora', $this->plugin_slug ),
			__( 'Logora', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $data = $this->getData();
        $status = 'OK';
        ?>
        <div class="wrap">
            <h3><?php _e('Logora API Settings', $this->plugin_slug); ?></h3>
            <p>
            <?php _e('You can get your Logora API settings from your <b>Integrations</b> page.', $this->plugin_slug); ?>
            </p>
            <hr>
            <form id="logora-admin-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <td scope="row">
                                <label><?php _e( 'Public key', $this->plugin_slug ); ?></label>
                            </td>
                            <td>
                                <input name="logora_public_key"
                                       id="logora_public_key"
                                       class="regular-text"
                                       value="<?php echo (isset($data['public_key'])) ? $data['public_key'] : ''; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row">
                                <label><?php _e( 'Private key', $this->plugin_slug ); ?></label>
                            </td>
                            <td>
                                <input name="logora_private_key"
                                       id="logora_private_key"
                                       class="regular-text"
                                       value="<?php echo (isset($data['private_key'])) ? $data['private_key'] : ''; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <hr>
                                <h4><?php _e( 'Statut', $this->plugin_slug ); ?></h4>
                            </td>
                        </tr>
 
                        <?php if (!empty($data['private_key']) && !empty($data['public_key'])): ?>
 
                            <?php
                            // if we don't even have a response from the API
                            if (empty($surveys)) : ?>
 
                                <tr>
                                    <td>
                                        <p class="notice notice-error">
                                            <?php _e( 'An error happened on the WordPress side. Make sure your server allows remote calls.', $this->plugin_slug ); ?>
                                        </p>
                                    </td>
                                </tr>
 
                            <?php
                            // If we have an error returned by the API
                            elseif (isset($surveys['error'])): ?>
 
                                <tr>
                                    <td>
                                        <p class="notice notice-error">
                                            <?php echo $surveys['error']; ?>
                                        </p>
                                    </td>
                                </tr>
 
                            <?php
                            // If the surveys were returned
                            else: ?>
 
                                <tr>
                                    <td>
                                        <p class="notice notice-success">
                                            <?php _e( 'The API connection is established!', $this->plugin_slug ); ?>
                                        </p>
                                        <hr>
                                </tr>
 
                            <?php endif; ?>
 
                        <?php else: ?>
 
                            <tr>
                                <td>
                                    <p>Please fill up your API keys to see the widget options.</p>
                                </td>
                            </tr>
 
                        <?php endif; ?>
 
                        <tr>
                            <td colspan="2">
                                <button class="button button-primary" id="logora-admin-save" type="submit"><?php _e( 'Save', $this->plugin_slug ); ?></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
	}
    
    /**
     * Callback for the Ajax request
     *
     * Updates the options data
     *
     * @return void
     */
    public function storeAdminData() {
        if (wp_verify_nonce($_POST['security'], $this->_nonce ) === false) {
            die('Invalid Request!');
        }
        
        $data = $this->getData();
        
        foreach ($_POST as $field=>$value) {
            if (substr($field, 0, 7) !== "logora_" || empty($value))
                continue;
     
            // We remove the logora_ prefix to clean things up
            $field = substr($field, 7);
     
            $data[$field] = $value;
        }
     
        update_option($this->option_name, $data);
        
        $api = Logora_API($data['logora_public_key'], $data['logora_private_key']);
        $a = $api->basic_auth();
     
        echo __($a, $this->plugin_slug);
        echo __('Saved!', $this->plugin_slug);
        die();
    }
    
    /**
	 * Test API connection
	 *
	 * @since    1.0.0
	 */
	public function api_status() {
        $data = $this->getData();
        
        $api = Logora_API($data['public_key'], $data['private_key']);
        $a = $api->basic_auth();        
    }

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>',
			),
			$links
		);
	}
}
