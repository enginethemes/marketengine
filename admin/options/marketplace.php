<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

return apply_filters('marketengine_marketplace_options',
    array(
        'general'      => array(
            'title'    => __("General", "enginethemes"),
            'slug'     => 'general-section',
            'type'     => 'section',
            'template' => array(
                'user-email-confirmation' => array(
                    'label'       => __("Email Confirmation", "enginethemes"),
                    'description' => __("", "enginethemes"),
                    'slug'        => 'user-email-confirmation',
                    'type'        => 'switch',
                    'name'        => 'user-email-confirmation',
                    'template'    => array(),
                ),
                'commission_fee'          => array(
                    'label'       => __("Commission Fee", "enginethemes"),
                    'description' => __("The commission fee will charge the seller", "enginethemes"),
                    'slug'        => 'paypal-commission-fee',
                    'type'        => 'number',
                    'class_name'  => 'positive',
                    'attributes'  => array(
                        'min' => 0,
                    ),
                    'name'        => 'paypal-commission-fee',
                ),
                'currency-sign'           => array(
                    'label'       => __("Currency Sign", "enginethemes"),
                    'description' => __("", "enginethemes"),
                    'slug'        => 'payment-currency-sign',
                    'type'        => 'textbox',
                    'name'        => 'payment-currency-sign',
                ),
                'currency-sign-postion'   => array(
                    'label'       => __("", "enginethemes"),
                    'description' => __("", "enginethemes"),
                    'slug'        => 'currency-sign-postion',
                    'type'        => 'switch',
                    'name'        => 'currency-sign-postion',
                    'text'        => array(__('Left', 'enginethemes'), __('Right', 'enginethemes')),
                    'template'    => array(),
                ),
                'currency-lable'          => array(
                    'label'       => __("Currency Lable", "enginethemes"),
                    'description' => __("", "enginethemes"),
                    'slug'        => 'payment-currency-lable',
                    'type'        => 'textbox',
                    'name'        => 'payment-currency-lable',
                ),
                'currency-code'           => array(
                    'label'       => __("Currency Code", "enginethemes"),
                    'description' => __("", "enginethemes"),
                    'slug'        => 'payment-currency-code',
                    'type'        => 'textbox',
                    'name'        => 'payment-currency-code',
                ),
                'dispute-time-limit'      => array(
                    'label'       => __("Displute Time Limit", "enginethemes"),
                    'description' => __("", "enginethemes"),
                    'slug'        => 'dispute-time-limit',
                    'type'        => 'number',
                    'class_name'  => 'no-zero positive',
                    'attributes'  => array(
                        'min' => 1,
                    ),
                    'name'        => 'dispute-time-limit',
                ),
            ),
        ),

        'listing-type' => array(
            'title'    => __("Listing Type", "enginethemes"),
            'slug'     => 'listing-type-section',
            'type'     => 'section',
            'template' => array(

            ),
        ),
        'sample-data'  => array(
            'title'    => __("Sample Data", "enginethemes"),
            'slug'     => 'sample-data-section',
            'type'     => 'section',
            'template' => array(

            ),
        ),
    )
);