<?php get_header(); ?>

<div class="container">

<?php 
	if ( !is_dynamic_sidebar('Archive Sidebar') ) : 
		
		?>
		
		<?php
	
		ListPosts::widget($args =array(), array('title' => 'Posts', 
															'auto_hide' => true, 
															'position' => 'left', 
															'order_by' => 'ID', 
															'order_type' => 'DESC', 
															'categorize' => false, 
															'categories' => null, 
															'show_category_titles' => false));
	else:
		get_dynamic_widgets();
	endif;
?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<?php get_stylized_title(); ?>
		<div id="content" class="<?php echo $current_type; ?>">

			<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
				<div class="entry">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

					<?php the_excerpt('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>

				</div>	
			</div>			
		</div>

	<?php endwhile;?>
<?php endif; ?>

</div>
<?php get_footer(); ?>


