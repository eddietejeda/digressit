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

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<?php comments_template(); ?>
				<div id="content" class="<?php echo $current_type; ?>">

					<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
						<div class="entry">
							<?php get_stylized_content_header(); ?>
							<?php the_content(); ?>
						</div>	

						<?php do_action('after_post_content'); ?>
					</div>			
				</div>

			<?php endwhile;?>
		<?php endif; ?>
		
		
	</div>
</div>

</div>
<?php get_footer(); ?>



