<?php

add_action( 'init', 'digressit_add_feeds' );		

/**
 *
 */
function digressit_add_feeds() {
	global $wp_rewrite;
	add_filter('query_vars', 'digressit_feed_query_vars', 0);
	add_action('rewrite_rules_array', 'digressit_feed_rewrite_rules_array', 0 );
	add_action('template_redirect', 'digressit_feed_template' );
//	$wp_rewrite->flush_rules();
}

/**
 * Create some extra variables to accept when passed through the url
 */
function digressit_feed_query_vars( $query_vars ) {
	$myvars = array('feed_name', 'feed_parameter' );
	$query_vars = array_merge( $query_vars, $myvars );
	return $query_vars;
}


/*
 * Create a rewrite rule if you want pretty permalinks
 */ 
function digressit_feed_rewrite_rules_array( $rules ) {

	$newrules = array();
	$newrules['feeds/([^/]+)/([^/]+)$'] = 'index.php?feed_name=$matches[1]&feed_parameter=$matches[2]';
	return $newrules + $rules;
}


/**
 *
 */
function digressit_feed_template(){
	global $wp;

	if(isset( $wp->query_vars['feed_name'] ) && !empty($wp->query_vars['feed_name'] ) ){
	
		switch($wp->query_vars['feed_name'] ){
			
			case 'usercomments':
				include('feeds/user-comments-feed.php');
				die();
			break;

			case 'paragraphcomments':
				include('feeds/paragraph-comments-feed.php');
				die();
			break;

			case 'paragraphlevel':
				include('feeds/paragraph-level-feed.php');
				die();
			break;
			
		}
	}
}


?>