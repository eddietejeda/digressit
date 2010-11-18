<?php

add_filter('init', 'single_init');
add_action('add_dynamic_widget', 'digressit_single_sidebar_widgets');




function single_init(){
	add_action('public_ajax_function', 'live_post_search_ajax');	
	add_action('wp_print_scripts', 'digressit_single_print_scripts');
}



function digressit_single_print_scripts(){
	if(is_single()){
		wp_enqueue_script('digressit.single', get_digressit_media_uri('js/digressit.single.js'), 'jquery', false, true );
	}
}


function digressit_single_sidebar_widgets(){
	if(is_single()){	
		$options = get_option('digressit');
		//var_dump(is_active_sidebar('Single Sidebar'));
		if(is_active_sidebar('single-sidebar') && (int)$options['enable_sidebar'] != 0){
			?>
			<div class="sidebar-widgets">
			<div id="dynamic-sidebar" class="sidebar  <?php echo $options['auto_hide_sidebar']; ?> <?php echo $options['sidebar_position']; ?>">		
			<?php
			dynamic_sidebar('Single Sidebar');
			?>
			</div>
			</div>
			<?php
		}
	}		
}


/** 
 * @description: 
 * @todo: 
 *
 */
function get_text_signature_count($post_ID, $text_signature)
{
	global $wpdb;
	
	$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE 	comment_approved = 1 AND comment_post_ID = %d", $post_ID) );
	$comment_count = count(get_text_signature_filter($comments, $text_signature));
	return $comment_count; //( $comment_count > 0) ? $comment_count : '';	
}








function live_post_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;

	$excluded_words = array('the','and');
	//every three letters we give results
	if(!in_array($request_params['value'], $excluded_words)){


		$query = 'SELECT ID, post_content FROM '.$wpdb->posts.' WHERE post_content LIKE "%'. $request_params['value'] .'%" OR post_title LIKE "%'. $request_params['value'] .'%"';
		$posts = $wpdb->get_results( $query);
		foreach($posts as $item){
			$message[] = $item->ID;
		}

		$query = 'SELECT comment_post_ID, comment_content FROM '.$wpdb->comments.' WHERE comment_content LIKE "%'. $request_params['value'] .'%"';
		$comments = $wpdb->get_results( $query);		
		foreach($comments as $item){
			$message[] = $item->comment_post_ID;
		}

/*
		$query = 'SELECT m.*, p. FROM '.$wpdb->commentmeta.' m, '.$wpdb->posts.' p WHERE meta_key = "comment_tag" AND meta_value LIKE "%'. $request_params['value'] .'%"';
		$tags = $wpdb->get_results( $query);		
		foreach($tags as $item){
			$message[] = $item->comment_post_ID;
		}
*/
	
		die(json_encode(array('status' => 1, "message" => $message)));
	}
	
	
}




?>
