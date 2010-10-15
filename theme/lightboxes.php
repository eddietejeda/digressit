<?php 

global $wpdb, $current_user, $post, $current_page_template;
add_action('add_lightbox', 'lightbox_login');
add_action('add_lightbox', 'lightbox_generic_response');

?>




<div class="lightbox-transparency"></div>


<?php
function lightbox_generic_response(){ ?>

<div class="lightbox-content" id="lightbox-generic-response">
	<span class="lightbox-close"></span>
	<p></p>
	
</div>
<?php 
}


function lightbox_login(){ ?>
	
	<?php  /******* THIS IS LOGIN. LOAD ON EVERY PAGE *******/ ?>
	<?php if(!is_user_logged_in()): ?>
	<div class="lightbox-content" id="lightbox-login">
		<div class="ribbon">
			<div class="ribbon-left"></div>
			<div class="ribbon-title">Login</div>
			<div class="ribbon-right"></div>
		</div>
	
		<?php

		global $password_just_reset;
	
		$referer_url = parse_url($_SERVER['HTTP_REFERER']);
	
		//var_dump($referer_url);
		//  && $referer_url['scheme']."//".$referer_url['host'] == get_root_domain() 
		?>
		<?php if($_GET['account-enabled'] == '0'): ?>
			<p>Your account has not been enabled. Please check your inbox for your activation code</p>
		<?php endif; ?>
	
		<?php if($_GET['password_reset_key'] && $password_just_reset): ?>
			<p>Your password was reset.<br>Check your email for your new password</p>
		<?php endif; ?>
	
		<form name="loginform" id="loginform" action="<?php echo get_root_domain() ?>/wp-login.php" method="post">
			<p>
				<label>Username<br />
				<input type="text" name="log" id="user_login" class="input" value="" size="25" tabindex="10" /></label>
			</p>

			<p>
				<label>Password<br />
				<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
			</p>
			<!--
			<div class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> Remember Me</label></div>
			-->
			
			<?php if(has_action('custom_register_links')) :?>
				<?php do_action('custom_register_links'); ?>
			<?php else: ?>
				<div class="login"><a href="<?php echo wp_login_url(); ?>?action=register">Register account</a></div>
				<div class="login"><a href="<?php echo wp_login_url(); ?>?action=lostpassword">Lost Password?</a></div>
				
			<?php endif; ?>

			<!--<input type="submit" name="wp-submit" id="wp-submit" value="Log In" tabindex="100" />-->
		
			<input type="hidden" name="wp-submit" value="Log In" id="wp-submit">
			<input type="hidden" name="redirect_to" value="<?php echo 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>?#login-success" />
			<input type="hidden" name="testcookie" value="1" />

			<span class="lightbox-submit" tabindex="100" >Login</span>
			<span class="lightbox-close"></span>
		
		</form>
	</div>
	<?php endif; ?>
<?php } ?>


