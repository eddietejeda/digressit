<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Mainpage
*/

global $using_mainpage_nav_walker, $digressit_options;
?>


<?php get_header(); ?>

<div id="container">

<?php 
digressit_get_dynamic_widgets();
digressit_get_stylized_title();
?>

<div id="content" role="main">
	<div id="mainpage">		
		<h3 class="toc"><?php echo $digressit_options['table_of_contents_label']; ?></h3>
		<div class="description"><?php echo html_entity_decode(get_bloginfo('description')); ?></div>
		<div class='comment-count-in-book'><?php _e('There are '.count(digressit_get_all_comments(false)).' comments in this document'); ?></div>
		<?php 		
			wp_nav_menu(array('walker' => new digressit_mainpage_nav_walker(), 'depth'=> 3, 'fallback_cb'=> 'digressit_mainpage_default_menu', 'echo' => true, 'theme_location' => 'Main Page', 'menu_class' => 'navigation'));
	
			if($using_mainpage_nav_walker){
				if (( $locations = get_nav_menu_locations() ) && isset( $locations[ 'Main Page' ] ) ){
					$menu = wp_get_nav_menu_object( $locations[ 'Main Page' ] );
				}
				$menu_items = wp_get_nav_menu_items( $menu->term_id );
	
				digressit_mainpage_content_display($menu_items); 
			}
			else{
				digressit_get_widgets('Mainpage Content');
			} 
		?>
		<div class="clear"></div>
	</div>
</div>


</div>
<?php get_footer(); ?>