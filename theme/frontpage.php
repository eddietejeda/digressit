<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Frontpage
*/
?>
<?php get_header(); ?>

<div id="container">
<?php get_dynamic_widgets(); ?>

<?php get_stylized_title(); ?>

<div id="content">
	<div id="frontpage">
		
		<?php get_widgets('Frontpage Content'); ?>
		
	</div>
</div>

</div>
<?php get_footer(); ?>



