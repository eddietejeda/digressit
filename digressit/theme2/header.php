<?php
/**
 * @package digress.it
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html <?php language_attributes(); ?>>
	
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		
		<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
		
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/blueprint.css?v=0.9.1" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/blueprint.css?v=0.9.1" type="text/css" media="print" />
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		
		<!--[if lt IE 8]>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie.css" type="text/css" media="screen" />
		<![endif]-->
		
		
		<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
		
		<?php wp_head(); ?>
	</head>
	
	<body <?php if(function_exists('body_class')){ body_class(); } ?>>
	
	<?php
	$options = get_option('digressit');
	extract($options);

	$page = get_post($front_page_content);
	$content = $page->post_content;
	$content = apply_filters('the_content', $content);
	?>
	
		<div id="application" class="container">
		
			<div class="span-24" id="header">
			
				<div class="span-18 first" id="logo">
				
					<h1 id="logo"><a name="top" title="<?php bloginfo('name'); ?>" href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a> <small><?php bloginfo('description'); ?></small></h1>
					
				</div>
				
				<div class="span-6 last" id="search">
					
					<?php echo digressit_searchbox(); ?>
					
				</div>
			
			</div>
			
			<div id="page" class="span-16 push-8">
			
				<div id="navigation" class="span-16">
					<ul id="front_menu">
						<li class="page_item page_item_table_of_contents"><a <?php echo (is_home()) ? ' class="s" ' : '';  ?> href="<?php bloginfo('url'); ?>"><span  ><img  class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/book_open.png"> Table of Contents</span></a></li>
						<li class="page_item page_item_comments"><a <?php echo ($_GET['comment-browser'] == 'posts') ? ' class="s" ' : '';  ?> title="Comments By Section" href="/?comment-browser=posts"><span ><img class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/text_padding_top.png"> Comments</span></a></li>
						<li class="page_item page_item_commenters"><a <?php echo ($_GET['comment-browser'] == 'users') ? ' class="s" ' : '';  ?> title="Comments By Section" href="/?comment-browser=users"><span ><img class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/user_comment.png"> Commenters</span></a></li>
						<li class="page_item page_item_general"><a title="General Comments" href="/?comment-browser=general"><span><img class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/user_comment.png"> General Comments</span></a></li>
						<?php echo digressit_wp_list_pages();  ?>
					</ul>
				</div>
			
				<!-- end header.php -->