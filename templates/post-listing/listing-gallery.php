<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


?>

<?php do_action('marketengine_before_post_listing_picture_form');?>

<div class="marketengine-group-field">

    <?php do_action('marketengine_before_post_listing_image_form');?>

    <div class="marketengine-upload-field">

        <label class="me-field-title" for="upload_company_gallery"><?php _e('Your listing image', 'enginethemes'); ?></label>
        <?php
        me_get_template('upload-file/upload-form', array(
            'id' => 'upload_listing_image',
            'name' => 'listing_image',
            'source' => $listing_image,
            'button' => 'btn-listing-image',
            'multi' => false,
            'maxsize' => esc_html( '2mb' ),
            'maxcount' => 1
        ));
        ?>
    </div>

    <?php do_action('marketengine_after_post_listing_image_form');?>

</div>
<div class="marketengine-group-field">

    <?php do_action('marketengine_before_post_listing_gallery_form');?>

    <div class="marketengine-upload-field">
        <label class="me-field-title" for="upload_company_gallery"><?php _e('Gallery', 'enginethemes'); ?></label>
        <?php

        ob_start();
        if($listing_gallery) {
            foreach($listing_gallery as $gallery) {
                me_get_template('upload-file/multi-file-form', array(
                    'image_id' => $gallery,
                    'filename' => 'listing_gallery',
                    'close' => true
                ));
            }
        }
        $listing_gallery = ob_get_clean();

        me_get_template('upload-file/upload-form', array(
            'id' => 'upload_listing_gallery',
            'name' => 'listing_gallery',
            'source' => $listing_gallery,
            'button' => 'me-btn-upload',
            'multi' => true,
            'maxsize' => esc_html( '2mb' ),
            'maxcount' => 5
        ));
        ?>
    </div>

    <?php do_action('marketengine_after_post_listing_gallery_form');?>

</div>

<?php do_action('marketengine_after_post_listing_picture_form');?>

