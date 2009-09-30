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

			<h2>Table of Contents</h2>
			<ol>
			<?php
			//global $post;

			extract($options);

			$myposts = null;
			if($front_page_post_type){
				$myposts = get_posts("post_type=$front_page_post_type&numberposts=-1&order=$front_page_order&orderby=$front_page_order_by");
			}
			else{
				$myposts = get_posts();				
			}
			
			foreach($myposts as $post) :
			?>
	
				<?php $comment_array = get_approved_comments($post->ID);  ?>

				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?> (<?php echo count($comment_array); ?>)</a></li>
			<?php endforeach; ?>

			</ol> 


		</div>
		
		<div id="middlecolumn">

			<div id="blurb">

			<?php 	

				if($front_page_content != ''){

					$page = get_post($front_page_content);
       				$content = $page->post_content;
       				$content = apply_filters('the_content', $content);

					echo "<h3>".$page->post_title."</h3>";
					echo $content;
					
				}
				else{

					$pages = get_pages(); 					
						foreach($pages as $key=>$page){ 
							if( $page->post_name == 'about'){
		        				$content = $page->post_content;
		        				$content = apply_filters('the_content', $content);
								echo "<h3>".$page->post_title."</h3>";
								echo $content;
								break;
							}
						}
				}	
			?>
			</div>

		
		</div>

		<div id="rightcolumn">
			<?php if($options['frontpage_sidebar'] == '1'): ?>
			<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
		
	</div>


<?php get_footer(); ?>