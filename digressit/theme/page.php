<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();

global $digressit_commentbrowser;
$options = get_option('digressit');



?>

	<div id="content" class="frontpage">

		<div id="leftcolumn"> 

			<h2>Table of Contents</h2>
			<h3>Pages</h3>			

			<ol>
			<?php
			//global $post;

			extract($options);

			$myposts = null;
			$myposts = get_posts("post_type=page&numberposts=-1&order=$front_page_order&orderby=$front_page_order_by");

			
			foreach($myposts as $post) :
			?>
	
				<?php $comment_array = get_approved_comments($post->ID);  ?>

				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?> (<?php echo count($comment_array); ?>)</a></li>
			<?php endforeach; ?>

			</ol> 


		</div>
		
		<div id="middlecolumn">


			<?php if (have_posts()) : ?>

				<?php while (have_posts()) : the_post(); ?>

				<div class="post">
					<h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>

					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				<?php comments_template(); ?>

				<?php endwhile; ?>

				<div class="navigation">
					<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
					<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
				</div>

			<?php else : ?>

				<h2 class="center">Not Found</h2>
				<p class="center">Sorry, but you are looking for something that isn't here.</p>

			<?php endif; ?>

		
		</div>

		<div id="rightcolumn">
			<?php if($options['frontpage_sidebar'] == '1'): ?>
			<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
		
	</div>


<?php get_footer(); ?>
