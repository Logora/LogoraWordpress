<?php
/**
 * The service for making requests to the Logora API
 *
 * @link       https://logora.fr
 * @since      1.0.0
 *
 * @package    Logora
 * @subpackage Logora/includes
 */

/**
 * Utility functions for Single Sign-On.
 *
 * @package    Logora
 * @subpackage Logora/includes
 * @author     Henry Boisgibault <henry@logora.fr>
 */
class Logora_Utils {
    
    /**
	 * Returns current user as an object.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array     Current user
	 */
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
    
    /**
	 * Returns the Single Sign-On message for current user.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string      Message composed of the user payload, current timestamp, and signature.
	 */
    public function get_sso_auth() {
        if(!is_user_logged_in()) {
            return "";
        }
        $secret = get_option('logora_secret_key');
        $data = $this->get_user_object();
        $message = base64_encode(json_encode($data));
        $timestamp = time();
        $hmac = hash_hmac( 'sha1', $message . ' ' . $timestamp, $secret );
        return $message . ' ' . $hmac . ' ' . $timestamp;
    }
}
