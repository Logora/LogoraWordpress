<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Logora_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $logora    The string used to uniquely identify this plugin.
     */
    protected $logora;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The name of the page where the Logora Debate Module is inserted.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $logora_page_slug    The slug of the Logora Debate Module page.
     */
    protected $logora_page_slug;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, and set the hooks for the admin area and the public-facing
     * side of the site.
     *
     * @since    1.0.0
     * @access   public
     * @param    string $version    The version of this plugin.
     */
    public function __construct( $version ) {

        $this->logora = 'logora';
        $this->version = $version;
        $this->logora_page_slug = 'logora-app-page';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_debate_hooks();
        $this->define_metabox_hooks();
        $this->define_synthesis_shortcode_hooks();
        $this->define_app_shortcode_hooks();
        
        $this->loader->add_action( 'plugins_loaded', $this, 'logora_load_plugin_textdomain' );
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Logora_Loader. Orchestrates the hooks of the plugin.
     * - Logora_Admin. Defines all hooks for the admin area.
     * - Logora_Debate. Defines all hooks for the debate module.
     * - Logora_Metabox. Defines all hooks for the metabox.
     * - Logora_Synthesis_Shortcode. Defines all hooks for the shortcode.
     * - Logora_App_Shortcode. Defines all hooks for the shortcode.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logora-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-logora-admin.php';

        /**
         * The class responsible for defining all actions that occur in the Logora Debate Module.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logora-debate.php';
        
        /**
         * The class responsible for defining the debate app shortcode
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logora-app-shortcode.php';
        
        /**
         * The class responsible for defining the debate synthesis shortcode
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logora-synthesis-shortcode.php';
        
        /**
         * The class responsible for defining the post metabox
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logora-metabox.php';
        
        /**
         * The class defining utility functions
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logora-utils.php';

        $this->loader = new Logora_Loader();
    }
    
    /**
     * Register plugin text domains
     *
     * @since    1.0.0
     * @access   public
     */
    public function logora_load_plugin_textdomain() {
        load_plugin_textdomain( 'logora', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Logora_Admin( $this->get_logora_name(), $this->get_version() );
        
		// Add the options page and menu item.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        
        // Initialize settings and form
        $this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );
		
		// Add settings link in plugins page
		$plugin = 'Logora/logora.php';
		$this->loader->add_filter( "plugin_action_links_$plugin", $plugin_admin, 'logora_settings_link' );
    }

    /**
     * Register all of the hooks related to the Logora Debate Module
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_debate_hooks() {
        $plugin_debate = new Logora_Debate( $this->get_logora_name(), $this->get_version() );

        $this->loader->add_filter( 'template_include', $plugin_debate, 'load_template' );
		
		$this->loader->add_filter( 'theme_page_templates', $plugin_debate, 'logora_add_page_template' );
    }
    
    /**
     * Register all of the hooks related to post metabox functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_metabox_hooks() {
        $plugin_metabox = new Logora_Metabox( $this->get_logora_name(), $this->get_version() );

        $this->loader->add_action( 'add_meta_boxes', $plugin_metabox, 'add_meta_box' );
        $this->loader->add_action( 'save_post', $plugin_metabox, 'save_metabox_data', 10, 3 );
    }
    
    /**
     * Register all of the hooks related to shortcode functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_synthesis_shortcode_hooks() {
        $plugin_shortcode = new Logora_Synthesis_Shortcode( $this->get_logora_name(), $this->get_version() );

		$this->loader->add_action('init', $plugin_shortcode, 'register_shortcode' );
		$this->loader->add_filter('the_content', $plugin_shortcode, 'insert_shortcode');
    }
    
    /**
     * Register all of the hooks related to shortcode functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_app_shortcode_hooks() {
        $plugin_shortcode = new Logora_App_Shortcode( $this->get_logora_name(), $this->get_version() );

		$this->loader->add_action('init', $plugin_shortcode, 'register_shortcode' );
		$this->loader->add_action( 'init', $plugin_shortcode, 'add_rewrite_rules' );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_logora_name() {
        return $this->logora;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Logora_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the Logora page name.
     *
     * @since     1.0.0
     * @return    string    The page name.
     */
    public function get_logora_page_slug() {
        return $this->logora_page_slug;
    }
}