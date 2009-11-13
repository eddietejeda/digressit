<?php

class Digress_It_CommentBrowser extends Digress_It_Base{


	function __construct(){
        parent::__construct();	
		$this->Digress_It_CommentBrowser();
		
	}
	/**
	 * create the hooks to wordpress
	 */
	function Digress_It_CommentBrowser() {
		//register_activation_hook( __FILE__, array( &$this, 'on_activation') );
		//register_deactivation_hook(__FILE__,  array(&$this,'on_deactivation') );

		add_action( 'init', array( &$this, 'on_init') );				
		add_action( 'widgets_init', array( &$this, 'on_widget_init') );
		add_action( 'template_redirect', array( &$this, 'on_template_redirect') );
		add_action( 'wp_print_styles', array( &$this, 'on_wp_print_styles') );		
		add_action( 'init', array( &$this, 'add_feeds') );		




	}

	function add_feeds() {
	  global $wp_rewrite;
	  add_feed('usercomments', array( &$this, 'create_user_feed'));
	  add_feed('paragraphcomments', array( &$this, 'create_paragraph_feed'));
	  add_feed('paragraphlevel', array( &$this, 'create_paragraph_level_feed'));
	  add_action('generate_rewrite_rules', array( &$this, 'rewrite_rules') );
	  $wp_rewrite->flush_rules();
	}
		
	function rewrite_rules( $wp_rewrite ) {
	  $new_rules = array(
	    'feed/(.+)/(.+)' => 'index.php?feed='.$wp_rewrite->preg_index(1)
	  );
	  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}




	function create_paragraph_level_feed(){
		include('paragraph-level-feed.php');
	}
	
	function create_paragraph_feed(){
		include('paragraph-comments-feed.php');
	}
	
	function create_user_feed(){
		include('user-comments-feed.php');
	}



	function get_paragraph_feed($post_id, $paragraph){
		if(strlen($user)){
			return $this->wp_path .'/feed/paragraphcomments/'.$post_id . '/'.$paragraph;
		}
		else{
			return false;
		}
	}		

	
	function get_user_feed($user){
		if(strlen($user)){
			return $this->wp_path .'/feed/usercomments/'.urlencode($user);
		}
		else{
			return false;
		}
	}		
	
	function on_init(){

		$this->wp_path = get_bloginfo('wpurl');
		$this->plugin_path = $this->wp_path.'/wp-content/plugins/'.dirname(plugin_basename(__FILE__)); 		

		if($_GET['comment-browser']){
			include( 'theme/comment-browser.php');
			exit;		
		}

	}


	function on_wp_print_styles(){

		if(is_page())
		{		
			wp_enqueue_style( 'commentbrowser', $this->plugin_path."/style.css");
		}

	}

	/**
	 * initialize widget sidebar and the backend interface
	 */
	function on_widget_init() {
		if (!function_exists('register_sidebar_widget'))
			return;

	  	register_sidebar_widget('CommentBrowser', array( &$this, 'on_widget_register') );
		register_widget_control('CommentBrowser', array( &$this, 'on_widget_control') );
	}

	function on_template_redirect(){
		global $post;		
		$pages = get_option('commentbrowser');		

	}

	/**
	 * 
	 */
	function on_activation() {

		//die('activate');
		if(get_option('commentbrowser'))
		{
			delete_option('commentbrowser');			
		}
//		else{
		$bysection = array();
		$bysection['post_title'] = 'Comments on Page';
		$bysection['post_content'] = 'This is my post.';
		$bysection['post_status'] = 'publish';
		$bysection['post_author'] = 1;
		$bysection['post_type'] = 'page';

		$byusers = array();
		$byusers['post_title'] = 'Comments by User';
		$byusers['post_content'] = 'This is my post.';
		$byusers['post_status'] = 'publish';
		$byusers['post_author'] = 1;
		$byusers['post_type'] = 'page';

		$generalcomments = array();
		$generalcomments['post_title'] = 'General Comments';
		$generalcomments['post_content'] = 'This is my post.';
		$generalcomments['post_status'] = 'publish';
		$generalcomments['post_author'] = 1;
		$generalcomments['post_type'] = 'page';

		$commentbrowser['bysection'] = wp_insert_post( $bysection );
		$commentbrowser['byusers'] = wp_insert_post( $byusers );
		//$commentbrowser['generalcomments'] = wp_insert_post( $generalcomments );

		add_option('commentbrowser', $commentbrowser);
//		}
	}

	function on_deactivation() {

		if(get_option('commentbrowser'))
		{
			delete_option('commentbrowser');			
		}
	}


	/** 
	 *
	 *
	 */		
	function on_widget_register($args) {
		extract($args);
		$options = get_option('commentbrowser');

		//$listofpages= implode(',', $options);
	?>			
		<?php echo $before_widget; ?>
		<?php echo $before_title . "Comments" . $after_title; ?>
		
		<ul>
			<li><a href="<?php echo $this->wp_path; ?>/?comment-browser=users">by Commenter</a></li>
			<li><a href="<?php echo $this->wp_path; ?>/?comment-browser=posts">by Section</a></li>
			<li><a href="<?php echo $this->wp_path; ?>/?comment-browser=general">General Comments</a></li>
		</ul>
		
		<?php echo $after_widget; ?>
		<?php
	}

	/**
	 */		
	function on_widget_control() {
		$options = $newoptions = get_option('commentbrowser');
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
		global $wp_rewrite, $post;

	?>
		<ol>
		<?php
		$options = get_option('digressit');

		extract($options);

		$myposts = null;
		if($front_page_post_type){
			$myposts = get_posts("post_type=$front_page_post_type&numberposts=-1&order=$front_page_order&orderby=$front_page_order_by");
		}
		else{
			$myposts = get_posts();				
		}
		$commentbrowser = get_option('commentbrowser');
		$bysection  = get_post($commentbrowser['bysection']);

		 foreach($myposts as $post) :
		 ?>

			<?php $permalink_name = $bysection->post_name; ?>
 			<?php $comment_array = get_approved_comments($post->ID);  ?>
			<?php $seperator = (strlen($wp_rewrite->permalink_structure)) ? "?" : "&"; ?>
			<?php $path = (strlen($wp_rewrite->permalink_structure)) ? $permalink_name .'' : '?page_id='.$commentbrowser['bysection']; ?>

		    <li><a href="<?php echo $this->wp_path. "/".$path.$seperator ."post=$post->ID"; ?>&comment-browser=posts"><?php the_title(); ?> (<?php echo count($comment_array); ?>)</a></li>
		 <?php endforeach; ?>
		 </ol> 
	<?php	
	}

	function list_users()
	{
		global $wp_rewrite;
		$users = $this->getUsersWhoHaveCommented();

		echo "<ol>";
		foreach($users as $user) :

			$commentbrowser = get_option('commentbrowser');

			$byusers = get_post($commentbrowser['byusers']);			
			$permalink_name = $byusers->post_name;

			$path = (strlen($wp_rewrite->permalink_structure)) ? $permalink_name .'' : '?page_id='.$commentbrowser['byusers'];

			$comments = $this->get_comments_from_user($user->user_id);
			$comments_count = $user->comments_per_user; 

			$seperator = (strlen($wp_rewrite->permalink_structure)) ? "?" : "&";


			$userdata = get_userdata($user->user_id);

			$user_display_name = ($user->user_id) ? ($userdata->user_login) : ($user->comment_author);
			$user_identifier = ($user->user_id) ? ($user->user_id) : ($user->comment_author);

			echo "<li><a href='$this->wp_path/$path".""."$seperator".""."user=".urlencode($user_identifier)."&comment-browser=users'>$user_display_name ($comments_count)</a></li>";

		endforeach;
		echo "</ol>";			
	}


	function list_general_comments()
	{
		global $wp_rewrite;
		$users = $this->getUsersWhoHaveCommented();

		echo "<ol>";
		foreach($users as $user) :

			$commentbrowser = get_option('commentbrowser');

			$byusers = get_post($commentbrowser['byusers']);			
			$permalink_name = $byusers->post_name;

			$path = (strlen($wp_rewrite->permalink_structure)) ? $permalink_name .'' : '?page_id='.$commentbrowser['byusers'];

			$comments = $this->get_approved_general_comments($user->user_id);
			$comments_count = count($comments);

			$seperator = (strlen($wp_rewrite->permalink_structure)) ? "?" : "&";


			$userdata = get_userdata($user->user_id);

			$user_display_name = ($user->user_id) ? ($userdata->user_login) : ($user->comment_author);
			$user_identifier = ($user->user_id) ? ($user->user_id) : ($user->comment_author);

			echo "<li><a href='$this->wp_path/$path".""."$seperator".""."user=".urlencode($user_identifier)."&comment-browser=general'>$user_display_name ($comments_count)</a></li>";

		endforeach;
		echo "</ol>";
	}


	function print_comments($request_section, $id)
	{
		global $in_comment_loop;

		global $comments;
		$comments = null;
		$id = urldecode($id);
		switch($request_section)
		{

			case "users":
				$comments = $this->get_comments_from_user($id);
			break;
			case "posts":
				$comments = get_approved_comments($id); 
			break;
			case "general":
				$comments = $this->get_approved_general_comments($id); 
			break;			
		}
		?>
		<?php

		if(function_exists('digressit_list_comments')){
			wp_list_comments('callback=digressit_list_comments', $comments);
		}
		else{
			wp_list_comments();			
		}

	}









	//the following were imported from CP1.4
	function get_approved_general_comments($id){
		$approved_comments = get_approved_comments($id);
		
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

	function getUsersWhoHaveCommented()
	{
		global $wpdb;
		$sql = "SELECT * , COUNT( * ) AS comments_per_user FROM $wpdb->comments c, $wpdb->posts p WHERE  p.ID = c.comment_post_ID  AND p.post_status='publish' AND c.comment_approved = 1 GROUP BY c.comment_author ORDER BY c.comment_author";
		return $wpdb->get_results($sql);
	}




	function get_comments_from_user($id){
	 	global $wpdb;	
		$sql = null;
		
		
		if(is_numeric($id))
		{
			$sql = "SELECT c.*, u.*, p.post_name, p.post_title FROM $wpdb->comments c, $wpdb->users u, $wpdb->posts p  WHERE c.comment_approved='1' AND p.post_status='publish' AND c.user_id = u.ID AND u.ID=$id AND c.comment_post_ID = p.ID ORDER BY comment_ID DESC";
		}
		else
		{
			$sql = "SELECT c.*, p.post_name, p.post_title FROM $wpdb->comments c, $wpdb->posts p  WHERE c.comment_approved='1' AND p.post_status='publish' AND c.comment_author = '".urldecode($id)."'  AND c.comment_post_ID = p.ID ORDER BY comment_ID DESC";
			//$sql = 'SELECT c.*, p.post_name, p.post_title FROM $wpdb->comments c, $wpdb->posts p  WHERE c.comment_approved="1" AND p.post_status="publish" AND c.comment_post_ID = p.ID AND c.comment_author = "'.urldecode($id).'" ORDER BY comment_ID DESC';

		}
			

		$results = $wpdb->get_results($sql);

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
		$sql = "SELECT * FROM $wpdb->comments, $wpdb->posts WHERE comment_post_ID=ID AND post_type='post' AND post_status='publish'";
		$result = $wpdb->get_results($sql);
		return (count($result));
	}
	
	function get_all_comments(){
		global $wpdb;
		$sql = "SELECT * FROM $wpdb->comments";
		return $result = $wpdb->get_results($sql);
				
	}
	
/*
	new_comment_text= '<li id="comment-' + new_comment['comment_ID'] + '" class="comment byuser comment-author-admin bypostauthor odd alt thread-even depth-1 parent"> ' +
		'<div class="comment-body" id="div-comment-' + new_comment['comment_ID'] + '"> ' +
		'<div class="comment-author vcard">' +
		'<cite class="fn">' + new_comment['comment_author'] + '</cite> <span class="says">says:</span>		</div>' +
		'<div class="comment-meta commentmetadata"><a href="'+ window.location.href +'#comment-'+ new_comment['comment_ID'] +'">'+ new_comment['comment_date'] +'</a> </div>' +
		'<p>' + new_comment['comment_content'] + '</p>' +
		'<div class="reply">' +
		'<a onclick=\'return addComment.moveForm("div-comment-'+ new_comment['comment_ID'] +'", "'+ new_comment['comment_ID'] +'", "respond", "'+ new_comment['comment_parent'] +'")\' href="'+ window.location.href +'?replytocom='+ new_comment['comment_ID'] +'#respond" class="comment-reply-link" rel="nofollow">Reply</a></div>' +
		'</div>' +
	'</li>';
*/
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
		$approved_comments = get_approved_comments($post_id);		
		$filtered = array();
		foreach($approved_comments as $comment){
			if($comment->comment_text_signature == $paragraph){
				$filtered[] = $comment;
			}
		}
		return $filtered;
	}
}



function digressit_pingback_ping($args) {
		global $wpdb;

		do_action('xmlrpc_call', 'pingback.ping');

		$this->escape($args);

		$pagelinkedfrom = $args[0];
		$pagelinkedto   = $args[1];

		$title = '';

		$pagelinkedfrom = str_replace('&amp;', '&', $pagelinkedfrom);
		$pagelinkedto = str_replace('&amp;', '&', $pagelinkedto);
		$pagelinkedto = str_replace('&', '&amp;', $pagelinkedto);

		// Check if the page linked to is in our site
		$pos1 = strpos($pagelinkedto, str_replace(array('http://www.','http://','https://www.','https://'), '', get_option('home')));
		if( !$pos1 )
			return new IXR_Error(0, __('Is there no link to us?'));

		// let's find which post is linked to
		// FIXME: does url_to_postid() cover all these cases already?
		//        if so, then let's use it and drop the old code.
		$urltest = parse_url($pagelinkedto);
		if ($post_ID = url_to_postid($pagelinkedto)) {
			$way = 'url_to_postid()';
		} elseif (preg_match('#p/[0-9]{1,}#', $urltest['path'], $match)) {
			// the path defines the post_ID (archives/p/XXXX)
			$blah = explode('/', $match[0]);
			$post_ID = (int) $blah[1];
			$way = 'from the path';
		} elseif (preg_match('#p=[0-9]{1,}#', $urltest['query'], $match)) {
			// the querystring defines the post_ID (?p=XXXX)
			$blah = explode('=', $match[0]);
			$post_ID = (int) $blah[1];
			$way = 'from the querystring';
		} elseif (isset($urltest['fragment'])) {
			// an #anchor is there, it's either...
			if (intval($urltest['fragment'])) {
				// ...an integer #XXXX (simpliest case)
				$post_ID = (int) $urltest['fragment'];
				$way = 'from the fragment (numeric)';
			} elseif (preg_match('/post-[0-9]+/',$urltest['fragment'])) {
				// ...a post id in the form 'post-###'
				$post_ID = preg_replace('/[^0-9]+/', '', $urltest['fragment']);
				$way = 'from the fragment (post-###)';
			} elseif (is_string($urltest['fragment'])) {
				// ...or a string #title, a little more complicated
				$title = preg_replace('/[^a-z0-9]/i', '.', $urltest['fragment']);
				$sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title RLIKE %s", $title);
				if (! ($post_ID = $wpdb->get_var($sql)) ) {
					// returning unknown error '0' is better than die()ing
			  		return new IXR_Error(0, '');
				}
				$way = 'from the fragment (title)';
			}
		} else {
			// TODO: Attempt to extract a post ID from the given URL
	  		return new IXR_Error(33, __('The specified target URL cannot be used as a target. It either doesn&#8217;t exist, or it is not a pingback-enabled resource.'));
		}
		$post_ID = (int) $post_ID;


		logIO("O","(PB) URL='$pagelinkedto' ID='$post_ID' Found='$way'");

		$post = get_post($post_ID);

		if ( !$post ) // Post_ID not found
	  		return new IXR_Error(33, __('The specified target URL cannot be used as a target. It either doesn&#8217;t exist, or it is not a pingback-enabled resource.'));

		if ( $post_ID == url_to_postid($pagelinkedfrom) )
			return new IXR_Error(0, __('The source URL and the target URL cannot both point to the same resource.'));

		// Check if pings are on
		if ( !pings_open($post) )
	  		return new IXR_Error(33, __('The specified target URL cannot be used as a target. It either doesn&#8217;t exist, or it is not a pingback-enabled resource.'));

		// Let's check that the remote site didn't already pingback this entry
		$wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_author_url = %s", $post_ID, $pagelinkedfrom) );

		if ( $wpdb->num_rows ) // We already have a Pingback from this URL
	  		return new IXR_Error(48, __('The pingback has already been registered.'));

		// very stupid, but gives time to the 'from' server to publish !
		sleep(1);

		// Let's check the remote site
		$linea = wp_remote_fopen( $pagelinkedfrom );
		if ( !$linea )
	  		return new IXR_Error(16, __('The source URL does not exist.'));

		$linea = apply_filters('pre_remote_source', $linea, $pagelinkedto);

		// Work around bug in strip_tags():
		$linea = str_replace('<!DOC', '<DOC', $linea);
		$linea = preg_replace( '/[\s\r\n\t]+/', ' ', $linea ); // normalize spaces
		$linea = preg_replace( "/ <(h1|h2|h3|h4|h5|h6|p|th|td|li|dt|dd|pre|caption|input|textarea|button|body)[^>]*>/", "\n\n", $linea );

		preg_match('|<title>([^<]*?)</title>|is', $linea, $matchtitle);
		$title = $matchtitle[1];
		if ( empty( $title ) )
			return new IXR_Error(32, __('We cannot find a title on that page.'));

		$linea = strip_tags( $linea, '<a>' ); // just keep the tag we need

		$p = explode( "\n\n", $linea );

		$preg_target = preg_quote($pagelinkedto, '|');

		foreach ( $p as $para ) {
			if ( strpos($para, $pagelinkedto) !== false ) { // it exists, but is it a link?
				preg_match("|<a[^>]+?".$preg_target."[^>]*>([^>]+?)</a>|", $para, $context);

				// If the URL isn't in a link context, keep looking
				if ( empty($context) )
					continue;

				// We're going to use this fake tag to mark the context in a bit
				// the marker is needed in case the link text appears more than once in the paragraph
				$excerpt = preg_replace('|\</?wpcontext\>|', '', $para);

				// prevent really long link text
				if ( strlen($context[1]) > 100 )
					$context[1] = substr($context[1], 0, 100) . '...';

				$marker = '<wpcontext>'.$context[1].'</wpcontext>';    // set up our marker
				$excerpt= str_replace($context[0], $marker, $excerpt); // swap out the link for our marker
				$excerpt = strip_tags($excerpt, '<wpcontext>');        // strip all tags but our context marker
				$excerpt = trim($excerpt);
				$preg_marker = preg_quote($marker, '|');
				$excerpt = preg_replace("|.*?\s(.{0,100}$preg_marker.{0,100})\s.*|s", '$1', $excerpt);
				$excerpt = strip_tags($excerpt); // YES, again, to remove the marker wrapper
				break;
			}
		}

		if ( empty($context) ) // Link to target not found
			return new IXR_Error(17, __('The source URL does not contain a link to the target URL, and so cannot be used as a source.'));

		$pagelinkedfrom = str_replace('&', '&amp;', $pagelinkedfrom);

		$context = '[...] ' . esc_html( $excerpt ) . ' [...]';
		$pagelinkedfrom = $wpdb->escape( $pagelinkedfrom );

		$comment_post_ID = (int) $post_ID;
		$comment_author = $title;
		$this->escape($comment_author);
		$comment_author_url = $pagelinkedfrom;
		$comment_content = $context;
		$this->escape($comment_content);
		$comment_type = 'pingback';

		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_content', 'comment_type');

		$comment_ID = wp_new_comment($commentdata);
		do_action('pingback_post', $comment_ID);


		$comment_text_signature = $_GET['paragraph'];
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->comments SET comment_text_signature = %s WHERE comment_ID = %d", $comment_text_signature, $comment_ID) );


		return sprintf(__('Pingback from %1$s to %2$s registered. Keep the web talking! :-)'), $pagelinkedfrom, $pagelinkedto);
	}

function attach_new_xmlrpc($methods) {
    $methods['pingback.ping'] = 'digressit_pingback_ping';
    return $methods;
}
add_action('xmlrpc_methods', 'attach_new_xmlrpc');



