<div class="me-orderlisting-info">
	<a class="me-orderlisting-thumbs" href="<?php echo $listing->get_permalink(); ?>">
		<?php echo $listing->get_listing_thumbnail(); ?>
	</a>
	<div class="me-listing-info">
		<h2>
			<a href="<?php echo $listing->get_permalink(); ?>">
				<?php echo esc_html( $listing->get_title() ); ?>
			</a>
		</h2>
		<div class="me-rating">
			<i class="icon-me-star"></i>
			<i class="icon-me-star"></i>
			<i class="icon-me-star"></i>
			<i class="icon-me-star"></i>
			<i class="icon-me-star-o"></i>
		</div>
		<div class="me-count-purchases-review">
			<span>12 Purchase</span><span>30 review</span>
		</div>
		<p>
			<?php echo $listing->get_short_description(); ?>
		</p>
	</div>
</div>