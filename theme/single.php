<?php get_header(); ?>

<div class="container">

<?php 
	if ( !is_active_sidebar('single-sidebar') ) : 
		
		?>
		
		<div class="sidebar-widgets default-list-post">
		<div id="dynamic-sidebar" class="sidebar">		
		<?php
	
		ListPostsWithCommentCount::widget($args =array(), array('title' => 'Posts', 
															'auto_hide' => true, 
															'position' => 'left', 
															'order_by' => 'ID', 
															'order_type' => 'ASC', 
															'categorize' => false, 
															'categories' => null, 
															'show_category_titles' => false));
		?>
		</div>
		</div>
		<?php
	else:
		do_action('add_dynamic_widget');

	endif;
?>

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
				<?php dynamic_sidebar('Single Content');		 ?>
				
			</div>			
		</div>

	<?php endwhile;?>
<?php endif; ?>

</div>
<?php get_footer(); ?>
