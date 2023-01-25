<?php
/**
 * Logora
 *
 *
 * @package   Logora
 * @author    Henry Boisgibault
 * @license   GPL-2.0
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
	 * Initialize settings by registering settings and settings form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    null
	 */
    public function settings_init() {        
        add_settings_section(
            'logora_main_settings',
            __('Main', 'logora'),
            array($this, 'logora_section_main_cb'),
            'logora'
        );

        register_setting("logora", "logora_shortname");
        register_setting("logora", "logora_prefix_path");
        register_setting("logora", "logora_enable_sso", [
            'default' => "1",
        ]);
        register_setting("logora", "logora_insert_shortcode", [
            'default' => "1",
        ]);
        register_setting("logora", "logora_secret_key");

        add_settings_field(
            'logora_shortname',
            __("Application name", 'logora'),
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_main_settings',
            array(
                'label_for' => 'logora_shortname',
                'type' => 'text',
                'option_name' => 'logora_shortname',
                'description' => __("Your application name is available in your Logora administration panel", 'logora'),
            )
        );

        add_settings_field(
            'logora_prefix_path',
            __("Path to the debate space", 'logora'),
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_main_settings',
            array(
                'label_for' => 'logora_prefix_path',
                'type' => 'text',
                'option_name' => 'logora_prefix_path',
                'description' => __("Path to the debate space. Refresh permalinks after changing this setting", 'logora'),
            )
        );

        add_settings_field(
            'logora_insert_shortcode',
            __("Insert shortcode", 'logora'),
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_main_settings',
            array(
                'label_for' => 'logora_insert_shortcode',
                'type' => 'checkbox',
                'option_name' => 'logora_insert_shortcode',
                'description' => __("Insert synthesis shortcode by default in all posts", 'logora'),
            )
        );

        add_settings_section(
            'logora_auth_settings',
            __('Authentication', 'logora'),
            array($this, 'logora_section_auth_cb'),
            'logora'
        );

        add_settings_field(
            'logora_enable_sso',
            __("Enable Single Sign-On (SSO)", 'logora'),
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_auth_settings',
            array(
                'label_for' => 'logora_enable_sso',
                'type' => 'checkbox',
                'option_name' => 'logora_enable_sso',
                'description' => __("Check this box to enable users to use their Wordpress account to sign in to the debate space", 'logora'),
            )
        );
        
        add_settings_field(
            'logora_secret_key',
            __("Secret key", 'logora'),
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_auth_settings',
            array(
                'label_for' => 'logora_secret_key',
                'type' => 'password',
                'option_name' => 'logora_secret_key',
                'description' => __("Your secret key is required if SSO is enabled. It is available in your Logora administration panel", 'logora'),
            )
        );
    }
    
    /**
	 * Callback function to print content at the top of the main settings section.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    None
	 */
    public function logora_section_main_cb( $args ) {
        echo '<span>'. _e("To finalize the Logora installation, input your application name that can be found in", 'logora') .'<a href="https://admin.logora.fr" target="_blank">'. _e("your administration panel", 'logora') .'</a>.</span><br>';
    }

     /**
	 * Callback function to print content at the top of the auth settings section.
	 *
	 * @since     1.2.0
	 * @access    public
	 * @return    None
	 */
    public function logora_section_auth_cb( $args ) {
        echo '<span>'. _e("Enable SSO so that your users can debate with their Wordpress account. Your secret key can be found in", 'logora') .'<a href="https://admin.logora.fr" target="_blank">'. _e("your administration panel", 'logora') .'</a>.</span><br>';
    }
    
    /**
	 * Callback function to print an input field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    None
	 */
    public function logora_input_field_cb( $args ) {
        $option_name   = $args['option_name'];
        $id     = $args['label_for'];
        $type   = $args['type'];
        $value  = sanitize_text_field( get_option($option_name) );
		
		if($type === 'checkbox') {
			$value = '1';
		}
        $name   =  $option_name;
        $desc   = $args["description"];
		$checked = checked($type === 'checkbox' && get_option($option_name, true), 1, false);
        
        print "<input type='$type' value='$value' name='$name' id='$id'
            class='regular-text code' $checked /> <span class='description'>$desc</span>";
    }

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
     * @access   public
     * @return   None
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Logora', $this->logora ),
			__( 'Logora', $this->logora ),
			'manage_options',
			'logora',
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
     * @access   public
	 */
	public function display_plugin_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
		
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'logora_messages', 'logora_message', __( 'Settings Saved', 'logora' ), 'updated' );
        }

        settings_errors( 'logora_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'logora' );
                do_settings_sections( 'logora' );
                submit_button( __( 'Save', 'logora' ));
                ?>
            </form>
        </div>
        <?php
	}
	
	/**
	 * Return link to the Logora settings page
	 *
	 * @since    1.0.0
     * @access   public
	 */
	public function logora_settings_link( $links ) {
		$url = esc_url( admin_url( 'admin.php?page=logora' ) );
		
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
		
		array_push(
			$links,
			$settings_link
		);
		return $links;
	}
}
