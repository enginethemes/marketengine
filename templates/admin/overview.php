<?php 
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$admin_report_tabs = array(
	'listings' => __("Listings", "enginethemes"),
	'members' => __("Members", "enginethemes"),
	'orders' => __("Orders", "enginethemes"),
	'inquiries' => __("Inquiries", "enginethemes"),
);

if(empty($_REQUEST['tab'])) {
	$_REQUEST['tab'] = 'listings'; 
}
?>

<div class="marketengine-tabs">
	<ul class="me-nav me-tabs-nav">
		<?php 
		foreach ($admin_report_tabs as $key => $tab) : ?>
			<li <?php if($_REQUEST['tab'] == $key ) {echo 'class="active"';} ?>><a href="?page=me-reports&tab=<?php echo $key; ?>"><?php echo $tab; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="me-tabs-container">
	<?php 
		me_get_template('admin/'. $_REQUEST['tab']);
	?>
	</div>
</div>
