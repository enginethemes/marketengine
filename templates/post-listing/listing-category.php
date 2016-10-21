<?php
$parent_categories = get_terms(array('taxonomy' => 'listing_category', 'hide_empty' => false, 'parent' => 0));
$selected_cat = empty($_POST['parent_cat']) ? $selected_cat : $_POST['parent_cat'];
if ($selected_cat) {
    $child_cats = get_terms(array('taxonomy' => 'listing_category', 'hide_empty' => false, 'parent' => $selected_cat));
}
$selected_sub_cat = empty($_POST['sub_cat']) ? $selected_sub_cat : $_POST['sub_cat'];
?>

<?php do_action('marketengine_before_post_listing_category_form');?>

<div class="marketengine-post-step active select-category">
	<div class="marketengine-group-field" id="me-parent-cat-container">
		<div class="marketengine-select-field">
		    <label class="text"><?php _e("Category", "enginethemes");?></label>
		    <select class="select-category  parent-category" name="parent_cat">
		    	<option value=""><?php _e("Select your category", "enginethemes");?></option>
		    	<?php foreach ($parent_categories as $key => $parent_cat): ?>
			    	<option value="<?php echo $parent_cat->term_id; ?>" <?php selected($selected_cat, $parent_cat->term_id);?> >
			    		<?php echo $parent_cat->name; ?>
			    	</option>
		    	<?php endforeach;?>
		    </select>
		</div>
	</div>
	<div class="marketengine-group-field" id="me-sub-cat-container">
		<div class="marketengine-select-field">
		    <label class="text"><?php _e("Sub-category", "enginethemes");?></label>
		    <select class="select-category sub-category" name="sub_cat">
		    	<option value=""><?php _e("Select sub category", "enginethemes");?></option>
		    	<?php foreach ($child_cats as $key => $sub_cat): ?>
			    	<option value="<?php echo $sub_cat->term_id; ?>" <?php selected($selected_sub_cat, $sub_cat->term_id);?> >
			    		<?php echo $sub_cat->name; ?>
			    	</option>
		    	<?php endforeach;?>
		    </select>
		</div>
	</div>
</div>

<?php do_action('marketengine_after_post_listing_category_form');?>