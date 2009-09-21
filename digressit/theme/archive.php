<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();

global $digressit_commentbrowser;
$options = get_option('digressit');



?>

	<div class="frontpage">

		<div id="leftcolumn"> 

<?php

	
 	$post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<?php /* If this is a category archive */ if (is_category()) { ?>
	<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
	<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
	<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
	<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
	<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
	<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
	<h2 class="pagetitle">Author Archive</h2>
	<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
	<h2 class="pagetitle">Blog Archives</h2>
	<?php }

?>

		</div>
		
		<div id="middlecolumn">


			<?php if (have_posts()) :  ?>

			<h2 class="pagetitle">Search Results</h2>

				<?php while (have_posts()) : the_post(); ?>


					<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

						<div class="entry">
							<?php the_excerpt('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
							<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

						</div>

						<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>

							<?php

							$comments = get_approved_comments($post->ID);
							$user_count;
							foreach($comments as $comment){
								$user_count[md5($comment->comment_author_email )] = 1;
							}

							?>

							| <a title="Comment on Post" href="<?php bloginfo('home'); ?>?post=<?php echo $post->ID; ?>&comment-browser=posts"><?php  echo count($user_count); ?> Commenters »</a>
						</p>

					</div>


				<?php endwhile; else: ?>

					<p>Sorry, no posts matched your criteria.</p>

			<?php endif; ?>

		
		</div>

		<div id="rightcolumn">
			<?php if($options['frontpage_sidebar'] == '1'): ?>
			<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
		
	</div>


<?php get_footer(); ?>


<?php die(); ?>
<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

	<div id="content" class="narrowcolumn searchpage" role="main">


	<?php if (have_posts()) :  ?>

	<h2 class="pagetitle">Search Results</h2>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>


			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

				<div class="entry">
					<?php the_excerpt('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

				</div>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>

					<?php

					$comments = get_approved_comments($post->ID);
					$user_count;
					foreach($comments as $comment){
						$user_count[md5($comment->comment_author_email )] = 1;
					}

					?>

					| <a title="Comment on Post" href="<?php bloginfo('home'); ?>?post=<?php echo $post->ID; ?>&comment-browser=posts"><?php  echo count($user_count); ?> Commenters »</a>
				</p>

			</div>


		<?php endwhile; else: ?>

			<p>Sorry, no posts matched your criteria.</p>

	<?php endif; ?>

		</div>
		<?php get_sidebar(); ?>

<?php get_footer(); ?>




<?php die(); ?>
<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

	<div id="content" class="narrowcolumn archive" role="main">


	<?php if (have_posts()) : 
	
	
 	$post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<?php /* If this is a category archive */ if (is_category()) { ?>
	<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
	<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
	<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
	<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
	<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
	<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
	<h2 class="pagetitle">Author Archive</h2>
	<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
	<h2 class="pagetitle">Blog Archives</h2>
	<?php } ?>

	<div class="navigation">
		<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
		<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
	</div>

	<?php while (have_posts()) : the_post(); ?>


		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

			<div class="entry">
				<?php the_excerpt('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>

			<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>

				<?php
				
				$comments = get_approved_comments($post->ID);
				$user_count;
				foreach($comments as $comment){
					$user_count[md5($comment->comment_author_email )] = 1;
				}

				?>

				| <a title="Comment on Post" href="<?php bloginfo('home'); ?>?post=<?php echo $post->ID; ?>&comment-browser=posts"><?php  echo count($user_count); ?> Commenters »</a>
			</p>
			
		</div>


	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>
	<?php get_sidebar(); ?>

<?php get_footer(); ?>
