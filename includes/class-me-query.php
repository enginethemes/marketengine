<?php
/**
 * Class ME Query
 *
 * Filter & sort the listing query, setup site enpoint, custom order post link
 *
 * @category Class
 * @package Includes/Query
 * @version 1.0
 */
class ME_Query
{
    static $instance = null;

    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        add_action('init', array($this, 'init_endpoint'));
        add_action('pre_get_posts', array($this, 'filter_pre_get_posts'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_filter('post_type_link', array($this, 'custom_order_link'), 1, 3);
    }

    /**
     * add account endpoint
     */
    public function init_endpoint()
    {
        $this->add_enpoint();

        $this->rewrite_payment_flow_url();

        $this->rewrite_account_url();

        $this->rewrite_edit_listing_url();

        $this->rewrite_order_url();
    }

    /**
     * Load the enpoints name
     *
     * Retrieve the enpoint list, if the value is not set get the default
     *
     * @since 1.0
     * @return array of endpoints
     */
    private function load_endpoints_name()
    {
        $endpoint_arr = me_default_endpoints();
        foreach ($endpoint_arr as $key => $value) {
            $option_value = me_option('ep_' . $key);
            if (isset($option_value) && !empty($option_value) && $option_value != $value) {
                $endpoint_arr[$key] = $option_value;
            }
        }
        return $endpoint_arr;
    }

    private function add_enpoint()
    {
        $endpoint_arr = $this->load_endpoints_name();
        foreach ($endpoint_arr as $key => $value) {
            add_rewrite_endpoint($value, EP_ROOT | EP_PAGES, str_replace('_', '-', $key));
        }
    }

    /**
     * Rewrite page url.
     *
     * @access public
     */
    public function rewrite_payment_flow_url()
    {
        $rewrite_args = array(
            array(
                'page_id'       => me_get_option_page_id('confirm_order'),
                'endpoint_name' => me_get_endpoint_name('order-id'),
                'query_var'     => 'order-id',
            ),

            array(
                'page_id'       => me_get_option_page_id('cancel_order'),
                'endpoint_name' => me_get_endpoint_name('order-id'),
                'query_var'     => 'order-id',
            ),
            array(
                'page_id'       => me_get_option_page_id('me_checkout'),
                'endpoint_name' => me_get_endpoint_name('pay'),
                'query_var'     => 'pay',
            ),
        );
        foreach ($rewrite_args as $key => $value) {
            if ($value['page_id'] > -1) {
                $page = get_post($value['page_id']);
                add_rewrite_rule('^/' . $page->post_name . '/' . $value['endpoint_name'] . '/([^/]*)/?', 'index.php?page_id=' . $value['page_id'] . '&' . $value['query_var'] . '=$matches[1]', 'top');
            }
        }
    }

    public function rewrite_account_url()
    {
        $endpoints = array('orders', 'purchases', 'listings');
        foreach ($endpoints as $endpoint) {
            add_rewrite_rule('^(.?.+?)/' . me_get_endpoint_name($endpoint) . '/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]&' . $endpoint, 'top');
        }
    }

    public function rewrite_edit_listing_url()
    {
        $edit_listing_page = me_get_option_page_id('edit_listing');
        if ($edit_listing_page > -1) {
            $page = get_post($edit_listing_page);
            add_rewrite_rule('^/' . $page->post_name . '/' . me_get_endpoint_name('listing_id') . '/?([0-9]{1,})/?$', 'index.php?page_id=' . $edit_listing_page . '&listing_id' . '=$matches[1]', 'top');
        }
    }
    /**
     * Filters order detail url.
     *
     * @since       1.0.0
     * @version     1.0.0
     */
    public function rewrite_order_url()
    {
        $order_endpoint = me_get_endpoint_name('order_id');
        add_rewrite_rule($order_endpoint . '/([0-9]+)/?$', 'index.php?post_type=me_order&p=$matches[1]', 'top');
    }

    /**
     * Filters order detail url.
     *
     * @param       string $permalink
     * @param       object $post
     * @return      string $permalink
     *
     * @since       1.0.0
     * @version     1.0.0
     */
    public function custom_order_link($order_link, $post = 0)
    {
        if ($post->post_type == 'me_order') {
            if (get_option('permalink_structure')) {
                $pos        = strrpos($order_link, '%/');
                $order_link = substr($order_link, 0, $pos + 1);
            }
            return str_replace('%post_id%', $post->ID, $order_link);
        } else {
            return $order_link;
        }
    }

    /**
     *
     */
    public function filter_pre_get_posts($query)
    {
        // Only affect the main query
        if (!$query->is_main_query()) {
            return;
        }

        if (is_archive('listing') && !is_admin()) {
            $query->set('post_status', 'publish');
        }

        if ($query->is_author()) {
            $query->set('post_type', 'listing');
            $query->set('post_status', 'publish');
        }

        global $wp_post_types;
        if (is_search()) {
            $wp_post_types['listing']->exclude_from_search = true;
        }

        return $this->filter_listing_query($query);

    }

    /**
     * Filter, sort listing
     */
    public function filter_listing_query($query)
    {
        if (!$query->is_post_type_archive('listing') && !$query->is_tax(get_object_taxonomies('listing'))) {
            return $query;
        }

        $query = $this->sort_listing_query($query);
        $query = $this->filter_price_query($query);
        $query = $this->filter_listing_type_query($query);
        $query = $this->filter_search_query($query);
    }
    /**
     * Filter query listing by price
     * @param object $query The WP_Query Object
     * @since 1.0
     */
    public function filter_price_query($query)
    {
        if (!empty($_GET['price-min']) && !empty($_GET['price-max'])) {
            $min_price                                       = $_GET['price-min'];
            $max_price                                       = $_GET['price-max'];
            $query->query_vars['meta_query']['filter_price'] = array(
                'key'     => 'listing_price',
                'value'   => array($min_price, $max_price),
                'type'    => 'numeric',
                'compare' => 'BETWEEN',
            );

            $query->query_vars['meta_query']['type'] = array(
                'key'     => '_me_listing_type',
                'value'   => 'purchasion',
                'compare' => '=',
            );

            $query->query_vars['meta_query']['relation'] = 'AND';

        }
        return $query;
    }

    /**
     * Filter query listing by listing type
     * @param object $query The WP_Query Object
     * @since 1.0
     */
    public function filter_listing_type_query($query)
    {
        if (!empty($_GET['type'])) {
            $query->query_vars['meta_query']['filter_type'] = array(
                'key'     => '_me_listing_type',
                'value'   => $_GET['type'],
                'compare' => '=',
            );
        }
        return $query;
    }

    /**
     * Filter query listing by keyword
     * @param object $query The WP_Query Object
     * @since 1.0
     */
    public function filter_search_query($query)
    {
        if (!empty($_GET['keyword'])) {
            $query->query_vars['s'] = $_GET['keyword'];
        }
        return $query;
    }

    /**
     * Sort the listing
     * @param object $query The WP_Query Object
     * @since 1.0
     */
    public function sort_listing_query($query)
    {
        if (!empty($_GET['orderby'])) {
            switch ($_GET['orderby']) {
                case 'date':
                    $query->set('orderby', 'date');
                    break;
                case 'price':
                    $query = $this->sort_by_price($query, 'asc');
                    break;
                case 'price-desc':
                    $query = $this->sort_by_price($query, 'desc');
                    break;
                case 'rating':
                    $query->set('meta_key', '_me_rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'desc');
            }
        }
        return $query;
    }

    public function sort_by_price($query, $asc = 'asc')
    {
        $query->set('meta_key', 'listing_price');
        $meta_query = array(
            'relation'     => 'AND',
            'filter_price' => array(
                'key' => 'listing_price',
            ),
            'type'         => array(
                'key'     => '_me_listing_type',
                'value'   => 'purchasion',
                'compare' => '=',
            ),
        );
        $query->set('meta_query', $meta_query);
        $query->set('orderby', 'meta_value_num');
        $query->set('order', $asc);
        return $query;
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'order-id';
        $vars[] = 'keyword';

        return $vars;
    }
}

ME_Query::instance();
