<?php
/**
 * @package Digress.it
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<?php global $blog_id, $current_user, $current_page_name; ?>
<?php get_currentuserinfo(); ?>
<?php wp_head(); ?>
<?php

$request_root = parse_url($_SERVER['REQUEST_URI']);

//var_dump(is_commentbrowser());
if(is_commentbrowser()){
	$current_page_name .= ' comment-browser ';	
}
elseif (!($request_root['path'] == '/')){
	$current_page_name .= basename(get_bloginfo('home'));
	if(is_home()){
		$current_page_name .= ' site-home ';
	}
}
else{
	$current_page_name = ' frontpage ';
}

?>
</head>

<body <?php body_class($current_page_name); ?>>

<?php do_action('after_body'); ?>	

<div id="wrapper"> <!-- this is closed in footer -->

<?php do_action('optional_pre_header'); ?>
<div id="header">
	<div class="site-title">
		<?php if(has_action('add_header_image')): ?>
			<?php do_action('add_header_image'); ?>
			<div class="description"><?php bloginfo('description'); ?></div>
		<?php else: ?>
			<a href="<?php bloginfo('home') ?>"><h1><?php bloginfo('name'); ?></h1></a>
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
		<li><a href="<?php echo get_bloginfo('home'); ?>/wp-admin/" title="My Account"><?php _e('My Account'); ?></a></li>			
		<li><a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>" title="Logout"><?php _e('Logout'); ?></a></li>			
	<?php else: ?>
		<?php if(get_option('users_can_register')): ?>
		<li><a href="<?php echo get_bloginfo('home'); ?>/wp-signup.php"   title="Register"><?php _e('Register'); ?></a></li>
		<?php endif; ?>
		<li><a href="<?php echo wp_login_url(); ?>" title="Login"><?php _e('Login'); ?></a></li>
	<?php endif;?>
	</ul>
	
	</div>
	
	<div class="horizontal"></div>
</div> 
<?php
do_action('secondary_menu');
?>

<?php

function header_default_top_menu(){

?>
	<ul>
		<li><a href="<?php bloginfo('home') ?>/comments-by-section/" title="Comments by Section"><?php _e('Comments by Section'); ?></a></li>
		<li><a href="<?php bloginfo('home') ?>/comments-by-contributor/" title="Users"><?php _e('Comments by Contributor'); ?></a></li>		
		<li><a href="<?php bloginfo('home') ?>/general-comments/" title="General Comments"><?php _e('General Comments'); ?></a></li>		
		<?php do_action('custom_default_top_menu'); ?>
	</ul>

<?php
}

?>



