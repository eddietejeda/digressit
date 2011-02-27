<?php get_header(); ?>

<?php $options = get_option('digressit'); ?>

<div class="container">

<?php get_single_default_widgets(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<?php comments_template(); ?>
		<?php get_stylized_title(); ?>
		<div id="content" class="<?php echo $current_type; ?>">

			<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
				<div class="entry">
					<?php get_stylized_content_header(); ?>
					
					<div id="previous-next"><?php posts_nav_link(); ?></div>
					<?php the_content(); ?>
				</div>	
				<div class="edit-this"><?php edit_post_link(); ?></div>

				<?php do_action('after_post_content'); ?>
				<?php dynamic_sidebar('Single Content'); ?>
				
			</div>			
		</div>

	<?php endwhile;?>
<?php endif; ?>

<?php get_footer(); ?>
</div>
