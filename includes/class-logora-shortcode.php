<?php
/**
 * Define the Logora Shortcode
 *
 * @link       https://logora.fr
 * @since      1.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Define the Logora Shortcode
 *
 * Add shortcode and define shortcode related functions
 *
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Shortcode {
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
     * Register shortcode.
     *
     * @since    1.0
     * @access   public
     */
    public function register_shortcode() {
        add_shortcode( 'logora-synthese', array($this, 'shortcode') );
    }
    
    
    /**
     * Create configuration script to be inserted with the Logora Shortcode.
     * 
     * @since    1.0
     * @access   public
     * @return   string   A string containing the configuration object as javascript object.
     */
    public function logora_config_script($object_name, $object) {
        return '<script>
                var '. $object_name .' = '. json_encode($object) .';
            </script>';
    }
    
    
    /**
     * Generates the code embedded by the Logora shortcode.
     *
     * @since    1.0
     * @access   public
     * @return   string    The code embedded by the Logora Shortcode.
     */
	public function shortcode( $atts ) {
        global $post;
        
        if( ! $this->embed_can_load_for_post( $post )) {
            return false;
        }
        
        $logora_utils = new Logora_Utils();
        $remote_auth = $logora_utils->get_sso_auth();
        
		$object_name = 'logora_object_' . uniqid();

        $post_id = $post->ID;
        $allowDebate = get_post_meta($post_id, "logora_allow_debate", true);
        $debateTitle = get_post_meta($post_id, 'logora_debate_title', true);
        $debateProThesis = get_post_meta($post_id, 'logora_debate_pro_thesis', true);
        $debateAgainstThesis = get_post_meta($post_id, 'logora_debate_against_thesis', true);
        $postThumbnailUrl = get_the_post_thumbnail_url($post_id);
        $thumbnailUrl = $postThumbnailUrl ? $postThumbnailUrl : "";
        $postTagsArray = get_the_tags($post_id);
        $postTags = array();
        if($postTagsArray) {
            foreach($postTagsArray as $tag) {
                array_push($postTags, $tag->name);
            }
        }
        $postUrl = get_the_permalink($post_id);
        
        $debateObject = $allowDebate ? array(
                'source_url' => $postUrl, 
                'tags' => implode(',', $postTags), 
                'image_url' => $thumbnailUrl, 
                'identifier' => $post_id, 
                'name' => $debateTitle, 
                'pro_side' => $debateProThesis, 
                'against_side' => $debateAgainstThesis,
                'started' => true
        ) : array();
        
		$object = array(
            'shortname' => get_option('logora_shortname'),
            'debate' => $debateObject,
            'remote_auth' => $remote_auth,
            'provider' => array('url' => get_site_url(), 'name' => get_bloginfo('name')),
            'auth' => array('login_url' => wp_http_validate_url(wp_login_url()),
                            'registration_url' => wp_http_validate_url(wp_registration_url()))
		);
        
        $api_shortcode_url = getenv("LOGORA_MODE") === 'staging' ? 'https://d2d2dbh4kbp3fl.cloudfront.net/synthese.js' : 'https://api.logora.fr/synthese.js';
		$shortcode = "<div class='logora_synthese' data-object-id=\"".$object_name."\"></div>
                      ". self::logora_config_script($object_name, $object) ."
                      <script>
                        (function() {
                            var d = document, s = d.createElement('script');
                            s.src = '".$api_shortcode_url."';
                            (d.head || d.body).appendChild(s);
                         })();
                      </script>";
		return $shortcode;
	}
    
    
    /**
	 * Determines if Logora is configured and can the debate synthesis embed on a given page.
	 *
	 * @since     1.0
	 * @access    private
	 * @param     WP_Post $post    The WordPress post used to determine if Logora can be loaded.
	 * @return    boolean          Whether Logora is configured properly and can load on the current page.
	 */
    private function embed_can_load_for_post( $post ) {
		if ( is_feed() ) {
			return false;
        }
        
        $illegal_post_statuses = array(
			'draft',
			'auto-draft',
			'pending',
			'future',
			'trash',
		);
        
        $postStatus = $post->post_status;
        
         if( in_array( $postStatus, $illegal_post_statuses ) ) {
            return false;
        }
        
        $shortname = get_option('logora_shortname', '');
        
        if( ! $shortname ) {
            return false;
        }
        
        return true;
    }
}
