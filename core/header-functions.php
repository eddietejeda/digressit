<?php

/**
 *
 */
function header_default_top_menu(){
	$digressit_options= get_option('digressit');
	?>
	<ul>
		<li><a title="<?php echo($digressit_options['table_of_contents_label']); ?>" href="<?php bloginfo('home'); ?>"><?php echo($digressit_options['table_of_contents_label']); ?></a></li>
		<li><a title="<?php echo($digressit_options['comments_by_section_label']); ?>" href="<?php bloginfo('home'); ?>/comments-by-section"><?php echo($digressit_options['comments_by_section_label']); ?></a></li>
		<li><a title="<?php echo($digressit_options['comments_by_users_label']); ?>"  href="<?php bloginfo('home'); ?>/comments-by-contributor"><?php echo($digressit_options['comments_by_users_label']); ?></a></li>
		<li><a title="<?php echo($digressit_options['general_comments_label']); ?>"  href="<?php bloginfo('home'); ?>/general-comments"><?php echo($digressit_options['general_comments_label']); ?></a></li>
		<?php do_action('add_commentbrowser_link'); ?>		
	</ul>
<?php
}



/**
 *
 */
function digressit_body_class(){
	global $blog_id, $post;
	$request_root = parse_url($_SERVER['REQUEST_URI']);
	
	$blog_name_unique = ereg_replace("[^A-Za-z0-9]", "-", strtolower(get_bloginfo('name') ));
	$post_name_unique = 'post-name-'. $post->post_name;
	
	if(function_exists('is_commentbrowser') && is_commentbrowser()){
		$current_page_name .= ' comment-browser '. $blog_name_unique ;
	}
	elseif(is_multisite() && $blog_id == 1 && is_frontpage()){
		$current_page_name = ' frontpage '. $blog_name_unique ;
	}
	else{
		$current_page_name .= basename(get_bloginfo('home'));
		if(is_home()){
			$current_page_name .= ' site-home '. $blog_name_unique ;
		}	
	}
	return $current_page_name. " ". $post_name_unique;
}

?>