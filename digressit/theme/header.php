<?php
/**
 * @package digress.it
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>





<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>


<?php wp_head(); ?>
<?php



?>
</head>
<body <?php if(function_exists('body_class')){ body_class(); } ?>>
<div id="page">

<?php
	$options = get_option('digressit');
	extract($options);

	$page = get_post($front_page_content);
	$content = $page->post_content;
	$content = apply_filters('the_content', $content);
?>
	<div id="header">

		<h1 id="logo"><a name="top" title="<?php bloginfo('name'); ?>" href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a> <small><?php bloginfo('description'); ?></small></h1>

		<div id="top_bar">
			<ul id="front_menu">
			<li class="page_item page_item_table_of_contents"><a <?php echo (is_home()) ? ' class="s" ' : '';  ?> href="<?php bloginfo('url'); ?>"><span  ><img  class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/book_open.png"> Table of Contents</span></a></li>
			<li class="page_item page_item_comments"><a <?php echo ($_GET['comment-browser'] == 'posts') ? ' class="s" ' : '';  ?> title="Comments By Section" href="?comment-browser=posts"><span ><img class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/text_padding_top.png"> Comments</span></a></li>
			<li class="page_item page_item_commenters"><a <?php echo ($_GET['comment-browser'] == 'users') ? ' class="s" ' : '';  ?> title="Comments By Section" href="?comment-browser=users"><span ><img class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/user_comment.png"> Commenters</span></a></li>
			<li class="page_item page_item_general"><a title="General Comments" href="?comment-browser=general"><span><img class="cute_menu_icons" src="<?php bloginfo('url'); ?>/wp-content/plugins/digressit/theme/images/famfamfam/user_comment.png"> General Comments</span></a></li>
			<?php echo digressit_wp_list_pages();  ?>
			</ul>
			<?php echo digressit_searchbox(); ?>
		</div>

	</div>
