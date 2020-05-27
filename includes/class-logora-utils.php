<?php
/**
 * The service for making requests to the Logora API
 *
 * @link       https://logora.fr
 * @since      1.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * The service for making requests to the Logora API
 *
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_API {
    protected $api_secret = null;
    
    public function __construct() {
        $this->api_secret = $this->get_api_secret();
    }
    
    public function get_user_object() {
        $current_user = wp_get_current_user();
        $first_name = $current_user->user_firstname;
        $last_name = $current_user->user_lastname;
        $user_name = $current_user->user_login;
        $user_image = get_avatar_url($current_user->ID, ['size' => '200']);
        if(empty($first_name) && empty($last_name) && !empty($user_name)) {
            $first_name = $user_name;
        }
        return array(
            "uid" => $current_user->ID,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $current_user->user_email,
            "image_url" => $user_image
        );
    }
    
    public function get_sso_auth() {
        if(!is_user_logged_in()) {
            return "";
        }
        $secret = $this->api_secret;
        $data = $this->get_user_object();
        $message = base64_encode(json_encode($data));
        $timestamp = time();
        $hmac = hash_hmac( 'sha1', $message . ' ' . $timestamp, $secret );
        return $message . ' ' . $hmac . ' ' . $timestamp;
    }
    
    private function get_api_secret() {
        $api_secret = get_option('logora_private_key');
        return $api_secret;
    }
}
