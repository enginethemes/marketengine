<?php
	$checked = !isset($_POST['me-receive-item']) || (isset($_POST['me-receive-item']) && $_POST['me-receive-item'] == 'true') ? true : false;
?>

<div class="me-receive-item">
	<h3><?php _e('Did you receive your item?', 'enginethemes'); ?></h3>
	<label for="me-receive-item-yes"><input class="me-receive-item-field" id="me-receive-item-yes" type="radio" data-get-refund-block="dispute-get-refund-yes" name="me-receive-item" value="true" <?php checked($checked, true); ?>><?php _e('Yes', 'enginethemes'); ?></label>

	<label for="me-receive-item-no"><input class="me-receive-item-field" id="me-receive-item-no" type="radio" data-get-refund-block="dispute-get-refund-no" name="me-receive-item" value="false" <?php checked($checked, false); ?>><?php _e('No', 'enginethemes'); ?></label>
</div>