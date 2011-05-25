<?php
add_action('add_dynamic_widget', 'digressit_single_sidebar_widgets');


/**
 *
 */
function digressit_single_sidebar_widgets(){
	if(is_single()){	
		$digressit_options = get_option('digressit');
		//var_dump(is_active_sidebar('Single Sidebar'));
		if(is_active_sidebar('single-sidebar') && (int)$digressit_options['enable_sidebar'] != 0){
			?>
			<div class="sidebar-widgets">
			<div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
			<?php dynamic_sidebar(__('Single Sidebar')); ?>
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
function get_text_signature_count($post_ID, $text_signature){
	global $wpdb;
	
	$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE 	comment_approved = 1 AND comment_post_ID = %d", $post_ID) );
	$comment_count = count(get_text_signature_filter($comments, $text_signature));
	return $comment_count; 
}

?>