<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<title><?php bloginfo('name'); ?>:<?php wp_title('&raquo;', true, 'left'); ?> </title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<?php global $blog_id, $current_user, $current_page_name, $digressit; ?>
<?php get_currentuserinfo(); ?>
<?php wp_head(); ?>

</head>

<?php
if(function_exists('digressit_body_class')){
	$digressit_body_class = digressit_body_class();
}
?>
<body <?php body_class($digressit_body_class); ?>>

<?php do_action('after_body'); ?>	

<div id="wrapper"> <!-- this is closed in footer -->

<?php do_action('optional_pre_header'); ?>
<div id="header">
	<div class="site-title">
		<div class="bubblearrow"></div>		
		<?php if(has_action('add_header_image')): ?>
			<?php do_action('add_header_image'); ?>
			<div class="description"><?php bloginfo('description'); ?></div>
		<?php else: ?>
			<a href="<?php bloginfo('home') ?>"><h1><?php bloginfo('name'); ?></h1></a>
		<?php endif; ?>

		<?php if($digressit['enable_instant_content_search'] == 'true'): ?>
		<div id="instant-search">
			<input type="text" value="Search" class="ajax-live live-content-search content-field-area" id="live-content-search">
			<div class="loading-throbber"></div>

			<div id="live-content-search-result"></div>
		</div>
		<?php endif; ?>

		
	</div>
		

	<div id="menu-primary">
	<?php if(has_action('primary_menu')): ?>
			<?php do_action('primary_menu'); ?>
			<?php do_action('optional_menu_item'); ?>
	<?php else: ?>
		<?php wp_nav_menu(array('depth'=> 3, 'fallback_cb'=> 'header_default_top_menu', 'echo' => true, 'theme_location' => 'Top Menu', 'menu_class' => 'navigation')); ?>
		<?php do_action('optional_menu_item'); ?>
	<?php endif; ?>

	<!-- this is some login stuff that should always be here -->
	<ul>
	<?php if(is_user_logged_in()): ?>
		<li id="menu-my-account"><a href="<?php echo get_bloginfo('home'); ?>/wp-admin/" title="My Account"><?php _e('My Account'); ?></a></li>			
		<li id="menu-logout"><a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>" title="Logout"><?php _e('Logout'); ?></a></li>			
	<?php else: ?>
		<?php if(get_option('users_can_register')): ?>
		<li id="menu-register"><a href="<?php echo get_bloginfo('home'); ?>/wp-signup.php"   title="Register"><?php _e('Register'); ?></a></li>
		<?php endif; ?>
		<li id="menu-login"><a href="<?php echo wp_login_url(); ?>" title="Login"><?php _e('Login'); ?></a></li>
	<?php endif;?>
	</ul>
	
	</div>
	
	<div class="horizontal"></div>
</div> 
<?php
do_action('secondary_menu');
?>