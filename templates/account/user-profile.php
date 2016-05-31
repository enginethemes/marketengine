<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/reset-pass.php.
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

global $current_user;
$user = new ME_User($current_user);
?>

<?php do_action('marketengine_before_user_profile', $user); ?>

<div class="marketengine marketengine-content">
	
	<?php me_print_notices(); ?>
	
	<div class="me-container-fluid">

		<?php do_action('marketengine_user_profile_start', $user); ?>

		<div class="me-row">

			<?php do_action('marketengine_before_user_profile_avatar', $user); ?>

			<div class="me-col-md-3">
				<div class="marketengine-avatar-user">
					<a class="avatar-user">
						<?php echo $user->get_avatar(); ?>
					</a>
					<span><?php echo $user->display_name; ?></span>
				</div>
			</div>

			<?php do_action('marketengine_after_user_profile_avatar', $user); ?>

			<div class="me-col-md-9">
				<div class="marketengine-profile-info">

					<?php do_action('marketengine_before_user_profile_information', $user); ?>

					<div class="me-row">
						<div class="me-col-md-6">
							<div class="marketengine-text-field">
								<label class="text"><?php _e("First name", "enginethemes");?></label>
								<p><?php echo $user->first_name; ?></p>
							</div>
						</div>
						<div class="me-col-md-6">
							<div class="marketengine-text-field">
								<label class="text"><?php _e("Last name", "enginethemes");?></label>
								<p><?php echo $user->last_name; ?></p>
							</div>
						</div>
					</div>
					
					<?php do_action('marketengine_user_profile_info', $user); ?>

					<div class="marketengine-text-field">
						<label class="text"><?php _e("Display name", "enginethemes");?></label>
						<p><?php echo $user->display_name; ?></p>
					</div>
					<div class="marketengine-text-field">
						<label class="text"><?php _e("Username", "enginethemes");?></label>
						<p><?php echo $user->user_login; ?></p>
					</div>
					<div class="marketengine-text-field">
						<label class="text"><?php _e("Email", "enginethemes");?></label>
						<p><?php echo $user->user_email; ?></p>
					</div>

					<!-- <div class="marketengine-text-field me-no-margin-bottom">
						<label class="text"><?php _e("Location", "enginethemes");?></label>
						<p>Vietnamese</p>
					</div> -->

					<?php do_action('marketengine_after_user_profile_information', $user); ?>

				</div>
				<div class="marketengine-text-field edit-profile">
					<a href="<?php echo me_get_endpoint_url('edit-profile'); ?>" class="marketengine-btn"><?php _e("Edit Profile", "enginethemes");?></a>
				</div>
			</div>
		</div>
		<?php do_action('marketengine_user_profile_end', $user); ?>
	</div>
</div>