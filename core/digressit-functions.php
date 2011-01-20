<?php



add_action('public_ajax_function', 'live_content_search_ajax');	
add_action('public_ajax_function', 'live_comment_search_ajax');	


function live_content_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;

	$excluded_words = array('the','and');
	//every three letters we give results
	if(!in_array($request_params['value'], $excluded_words)){


		$blog_list = get_blog_list( 0, 'all' );

		$posts = null;
		$message = null;		
		foreach ($blog_list AS $blog) {
			switch_to_blog($blog['blog_id']);
				$sql = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_content LIKE '%".$request_params['value']."%'  OR post_content LIKE '%".$request_params['value']."%' GROUP BY ID LIMIT 3";

				$posts = $wpdb->get_results($sql);			

				foreach($posts as $post){
					$message .= "<div class='search-result'>".
								"<div class='post-title'><a href='".get_permalink($post->ID)."'>".$post->post_title."</a></div>".
								"</div>";
				}

			restore_current_blog();
		}
		
		

		

	
		die(json_encode(array('status' => count($posts), "message" => $message)));
	}
	
	
}



function live_comment_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;


	$excluded_words = array('the','and');
	//every three letters we give results
	if(!in_array($request_params['value'], $excluded_words)){


		$blog_list = get_blog_list( 0, 'all' );

		$posts = null;
		$message = null;		
		foreach ($blog_list AS $blog) {
			switch_to_blog($blog['blog_id']);
				$sql = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND comment_content LIKE '%".$request_params['value']."%' GROUP BY comment_ID LIMIT 3";

				$posts = $wpdb->get_results($sql);			

				foreach($posts as $post){
					$message .= "<div class='search-result'>".
								"<div class='post-title'><a href='".get_permalink($post->ID)."'>".$post->post_title."</a></div>".
								"</div>";
				}

			restore_current_blog();
		}
		die(json_encode(array('status' => count($posts), "message" => $message)));
	}
}

?>