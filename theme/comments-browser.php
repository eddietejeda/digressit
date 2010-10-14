<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: CommentsBrowser
*/

global $current_browser_section, $wp ;

//var_dump($current_browser_section);
?>

<?php get_header(); ?>

<div id="container">



<div id="content">
	<div id="mainpage"  class="comment-browser">
		<?php 
		
		//var_dump($current_browser_section);
		if($current_browser_section == "comments-by-section"){
			echo "<h3>Comments by Section</h3>";
			list_posts();
			$comment_list = get_comments('post_id='.$wp->query_vars['comments_by_section']);
		}
		else if($current_browser_section == "comments-by-user"){
			echo "<h3>Comments by Users</h3>";
		    if(is_numeric($wp->query_vars['comments_by_user'])) :
		        $curauth = get_user_by('id', $wp->query_vars['comments_by_user']);
		    else :
	        	$curauth = get_user_by('login', $wp->query_vars['comments_by_user']);
		    endif;
			list_users();

			$comment_list = get_comments_from_user($curauth->ID);
		}
		else if($current_browser_section == "general-comments"){
			echo "<h3>General Comments</h3>";
			list_general_comments();
			$comment_list = get_comments_from_user($curauth->ID);
		}
		else if($current_browser_section == "comments-by-tag"){
			echo "<h3>Comments by Tags</h3>";
			comments_by_tag_list();
			$comment_list = get_comments_by_tag($wp->query_vars['comments_by_tag']);
		}
			
		?>		
		
	
		
	
		<div class="commentlist">			
		<?php if(count($comment_list)): ?>
		<?php foreach($comment_list as $comment): ?>

	
		<div <?php comment_class($classes); ?> id="comment-<?php echo (int)$comment->blog_id ?>-<?php echo $comment->comment_ID; ?>">
			<div id="div-comment-<?php echo (int)$comment->blog_id; ?>-<?php echo $comment->comment_ID;; ?>" class="comment-body">
			
				<div class="comment-header">
				
					<div class="comment-author vcard">

						<?php echo get_avatar( $comment, 15 ); ?>

	 					<?php $comment_user = get_userdata($comment->user_id); ?> 

						<?php
						$profile_url = get_bloginfo('home')."/comments-by-user/" . $comment_user->user_login	;					

						echo "<a href='$profile_url'>$comment_user->display_name</a>";
						?>
					

					</div>
				
					<div class="comment-meta">
					
						<span class="comment-id" title="<?php echo $comment->comment_ID; ?>"></span>
						<span class="comment-parent" title="<?php echo $comment->comment_parent; ?>"></span>
						<span class="comment-paragraph-number" title="<?php echo $comment->comment_text_signature; ?>"></span>


						<span class="comment-date"><?php comment_date('n/j/Y'); ?></span>
					

					
						<?if (function_exists('switch_to_blog')):?>
						<?php switch_to_blog( (int)$comment->blog_id); ?>
						<?php endif; ?>
						<div class="comment-goto">
							<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#<?php echo $comment->comment_text_signature; ?>">GO TO TEXT</a>
						</div>

						<?if (function_exists('switch_to_blog')):?>
						<?php restore_current_blog(); ?>
						<?php endif; ?>
					
						<?php do_action('digressit_custom_meta_data'); ?>

										
					</div>
				</div>
				<div class="comment-text">
				

					<?php echo $comment->comment_content; ?>
				
				</div>
			</div>
		</div>

		
		<?php endforeach; ?>
		<?php else: ?>
			<div class="comment">
				<br>
			No comments
			</div>
		<?php endif; ?>
		</div>

	</div>
	<div class="clear"></div>
</div>


</div>
<?php get_footer(); ?>

