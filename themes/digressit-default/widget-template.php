<?php
/*
Template Name: Widgets Page
*/
?>
<?php get_header(); ?>
<div id="content">
	<div id="widget-page">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div id="content" class="<?php echo $current_type; ?>" role="main">
					<?php digressit_get_widgets(); ?>
				</div>
			<?php endwhile;?>
		<?php endif; ?>		
	</div>
</div>
<?php get_footer(); ?>








