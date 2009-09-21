<?php

if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));


$options = get_option('digress');


if($options['collapse_sidebar'] == 1){
	add_action('wp_print_scripts',  'digress_theme_wp_print_scripts' );
	add_action('wp_print_styles',  'digress_theme_print_styles' );

	function digress_theme_wp_print_scripts(){


		if(!is_home()){
			//wp_enqueue_script('jquery.google',		'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'); 		
			//wp_enqueue_script('jquery.cookie',		WP_PLUGIN_URL .'/Digress/js/jquery/external/cookie/jquery.cookie.js'); 		
			//wp_enqueue_script('wordpress.sidebar',	WP_PLUGIN_URL . '/Digress/js/sidebar.src.js'); 	
		}
	}

	function digress_theme_print_styles(){

		if(!is_home()){
			//wp_enqueue_style( 'jquery.smoothness.core', get_bloginfo('template_directory'). "/js/jquery/themes/smoothness/ui.core.css");		
			//wp_enqueue_style( 'jquery.smoothness.base', get_bloginfo('template_directory'). "/js/jquery/themes/smoothness/ui.base.css");		
			//wp_enqueue_style( 'jquery.smoothness.theme', get_bloginfo('template_directory'). "/js/jquery/themes/smoothness/ui.theme.css");		
		}
	}
}


function digressit_list_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<div class="comment-author vcard">
		         <?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
		         <?php printf(__('<cite class="fn" title="%1$s at %2$s">%3$s</cite> <span class="says" >says:</span>'), get_comment_date(),  get_comment_time(), get_comment_author_link()) ?>
			</div>

			<?php if ($comment->comment_approved == '0') : ?>
				<em><?php _e('Your comment is awaiting moderation.') ?></em>
				<br />
			<?php endif; ?>

			<?php
				$post = get_post($comment->comment_post_ID);
			?>

			<div class="comment-meta commentmetadata"><a href="<?php echo get_permalink($post->ID) ?>#comment-<?php echo $comment->comment_ID; ?>"></a></div>
			<?php comment_text() ?>
			
			
			<?php if(is_single()): ?>
				<div class="reply">
				<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</div>
			<?php else: ?>
				<?php global $digressit_commentbrowser; ?>
				<a href="<?php echo get_permalink($post->ID) ?>#comment-<?php echo $comment->comment_ID; ?>"><small style="float: right"><img src="<?php echo $digressit_commentbrowser->image_path; ?>pagegototext.png">go to thread</small></a>
				<br style="clear:both;"/>				
			<?php endif; ?>
		</div>
	<?php
}

?>