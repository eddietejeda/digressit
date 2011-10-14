<?php


add_action('public_ajax_function', 'lightbox_login_ajax');
add_action('public_ajax_function', 'lightbox_login_success_ajax');


/**
 *
 */
function get_lightboxes(){
    ?>
    <div id="lightbox-transparency"></div>    
    <div id="lightbox-content"></div>    
    <?php
    do_action('add_lightbox');
}

function start_lightbox($lightbox_name = 'Lightbox: Generic'){
    global $blog_id;
    ob_start();
    do_action('digressit_start_lightbox', $lightbox_name);
    //This is for Accessability support
    echo '<span class="hidden-offscreen"> Beginning of dialog content </span>';
}

function end_lightbox($status = 1){
    //This is for accessibility support
    echo '<span class="hidden-offscreen"> End of dialog content </span>';
    do_action('digressit_end_lightbox', $lightbox_name);
    $html = ob_get_contents();
    ob_end_clean();
    die(json_encode(array('status' => $status, "message" => $html)) );    
}




function lightbox_login_ajax(){ 
    start_lightbox('Lightbox: Login');    
    global $password_just_reset;

    if(!is_user_logged_in()): 
        $status  = 1;
        ?>
        <div class="lightbox-content" id="lightbox-login">
            <form method="post" action="<?php echo wp_login_url() ?>" id="login-form" name="loginform">
                <fieldset>
                    <legend>
                        <h3 tabindex="0">Sign in</h3>

                        <?php if(has_action('custom_login_header')) :?>
                            <?php do_action('custom_login_header'); ?>
                        <?php endif; ?>
                                
                        <div class="status-message error">
    
                            <?php
                            $referer_url = parse_url($_SERVER['HTTP_REFERER']);
                            ?>
                            <?php if($_POST['error'] == 'empty_fields'): ?>
                                <?php _e('<p tabindex="1"><strong>ERROR</strong>: <a href="#user_login" class="clickfocus user_login">Enter a username and password. Try again.</a></p>'); ?>
                            <?php endif; ?>
    
                            <?php if($_POST['error'] == 'invalid_email'): ?>
                                <?php _e('<p tabindex="1"><strong>ERROR</strong>: <a href="#user_login" class="clickfocus user_login">Not a valid account. Try again.</a>'); ?>
                            <?php endif; ?>
    
                            <?php if($_POST['error'] == 'signin_failed'): ?>
                                <?php _e('<p tabindex="1"><strong>ERROR</strong>: <a href="#user_login" class="clickfocus user_login">Authentication failed. Try again.</a></p>'); ?>
                            <?php endif; ?>
    
                            <?php if($_POST['error'] == 'account_enabled'): ?>
                                <?php _e('<p tabindex="1"><strong>ERROR</strong>: Your account has not been enabled. Please check your inbox for your activation code.</p>'); ?>
                            <?php endif; ?>
    
                            <?php if($_POST['password_reset_key'] && $password_just_reset): ?>
                                <?php _e('<p tabindex="1"><strong>ERROR</strong>: Your password was reset.<br>Check your email for your new password.</p>'); ?>
                            <?php endif; ?>
                            
                         </div>
                 
                    </legend>
                </fieldset>
                
                <p>
                    <label for="user_login"><?php _e('Username'); ?></label><br />
                    <input type="text" name="log" id="user_login" class="input required" value="" size="25" />
                </p>

                <p>
                    <label for="user_pass"><?php _e('Password'); ?></label><br />
                    <input type="password" name="pwd" id="user_pass" class="input required" value="" size="25" />
                </p>
            
                <div class="custom_register_links">
                <?php if(has_action('custom_register_links')) :?>
                    <p><?php do_action('custom_register_links'); ?></p>
                <?php else: /* Applies only to digressit, not regulation room, so leaving non-accessible markup for now. */ ?>                    
                    <p class="register-account-link">New user? <a href="<?php echo get_bloginfo('home'); ?>/wp-signup.php"  title="<?php _e('Create an account if you are a new user'); ?>"><?php _e('Create an account'); ?></a></p>
                    <p class="lost-password-link"><a href="<?php echo wp_login_url(); ?>?action=lostpassword" title="<?php _e('Reset your password if you have lost it'); ?>"><?php _e('Lost Password?'); ?></a></p>
                <?php endif; ?>
                </div>
        
                <input type="hidden" name="wp-submit" value="Log In" id="wp-submit">
                <input type="hidden" name="redirect_to" value="<?php echo $_REQUEST['data']; ?>#login-success" /> 
                <input type="hidden" name="testcookie" value="1" />

                <?php do_action('digressit_login_form'); ?>    
                <span class="loading-bars"></span>
                
                <div class="lightbox_buttons">
                    <input type="submit" id="login-submit" class="lightbox-submit lightbox-button disabled" disabled='disabled' value="<?php _e('Sign in'); ?>">            
                    <input type="button" class="lightbox-close" value="Cancel" />
                </div>
            
            </form>
        </div>
    <?php 
    else: 
        $status = 0;
    endif;
    
    end_lightbox($status);
    
} 


/**
 *
 */
function lightbox_login_success_ajax(){
    start_lightbox('Lightbox: Sign In Success');    
    if(is_user_logged_in()): 
        $status  = 1;
    ?>
        <div class="lightbox-content center" id="lightbox-login-success">
            <h3 tabindex="0"><?php _e('Sign In Successful'); ?></h3>
            
            <?php /* Test the auto-close lightbox with JAWS. If it doesn't work, 
                     use the close button as an alternative. */ ?>
            <span class="lightbox-delay-close"></span>     
            <!-- 
            <div class="lightbox_buttons">                          
                <input type="button" class="lightbox-close" value="OK" /> 
            </div> 
            -->
        </div><?php 
    else:
        $status = 0;    
    endif;
    end_lightbox($status);
}


/**
 *
 */
function lightbox_submit_comment_success_ajax(){
    start_lightbox('Lightbox: Comment Success');
    ?>
        <div class="lightbox-content center" id="lightbox-submit-comment-success">
            <h3 tabindex="0"><?php _e('Comment saved.  Thank you for your comment.'); ?></h3>
            
            <?php /* Test the auto-close lightbox with JAWS. If it doesn't work, 
                     use the close button as an alternative. */ ?>
            <span class="lightbox-delay-close"></span>     
            <!-- 
            <div class="lightbox_buttons">                          
                <input type="button" class="lightbox-close" value="OK" /> 
            </div> 
            -->
        </div>
	<?php 
    end_lightbox(1);
}


?>
