<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments-functions.php' == basename($_SERVER['SCRIPT_FILENAME'])):
	die (':)');
endif;

add_action('init', 'commentbrowser_flush_rewrite_rules' );
add_filter('query_vars', 'commentbrowser_query_vars' );
add_action('generate_rewrite_rules', 'commentbrowser_add_rewrite_rules' );
add_action('template_redirect', 'commentbrowser_template_redirect' );


add_action('wp_print_styles', 'comments_wp_print_styles');
add_action('wp_print_scripts', 'comments_wp_print_scripts' );
add_action('public_ajax_function', 'add_comment_ajax');

add_action('widgets_init', create_function('', 'return register_widget("CommentBrowserLinks");'));

// Flush your rewrite rules if you want pretty permalinks
function commentbrowser_flush_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}


// Create some extra variables to accept when passed through the url
function commentbrowser_query_vars( $query_vars ) {
    $myvars = array( 'comments_by_section', 'comments_by_user',  'general_comments');
    $query_vars = array_merge( $query_vars, $myvars );
    return $query_vars;
}


// Create a rewrite rule if you want pretty permalinks
function commentbrowser_add_rewrite_rules( $wp_rewrite ) {
    $wp_rewrite->add_rewrite_tag( "%comments_by_section%", "(.+?)", "comments_by_section=" );
    $wp_rewrite->add_rewrite_tag( "%comments_by_user%", "(.+?)", "comments_by_user=" );
    $wp_rewrite->add_rewrite_tag( "%general_comments%", "(.+?)", "general_comments=" );


    $urls = array( 'comments-by-section/%comments_by_section%', 'comments-by-user/%comments_by_user%','general-comments/%general_comments%');
    foreach( $urls as $url ) {
        $rule = $wp_rewrite->generate_rewrite_rules($url, EP_NONE, false, false, false, false, false);
        $wp_rewrite->rules = array_merge( $rule, $wp_rewrite->rules );
    }
    return $wp_rewrite;
}


// Let's echo out the content we are looking to dynamically grab before we load any template files
function commentbrowser_template_redirect() {
    global $wp, $wpdb, $current_user, $current_browser_section;

	//var_dump($wp->query_vars);


	if( isset( $wp->query_vars['comments_by_section'] ) && !empty($wp->query_vars['comments_by_section'] ) || 
		isset( $wp->query_vars['comments_by_user'] ) && !empty($wp->query_vars['comments_by_user'] ) ||
		isset( $wp->query_vars['general_comments'] ) && !empty($wp->query_vars['general_comments'] )):



		if(isset($wp->query_vars['comments_by_section'] )){
			$current_browser_section = 'comments-by-section';
		} 
		elseif(isset($wp->query_vars['comments_by_user'] )){
			$current_browser_section = 'comments-by-user';			
		} 
		elseif(isset($wp->query_vars['general_comments'] )){
			$current_browser_section = 'general-comments';
		} 

		include('comments-browser.php');
		exit;
	endif;
}







function comments_wp_print_styles(){
	if(is_single()):
	?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/comments.css" type="text/css" media="screen" />
	<?php endif; ?>
<?php
}

function comments_wp_print_scripts(){		
	if(is_single()):
		wp_enqueue_script('digressit.comments', get_template_directory_uri().'/comments.js', 'jquery', false, true );
	endif;
}






function add_comment_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;

	switch_to_blog($request_params['blog_id']);
	$time = current_time('mysql', $gmt = get_option('gmt_offset')); 
	$time_gmt = current_time('mysql', $gmt = 0); 
	
	$display_name = isset($request_params['display_name']) ? $request_params['display_name'] : $current_user->display_name;
	$user_email = isset($request_params['user_email']) ? $request_params['user_email'] : $current_user->user_email;
	$user_ID = isset($current_user->ID) ? $current_user->ID : '';

//	var_dump($display_name);
	$data = array(
	    'comment_post_ID' => $request_params['comment_post_ID'],
	    'comment_author' => $display_name,
	    'comment_author_email' => $user_email,
	    'comment_content' => $request_params['comment'],
	    'comment_parent' => $request_params['comment_parent'],
	    'user_id' => $user_ID,
	    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
	    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
	    'comment_date' => $time,
	    'comment_date_gmt' => $time_gmt,
	    'comment_approved' => 1, //TODO: we kinda have to approve automatically. because we don't have a way to notify user of approval yet
	);
	
	
	

	if(strlen($display_name) < 2){
		die(json_encode(array('status' => 0, "message" => 'Please enter a valid name.')));				
	}

	if(!is_email($user_email)){
		die(json_encode(array('status' => 0, "message" => 'Not a valid email.')));				
	}

	if(strlen($request_params['comment']) < 2){
		die(json_encode(array('status' => 0, "message" => 'Your comment is too short.')));				
	}
	
	if(digressit_live_spam_check_comment( $data )){
		die(json_encode(array('status' => 0, "message" => 'Your comment looks like spam. You might want to try again with out links')));						
	}


	
	if($wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) as comment_exists FROM $wpdb->comments WHERE user_id = $current_user->ID AND $comment_content = %s " , $comment_ID, $request_params['comment']) ) > 0){
		die(json_encode(array('status' => 0, "message" => 'This comment already exists')));		
	}

	
	
	
	$comment_ID = wp_insert_comment($data);					
	
	$request_params['comment_ID'] = $comment_ID;
	
	
	//TODO: we are moving away from the extra column
	$result = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->comments SET comment_text_signature = %s WHERE comment_ID = %d", $request_params['selected_paragraph_number'], $comment_ID) );


	//TODO: FOR FUTURE VERSIONS we will just use comment meta
	add_metadata('post', $request_params['comment_post_ID'], 'comment_text_signature', $request_params['selected_paragraph_number'], true);

	$comment_date = date('m/d/y');
	
	$message['comment_ID'] = $comment_ID;
	$message['comment_parent'] = $request_params['comment_parent'];
	$message['comment_date'] = $comment_date;
	$message['comment_author'] = $display_name;
	
	$status = 1;

	//an extra hook
	do_action('add_comment_ajax_metadata', $request_params);
	
	$commentcount = count(get_approved_comments($request_params['comment_post_ID']));
	delete_metadata('post', $request_params['comment_post_ID'], 'comment_count');
	add_metadata('post', $request_params['comment_post_ID'], 'comment_count', $commentcount, true);
	$message['comment_count'] = $commentcount;

	
	
	restore_current_blog();
	
	die(json_encode(array('status' => $status, "message" => $message)));
	
	
	
}

function standard_digressit_comment_parser($comment, $args, $depth) {
 	global $current_page_template, $blog_id, $current_user; 

	$GLOBALS['comment'] = $comment; 	
	$classes = null;
	?>
	<?php $current_blog_id = is_single() ? $blog_id : $comment->blog_id; ?>
	<?php $paragraphnumber = is_numeric($comment->comment_text_signature) ? $comment->comment_text_signature : 0; ?>
	<?php $classes .= " paragraph-".$paragraphnumber; ?>
	
	
	<div <?php comment_class($classes); ?> id="comment-<?php echo $current_blog_id ?>-<?php comment_ID() ?>">
		<div id="div-comment-<?php echo $current_blog_id; ?>-<?php comment_ID(); ?>" class="comment-body">
			
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
					
					<?php if(is_single()):  ?>
					<?php global $blog_id; ?>
						<span class="comment-blog-id" title="<?php echo $blog_id; ?>"></span>
					<?php else: ?>
						<span class="comment-blog-id" title="<?php echo $comment->blog_id; ?>"></span>
					<?php endif; ?>
					<span class="comment-id" title="<?php comment_ID(); ?>"></span>
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
				
				<?php 
				//
				if(false){ ?>
					<p><i>This comment has been quarantined for violating Site Use Guidelines.</i></p><?php
				}
				else{
					comment_text();
				}	
				
				?>						
				
			</div>
			
			
			
			<?php if(($comment->comment_parent == 0 || is_null($comment->comment_parent)) && (is_user_logged_in() || !get_option('comment_registration')) && is_single()): ?>
			<div class="comment-reply comment-hover small-button" title="<?php comment_ID(); ?>">reply</div>
			<?php endif; ?>

			<?php do_action('digressit_custom_comment_footer'); ?>

			<div class="comment-respond">
			</div>
			
		</div>
	</div>
	

	<?php
}


function digressit_comment_form(){
global $blog_id;
?>
<form method="post" action="/" id="add-comment">

	<?php if(!is_user_logged_in()): ?>
		
		<p><input type="text" class="comment-field-area" id="display_name"  name="display_name" value="Your Name" ><p>
		<p><input type="text" class="comment-field-area" id="user_email" name="user_email" value="Email"></p>
		
		
	<?php endif; ?>
	<div id="textarea-wrapper">
		<div class="left"></div>
		<div class="right">
		<textarea name="comment" class="comment-textarea comment-collapsed" id="comment" tabindex="1">Click here add a new comment...</textarea>
		</div>
	</div>

	<input name="blog_id" type="hidden"  value="<?php echo $blog_id; ?>" />
	<input name="selected_paragraph_number" type="hidden" id="selected_paragraph_number"  value="0" />

	<div id="submit-wrapper">
		<div name="cancel-response" id="cancel-response" class="button link">Cancel</div>
		<div name="submit" id="submit-comment"  class="submit ajax"><div class="loading-bars"></div>Submit</div>
	</div>
	<?php comment_id_fields(); ?>
	<?php do_action('comment_form', $post->ID); ?>

</form>
<?php
}


function digressit_live_spam_check_comment( $comment ) {
	global $akismet_api_host, $akismet_api_port;
	
	if(function_exists('akismet_verify_key')){
	
		if(akismet_verify_key(akismet_get_key())){
			$comment['user_ip']    = $_SERVER['REMOTE_ADDR'];
			$comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$comment['referrer']   = $_SERVER['HTTP_REFERER'];
			$comment['blog']       = get_option('home');
			$comment['blog_lang']  = get_locale();
			$comment['blog_charset'] = get_option('blog_charset');
			$comment['permalink']  = get_permalink($comment['comment_post_ID']);
	
			$comment['user_role'] = akismet_get_user_roles($comment['user_ID']);

			$ignore = array( 'HTTP_COOKIE' );

			foreach ( $_SERVER as $key => $value )
				if ( !in_array( $key, $ignore ) && is_string($value) )
					$comment["$key"] = $value;

			$query_string = '';
			foreach ( $comment as $key => $data )
				$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';

			$response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
			if ( 'true' == $response[1] ) {
				return true;
			}
			return false;
		}
	}
	
	return false;
}

function get_comments_toolbar(){
	do_action('comments_toolbar');
}

function indexOf($needle, $haystack)
{
	if(($index =array_search($needle,$haystack)) !==false)
		return $index;
	else
		return -1;
}

function list_posts()
{
	global $current_browser_section;
	
	$myposts = get_posts('numberposts=-1');
	
	?>
	
	<ol class="navigation">
	<?php
	foreach($myposts as $post): ?>
	<?php
	$permalink = get_bloginfo('siteurl')."/".$current_browser_section.'/'.$post->ID;
	?>
	<li><a href="<?php echo $permalink; ?>"><?php echo get_the_title($post->ID); ?> (<?php echo get_post_comment_count($post->ID); ?>)</a></li>
	<?php endforeach;
	?>
	</ol>
	<?php
}



function list_users()
{
	global $current_browser_section;
	$users = get_users_who_have_commented();
	//var_dump($users);
	?>
	<ol class="navigation">
	<?php
	foreach($users as $user) :
		$permalink = get_bloginfo('siteurl')."/".$current_browser_section.'/'.$user->ID;
		?>
		<li><a href="<?php echo $permalink; ?>"><?php echo $user->user_login; ?> (<?php echo $user->comments_per_user; ?>)</a></li>
	<?php endforeach; 
	?>
	</ol>
	<?php

}



function list_general_comments()
{
	global $wp_rewrite, $post;

?>
	<ol>
	<?php
	$options = get_option('digressit');

	extract($options);
	global $post;
	$myposts = get_posts('numberposts=-1');
	return $myposts;
	/*
	foreach($myposts as $post) :
	 	setup_postdata($post);
		$permalink = get_permalink($post->ID);
		$permalink = str_replace(get_bloginfo('siteurl'),get_bloginfo('siteurl')."/".$current_browser_section,$permalink);
		?>
		<li><a href="<?php echo $permalink; ?>"><?php the_title(); ?> (<?php echo get_post_comment_count($post->ID); ?>)</a></li>
	<?php endforeach; 
	*/
}




//the following were imported from CP1.4
function get_approved_general_comments($id){
	$approved_comments = get_approved_comments_and_pingbacks($id);
	
	$general_comments = null;

	foreach($approved_comments as $comment){
		if(!$comment->comment_text_signature){
			$general_comments[] = $comment;
		}
	}
	
	return $general_comments;

}

function getCommentCount($title)
{
	global $wpdb;
	$title = strip_tags($title);
	$title = addslashes($title);
	$sql = "SELECT * FROM $wpdb->comments, $wpdb->posts WHERE comment_post_ID=ID AND post_status='publish' AND comment_approved='1' AND post_name = '$title' AND post_status='publish'";
	$result = $wpdb->get_results($sql);
	return ( count($result) );
}

/* REDO THIS FUNCTION */
$comments_for_counting = null;
function get_post_comment_count($post_ID, $metatag = null, $metavalue = null){
	global $wpdb, $comments_for_counting;
	
	$sql = "SELECT * FROM $wpdb->comments c 
			WHERE c.comment_post_ID = $post_ID 
			AND c.comment_approved = 1 
			AND c.comment_type = ''";	

	$comments_for_counting = $wpdb->get_results($sql);		

	$count= 0;

	if($metatag){
		foreach($comments_for_counting as $c){
			$value = get_metadata('comment', $c->comment_ID, $metatag, true);
			if((int)$metavalue == (int)$value){
				$count++;					
			}
		}
		return $count;
	}
	
	return count($comments_for_counting);
	
}
function getCommentCountByCategory($cat)
{
	global $wpdb;
	$cat = strip_tags($cat);
	$cat = addslashes($cat);
	$sql = "SELECT * FROM $wpdb->posts, $wpdb->post2cat, $wpdb->categories  WHERE category_nicename = '$cat' AND cat_ID = category_id AND post_id = ID";


	$commentCategories = $wpdb->get_results($sql);
	$count = 0;
	foreach($commentCategories as $c)
	{
		$count += $c->comment_count;
	}
	
	return $count;
	
}



function get_users_who_have_commented()
{
	global $wpdb;
	$sql = "SELECT * , COUNT( * ) AS comments_per_user FROM $wpdb->comments c, $wpdb->posts p,  $wpdb->users u
	WHERE  p.ID = c.comment_post_ID  
	AND p.post_status='publish' 
	AND c.comment_approved = 1 
	AND c.user_id = u.ID
	GROUP BY c.comment_author 
	ORDER BY c.comment_author";
	
	$results = $wpdb->get_results($sql);
	
	//TODO: do in SQL
	$filtered = array();
	foreach($results as $result){
		$filtered[] = $result;
	}

	return $filtered;
}



function get_comments_from_user($id){
 	global $wpdb;	

	$sql = "SELECT c.*, u.*, p.post_name, p.post_title FROM $wpdb->comments c, $wpdb->users u, $wpdb->posts p  WHERE p.post_status='publish' AND c.user_id = u.ID AND u.ID=$id AND c.comment_post_ID = p.ID ORDER BY comment_ID DESC";
	$results = $wpdb->get_results($sql);

	//var_dump($results);
	return $results;
	
}



function getContributorsWhoHaveCommented(){
	global $wpdb;
	$sql = "SELECT * , COUNT( * ) AS comments_per_user FROM $wpdb->usermeta m, $wpdb->comments c, $wpdb->users u, $wpdb->posts p WHERE  p.ID = c.comment_post_ID AND u.ID = m.user_id  AND p.post_status='publish' AND c.user_id = u.ID GROUP BY u.ID ORDER BY u.user_login";
	return $wpdb->get_results($sql);              
}

function getParentPosts(){
	global $wpdb;
	$sql = "SELECT * FROM `$wpdb->posts` WHERE post_status='publish' AND post_parent='0' AND post_type = 'post'";
	$result = $wpdb->get_results($sql);
	return $result;
}

/* this might be useless */
function getAllCommentCount(){
	global $wpdb;
	$sql = "SELECT * FROM $wpdb->comments, $wpdb->posts WHERE comment_approved <> 'spam' AND comment_post_ID=ID AND post_type='post' AND post_status='publish'";
	$result = $wpdb->get_results($sql);
	return (count($result));
}

function get_all_comments($only_approved = true){
	global $wpdb;
	
	if($only_approved){
		$clause = "AND comment_approved='1'";
	}
	else{
		$clause = '';
	}
	
	$sql = "SELECT * FROM $wpdb->comments WHERE comment_type = '' " . $clause;
	return $result = $wpdb->get_results($sql);
			
}


function getRecentComments($limit = 5, $cleaned = false){
	global $wpdb;
	$sql = null;
	if($cleaned){
		$sql = "SELECT c.comment_ID,  c.comment_author, c.comment_date, c.comment_content, c.comment_parent, c.comment_post_ID, c.comment_text_signature, p.ID  FROM $wpdb->comments c, $wpdb->posts p WHERE comment_post_ID=ID AND post_status='publish' AND comment_approved='1' ORDER BY comment_date DESC LIMIT $limit ";			
	}
	else{
		$sql = "SELECT * FROM $wpdb->comments, $wpdb->posts WHERE comment_post_ID=ID AND post_status='publish' AND comment_approved='1' ORDER BY comment_date DESC LIMIT $limit ";
	}
	return $wpdb->get_results($sql);               
}


function get_approved_comments_for_paragraph($post_id, $paragraph){
	$approved_comments = get_approved_comments_and_pingbacks($post_id);		
	$filtered = null;
	foreach($approved_comments as $comment){
		if($comment->comment_text_signature == $paragraph){
			$filtered[] = $comment;
		}
	}
	return $filtered;
}



function mu_get_all_comments($user_id = null, $blog_id = null){
	
	$rule_list = null;
	if($blog_id){
		$rule['blog_id']  = $blog_id;
		$rule_list = $rule;
	}
	else{
		$rule_list = get_blog_list ( 0, 'all' );
	}
	
	$comments = array();
	foreach($rule_list as $rule){
		switch_to_blog( $rule['blog_id']);
		
		
		if($user_id){
			$current_comments= get_comments_from_user($user_id);				
		}
		else{
			$current_comments= get_all_comments();
		}
		
		$comments = array_merge($comments, $current_comments);
		restore_current_blog();
	}		
	
	return $comments;
}



function mu_get_comments_from_user($user_id){
	$rule_list = get_rules();
	
	//var_dump($rule_list);

	$comments = array();
	foreach($rule_list as $rule){
		switch_to_blog( $rule['blog_id']);
		$current_comments= get_comments_from_user($user_id);
		$comments = array_merge($comments, $current_comments);
		restore_current_blog();
	}		
	
	return $comments;
}





class CommentBrowserLinks extends WP_Widget {
	/** constructor */
	function CommentBrowserLinks() {
		parent::WP_Widget(false, $name = 'Comment Browser Links');	
	}

	function widget($args = array(), $defaults) {		
		extract( $args );

		?>
		<h4>Comment Browser</h4>
		<ul>
			<li><a href="<?php bloginfo('home'); ?>/comments-by-section/1">Comments by Section</a></li>
			<li><a href="<?php bloginfo('home'); ?>/comments-by-user/1">Comments by Users</a></li>
			<li><a href="<?php bloginfo('home'); ?>/general-comments/1">General Comments</a></li>
			<?php do_action('add_commentbrowser_link'); ?>
		</ul>
		<?php
    }

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		return $instance;
	}



	/** @see WP_Widget::form */
	function form($instance) {				
		global $blog_id, $wpdb;
		return $instance;
	}

}


?>