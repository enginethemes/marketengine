<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/post-listing/post-listing.php.
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if(me_option('user-email-confirmation')) {
	$curr_user_id = get_current_user_id();
	$current_user = new ME_User(wp_get_current_user());
	$is_activated = $current_user->is_activated();

	$can_post_listing = (current_user_can( 'publish_posts' ) && $is_activated ) || current_user_can('manage_options');
} else {
	$can_post_listing = current_user_can( 'publish_posts' );
}

?>

<?php if($can_post_listing) : ?>

<?php do_action('marketengine_before_post_listing_form'); ?>

<div id="marketengine-wrapper" class="marketengine">
	<div class="marketengine-post-listing-wrap">
		<form  id="post-listing-form" class="post-listing-form" method="post" accept-charset="utf-8" enctype="multipart/form-data">

			<?php me_print_notices(); ?>

			<?php do_action('marketengine_post_listing_form_start'); ?>

			<?php me_get_template('post-listing/listing-category', array('selected_cat' => '', 'selected_sub_cat' => '') );  ?>

			<?php me_get_template('post-listing/listing-type', array('selected_listing_type' => 'purchasion', 'contact_email' => '', 'price' => '', 'unit' => '')); ?>

			<?php me_get_template('post-listing/listing-information', array('listing_content' => '',  'listing_title' => '')); ?>

			<?php do_action('marketengine_post_listing_information_form_fields'); ?>

			<?php me_get_template('post-listing/listing-gallery',  array('listing_gallery' => '', 'listing_image' => '')); ?>

			<?php me_get_template('post-listing/listing-tags', array('default' => '')); ?>

			<?php do_action('marketengine_post_listing_form_fields'); ?>

			<?php wp_nonce_field('me-insert_listing'); ?>

			<div class="marketengine-group-field me-text-center submit-post">
				<input class="marketengine-post-submit-btn" type="submit" name="insert_lisiting" value="<?php _e("SUBMIT", "enginethemes"); ?>">
			</div>
			<div id="debug">
			<a href="<?php echo home_url(); ?>" class="back-link-page"><?php _e("&lt; Cancel", "enginethemes"); ?></a>
			</div>

			<?php do_action('marketengine_post_listing_form_end'); ?>

		</form>
	</div>
</div>

<?php do_action('marketengine_after_post_listing_form'); ?>

<?php else:
	me_get_template('post-listing/post-listing-none');
endif; ?>