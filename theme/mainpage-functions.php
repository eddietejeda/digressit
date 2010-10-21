<?php

//add_action('wp', 'mainpage');
add_action('wp_print_styles', 'mainpage_wp_print_styles');
add_action('wp_print_scripts', 'mainpage_wp_print_scripts' );
add_action('wp', 'mainpage_load');


function mainpage_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mainpage.css" type="text/css" media="screen" />
<?php
}

function mainpage_wp_print_scripts(){
	wp_enqueue_script('digressit.mainpage', get_template_directory_uri().'/mainpage.js', 'jquery', false, true );
}

function mainpage_sidebar_widgets(){
	if(is_active_sidebar(1)){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar">		
		<?php
		get_widgets('Mainpage Sidebar');
		?>
		</div>
		</div>
		<?php
	}
	
	
}

function mainpage_load(){
	//var_dump(is_mainpage());
	if(is_mainpage() && !is_single()){
		add_action('add_dynamic_widget', 'mainpage_sidebar_widgets');
	}
}



function mainpage_default_menu(){
	?>
	<ol class="navigation">

	 <?php
	 global $post;
	 $myposts = get_posts('numberposts=-1');
	 foreach($myposts as $post) :
	   setup_postdata($post);
		$comment_count = get_post_comment_count($post->ID);
	 ?>
	    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?> (<?php echo $comment_count;  ?>)</a></li>
	 <?php endforeach; ?>
	 </ol> 


	<div class="previews">

		<div class="preview default">
		<?php
		
		$options = get_option('digressit');
		$front_page_content = $options['front_page_content'];
		
		//var_dump($front_page_content);
		if((int)$front_page_content){
			$page = get_post($front_page_content);
			$content = $page->post_content;
			$content = apply_filters('the_content', $content);
			echo (strlen($content)) ? $content : "<p>This introduction can be changed by creating a new page titled \"about\"</p>";
			
		}
		else{
			$pages = get_pages();                                   
			foreach($pages as $key=>$page){ 
				if( $page->post_name == 'about'){
					$content = $page->post_content;
					$content = force_balance_tags(apply_filters('the_content', $content));
					break;
				}
			}
			echo (strlen($content)) ? $content : "<p>This introduction can be changed by creating a new page titled \"about\"</p>";
			
		}
		?>

		</div>

		<?php
			foreach($myposts as $post) :
		   setup_postdata($post);
		?>
			<div class="preview">
				<?php 

				the_excerpt();  
				global $post;
				$comment_count = get_post_comment_count($post->ID);
				?>

				<div class="comment-count">
					<?php echo $comment_count ?> Comments
				</div>	
			</div>			
		 <?php endforeach; ?>
		</div>			
	<?php
	
}
?>