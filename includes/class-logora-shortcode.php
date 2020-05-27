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
        
		add_shortcode( 'logora-synthese', array( $this, 'shortcode' ) );
    }
    
    public function logora_config_script($object_name, $object) {
        return '<script>
                var '. $object_name .' = '. json_encode($object) .';
            </script>';
    }
    
	public function shortcode( $atts ) {
        global $post;
        
        $logora_api = Logora_API::get_instance();
        $remote_auth = $logora_api->get_sso_auth();
        
		$object_name = 'logora_object_' . uniqid();

        $post_id = $post->ID;
        $allowDebate = get_post_meta($post_id, "logora_allow_debate", true);
        $showDebate = get_post_meta($post_id, "logora_show_debate", true);
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
        
        $debateObject = ($allowDebate && $showDebate) ? array('source_url' => $postUrl, 'tags' => implode(',', $postTags), 'image_url' => $thumbnailUrl, 'identifier' => $post_id, 'name' => $debateTitle, 'pro_side' => $debateProThesis, 'against_side' => $debateAgainstThesis, 'started' => true) : array();
        
		$object = array(
            'shortname' => get_option('logora_shortname'),
            'debate' => $debateObject,
            'remote_auth' => $remote_auth,
            'provider' => array('url' => get_site_url(), 'name' => get_bloginfo('name')),
            'login_url' => get_option('logora_login_url', wp_login_url()),
            'registration_url' => get_option('logora_registration_url', wp_registration_url()),
            'hasVote' => true
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
}
