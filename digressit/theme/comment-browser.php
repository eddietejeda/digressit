<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();

global $digressit_commentbrowser, $post;

$request_post = $_GET['post'];
$request_user = $_GET['user'];
$request_section = $_GET['comment-browser'];

?>

	<div class="commentbrowser">

		<div id="leftcolumn"> 

			<h2>Table of Comments (<?php echo $digressit_commentbrowser->getAllCommentCount(); ?>)</h2>
			<?php
						
			switch($request_section)
			{
				case 'posts':
					?><h3>Sections</h3><?php
					$digressit_commentbrowser->list_posts();				
				break;
				default:// 'users':
					?><h3>Commenters</h3><?php
					$digressit_commentbrowser->list_users();				
				break;/*
				default:
					?><h3>by commenter</h3><?php
					$digressit_commentbrowser->list_general_comments();				
				break;*/
			}
			
			
			?>

		</div>
		
		<div id="middlecolumn">

			<?php
			switch($request_section)
			{
				case 'posts':
			?>
				<h3>Comments on </h3> 
				<?php if( $request_post ) : ?>
					<?php $selected_post = get_post($request_post); ?>
					<h2><a href="<?php echo get_permalink($request_post); ?>"><?php echo apply_filters('the_title',$selected_post->post_title); ?></a> <a href="<?php echo get_permalink($request_post); ?>feed"><img src="<?php echo $this->image_path; ?>rss.png"></a></h2>					
				<?php endif; ?>
				
				<div style="display: block;" class="commentResponse">This page contains a running transcript of all conversations 
				taking place on the section. Click through the menu on the left to view comments by individual contributor.
				 Click the "go to thread" link to see the comment in context.</div>										
				
				<?php $digressit_commentbrowser->print_comments($request_section, $request_post); ?>
				

			<?php
				break;
				default: //case 'users':
				?>
				<h3>Comments by </h3> 
					
					<?php if(is_numeric($request_user) ): ?>						
					<?php $userdata = get_userdata((int)strip_tags($request_user)); ?>
					<h2><?php echo apply_filters('the_title', $userdata->user_nicename); ?> <a href="<?php echo $this->get_user_feed($request_user); ?>"><img src="<?php echo $this->image_path; ?>rss.png"></a></h2>
					<?php else: ?>
						<?php //lets make this comment really exists and we're not injecting funny names to print in the form ?>
						<?php if( $digressit_commentbrowser->getCommentsFromUser($request_user) ):?>
							<h2><?php echo html_entity_decode($request_user); ?> <a href="<?php echo $this->get_user_feed($request_user); ?>"><img src="<?php echo $this->image_path; ?>rss.png"></a></h2>							
						<?php endif; ?>
					<?php endif; ?>

					<div style="display: block;" class="commentResponse">This page contains a running transcript of all conversations 
					taking place on the section. Click through the menu on the left to view comments by individual contributor.
					 Click the "go to thread" link to see the comment in context.</div>						

					<?php $digressit_commentbrowser->print_comments($request_section, $request_user); ?>						


				<?php
				break;
/*				default:
				?>
				<h3>Comments by </h3> 
				<div style="display: block;" class="commentResponse">This page contains a running transcript of all conversations 
					taking place on the site. Click through the menu on the left to view comments by individual contributor.
					 Click the "go to thread" link to see the comment in context.</div>						
				<?php
				break;*/
			}

			?>				

		
		</div>

		<div id="rightcolumn">
			<?php get_sidebar();?>
		</div>
		<div class="clear"></div>
		
	</div>


<?php get_footer(); ?>