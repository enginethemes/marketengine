<?php
/**
 * MarketEngine Custom Field Handle
 *
 * @author  EngineThemes
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class MarketEngine Custom Field Handle
 * Handle MarketEngine Custom Field in post, edit listing form and listing details
 *
 * @package Includes/CustomFields
 * @category Class
 *
 * @since 	1.0.1
 * @version 1.0.0
 */

class ME_Custom_Field_Handle {
	public $instance;

	public static function get_instance() {
		if(is_null(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function init() {
		add_action('marketengine_section', array(__CLASS__, 'marketengine_add_custom_field_section'));

		add_action('wp_loaded', array(__CLASS__, 'marketengine_add_actions'));
		add_action('wp_loaded', array(__CLASS__, 'insert'));
		add_action('wp_loaded', array(__CLASS__, 'delete'));
		add_action('wp_loaded', array(__CLASS__, 'remove_from_category'));

		add_action('me_load_cf_input', array(__CLASS__, 'load_field_input'));
		add_action('wp_ajax_me_cf_load_input_type', array(__CLASS__, 'load_field_input_ajax'));
		add_action('wp_ajax_check_field_name', array(__CLASS__, 'check_field_name'));
		add_action('wp_ajax_me_cf_sort', array(__CLASS__, 'me_cf_sort'));
	}

	/**
	 * Prepares content of custom field section
	 *
	 * @since 	1.0.1
	 * @version 1.0.0
	 */
	public static function marketengine_add_actions() {
	    if( is_admin() && isset($_REQUEST['section']) && $_REQUEST['section'] == 'custom-field') {
	        add_action( 'wp_print_scripts', array(__CLASS__, 'marketengine_print_script'), 100 );
	        add_action('get_custom_field_template', 'marketengine_custom_field_template');
	    }
	}

	/**
	 * Removes ajax handle of option
	 *
	 * @since 	1.0.1
	 * @version 1.0.0
	 */
	public static function marketengine_print_script() {
	   wp_dequeue_script( 'option-view' );
	   if( is_admin() && isset($_REQUEST['view']) && $_REQUEST['view'] == 'group-by-category' ) {
	   		wp_enqueue_script('cf_sort', ME_PLUGIN_URL . "assets/admin/custom-field-sort.js", array('jquery-ui'));
	   }
	}

	public static function insert() {
		if( is_admin() && isset($_REQUEST['view']) && ($_REQUEST['view'] == 'add' || $_REQUEST['view'] == 'edit' ) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-insert_custom_field') ) {
			$term_ids = isset($_POST['field_for_categories']) ? $_POST['field_for_categories'] : array();
            $_POST['count'] = count($term_ids);

            $attributes = self::filter_field_attribute();
            $_POST['field_constraint'] = $attributes;

            if($_REQUEST['view'] == 'add') {
				$field_id = me_cf_insert_field($_POST, true);
            } else {
            	$_POST['field_id'] = $_REQUEST['custom-field-id'];
            	$current_cats = me_cf_get_field_categories($_REQUEST['custom-field-id']);
            	self::remove_categories(array(
            		'field_id'		=> $_POST['field_id'],
            		'current_cats' => $current_cats,
            		'new_cats'	=> $term_ids,
            	));

            	$field_id = me_cf_update_field($_POST, true);
            }

			if(is_wp_error($field_id)) {
				me_wp_error_to_notices($field_id);
			} else {

				$result = self::set_field_category($field_id, $term_ids);

				if(is_wp_error($result)) {
					me_wp_error_to_notices($result);
					return;
				}

				self::me_cf_add_options();

				if($_POST['redirect']) {
					wp_redirect($_POST['redirect']);
					exit;
				}
			}
		}
	}

	public static function delete() {
		if(is_admin() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete-custom-field' && isset($_REQUEST['_wp_nonce']) && wp_verify_nonce($_REQUEST['_wp_nonce'], 'delete-custom-field') && isset($_REQUEST['custom-field-id'])) {
			$result = me_cf_delete_field($_REQUEST['custom-field-id']);
			if(is_wp_error($result)) {
				me_wp_error_to_notices($result);
				return;
			}

			$redirect = remove_query_arg(array('action', '_wp_nonce', 'custom-field-id'));
			wp_redirect($redirect);
			exit;
		}
	}

	public static function remove_from_category() {
		if(is_admin() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'remove-from-category' && isset($_REQUEST['_wp_nonce']) && wp_verify_nonce($_REQUEST['_wp_nonce'], 'remove-from-category') && isset($_REQUEST['custom-field-id'])) {

			$term_ids = me_cf_get_field_categories($_REQUEST['custom-field-id']);
			if(count($term_ids) == 1) {
				$result = me_cf_delete_field($_REQUEST['custom-field-id']);
				if(is_wp_error($result)) {
					me_wp_error_to_notices($result);
					return;
				}
			} else {
				me_cf_remove_field_category($_REQUEST['custom-field-id'], $_REQUEST['category-id']);
			}

			$redirect = remove_query_arg(array('action', '_wp_nonce', 'custom-field-id'));
			wp_redirect($redirect);
			exit;
		}
	}

	public static function load_field_input_ajax() {
		$options = marketengine_load_input_by_field_type($_POST);
	    wp_send_json(array(
	    	'options' => $options,
	    ));
	}

	public static function load_field_input() {
		$options = marketengine_load_input_by_field_type($_POST);
	    echo $options;
	}

	public static function set_field_category($field_id, $term_ids) {
		$result = '';
		if(isset($term_ids) && !empty($term_ids)) {

			$field_cat = me_cf_get_field_categories($field_id);

			foreach($term_ids as $key => $term_id) {
				if(!in_array($term_id, $field_cat) ) {
					$field_count = count(me_cf_get_fields($term_id));
					$result = me_cf_set_field_category( $field_id, $term_id, $field_count);
				}
			}
		} else {
			$result = new WP_Error('invalid_taxonomy', __('Categories is required!', 'enginethemes'));
		}
		return $result;
	}

	public static function remove_categories($args) {
		extract($args);
		$unuse_cats = array_diff($current_cats, $new_cats);

		foreach ($unuse_cats as $key => $cat) {
    		$removed = me_cf_remove_field_category($field_id, $cat);
	    	if(is_wp_error($removed)) {
	    		me_wp_error_to_notices($removed);
				return;
	    	}
	    }
	}

	public static function filter_field_attribute() {
		$temp = '';
		if(isset($_POST['field_constraint']) && !empty($_POST['field_constraint'])) {
			$temp .= 'required';
		}

		if(isset($_POST['field_minimum_value']) && !empty($_POST['field_minimum_value'])) {
			$temp .= '|min:' . $_POST['field_minimum_value'];
		}

		if(isset($_POST['field_maximum_value']) && !empty($_POST['field_maximum_value'])) {
			$temp .= '|max:' . $_POST['field_maximum_value'];
		}

		if(isset($_POST['field_type']) && $_POST['field_type'] == 'date') {
			$temp .= '|date';
		}

		return $temp;
	}

	public static function check_field_name() {
		if($_POST['current_field_id'] != -1) {
			$field = me_cf_get_field($_POST['current_field_id']);

			if($field) {
				wp_send_json( array(
					'unique'	=> false,
					'message'	=> __('Field name cannot be changed.', 'enginethemes'),
				) );
			}
		}

		$field = me_cf_is_field_name_exists($_POST['field_name']);

		if($field) {
			$unique = false;
			$message = __('Field name must be unique.', 'enginethemes');
		} else {
			$unique = true;
			$message = '';
		}

		wp_send_json( array(
			'unique'	=> $unique,
			'message'	=> $message,
		) );
	}

	public static function marketengine_add_custom_field_section( $sections ) {
	    if(!isset($_REQUEST['tab']) || $_REQUEST['tab'] == 'marketplace-settings') {
	        $sample_data = $sections['sample-data'];
	        $sections['custom-field'] = array(
	            'title'  => __('Custom Fields', 'enginethemes'),
	            'slug'   => 'custom-field',
	            'type'   => 'section',
	        );
	        unset($sections['sample-data']);
	        $sections['sample-data'] = $sample_data;
	    }
	    return $sections;
	}

	public static function me_cf_add_options() {
		if(isset($_POST['field_options'])) {
			me_cf_register_field_taxonomy($_POST);

        	if(empty($_POST['field_options'])) {
        		return new WP_Error('field_option_empty', __("Field option cannot be empty.", 'enginethemes'));
        	}

        	$old_options = me_cf_get_field_options($_POST['field_name']);
        	$new_options = self::field_options_to_array(trim($_POST['field_options']));
        	$new_options = self::filter_field_options($new_options, $old_options);
        	$new_options = self::sanitize_field_options_array($new_options);

        	foreach($new_options as $key => $option) {
        		wp_insert_term(trim($option), $_POST['field_name'] , array('slug' => sanitize_title(trim($key))) );
        	}
        }
	}

	private static function filter_field_options($new_options, $old_options = array()) {
		$old = array();
        foreach($old_options as $key => $option) {
            $old[$option['key']] = $option['label'];
        }

        $options_remove = array_diff($old, $new_options);

        foreach ($options_remove as $key => $option) {
        	$term = get_term_by('slug', $key, $_POST['field_name']);
        	wp_delete_term($term->term_id, $_POST['field_name']);
        }
        return $new_options;
	}

	private static function field_options_to_array($options) {
		$options = explode(PHP_EOL, $options);
		$array = array();
		foreach($options as $key => $option) {
    		$temp = explode(':', $option);
    		$temp = self::sanitize_field_options_array($temp);
        	$array[sanitize_title(trim($temp[0]))] = trim($temp[1]);
    	}
    	return $array;
	}

	private static function sanitize_field_options_array($options) {
		if(sizeof($options) == 1) {
			$options[1] = $options[0];
		}
		if(!empty($options[0]) || !empty($options[1])) {
    		if(empty($options[0])) {
    			$options[0] = sanitize_title($options[1]);
    		}
    		if(empty($options[1])) {
    			$options[1] = $options[0];
    		}
    	}
    	return $options;
	}

	public static function me_cf_sort() {
		if( is_admin() ) {
			parse_str($_POST['order'], $fields);
			$fields = $fields['me-cf-item'];
			foreach ($fields as $order => $field_id) {
				$result = me_cf_set_field_category( $field_id, $_POST['category_id'], $order);
				if( is_wp_error($result) ) {
					wp_send_json(array(
						'status'	=> false,
						'message'	=> $result,
					));
				}
			}

			wp_send_json(array(
				'status'	=> true,
				'message'	=> 'Sort custom fields successfully',
				'asd'		=> $result,
				'field_id'	=> $field_id,
				'category'	=> $_POST['category_id'],
				'order' 	=>$order
			));
		}
	}
}


ME_Custom_Field_Handle::init();