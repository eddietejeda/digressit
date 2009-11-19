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

			<?php


			 	$post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
				<h2 class="pagetitle">Search Results for &#8216;<?php echo $_GET['s']; ?>&#8217;</h2>
<br>

			<div id="-blurb">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


						<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="-entry">
								<?php the_excerpt('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

								<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
								<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>

							</div>			

						</div>
						<br>
					<?php endwhile; else: ?>

						<p>Sorry, no posts matched your criteria.</p>

				<?php endif; ?>			
			</div>
			
			<div class="navigation">
				<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
				<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
			</div>
			
		</div>


		<div id="-rightcolumn">
			<?php //get_sidebar(); ?>
		</div>
		
	</div>


<?php get_footer(); ?>

