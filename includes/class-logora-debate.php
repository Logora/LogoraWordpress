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
}
