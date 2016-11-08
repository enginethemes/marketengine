<?php 
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$inquiries = marketengine_inquiries_report($_REQUEST);
if(empty($_REQUEST['paged'])) {
	$i = 1;	
}else {
	$i = ($_REQUEST['paged'] - 1) * get_option( 'posts_per_page' ) + 1;
}

$quant = empty($_REQUEST['quant']) ? 'day' : $_REQUEST['quant'];
?>

<div class="me-tabs-content">
	<!-- <ul class="me-nav me-section-nav">
		<li class="active"><span>Revenue</span></li>
		<li><span>Members</span></li>
		<li><span>Orders &amp; Inquiries</span></li>
	</ul> -->
	<div class="me-section-container">

		<div class="me-section-content">
			<div class="me-revenue-section">
				<h3>Report 4 fields listing</h3>
				<?php me_get_template('admin/filter'); ?>
				<div class="me-table me-report-table">
					<div class="me-table-rhead">
						<div class="me-table-col">
							<a href="#" class="me-sort-asc"><?php _e("No.", "enginethemes"); ?></a>
						</div>
						<div class="me-table-col">
							<a href="#" class="me-sort-desc"><?php _e("Date", "enginethemes"); ?></a>
						</div>
						<div class="me-table-col">
							<a href="#" class="me-sort-asc"><?php _e("Total Inquiries", "enginethemes"); ?></a>
						</div>
					</div>
					<?php foreach ($inquiries['posts'] as $key => $inquiry) : ?>
						
						<div class="me-table-row">
							<div class="me-table-col"><?php echo $i ?></div>
							<div class="me-table-col">
								<?php echo marketengine_get_start_and_end_date($quant, $inquiry->quant, $inquiry->year); ?>
							</div>
							<div class="me-table-col"><?php echo $inquiry->count; ?></div>
						</div>

						<?php $i++; ?>

					<?php endforeach; ?>
				</div>
				<?php me_get_template('admin/pagination', array('query' => $inquiries)); ?>
			</div>
		</div>
	</div>
</div>