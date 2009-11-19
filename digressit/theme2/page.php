<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();

global $digressit_commentbrowser;
$options = get_option('digressit');



?>

	<div class="-frontpage">

		<div id="-leftcolumn"> 

		</div>
		
		<div id="-middlecolumn">

			<div id="-blurb">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


						<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="entry">
								<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

								<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
								<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>

							</div>			


					<?php comments_template(); ?>


					<?php endwhile; else: ?>

						<p>Sorry, no posts matched your criteria.</p>

				<?php endif; ?>			</div>

		</div>
		</div>

		<div id="-rightcolumn">
			<?php //get_sidebar(); ?>
		</div>
		
	</div>


<?php get_footer(); ?>

