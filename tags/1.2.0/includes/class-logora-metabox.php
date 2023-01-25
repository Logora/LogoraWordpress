<?php
/**
 * Define the Logora Post Metabox
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Define the Logora Post Metabox
 *
 * Add the Logora Post Metabox that lets publishers
 * create and configure a debate related to an article
 *
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Metabox {
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
     * Hook to add the post metabox.
     *
     * @since    1.0.0
     * @return   None
     */
	public static function add_meta_box() {
		$post_types = get_post_types(array( 'publicly_queryable'=>true )) + array('page'=>'page');
		unset( $post_types['attachment'] );

		add_meta_box( 'logora-metabox', __('Logora', 'logora'), array( __CLASS__, 'meta_box_html' ), $post_types, 'side' );
	}

    /**
     * Define the metabox content.
     *
     * @since    1.0.0
     * @param    $post       The Wordpress post related to the metabox
     */
	public static function meta_box_html( $post ) {
		$debateTitle = $post->logora_debate_title;
		$debateProThesis = $post->logora_debate_pro_thesis;
		$debateAgainstThesis = $post->logora_debate_against_thesis;
        $debate_allowed = $post->logora_allow_debate;
        if(!metadata_exists('post', $post->ID, 'logora_allow_debate')) {
            $debate_allowed = true;
        }

        wp_nonce_field('save_metabox_data', "logora_metabox_nonce");

        ?>
			<input type="checkbox" name="logora_metabox_allow_debate" id="logora_metabox_allow_debate" <?php checked($debate_allowed, true); ?> />
			<label for="logora_metabox_allow_debate"><?php echo __("Allow debate for this post", 'logora') ?></label><br><br>
			<label for="logora_metabox_debate_title"><?php echo __("Debate question", 'logora') ?></label>
			<input type="text" name="logora_metabox_debate_title" style="width:100%" value="<?php echo $debateTitle ?>" />
			<label for="logora_metabox_debate_pro_thesis"><?php echo __("Side 1", 'logora') ?></label>
			<input type="text" name="logora_metabox_debate_pro_thesis" style="width:100%" value="<?php echo $debateProThesis ?>" />
			<label for="logora_metabox_debate_against_thesis"><?php echo __("Side 2", 'logora') ?></label>
			<input type="text" name="logora_metabox_debate_against_thesis" style="width:100%" value="<?php echo $debateAgainstThesis ?>" />
        <?php
	}

    /**
     * A hook to save post metadata defined by the metabox inputs.
     *
     * @since    1.2.0
     * @param    $post_id       The Wordpress post ID related to the metabox
     * @param    $post          The Wordpress post related to the metabox
     * @param    $update        A boolean that defines whether post is created or updated
     */
	public static function save_metabox_data( $post_id, $post, $update) {
        // Exits depending on save status
        if(!isset($_POST['logora_metabox_nonce']) || !wp_verify_nonce($_POST['logora_metabox_nonce'], 'save_metabox_data')){
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if(isset($_POST['logora_metabox_allow_debate'])) {
            update_post_meta($post_id, 'logora_allow_debate', true);
        } else {
            update_post_meta($post_id, 'logora_allow_debate', false);
        }

		$debateTitle = "";
		if( isset($_POST['logora_metabox_debate_title']) && !empty($_POST['logora_metabox_debate_title'])) {
            $debateTitle = sanitize_text_field($_POST['logora_metabox_debate_title']);
			update_post_meta( $post_id, "logora_debate_title", $debateTitle);
        }
        
        if( isset($_POST['logora_metabox_debate_pro_thesis']) && !empty($_POST['logora_metabox_debate_pro_thesis']) ) {
            $debateProThesis = sanitize_text_field($_POST['logora_metabox_debate_pro_thesis']);
			update_post_meta( $post_id, "logora_debate_pro_thesis", $debateProThesis );
        } else {
			if(!empty($debateTitle)) {
				update_post_meta( $post_id, "logora_debate_pro_thesis", __("Yes", 'logora'));
			}
		}
        
        if( isset($_POST['logora_metabox_debate_against_thesis']) && !empty($_POST['logora_metabox_debate_against_thesis'])) {
            $debateAgainstThesis = sanitize_text_field($_POST['logora_metabox_debate_against_thesis']);
			update_post_meta( $post_id, "logora_debate_against_thesis", $debateAgainstThesis );
        } else {
			if(!empty($debateTitle)) {
				update_post_meta( $post_id, "logora_debate_against_thesis", __("No", 'logora'));
			}
		}
	}
}
