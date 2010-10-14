<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: MainPage
*/

?>

<?php get_header(); ?>

<div id="container">
<?php 	get_dynamic_widgets(); ?>


<?php get_stylized_title(); ?>

<div id="content">
	<div id="mainpage">
		
		<h3 class="toc">Table of Contents</h3>

		<?php wp_nav_menu(array('depth'=> 3, 'fallback_cb'=> 'mainpage_default_menu', 'echo' => true, 'theme_location' => 'Main Page', 'menu_class' => 'navigation')); ?>
		

		<?php get_widgets('Mainpage Content'); ?>
		<div class="clear"></div>
	</div>
</div>


</div>
<?php get_footer(); ?>

