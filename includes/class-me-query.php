<?php
add_action('init', 'me_init');
function me_init() {
    add_rewrite_endpoint('forgot-password', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('reset-password', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('register', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('edit-profile', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('change-password', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('listings', EP_ROOT | EP_PAGES);
}

// todo: query listing order by date, rating, price
// todo: filter listing by price
// todo: filter listing by listing type

function me_filter_listing_query($query) {
    // We only want to affect the main query
    if (!$query->is_main_query()) {
        return;
    }

    if (!$query->is_post_type_archive('listing') && !$query->is_tax(get_object_taxonomies('listing'))) {
        return $query;
    }

    $query = me_filter_price_query($query);
    $query = me_filter_listing_type_query($query);
    $query = me_sort_listing_query($query);

    return $query;
}
add_filter('pre_get_posts', 'me_filter_listing_query');

function me_filter_price_query($query) {
    if (!empty($_GET['price-min']) && !empty($_GET['price-max'])) {
        $min_price                                       = $_GET['price-min'];
        $max_price                                       = $_GET['price-max'];
        $query->query_vars['meta_query']['filter_price'] = array(
            'key'     => 'listing_price',
            'value'   => array($min_price, $max_price),
            'type'    => 'numeric',
            'compare' => 'BETWEEN',
        );
    }
    return $query;
}

function me_filter_listing_type_query($query) {
    if (!empty($_GET['type'])) {
        $query->query_vars['meta_query']['filter_type'] = array(
            'key'     => '_me_listing_type',
            'value'   => $_GET['type'],
            'compare' => '=',
        );
    }
    return $query;
}

function me_sort_listing_query($query) {
    if (!empty($_GET['orderby'])) {
        switch ($_GET['orderby']) {
        case 'date':
            $query->set('orderby', 'date');
            break;
        case 'price':
            $query->set('meta_key', 'listing_price');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'asc');
            break;
        case 'price-desc':
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'desc');
            break;
        case 'rating':
            $query->set('meta_key', '_me_rating');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'desc');
        }
    }
    return $query;
}