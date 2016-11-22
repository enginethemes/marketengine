<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function me_get_listing($post = null) {
    if (null === $post) {
        global $post;
    }

    if (is_numeric($post)) {
        $post = get_post($post);
    }

    return ME_Listing_Factory::instance()->get_listing($post);
}

function me_get_listing_types() {
    $listing_types = array(
        'purchasion' => me_option('purchasion-title', __("Selling", "enginethemes")),//__("Selling", "enginethemes"),
        'contact'    => me_option('contact-title', __("Offering", "enginethemes")),//__("Offering", "enginethemes"),
    );
    return apply_filters('me_get_listing_types', $listing_types);
}

function me_get_listing_type_lable($type) {
    $types = me_get_listing_types();
    return $types[$type];
}

function get_listing_type_by_cat($cat_id) {
    $default_listing_types = me_get_listing_types();
    $type                  = array_rand($default_listing_types);
    return array($type => $default_listing_types[$type]);
}

function me_get_categories($taxonomy = '') {
    if (!taxonomy_exists($taxonomy)) {
        return;
    }

    $terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ));
    return $terms;
}

/**
 * MarketEngine Get Listing Status List
 *
 * Retrieve marketengine listing status list
 *
 * @since 1.0
 * @return array
 */
function me_listings_status_list() {
    $listing_status = array(
        'publish'     => __("Published", "enginethemes"),
        // 'me-pending'  => __("Pending", "enginethemes"),
        'me-archived' => __("Archived", "enginethemes"),
        // 'draft'       => __("Draft", "enginethemes"),
        // 'me-paused'   => __("Paused", "enginethemes"),
    );
    return apply_filters('marketengine_listing_status_list', $listing_status);
}

function me_get_user_rate_listing_score($listing_id, $user_id) {
    $args = array(
        'post_id'        => $listing_id,
        'type'           => 'review',
        'number'         => 1,
        'comment_parent' => 0,
    );

    if($user_id) {
        $args['user_id'] = $user_id;
    }

    $comments = get_comments($args);

    if(!empty($comments)) {
        return get_comment_meta( $comments[0]->comment_ID, '_me_rating_score', true );
    }
    return 0;
}

function me_filter_order_count_result( $results ) {
    $temp = array();
    foreach( $results as $key => $result) {
        $temp[$result->status] = $result->count;
    }

    return $temp;
}

function me_get_listing_categories($args = array('parent' => 0 , 'hide_empty' => false))
{
    $result   = array();
    $termlist = get_terms('listing_category', $args );

    foreach ($termlist as $term) {
        $result[$term->term_id] =  $term->name;
    }

    return $result;
}