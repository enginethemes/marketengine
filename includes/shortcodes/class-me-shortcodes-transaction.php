<?php
class ME_Shortcodes_Transaction
{
    public static function init_shortcodes()
    {
        add_shortcode('me_checkout_form', array(__CLASS__, 'checkout_form'));
        add_shortcode('me_confirm_order', array(__CLASS__, 'confirm_order'));
        add_shortcode('me_cancel_payment', array(__CLASS__, 'cancel_order'));
        add_shortcode('me_inquiry_form', array(__CLASS__, 'inquiry_form'));
    }

    public static function checkout_form()
    {
        if (is_user_logged_in()) {
            ob_start();
            me_get_template('checkout/checkout');
            $content = ob_get_clean();
            return $content;
        } else {
            return ME_Shortcodes_Auth::me_login_form();
        }
    }

    public static function confirm_order()
    {
        $paypal = ME_PPAdaptive_Request::instance();
        $paypal->complete_payment($_REQUEST);

        $order_id = get_query_var('order-id');
        if ($order_id) {
            $order = new ME_Order($order_id);
            ob_start();
            me_get_template('checkout/confirm', array('order' => $order));
            $content = ob_get_clean();
            return $content;
        }
    }

    public static function cancel_order()
    {
        $order_id = get_query_var('order-id');
        if ($order_id) {
            $order = new ME_Order($order_id);
            ob_start();
            me_get_template('checkout/cancel-payment', array('order' => $order));
            $content = ob_get_clean();
            return $content;
        }
    }

    public static function inquiry_form()
    {
        $user_id = get_current_user_id();

        if (!$user_id) {
            return ME_Shortcodes_Auth::me_login_form();
        }

        if (!empty($_GET['id'])) {
            $listing_id = $_GET['id'];
            $listing    = get_post($listing_id);

            if ($listing) {
                $listing = new ME_Listing_Contact($listing);
            } else {
                return __("Invalid listing.", "enginethemes");
            }

            if ($user_id == $listing->post_author) {
                return __("You can not inquire your self.", "enginethemes");
            }

            if(!ME()->get_current_user()->is_activated()) {
                return __("You can must confirm your email account to start this conversation.", "enginethemes");
            }

            ob_start();
            me_get_template('inquiry/inquiry', array('listing' => $listing));
            $content = ob_get_clean();
            return $content;
        }

        if (!empty($_GET['inquiry_id'])) {
            $inquiry_id = $_GET['inquiry_id'];
            $inquiry    = me_get_message($inquiry_id);

            if ($user_id != $inquiry->sender && $user_id != $inquiry->receiver) {
                return __("You do not have permision to view this inquiry details.", "enginethemes");
            }
            ob_start();
            me_get_template('inquiry/inquiry-message', array('inquiry' => $inquiry));
            $content = ob_get_clean();
            return $content;
        }

    }
}
ME_Shortcodes_Transaction::init_shortcodes();