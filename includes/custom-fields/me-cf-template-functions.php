<?php
/**
 * List custom field type
 *
 * @since 	1.0.1
 */
function me_list_custom_field_type() {
	$field_types = array(
		array(
			'label'		=> __("Basic", "enginethemes"),
			'options'	=> array(
				'text'		=> 'Text',
				'textarea'	=> 'Textarea',
				'number'	=> 'Number',
				'date'		=> 'Date',
			),
		),
		array(
			'label'		=> __("Choose", "enginethemes"),
			'options'	=> array(
				'checkbox'				 => 'Checkbox',
				'radio'					 => 'Radio',
				'single-select' => 'Dropdown Single Select',
				'multi-select'	 => 'Dropdown Multi Select',
			),
		),
	);

	$field_types = apply_filters('filter_custom_field', $field_types);

	return $field_types;
}

/**
 * Loads custom field templates
 *
 * @since 	1.0.1
 * @version 1.0.0
 */
function marketengine_custom_field_template() {
    if(isset($_REQUEST['view'])) {
        if($_REQUEST['view'] == 'add') {
            me_get_template('custom-fields/admin-custom-field-form');
        } elseif($_REQUEST['view'] == 'edit') {
            $field_obj = me_cf_get_field($_REQUEST['custom-field-id']);
            $field_obj['field_for_categories'] = me_cf_get_field_categories($_REQUEST['custom-field-id']);
            me_get_template('custom-fields/admin-custom-field-form', array('field_obj' => $field_obj));
        } elseif($_REQUEST['view'] == 'group-by-category') {
            me_get_template('custom-fields/admin-custom-field');
        }
    } else {
        me_get_template('custom-fields/admin-custom-field');
    }
}

function marketengine_load_input_by_field_type($args) {
	$placeholder = isset($args['field_placeholder']) ? $args['field_placeholder'] : '';
    $attribute = me_field_attribute_array($args);
    $attribute = wp_parse_args($attribute, array('min'=>'','max'=>''));

	$option_values = isset($args['option_values']) ? $args['option_values'] : '';
	$option_none = isset($args['option_none']) ? $args['option_none'] : '';

    $options = '';

    switch($args['field_type']) {
        case 'text':
        case 'textarea':
            ob_start();
            me_get_template('custom-fields/admin-field-placeholder', array('placeholder' => $placeholder));
            $options = ob_get_clean();
            break;

        case 'number':
            ob_start();
            me_get_template('custom-fields/admin-field-placeholder', array('placeholder' => $placeholder));

            me_get_template('custom-fields/admin-field-minimum-value', array('min' => $attribute['min']));

            me_get_template('custom-fields/admin-field-maximum-value', array('max' => $attribute['max']));
            $options = ob_get_clean();
            break;
        case 'date':
            break;

        case 'checkbox':
        case 'radio':
            ob_start();
            me_get_template('custom-fields/admin-field-option');
            $options = ob_get_clean();
            break;

        case 'single-select':
        case 'multi-select':
            $options .= '<div class="me-group-field">';
            $options .= '<label class="me-title">'.__('Option none', 'enginethemes').'</label>';
            $options .= '<span class="me-field-control">';
            $options .= '<input class="me-input-field" type="text" name="option_none" value="'.esc_attr($placeholder).'">';
            $options .= '</span>';
            $options .= '</div>';
            $options .= '<div class="me-group-field">';
            $options .= '<label class="me-title">'.__('Option','enginethemes').'</label>';
            $options .= '<span class="me-field-control">';
            $options .= '<textarea class="me-textarea-field" name="option_values" placeholder="'.__('Enter each option on a new line', 'enginethemes').'">'.$option_values.'</textarea>';
            $options .= '</span>';
            $options .= '</div>';
            break;

        default:
            break;
    }
    return $options;
}

function marketengine_load_inputs_for_view( $field ) {
	extract($field);
    switch($field_type) {
        case 'text':
        case 'textarea':
            $field_placeholder = isset($field_placeholder) && !empty($field_placeholder) ? $field_placeholder : 'N/A';
    		echo "<p><span>".__('Placeholder:', 'enginethemes')."</span>".$field_placeholder."</p>";

            break;

        case 'number':
        	$field_placeholder = isset($field_placeholder) && !empty($field_placeholder) ? $field_placeholder : 'N/A';
        	$field_attribute = me_field_attribute_array($field);
            $field_attribute = wp_parse_args($field_attribute, array('min' => 'N/A', 'max' => 'N/A'));

	        echo "<p><span>".__('Placeholder:', 'enginethemes')."</span>".$field_placeholder."</p>";
	        echo "<p><span>".__('Minimum value:', 'enginethemes')."</span>".$field_attribute['min']."</p>";
	        echo "<p><span>".__('Maximum value:', 'enginethemes')."</span>".$field_attribute['max']."</p>";

            break;

        case 'date':
            break;

        case 'checkbox':
        case 'radio':
            break;

        case 'single-select':
        case 'multi-select':
            break;

        default:
            break;
    }
}
add_action('me_load_inputs_for_view', 'marketengine_load_inputs_for_view');

function marketengine_cf_pagination($args) {
    $big = 999999999;
    $current_page = empty($_REQUEST['paged']) ? 1 : $_REQUEST['paged'];
    echo paginate_links( array(
        'base' => add_query_arg( 'paged', '%#%' ),
        'format' => '',
        'current' => max( 1, $current_page ),
        'total' => $args['max_numb_pages']
    ) );
}