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
global $user_ID;
?>

<form id="edit-rpfile-form" action="" method="post">
	<div class="me-container-fluid">
		<div class="me-row">
			<div class="me-col-md-3">
				<div class="marketengine-avatar-user">
					<a class="avatar-user">
						<?php echo get_avatar($user_ID); ?>
					</a>
					<span>Test Administrator</span>
				</div>
			</div>
			<div class="me-col-md-9">
				<div class="marketengine-profile-info">
					<div class="me-row">
						<div class="me-col-md-6">
							<div class="marketengine-group-field">
								<div class="marketengine-input-field">
									<label class="text"><?php _e("First name", "enginethemes"); ?></label>
									<input type="text" value="Text">
								</div>
							</div>
						</div>
						<div class="me-col-md-6">
							<div class="marketengine-group-field">
								<div class="marketengine-input-field">
									<label class="text"><?php _e("Last name", "enginethemes"); ?></label>
									<input type="text" value="Admin">
								</div>
							</div>
						</div>
					</div>
					<div class="marketengine-group-field">
						<div class="marketengine-input-field">
							<label class="text"><?php _e("Display name", "enginethemes"); ?></label>
							<input type="text" value="Admin">
						</div>
					</div>
					<div class="marketengine-group-field">
						<div class="marketengine-input-field">
							<label class="text"><?php _e("Email", "enginethemes"); ?></label>
							<input type="email" value="admin@enginethemes.com">
						</div>
					</div>
					<div class="marketengine-group-field me-no-margin-bottom">
						<div class="marketengine-input-field">
							<label class="text"><?php _e("Location", "enginethemes"); ?></label>
							<input type="text" value="Vietnamese">
						</div>
					</div>

				</div>
				<div class="marketengine-text-field edit-profile">
					<input type="submit" class="marketengine-btn" value="<?php _e("Update Profile", "enginethemes");?>">
				</div>
			</div>
		</div>
	</div>
</form>