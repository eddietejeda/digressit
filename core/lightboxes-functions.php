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
	//This is for Accessability support
	echo '<span class="hidden-offscreen"> End of dialog content </span>';
	do_action('digressit_end_lightbox', $lightbox_name);
	$html = ob_get_contents();
	ob_end_clean();
	die(json_encode(array('status' => $status, "message" => $html)) );	
}

/**
 *
 */
function lightbox_login_ajax(){ 
	start_lightbox('Lightbox: Login');	
	global $password_just_reset;

	if(!is_user_logged_in()): 
		$status  = 1;
		?>
		<div class="lightbox-content" id="lightbox-login">
			<div class="ribbon">
				<div class="ribbon-left"></div>
				<div class="ribbon-title">Log in</div>
				<div class="ribbon-right"></div>
			</div>
		
			<?php
	
		
			$referer_url = parse_url($_SERVER['HTTP_REFERER']);
			?>
			<?php if($_GET['account-enabled'] == '0'): ?>
				<p><?php _e('Your account has not been enabled. Please check your inbox for your activation code'); ?></p>
			<?php endif; ?>
		
			<?php if($_GET['password_reset_key'] && $password_just_reset): ?>
				<p><?php _e('Your password was reset.<br>Check your email for your new password'); ?></p>
			<?php endif; ?>
			
			<?php if(has_action('custom_login_header')) :?>
				<?php do_action('custom_login_header'); ?>
			<?php endif; ?>
		
			<form method="post" action="<?php echo wp_login_url() ?>" id="login-form" name="loginform">
				<p>
					<label for="user_login"><?php _e('Username'); ?></label><br />
					<input type="text" name="log" id="user_login" class="input required" value="" size="25" tabindex="1" />
				</p>
	
				<p>
					<label for="user_pass"><?php _e('Password'); ?></label><br />
					<input type="password" name="pwd" id="user_pass" class="input required" value="" size="25" tabindex="2" />
				</p>
				
				<div class="custom_register_links">
				<?php if(has_action('custom_register_links')) :?>
					<?php do_action('custom_register_links'); ?>
				<?php else: ?>
					<p class="register-account-link">New user? <a href="<?php echo get_bloginfo('home'); ?>/wp-signup.php"   title="<?php _e('Create an account'); ?>"><?php _e('Create an account'); ?></a></p>
					<p class="lost-password-link"><a href="<?php echo wp_login_url(); ?>?action=lostpassword" title="<?php _e('Lost Password'); ?>"><?php _e('Lost Password?'); ?></a></p>
				<?php endif; ?>
				</div>
				<!--<input type="submit" name="wp-submit" id="wp-submit" value="Log In" tabindex="100" />-->
			
				<input type="hidden" name="wp-submit" value="Log In" id="wp-submit">
				<input type="hidden" name="redirect_to" value="<?php echo bloginfo('url'); ?>#login-success" />
				<input type="hidden" name="testcookie" value="1" />
	
				<?php do_action('digressit_login_form'); ?>	
				<span class="loading-bars"></span>
				<input type="submit" id="login-submit" tabindex="5" class="lightbox-submit lightbox-button disabled" disabled='disabled' value="<?php _e('Sign in'); ?>">

				<span class="lightbox-close"></span>
			
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
	start_lightbox('Lightbox: Login Success');	
	if(is_user_logged_in()): 
		$status  = 1;
	?>
		<div class="lightbox-content" id="lightbox-login-success">
			<h2><?php _e('Login Successful'); ?></h2>
			<span class="lightbox-delay-close"></span>
		</div><?php 
	else:
		$status = 0;	
	endif;
	end_lightbox($status);
} 


?>
