<?php
/**
 * @package Digressit
 * @subpackage Digressit_Wireframe
 */
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
	
if ( function_exists('post_password_required')  ) {
	if( post_password_required() ){
	 ?>
	<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'digressit'); ?></p>
	<?php
	return;
	}
}
?>
<div id="commentbox-header">
	<?php do_action('commenbox_header'); ?>
</div>

<div id="commentbox">
<div id="commentwindow">
	<?php do_action('digressit_custom_commenbox_header'); ?>
	
	<div id="toplevel-commentbox">
	<div name="respond-form" id="respond">

		<?php
		$custom_comment_open = true;
		if(function_exists('custom_conditional_comment_open')){
			$custom_comment_open = custom_conditional_comment_open();
		}
		?>
		
		<?php if ( comments_open() && $custom_comment_open) : ?>
			<?php if ( is_user_logged_in() ) : ?>
				<?php digressit_comment_form(); ?>
			<?php else: ?>
				<?php if(get_option('comment_registration')): ?>
					<div id="must-be-logged-in">
						<?php 
						
						if(has_action('digressit_custom_login_message')){
							do_action('digressit_custom_login_message');
						}
						else{
							_e('You must be logged in to write a comment.', 'digressit');
						
							if(function_exists('lightbox_login_ajax')){
								$lightbox_login_class = 'lightbox lightbox-login';
							}
							if(function_exists('lightbox_register_ajax')){
								$lightbox_register_class = 'lightbox lightbox-register';
							}
						
							?>
							<input type='button' class="<?php echo $lightbox_login_class; ?> transparent-submit-button" id="login-button" value="<?php _e('Sign In', 'digressit'); ?>">
							<input type='button' class="<?php echo $lightbox_register_class; ?> transparent-submit-button" id="register-button" value="<?php _e('Create Account', 'digressit'); ?>">
							<?php						
						}
						?>

						<input name="selected_paragraph_number" type="hidden" id="selected_paragraph_number"  value="0" />
					</div>
				<?php else: ?>
					<?php digressit_comment_form(); ?>
				<?php endif; ?>			
			<?php endif;?>
		<?php else: ?>
			<div id="must-be-logged-in">
			<?php echo strlen(get_post_meta($post->ID, 'discussion_closed', true)) ? get_post_meta($post->ID, 'discussion_closed', true) : __('This discussion is now closed.','digressit'); ?>
			</div>
		
		<?php endif; ?>
	</div>
	<?php do_action('digressit_toplevel_commentbox'); ?>
	</div>
	
	<div id="comments-sort-all"><?php _e('show all', 'digressit'); ?> (<?php echo count(get_approved_comments($post->ID)); ?>)</div>	
	<div id="no-comments"><?php _e('There are no comments. Click the text to your left to make a new comment.', 'digressit') ; ?></div>
	<?php if ( have_comments() ) : ?>
		<?php global $post; ?>	
		<div id="commentlist" class="commentlist">
		<?php wp_list_comments(array('type' => 'comment', 'callback' => get_digressit_comments_function() )); ?>
		</div>

		<?php if ( function_exists('previous_comments_link')  ):  ?>
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	<?php endif; ?>

<?php else : // this is displayed if there are no comments so far ?>

<?php /* 	QUARANTINE */   ?> 
	<div class="commentlist">

	<?php if ( comments_open() ) : ?>
<!--		<p class="nocomments">There are no comments.</p>- -->
	 <?php else : // comments are closed ?>
<!--		<p class="nocomments">Comments are closed.</p>- -->
	<?php endif; ?>
	</div>
<?php endif; // if you delete this the sky will fall on your head ?>

</div>
</div>

