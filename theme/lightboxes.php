<?php 

global $wpdb, $current_user, $post, $current_page_template;
add_action('add_lightbox', 'lightbox_login');
add_action('add_lightbox', 'lightbox_register');
//add_action('add_lightbox', 'lightbox_account_activation');
add_action('add_lightbox', 'lightbox_generic_response');

?>

<div class="lightbox-transparency"></div>

<?php
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
				<input type="password" name="pwd" id="user_pass" class="input" value="" size="25" tabindex="20" /></label>
			</p>
			
			<?php if(has_action('custom_register_links')) :?>
				<?php do_action('custom_register_links'); ?>
			<?php else: ?>
				<div class="login"><a href="<?php bloginfo('home') ?>#register">Register account</a></div>
				<div class="login"><a href="<?php bloginfo('home'); ?>#lostpassword">Lost Password?</a></div>
				
			<?php endif; ?>

			<!--<input type="submit" name="wp-submit" id="wp-submit" value="Log In" tabindex="100" />-->
		
			<input type="hidden" name="wp-submit" value="Log In" id="wp-submit">
			<input type="hidden" name="redirect_to" value="<?php echo 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>#login-success" />
			<input type="hidden" name="testcookie" value="1" />

			<span class="lightbox-submit" tabindex="100" >Login</span>
			<span class="lightbox-close"></span>
		
		</form>
	</div>
	<?php endif; ?>
<?php } 



function lightbox_register(){ ?>
<?php if(!is_user_logged_in()): ?>
<div class="lightbox-content" id="lightbox-register">	
	<div class="ribbon">
		<div class="ribbon-left"></div>
		<div class="ribbon-title">Register</div>
		<div class="ribbon-right"></div>
	</div>
	<form method="post" action="/" id="register-user">
		<div class="status-message error"></div>

		<div class="lightbox-slider">
		<div class="lightbox-slot">
			<p>
			<label>Username (required)<br>
			<input name="user_login" id="user_login" class="input" value="" size="25" type="text">
			<div class="status"></div>
			</label>
			</p>
			<p>
			<label>E-mail (required)<br>
			<input name="user_email" id="user_email" class="input" value="" size="25" type="text"></label>
			</p>
			<p><label>First Name: <br>
			<input autocomplete="off" name="firstname" id="firstname" size="25" value="" type="text"></label><br>
			</p>
			<p><label>Last Name: <br>
			<input autocomplete="off" name="lastname" id="lastname" size="25" value="" type="text"></label><br>
			</p>
		
			<p id="reg_passmail">A password will be e-mailed to you.</p>

		</div>

		<?php if(has_action('lightbox_registration_extra_fields')): ?>
		<div class="lightbox-slot">
		<?php do_action('lightbox_registration_extra_fields'); ?>
		</div>
		<?php endif; ?>
		</div>
		
		
		<input type="hidden" value="1" name="register-event">
		<div class="status"></div>
		<span class="lightbox-close"></span>
		
		<span class="lightbox-button lightbox-previous">Previous</span>
		<span id="register-submit" class="lightbox-submit ajax button-disabled"><span class="loading-bars"></span>Register</span>
		<span class="lightbox-button lightbox-next">Next</span>

	</form>

</div>
<?php endif; ?>
<?php } 




function lightbox_login_success(){ ?>
<div class="lightbox-content" id="lightbox-login-success">
	<p>Login successful</p>
	<span class="lightbox-delay-close"></span>
</div>
<?php } 

function lightbox_register_status(){ ?>
<div class="lightbox-content" id="lightbox-register-status">
	<p></p>
	<span class="lightbox-close"></span>
</div>
<?php
}
?>

<?php
function lightbox_generic_response(){ ?>

<div class="lightbox-content" id="lightbox-generic-response">
	<span class="lightbox-close"></span>
	<p></p>
	
</div>
<?php 
}
?>