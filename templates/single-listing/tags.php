<?php do_action('marketengine_before_single_listing_tags'); ?>

<div class="me-tags">
	<?php if( get_the_terms( '', 'listing_tag', '&nbsp') ) : ?>
	<span><?php _e("Tags:", "enginethemes"); the_terms('', 'listing_tag', '&nbsp;'); ?> </span>
	<?php endif; ?>
</div>

<?php do_action('marketengine_after_single_listing_tags'); ?>