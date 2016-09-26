<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function me_insert_message($message_arr, $wp_error = false){
	global $wpdb;
 
    $user_id = get_current_user_id();

    $defaults = array(
        'sender' => $user_id,
        'receiver' => '',
        'post_content' => '',
        'post_content_filtered' => '',
        'post_title' => '',
        'post_status' => 'draft',
        'post_type' => 'post',
        'comment_status' => '',
        'post_password' => '',
        'post_parent' => 0,
        'guid' => ''
    );

    $message_arr = wp_parse_args($message_arr, $defaults);

    $message_arr = sanitize_post( $message_arr, 'db' );
    // Are we updating or creating?
	$message_ID = 0;
	$update = false;
	$guid = $message_arr['guid'];

	if ( ! empty( $message_arr['ID'] ) ) {
		$update = true;

		// Get the post ID and GUID.
		$message_ID = $message_arr['ID'];
		$message_before = me_get_message( $message_ID );
		if ( is_null( $message_before ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'invalid_post', __( 'Invalid message ID.' ) );
			}
			return 0;
		}

		$guid = me_get_message_field( 'guid', $message_ID );
		$previous_status = me_get_message_field('message_status', $message_ID ); // get_post_field
	} else {
		$previous_status = 'new';
	}

	$post_type = empty( $message_arr['post_type'] ) ? 'message' : $message_arr['post_type'];

	$post_title = $message_arr['post_title'];
	$post_content = $message_arr['post_content'];
	$post_excerpt = $message_arr['post_excerpt'];
	if ( isset( $message_arr['post_name'] ) ) {
		$post_name = $message_arr['post_name'];
	} elseif ( $update ) {
		// For an update, don't modify the post_name if it wasn't supplied as an argument.
		$post_name = $post_before->post_name;
	}

	$maybe_empty = 'attachment' !== $post_type
		&& ! $post_content && ! $post_title && ! $post_excerpt;

	/**
	 * Filters whether the message should be considered "empty".
	 *
	 * Returning a truthy value to the filter will effectively short-circuit
	 * the new post being inserted, returning 0. If $wp_error is true, a WP_Error
	 * will be returned instead.
	 *
	 * @since 1.0
	 *
	 * @param bool  $maybe_empty Whether the post should be considered "empty".
	 * @param array $message_arr     Array of post data.
	 */
	if ( apply_filters( 'me_insert_message_empty_content', $maybe_empty, $message_arr ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'empty_content', __( 'Content, title, and excerpt are empty.' ) );
		} else {
			return 0;
		}
	}

	if(empty($message_arr['receiver'])) {
		if ( $wp_error ) {
			return new WP_Error( 'empty_receiver', __( 'Receiver is empty.' ) );
		} else {
			return 0;
		}	
	}

	$post_status = empty( $message_arr['post_status'] ) ? 'unread' : $message_arr['post_status'];
	/*
	 * Create a valid post name. Drafts and pending posts are allowed to have
	 * an empty post name.
	 */
	if ( empty($post_name) ) {
		if ( !in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$post_name = sanitize_title($post_title);
		} else {
			$post_name = '';
		}
	} else {
		// On updates, we need to check to see if it's using the old, fixed sanitization context.
		$check_name = sanitize_title( $post_name, '', 'old-save' );
		if ( $update && strtolower( urlencode( $post_name ) ) == $check_name && get_post_field( 'post_name', $post_ID ) == $check_name ) {
			$post_name = $check_name;
		} else { // new post, or slug has changed.
			$post_name = sanitize_title($post_name);
		}
	}

	/*
	 * If the post date is empty (due to having been new or a draft) and status
	 * is not 'draft' or 'pending', set date to now.
	 */
	if ( empty( $message_arr['post_date_gmt'] ) || '0000-00-00 00:00:00' == $message_arr['post_date_gmt'] ) {
		$post_date = current_time( 'mysql' );
	} else {
		$post_date = get_date_from_gmt( $message_arr['post_date_gmt'] );
	}
	
	if ( ! in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
		$post_date_gmt = get_gmt_from_date( $post_date );
	} else {
		$post_date_gmt = '0000-00-00 00:00:00';
	}

	if ( $update || '0000-00-00 00:00:00' == $post_date ) {
		$post_modified     = current_time( 'mysql' );
		$post_modified_gmt = current_time( 'mysql', 1 );
	} else {
		$post_modified     = $post_date;
		$post_modified_gmt = $post_date_gmt;
	}

	// These variables are needed by compact() later.
	$post_content_filtered = $postarr['post_content_filtered'];
	$sender = $user_id;
	$receiver = isset( $postarr['receiver'] ) ? $postarr['receiver'] : '';

	if ( isset( $postarr['post_parent'] ) ) {
		$post_parent = (int) $postarr['post_parent'];
	} else {
		$post_parent = 0;
	}

}

function me_update_message() {

}

function me_get_message_status_list() {

}

function me_get_messages() {

}

function me_get_message() {

}

function me_get_message_field( $field, $message = null, $context = 'display' ) {
    $message = me_get_message( $message );
 
    if ( !$message )
        return '';
 
    if ( !isset($message->$field) )
        return '';
 
    return sanitize_post_field($field, $message->$field, $message->ID, $context);
}

function me_get_message_meta() {

}

function me_add_message_meta() {

}

function me_update_message_meta() {

}

function me_delete_message_meta() {

}