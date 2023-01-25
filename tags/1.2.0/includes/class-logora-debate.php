<?php
/**
 * Define the Logora Debate Module
 *
 * @link       https://logora.fr
 * @since      1.0.0
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
     * @since      1.0.0
     * @access     public
     * @return     object Template
     */
    public function load_template( $template ) {
        if(  is_page( 'Logora App Page' ) ) {
			$template_slug = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if($template_slug == 'templates/template-logora-app.php') {
				$template = LOGORA_PLUGIN_DIR_PATH . '/templates/template-logora-app.php';
			}
		}
		return $template;
    }
	
	/**
     *
     * Add Logora App page template
     * 
     * @since      1.0.0
     * @access     public
     * @return     object Template
     */
	public function logora_add_page_template ($templates) {
		$template_path = 'templates/template-logora-app.php'; 
		$templates[$template_path] = 'Logora App Page';
		return $templates;
    }
}
