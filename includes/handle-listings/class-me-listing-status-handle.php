<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Listing Status Handle
 *
 * Class control listing status
 *
 * @version     1.0
 * @package     Includes/My Listing
 * @author      KyNguyen
 * @category    Class
 */
class ME_Listing_Status_Handle extends ME_Form {
    public static function init_hook() {

        add_action('wp_ajax_me_update_listing_status', array(__CLASS__, 'update_status'));
        add_action('wp_ajax_nopriv_me_update_listing_status', array(__CLASS__, 'update_status'));
    }

    /**
     * Update listing status.
     *
     */

    public static function update_status() {
        if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me_update_listing_status')) {
            $update_data = array(
                'ID'          => $_POST['listing_id'],
                'post_status' => $_POST['status']
            );
            $result = wp_update_post( $update_data, true );

            if( is_wp_error($result) ) {
                $response = array(
                    'success'    => false,
                    'error' => $result,
                );
            } else {
                $redirect = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : me_get_auth_url('listings');
                $response = array(
                    'success'    => true,
                    'listing' => $result,
                    'redirect' => $redirect,
                );
            }
            wp_send_json( $response);
        }
    }
}
ME_Listing_Status_Handle::init_hook();