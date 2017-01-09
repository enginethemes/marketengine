<?php 
get_header();
$case_id = get_query_var( 'case_id' );
$case = me_get_message($case_id);
?>
<div id="marketengine-page">
    <div class="me-container">
        <div class="marketengine-content-wrap">

            <?php me_get_template('resolution/case-details/heading', array('case' => $case)); ?>
            <!-- marketengine-content -->
            <div class="marketengine-content">
                
                <?php me_get_template('resolution/case-details/mobile-nav'); ?>

                <div class="me-disputed-case">
                    
                    <?php me_get_template('resolution/case-details/info', array('case' => $case)); ?>

                    <div class="me-disputed-conversation">
                        <div class="me-row">
                            <div class="me-col-md-3 me-col-md-pull-9 me-col-sm-4 me-col-sm-pull-8">
                                <div class="me-sidebar-contact">
                                        
                                    <?php me_get_template('resolution/case-details/related-party', array('case' => $case)); ?>
                                    <?php me_get_template('resolution/case-details/dispute-event', array('case' => $case)); ?>
                                    
                                </div>
                            </div>
                            <div class="me-col-md-9 me-col-md-push-3 me-col-sm-8 me-col-sm-push-4">
                                <div class="me-contact-messages-wrap">
                                    <div class="me-contact-message-user">
                                        <p>
                                            <?php 
                                            if(get_current_user_id() == $case->sender) {
                                                echo get_the_author_meta( 'display_name', $case->receiver );
                                            }elseif(get_current_user_id() == $case->receiver) {
                                                echo get_the_author_meta( 'display_name', $case->sender );
                                            }
                                            ?>
                                        </p>
                                    </div>

                                    <?php 
                                        $message_query = new ME_Message_Query(array('post_type' => array('message', 'revision'), 'post_parent' => $case->ID, 'showposts' => 12));
                                        $messages = array_reverse ($message_query->posts);
                                    ?>
                                    
                                    <div class="me-contact-messages">
                                        <ul class="me-contact-messages-list">
                                        <?php if( $messages ) : ?>
										<?php foreach ($messages  as $key => $message) : ?>
											<?php 
                                            if($message->post_type == 'revision') {
                                                me_get_template('resolution/revision-item', array('message' => $message));
                                            }else {
                                                me_get_template('resolution/message-item', array('message' => $message));    
                                            }
                                             ?>
										<?php endforeach; ?>
										<?php endif; ?>
                                            
                                        </ul>
                                    </div>
                                    
                                    <div class="me-message-typing-form">
                                        <form id="dispute-message-form" action="">
                                            <textarea name="content" id="debate_content" placeholder="New message"></textarea>
                                            <div class="me-dispute-attachment">
                                                <div class="me-row">
                                                    <div class="me-col-lg-10 me-col-md-9">
                                                        <p>
                                                            <label class="me-dispute-attach-file" for="me-dispute-file">
                                                                <input id="me-dispute-file" type="file">
                                                                <i class="icon-me-attach"></i>
                                                                <?php _e("Add attachment", "enginethemes"); ?>
                                                            </label>
                                                        </p>
                                                        <ul class="me-list-dispute-attach">
                                                            <li>abc.file<span class="me-remove-dispute-attach"><i class="icon-me-remove"></i></span></li>
                                                            <li>ksafdkl.sf<span class="me-remove-dispute-attach"><i class="icon-me-remove"></i></span></li>
                                                            <li>Kronog backls<span class="me-remove-dispute-attach"><i class="icon-me-remove"></i></span></li>
                                                            <li>con duong mua dnoh nkd.sf<span class="me-remove-dispute-attach"><i class="icon-me-remove"></i></span></li>
                                                        </ul>
                                                    </div>
                                                    <div class="me-col-lg-2 me-col-md-3">
                                                        <input type="hidden" name="dispute_id" id="dispute_id" value="<?php echo $case->ID; ?>">
                                                        <?php wp_nonce_field( 'me-debate', "_debate_nonce"); ?>
                                                        <input class="marketengine-btn me-dispute-message-btn" type="submit" value="<?php _e("SUBMIT", "enginethemes"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--// marketengine-content -->
        </div>
    </div>
</div>
<?php
get_footer();
?>
