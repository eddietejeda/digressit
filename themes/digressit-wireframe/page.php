<?php get_header(); ?>

<?php digressit_get_single_default_widgets(); ?>

<div class="container">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php global $digressit_options; ?>
	<?php if($digressit_options['digressit_enabled_for_pages']):

			digressit_get_stylized_title(); 
			?>
			<div id="content" class="<?php echo $current_type; ?>" role="main">
	
				<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
					<article class="entry" role="article">
						<?php digressit_get_stylized_content_header(); ?>
						
						<div class="navigation-previous" role="navigation"><?php previous_post_link('%link', '&laquo; Previous'); ?></div>
						<div class="navigation-next" role="navigation"><?php next_post_link('%link', 'Next &raquo;'); ?> </div>
						<div class="clear"></div>
	
						<?php if(post_password_required()): ?>
							<form method="post" action="<?php bloginfo('url'); ?>/wp-pass.php">
							<p><?php _e('This post is password protected. To view it please enter your password below:'); ?></p>
							<p><label>Password:<br/>
							<input type="password" size="20" name="post_password"/></label><br/>
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
				</article>			
				
					<?php
					if(!post_password_required())
						comments_template();
					?>
				
			</div>
		<?php else:
			
			digressit_get_stylized_title(); ?>
			<div id="content" class="<?php echo $current_type; ?>" role="main">
		
				<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
					<article class="entry" role="article">
						<?php digressit_get_stylized_content_header(); ?>
						<?php the_content(); ?>
						<?php dynamic_sidebar('Page Content'); ?>					
						<?php edit_post_link(); ?>					
					</article>	
				</div>			
			</div>
		<?php endif; ?>


	<?php endwhile;?>
<?php endif; ?>

</div>
<?php get_footer(); ?>
