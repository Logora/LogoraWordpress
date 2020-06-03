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
	 *
	 * @return    null
	 */
    public function settings_init() {        
        add_settings_section(
         'logora_main_settings',
         'Général',
         array($this, 'logora_section_main_cb'),
         'logora'
        );
        
        register_setting("logora", "logora_shortname");
        register_setting("logora", "logora_secret_key");
        register_setting("logora", "logora_prefix_path");
        
        add_settings_field(
            'logora_shortname',
            "Nom d'application",
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_main_settings',
            array(
                'label_for' => 'logora_shortname',
                'type' => 'text',
                'option_name' => 'logora_shortname',
                'description' => "Le nom de votre application disponible dans l'espace d'administration Logora",
            )
        );
        
        add_settings_field(
            'logora_secret_key',
            "Clé secrète",
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_main_settings',
            array(
                'label_for' => 'logora_secret_key',
                'type' => 'password',
                'option_name' => 'logora_secret_key',
                'description' => "La clé secrète de votre application disponible dans l'espace d'administration Logora",
            )
        );
        
        add_settings_field(
            'logora_prefix_path',
            "Chemin de l'espace de débat",
            array($this, 'logora_input_field_cb'),
            'logora',
            'logora_main_settings',
            array(
                'label_for' => 'logora_prefix_path',
                'type' => 'text',
                'option_name' => 'logora_prefix_path',
                'description' => "Le chemin vers l'espace de débat. Rafraîchissez les permaliens après avoir modifié ce champ",
            )
        );
    }
    
    public function logora_section_main_cb( $arg ) {
        echo "<span>Pour finaliser l'installation de Logora, rentrez votre nom d'application et votre clé secrète, disponibles dans <a href='https://admin.logora.fr' target='_blank'>votre espace d'administration</a>.</span><br>";
    }
    
    public function logora_input_field_cb( $args ) {
        $option_name   = $args['option_name'];
        $id     = $args['label_for'];
        $type   = $args['type'];
        $value  = get_option($option_name);

        $value  = esc_attr( $value );
        $name   =  $option_name;
        $desc   = $args["description"];
        
        print "<input type='$type' value='$value' name='$name' id='$id'
            class='regular-text code' /> <span class='description'>$desc</span>";
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
	 */
	public function display_plugin_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        // add error/update messages
 
        // check if the user have submitted the settings
        // wordpress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'logora_messages', 'logora_message', __( 'Settings Saved', 'logora' ), 'updated' );
        }

        // show error/update messages
        settings_errors( 'wporg_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "logora"
                settings_fields( 'logora' );
                // output setting sections and their fields
                do_settings_sections( 'logora' );
                // output save settings button
                submit_button( 'Enregistrer' );
                ?>
            </form>
        </div>
        <?php
	}
}
