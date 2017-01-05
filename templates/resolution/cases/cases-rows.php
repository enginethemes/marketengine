<?php
/**
 * The template for displaying dispute cases.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/cases-rows.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */

$transaction = me_get_order($case->post_parent);
$problem = me_get_message_meta($case->ID, '_case_problem', true);

$current_user = get_current_user_id();
$related_party = $case->sender == $current_user ? $case->receiver : $case->sender;
$userdata = get_userdata($related_party);

?>
<div class="me-table-row">
	<div class="me-table-col me-rslt-case">
		<a href="<?php me_rc_dispute_link($case->ID); ?>">
			<?php printf("#%s", $case->ID); ?>
		</a>
	</div>
	<div class="me-table-col me-rslt-status"><?php echo me_dispute_status_label($case->post_status); ?></div>
	<div class="me-table-col me-rslt-problem"><?php echo me_rc_dispute_problem_text($problem); ?></div>
	<div class="me-table-col me-rslt-date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $case->post_date ) ); ?></div>
	<div class="me-table-col me-rslt-related"><?php echo $userdata->display_name; ?></div>
	<div class="me-table-col me-rslt-amount"><?php echo me_price_format($transaction->get_total()); ?></div>
</div>