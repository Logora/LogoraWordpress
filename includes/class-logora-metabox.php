<?php
/**
 * Define the Logora Post Metabox
 *
 * @link       https://logora.fr
 * @since      1.0
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
    }
    
    /**
     * Hook to add the post metabox.
     *
     * @since    1.0
     * @return   None
     */
	public static function add_meta_box() {
		$post_types = get_post_types(array( 'publicly_queryable'=>true )) + array('page'=>'page');
		unset( $post_types['attachment'] );

		// WP 4.4+
		if( function_exists('get_term_meta') ){
			add_meta_box( 'logora-metabox', __('Logora','logora'), array( __CLASS__, 'meta_box' ), $post_types, 'side' );
		}
		// OLD WP
		else {
			foreach( $post_types as $ptype )
				add_meta_box( 'logora-metabox', __('Logora','logora'), array( __CLASS__, 'meta_box' ), $ptype, 'side' );
		}
	}

    /**
     * Define the metabox content.
     *
     * @since    1.0
     * @param    $post       The Wordpress post related to the metabox
     */
	public static function meta_box( $post ) {
		$debateTitle = get_post_meta($post->ID, "logora_debate_title", true);
		$debateProThesis = get_post_meta($post->ID, "logora_debate_pro_thesis", true);
		$debateAgainstThesis = get_post_meta($post->ID, "logora_debate_against_thesis", true);
		$allowDebate = boolval(get_post_meta($post->ID, "logora_allow_debate", true));
        $debateKey = get_post_meta($post->ID, "logora_debate_key", true);
        $checked = $allowDebate === true ? 'checked' : '';
        
		echo '
        <input type="checkbox" name="logora_metabox_allow_debate" value="is_allowed" '. $checked .' />
        <label for="logora_metabox_allow_debate">Autoriser le débat pour cet article</label><br><br>
        <label for="logora_metabox_debate_title">Question liée à l\'article (par défaut, titre de l\'article) :</label>
		<input type="text" name="logora_metabox_debate_title" style="width:100%" value="'. $debateTitle .'" />
		<label for="logora_metabox_debate_pro_thesis">Thèse pour (par défaut, Pour) :</label>
		<input type="text" name="logora_metabox_debate_pro_thesis" style="width:100%" value="'. $debateProThesis .'" />
        <label for="logora_metabox_debate_against_thesis">Thèse contre (par défaut, Contre) :</label>
		<input type="text" name="logora_metabox_debate_against_thesis" style="width:100%" value="'. $debateAgainstThesis .'" />
        ';
	}

    /**
     * A hook to save post metadata defined by the metabox inputs.
     *
     * @since    1.0
     * @param    $post_id       The Wordpress post ID related to the metabox
     * @param    $post          The Wordpress post related to the metabox
     * @param    $update        A boolean that defines whether post is created or updated
     */
	public static function save_post( $post_id, $post, $update) {
        $postTitle = get_the_title( $post_id );
        $postStatus = $post->post_status;
        $debateTitle = get_post_meta($post_id, "logora_debate_title", true);
		$allowDebate = get_post_meta($post_id, "logora_allow_debate", true);
        $showDebate = get_post_meta($post_id, "logora_show_debate", true );
        
        $illegal_post_statuses = array(
			'draft',
			'auto-draft',
			'pending',
			'future',
			'trash',
		);
        
        if( array_key_exists("logora_metabox_allow_debate", $_POST)) {
            $allowDebate = $_POST['logora_metabox_allow_debate'];
            if($allowDebate == 'is_allowed') {
                update_post_meta( $post_id, "logora_allow_debate", true );
                update_post_meta( $post_id, "logora_show_debate", true );
            }
        } else {
            update_post_meta( $post_id, "logora_allow_debate", false );
            update_post_meta( $post_id, "logora_show_debate", false );            
        }
        
        if( in_array( $postStatus, $illegal_post_statuses ) ) {
            update_post_meta( $post_id, "logora_show_debate", false );
        }
        
		if( isset($_POST['logora_metabox_debate_title']) && !empty($_POST['logora_metabox_debate_title'])) {
            $debateTitle = $_POST['logora_metabox_debate_title'];
        } else {
            if( !in_array( $postStatus, $illegal_post_statuses ) ) {
                $debateTitle = $postTitle;
            }
        }
        update_post_meta( $post_id, "logora_debate_title", $debateTitle);
        
        if( isset($_POST['logora_metabox_debate_pro_thesis']) && !empty($_POST['logora_metabox_debate_pro_thesis']) ) {
            $debateProThesis = $_POST['logora_metabox_debate_pro_thesis'];
        } else {
            $debateProThesis = "Pour";
        }
        update_post_meta( $post_id, "logora_debate_pro_thesis", $debateProThesis );
        
        if( isset($_POST['logora_metabox_debate_against_thesis']) && !empty($_POST['logora_metabox_debate_against_thesis'])) {
            $debateAgainstThesis = $_POST['logora_metabox_debate_against_thesis'];
        } else {
            $debateAgainstThesis = "Contre";
        }
        update_post_meta( $post_id, "logora_debate_against_thesis", $debateAgainstThesis );
	}
}
