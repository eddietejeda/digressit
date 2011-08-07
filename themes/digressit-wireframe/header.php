<!DOCTYPE HTML>
<?php global $blog_id, $current_user, $current_page_name, $digressit_options; ?>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8,chrome=1">
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php get_currentuserinfo(); ?>
<?php wp_head(); ?>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
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
<header id="header" role="header">
	<div class="site-title" role="banner">
		<div class="bubblearrow"></div>		
		<?php if(has_action('add_header_image')): ?>
			<?php do_action('add_header_image'); ?>
			<div class="description" role="contentinfo"><?php bloginfo('description'); ?></div>
		<?php else: ?>
			<a href="<?php bloginfo('url') ?>"><h1 role="heading"><?php bloginfo('name'); ?></h1></a>
		<?php endif; ?>

	</div>
		


	<nav id="menu-primary" role="navigation">
	<?php 
		if(has_action('primary_menu')){
			do_action('primary_menu');
			do_action('optional_menu_item');
		} 
		else{
			wp_nav_menu(array('depth'=> 3, 'fallback_cb'=> 'header_default_top_menu', 'echo' => true, 'theme_location' => 'Top Menu', 'menu_class' => 'navigation'));
			do_action('optional_menu_item');
			if($digressit_options['show_pages_in_menu']==1){
				$front_page_content = $digressit_options['front_page_content'];
				echo "<ul>";
				wp_list_pages('exclude='.$front_page_content.'=menu_order&title_li=');
				echo "</ul>";
			}
		} 
	?>

	<!-- this is some login stuff that should always be here -->
	<div class="menu-site-container">
	<ul>
	<?php if(is_user_logged_in()): ?>
		<li class="menu-my-account"><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/" title="My Account"><?php _e('My Account'); ?></a></li>			
		<li class="menu-logout"><a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>" title="Logout"><?php _e('Logout'); ?></a></li>			
	<?php else: ?>
		<?php if(get_option('users_can_register')): ?>
		<li  class="menu-sign-up"><a href="<?php echo get_bloginfo('url'); ?>/wp-signup.php"   title="Register"><?php _e('Register'); ?></a></li>
		<?php endif; ?>
		<li class="menu-login"><a href="<?php echo wp_login_url(); ?>" title="Login"><?php _e('Login'); ?></a></li>
	<?php endif;?>
	</ul>
	</div>
	</nav>

	<?php if($digressit_options['enable_instant_content_search'] == 'true'): ?>
	<div id="instant-content-search">
	  	<label for="live-comment-search">Search</label>
		<input alt="Search Content" type="search" value="Search Content" class="ajax-live live-content-search content-field-area" id="live-content-search">
		<div class="loading-throbber"></div>
		<div id="live-content-search-result"></div>
	</div>
	
	<div id="instant-comment-search">
	  	<label for="live-comment-search">Search</label>
		<input value="Search Comments" class="ajax-live live-comment-search content-field-area" id="live-comment-search">
		<div class="loading-throbber"></div>
		<div id="live-comment-search-result"></div>
	</div>
	
	<br>
	<br>
	<?php endif; ?>
	<?php do_action('after_nav'); ?>
	<div class="horizontal"></div>
</header> 
<?php
do_action('secondary_menu');
?>