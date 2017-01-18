<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Listing Handle Form
 *
 * Class control listing data submit by user fromt post listing form
 *
 * @version     1.0
 * @package     Includes/Post-Listings
 * @author      Dakachi
 * @category    Class
 */
class ME_Listing_Handle_Form extends ME_Form {
    public static function init_hook() {

        add_action( 'template_redirect', array(__CLASS__, 'redirect_to_login') );

        add_action('wp_loaded', array(__CLASS__, 'process_insert'));
        add_action('wp_loaded', array(__CLASS__, 'process_update'));
        // ajax action
        add_action('wp_ajax_me-load-sub-category', array(__CLASS__, 'load_sub_category'));
        add_action('wp_ajax_nopriv_me-load-sub-category', array(__CLASS__, 'load_sub_category'));        
    }
    /** 
     * Handle redirect user to page login when not logged in
     */
    public static function redirect_to_login() {
        if(!is_user_logged_in() && is_page('post-listing')) {
            $link = me_get_page_permalink('user-profile');
            $link = add_query_arg(array('redirect' => get_permalink()), $link);
            wp_redirect( $link );
            exit;
        }
    }
    /**
     * Handling listing data to create new listing
     * @since 1.0
     */
    public static function process_insert($data) {
        if (!empty($_POST['insert_lisiting']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-insert_listing')) {
            $new_listing = ME_Listing_Handle::insert($_POST, $_FILES);
            if (is_wp_error($new_listing)) {
                me_wp_error_to_notices($new_listing);
            } else {
                // set the redirect link after submit new listing
                if (isset($_POST['redirect'])) {
                    $redirect = $_POST['redirect'];
                } else {
                    $redirect = get_permalink($new_listing);
                }
                /**
                 * action filter redirect link after user submit a new listing
                 * @param String $redirect
                 * @param Object $new_listing Listing object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_insert_listing_redirect', $redirect, $new_listing);
                wp_redirect($redirect, 302);
                exit;
            }
        }
    }
    /**
     * Handling listing data to update
     * @since 1.0
     */
    public static function process_update($data) {
        if (!empty($_POST['update_lisiting']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-update_listing')) {
            $listing = ME_Listing_Handle::update($_POST);
            if (is_wp_error($new_listing)) {
                me_wp_error_to_notices($listing);
            } else {
                // set the redirect link after update listing
                if (isset($_POST['redirect'])) {
                    $redirect = $_POST['redirect'];
                } else {
                    $redirect = get_permalink($listing->ID);
                }
                /**
                 * action filter redirect link after user update listing
                 * @param String $redirect
                 * @param Object $listing Listing object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_update_listing_redirect', $redirect, $listing);
                wp_redirect($redirect, 302);
                exit;
            }
        }
    }
    /**
     * Retrieve sub category select template
     * @since 1.0
     */
    public static function load_sub_category() {
        if (isset($_REQUEST['parent-cat'])) {
            ob_start();
            me_get_template_part('post-listing/sub-cat');
            $content = ob_get_clean();
            wp_send_json_success($content);
        }
    }
}

ME_Listing_Handle_Form::init_hook();