<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function me_add_user_meta($meta) {
    if (isset($_POST['location'])) {
        $meta['location'] = $_POST['location'];
        $meta['paypal_email'] = $_POST['paypal_email'];
    }

    if(!empty($_POST['user_avatar'])) {
    	$meta['user_avatar'] = esc_attr( $_POST['user_avatar'] );
    }

    return $meta;
}
add_filter('insert_user_meta', 'me_add_user_meta');

/**
 * Retrieve the avatar `<img>` tag for a user, email address, MD5 hash, comment, or post.
 *
 * @param int $user_id
 * @return string
*/
function me_get_avatar($user_id) {
    $size= 32;
	$user_avatar = get_user_meta( $user_id, 'user_avatar', true);
    if($user_avatar) {
        $avatar_obj = wp_get_attachment_image_src( $user_avatar, 'thumbnail' );
        return '<img alt="" src="'.$avatar_obj[0].'" class="avatar avartar-'.$size.' photo" height="'.$size.'" width="'.$size.'">';
    }
    return get_avatar( $user_id );
}

function me_is_activated_user() {
    $current_user = ME()->get_current_user();
    return $current_user->is_activated();
}