<div class="marketengine-group-field">
	<div class="marketengine-input-field">
	    <label for="me_cf_number_3" class="me-field-title">
	    	<?php echo $field['field_title'] ?> 

	    	<?php if(strpos($field['field_constraint'], 'required') === false) : ?>
	    	<small><?php _e("(optional)", "enginethemes"); ?></small>
	    	<?php endif; ?>

	    	<i class="me-help-text icon-me-question-circle" title="<?php echo $field['field_help_text']; ?>"></i>
	    </label>
	    <input <?php //echo me_field_attribute($field); ?> id="<?php echo $field['field_name'] ?>" type="number" placeholder="<?php echo $field['field_placeholder'] ?>" name="<?php echo $field['field_name'] ?>" value="<?php echo $value; ?>">
	</div>
</div>