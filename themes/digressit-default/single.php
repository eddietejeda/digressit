<?php get_header(); ?>

<?php $options = get_option('digressit'); ?>

<div class="container">

<?php get_single_default_widgets(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


	<?php if(!post_password_required()): ?>
		<?php comments_template(); ?>
	<?php endif; ?>
		<?php get_stylized_title(); ?>
		<div id="content" class="<?php echo $current_type; ?>" role="main">

			<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
				<div class="entry" role="article">
					<?php get_stylized_content_header(); ?>
					
					<div class="navigation-previous" role="navigation"><?php previous_post_link('%link', '&laquo; Previous'); ?></div>
					<div class="navigation-next" role="navigation"><?php next_post_link('%link', 'Next &raquo;'); ?> </div>
					<div class="clear"></div>

					<?php if(post_password_required()): ?>
						<form method="post" action="http://digwp.com/wp-pass.php">
						<p>This post is password protected. To view it please enter your password below:</p>
						<p><label for="pwbox-531">Password:<br/>
						<input type="password" size="20" id="pwbox-531" name="post_password"/></label><br/>
						<input type="submit" value="Submit" name="Submit"/></p>
						</form>
					<?php else: ?>
						<?php the_content(); ?>
					<?php endif; ?>
					<div class="navigation-previous" role="navigation"><?php previous_post_link('%link', '&laquo; Previous'); ?></div>
					<div class="navigation-next" role="navigation"><?php next_post_link('%link', 'Next &raquo;'); ?> </div>
					<div class="clear"></div>
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
