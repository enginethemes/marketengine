<?php
/**
 * The templates for displaying custom field manage page
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.1
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$categories = me_get_listing_categories();

?>

<div class="me-custom-field">
	<h2><?php _e('List of Custom Field', 'enginethemes'); ?></h2>
	<?php me_print_notices(); ?>

	<?php me_get_template('custom-fields/category-select', array('categories' => $categories )); ?>

	<a class="me-add-custom-field-btn" href="<?php echo add_query_arg('view', 'add'); ?>"><?php _e('Add New Custom Field', 'enginethemes'); ?></a>

	<div class="me-custom-field-list">
		<ul class="me-cf-list">
			<?php me_get_template('custom-fields/table-header'); ?>

			<?php
			if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'group-by-category') {
				$customfields = me_cf_get_fields($_REQUEST['category-id']);
				me_get_template('custom-fields/field-list-by-category', array('customfields' => $customfields ));
			} else {
				$customfields = me_cf_fields_query($_REQUEST);
				me_get_template('custom-fields/field-list', array('customfields' => $customfields ));
			}
			?>
		</ul>
	</div>

	<?php if(isset($_REQUEST['view']) && $_REQUEST['view'] != 'group-by-category') : ?>
	<div class="me-pagination-wrap">
		<span class="me-paginations">
		<?php marketengine_cf_pagination( $customfields ); ?>
		</span>
	</div>
	<?php endif; ?>
</div>