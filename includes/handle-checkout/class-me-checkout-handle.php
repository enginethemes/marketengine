<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME_Order_Handle
 *
 * Handling buyer order order behavior
 *
 * @class       ME_Order_Handle
 * @version     1.0
 * @package     Includes/Orders
 * @author      EngineThemesTeam
 * @category    Class
 */
class ME_Checkout_Handle {
    public static function checkout($data) {
        $rules = array(
            'listing_id'     => 'required|numeric',
            'payment_method' => 'required|string',
            'billing_info'   => 'required|array',
        );

        $billing_rules = array(
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|numeric',
            'email'      => 'required|email',
            'address'    => 'required|string',
            'city'       => 'required',
            'country'    => 'required',
        );

        $custom_attributes = array(
            'first_name' => __("first name", "enginethemes"),
            'last_name'  => __("last name", "enginethemes"),
            'phone'      => __("phone", "enginethemes"),
            'email'      => __("billing email", "enginethemes"),
            'city'       => __("billing city", "enginethemes"),
            'address'    => __("billing address", "enginethemes"),
            'country'    => __("billing country", "enginethemes"),
        );

        $is_valid = me_validate($data['billing_info'], $billing_rules, $custom_attributes);
        if (!$is_valid) {
            $errors = new WP_Error();
            $invalid_data = me_get_invalid_message($data['billing_info'], $billing_rules, $custom_attributes);
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        // create order
        $order = self::create_order($data);

        if (!is_wp_error($order)) {
            $order->set_address($data['billing_info']);
            if (!empty($data['shipping'])) {
                $order->set_address($data['shipping'], 'shipping');
            }
        }

        return $order;
    }

    public static function pay($order) {
        $user_id = get_current_user_id();

        if ($order->post_author != $user_id) {
            return new WP_Error('permission_denied', __("You do not have permission to process this order.", "enginethemes"));
        }

        $payments       = me_get_available_payment_gateways();
        $payment_method = $order->get_payment_method();

        if (!isset($payments[$payment_method])) {
            return new WP_Error("invalid_payment_method", __("The selected payment method is not available now.", "enginethemes"));
        }

        $result = $payments[$payment_method]->setup_payment($order);

        return $result;
    }

    /**
     * Handle order data and store to database
     *
     * @param array $data
     *
     * @since 1.0
     * @return WP_Error | ME_Order
     */
    public static function create_order($data) {
        $current_user_id     = get_current_user_id();
        $data['post_author'] = $current_user_id;

        if (empty($data['payment_method']) || !me_is_available_payment_gateway($data['payment_method'])) {
            return new WP_Error("invalid_payment_method", __("The selected payment method is not available now.", "enginethemes"));
        }

        if (empty($data['listing_item'])) {
            return new WP_Error("empty_cart", __("The order is empty.", "enginethemes"));
        }

        $items = array();
        foreach ($data['listing_item'] as $key => $value) {
            $listing = me_get_listing($value['id']);
            if (!$listing) {
                return new WP_Error("invalid_listing", __("The selected listing is invalid.", "enginethemes"));
            }

            if (!$listing->is_available()) {
                return new WP_Error("unavailable_listing", __("The listing is not available for sale.", "enginethemes"));
            }

            if ($listing->post_author == $current_user_id) {
                return new WP_Error('purchase_yourself', __("You cannot buy your listing.", "enginethemes"));
            }

            if (!$value['qty'] || $value['qty'] <= 0) {
                return new WP_Error("invalid_qty", __("The listing quantity must be greater than 1.", "enginethemes"));
            }

            $items[] = array('id' => $listing, 'qty' => $value['qty']);
        }

        $order = me_insert_order($data);
        if (is_wp_error($order)) {
            return $order;
        }

        $order = new ME_Order($order);
        foreach ($items as $item) {
            $order->add_listing($item['id'], $item['qty']);
        }
        $order->set_payment_method($data['payment_method']);

        return $order;
    }

    /**
     * Handle buyer inquire a listing
     */
    public static function inquiry($data) {

        if (empty($data['inquiry_listing'])) {
            return new WP_Error('empty_listing', __("The listing is required.", "enginethemes"));
        }

        if (empty($data['content'])) {
            return new WP_Error('empty_inquiry_content', __("The inquiry content is required.", "enginethemes"));
        }
        //TODO: validate listing id
        $listing_id = $data['inquiry_listing'];
        $listing    = get_post($listing_id);

        if (is_wp_error($listing) || $listing->post_type != 'listing') {
            return new WP_Error('invalid_listing', __("Invalid listing.", "enginethemes"));
        }

        $inquiry_id = me_get_current_inquiry($listing_id);
        // strip html tag
        $content = strip_tags($data['content']);
        if (!$inquiry_id) {
            // create inquiry
            $inquiry_id = me_insert_message(
                array(
                    'post_content' => 'Inquiry listing #' . $listing_id,
                    'post_title'   => 'Inquiry listing #' . $listing_id,
                    'post_type'    => 'inquiry',
                    'receiver'     => get_post_field('post_author', $listing_id),
                    'post_parent'  => $listing_id,
                ), true
            );
            if (is_wp_error($inquiry_id)) {
                return $inquiry_id;
            }
        }

        $message_data = array(
            'listing_id' => $listing_id,
            'content'    => $content,
            'inquiry_id' => $inquiry_id,
        );
        return self::insert_message($message_data);
    }

    public static function insert_message($message_data) {
        $inquiry_id = $message_data['inquiry_id'];
        if ($inquiry_id) {
            // add message to inquiry
            $current_user = get_current_user_id();
            $inquiry      = me_get_message($message_data['inquiry_id']);

            if (!$inquiry) {
                return new WP_Error('invalid_inquiry', __("Invalid inquiry.", "enginethemes"));
            }

            if ($inquiry->sender == $current_user) {
                $receiver = $inquiry->receiver;
            } elseif ($inquiry->receiver == $current_user) {
                $receiver = $inquiry->sender;
            } else {
                return new WP_Error('permission_denied', __("You do not have permision to post message in this inquiry.", "enginethemes"));
            }

            $message_data = array(
                'post_content' => $message_data['content'],
                'post_title'   => 'Message listing #' . $message_data['listing_id'],
                'post_type'    => 'message',
                'receiver'     => $receiver,
                'post_parent'  => $message_data['inquiry_id'],
            );

            $message_id = me_insert_message($message_data, true);
            if (is_wp_error($message_id)) {
                return $message_id;
            }
        }
        return $inquiry_id;
    }

    public static function message($data) {
        $listing_id = $data['inquiry_listing'];
        $inquiry_id = $data['inquiry_id'];
        // strip html tag
        $content = strip_tags($data['content']);
        // add message
        $message_data = array(
            'listing_id' => $listing_id,
            'content'    => $content,
            'inquiry_id' => $inquiry_id,
        );
        return self::insert_message($message_data);
    }
}
