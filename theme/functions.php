<?php 
global $commentbrowser, $blog_id, $current_user, $current_user_comments, $development_mode, $testing_mode, $production_mode;
global $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function,$is_commentbrowser;

global $browser;

$browser = current_browser();
$is_commentbrowser= false;


get_currentuserinfo();

/*

New Theme Function
function get_primary_menu();
function get_secondary_menu();
function get_widgets($widget_name);
function get_stylized_content_header();
function get_stylized_title();
function is_frontpage();
function get_dynamic_widgets();
function add_digressit_parsing_function();
*/



add_action( 'after_setup_theme', 'digressit_setup' );

add_digressit_content_function('standard_digressit_content_parser');
add_digressit_comments_function('standard_digressit_comment_parser');
add_digressit_commentbox_function('standard_digressit_commentbox_parser');


if(file_exists(TEMPLATEPATH . '/extensions.php')){
	require_once(TEMPLATEPATH . '/extensions.php');	
}

add_action('wp', 'digressit_wp');

add_action('admin_head-post.php', 'add_comment_change_notice');

add_action('wp', 'apply_content_parser');


add_action('wp', 'digressit_init_beta');

function digressit_init_beta(){

	if(strtotime('now') > strtotime("30 October 2010")){
		die('This is BETA testing period has expired. Please install official release');
	}
}


function apply_content_parser(){
	if(is_single()){
		add_filter('the_content', 'digressit_parser', 10000);	
	}
}


function digressit_wp(){
	//after extensions are loaded
	add_action('wp_print_scripts',  'functions_wp_print_scripts' );
	add_action('wp_print_styles',  'functions_wp_print_styles') ; 		
	
}



function digressit_setup(){
	global $wpdb;
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu()
	add_theme_support( 'nav-menus' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'digressit', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) ){
		require_once( $locale_file );
	}


	
	$sql = "SHOW COLUMNS FROM $wpdb->comments";	
	$columns = $wpdb->get_results($sql);

	$comment_text_signature_exists = false;
	foreach($columns as $col){
		if($col->Field == 'comment_text_signature'){
			$comment_text_signature_exists = true;
		}
	}

	

	if($comment_text_signature_exists == false){
		$sql = "ALTER TABLE `$wpdb->comments` ADD `comment_text_signature` VARCHAR( 255 ) NULL;";	
		$wpdb->query($sql);
	}


	// This theme allows users to set a custom background
	add_custom_background();	
	
	register_nav_menus(

		array(
		  'Main Page' => 'This menu appears in the first page',
		  'Top Menu' => 'A custom top menu',
		  'Sidebar' => 'A custom sidebar menu'
		)

	);

	
}
	
		
/*
function get_primary_menu(){
	do_action('primary_menu');
}
*/

function get_secondary_menu(){
	do_action('secondary_menu');
}

function get_widgets($widget_name){
	return dynamic_sidebar($widget_name);
}

function get_dynamic_widgets(){
	do_action('add_dynamic_widget');
}


function get_stylized_content_header(){
	
	if(has_action('stylized_content_header')){
		do_action('stylized_content_header');
	}
}



function get_stylized_title(){
	
	echo "<div id='the_title'  class='the_title'>";	
	if(has_action('stylized_title')){
		do_action('stylized_title');
	}
	else{			
		if(is_single() || is_page() || is_search()){
			echo "<h2><a href='".get_permalink()."'>".get_the_title()."</a></h2>";
		}			
	}
	echo "</div>";
}


/*
add_action('stylized_title', 'standard_stylized_title');


function standard_stylized_title($title){
	
		echo "<h3>{$title}</h3>";
}
*/

function is_frontpage(){
	global $is_frontpage, $is_mainpage, $blog_id;
	
	if(WP_ALLOW_MULTISITE && file_exists(get_template_directory(). '/frontpage.php')){
		if(is_home()){
			if($blog_id == 1):			
				return true;
			else:
				return false;
			endif;
		}
	}
	else{
		return false;
	}
}


function is_mainpage(){
	global $is_frontpage, $is_mainpage, $blog_id;
	
	if(WP_ALLOW_MULTISITE && file_exists(get_template_directory(). '/frontpage.php')){
		if(is_home()){
			if($blog_id == 1):			
				return false;
			else:
				return true;
			endif;
		}
	}
	else{
		return false;
	}
}




function lightbox_custom_login(){
	//header('Location: /#login');
	//die();
}



function regexp_digressit_content_parser($html){
	$matches = array();
	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);

	$options = get_site_option('digressit');
	if($options['parse_list_items'] == 1){
		$html = preg_replace('/<(\/?ul|ol)>/', '', $html);
		$html = preg_replace('/<li>/', '<p>&bull;   ', $html);
	}
	$html = html_entity_decode(force_balance_tags($html));


	@preg_match_all('#<('.$tags.')>(.*?)</('.$tags.')>#si',$html,$matches_array);
	$matches = $matches_array[0];

		
	return  $matches;
}

function standard_digressit_content_parser($html, $tags = 'div|table|object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8'){
	global $post;
	$matches = array();

	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);

	$options = get_site_option('digressit');
	
	$blocks = null;
	$text_signatures = null;
	$permalink = get_permalink($post->ID);



	$defaults = array('post_id' => $post->ID);
	$total_comments = get_comments($defaults);
	$total_count = count($total_comments);
	
		
	if($options['parse_list_items'] == 1){
		$html = preg_replace('/<(\/?ul|ol)>/', '', $html);
		$html = preg_replace('/<li>/', '<p>&bull;   ', $html);
	}
	$html = html_entity_decode(wpautop(force_balance_tags($html)));

	if($result = @simplexml_load_string(trim('<content>'.$html.'</content>'))){
		$xml = $result->xpath('/content/'. $tags);
		foreach($xml as $match){
			$matches[] = $match->asXML();
		}
	}




	foreach($matches as $key=>$paragraph)
	{
 
		$text_signature = $key+1;
		$text_signatures[] = $text_signature;
		$paranumber = $number = ( $key+1 );


		$comment_count = 0;

		foreach($total_comments as $c){
			if($c->comment_text_signature == $paranumber){
				$comment_count++;
			}
		}
		
				
		$paragraphnumber = '<span class="paragraphnumber">';
		
		
		
	 	$numbertext = ($comment_count == 1) ?  'is one comment' : 'are '.$comment_count.' comments';
	 	$numbertext = ($comment_count == 0) ?  'are no comments' : $numbertext;
		
		$digit_count = strlen($comment_count);
		$commenticon =	'<span  title="There '.$numbertext.' for this paragraph" class="commenticonbox"><small class="commentcount fff commentcount'.$digit_count.'">'.$comment_count.'</small></span>'."\n";


		if($number == 1){
			//$morelink = '<span class="morelink"></span>';
		}
		else{
			$morelink = null;
		}


		$matches = null;

		preg_match_all('/class=\"([^"]+)\"/is', $paragraph, $matches); 		
		
		//var_dump($matches);
		
		if(count($matches)){
			foreach($matches[1] as $match){
				//var_dump( $match);
				if(strstr($match, 'wp-image')){		
					$paragraph = str_replace($match, 'lightbox lightbox-images '.$match, $paragraph);
				}
				$paragraph = str_replace(" class=\"$matches\" ", " class=\"lightbox lighbox-images $classes\" ", $paragraph);
				//break;
			}
		}
		$block_content = "<div id='textblock-$number' class='textblock'>
			<span class='paragraphnumber'><a href='$permalink#$number'>$number</a></span>
			<span  title='There $numbertext for this paragraph' class='commenticonbox'><small class='commentcount commentcount".$digit_count."'>".$comment_count."</small></span>
			<span class='paragraphtext'>".force_balance_tags($paragraph)."</span>
		</div>" .  $morelink;
		
		$blocks[$paranumber] = $block_content;
    }

	
	return $blocks;

}




function add_digressit_content_function($function_name){
	global $digressit_content_function;
	$digressit_content_function[$function_name] = $function_name;
}
function add_digressit_comments_function($function_name){
	global $digressit_comments_function;
	$digressit_comments_function[$function_name] = $function_name;
}

function add_digressit_commentbox_function($function_name){
	global $digressit_commentbox_function;
	$digressit_commentbox_function[$function_name] = $function_name;
}


function digressit_parser($content){
	return implode("\n",digressit_paragraphs($content));
}

function digressit_paragraphs($content){	
	return call_user_func(get_digressit_content_parser_function(), $content); 
}

function the_paragraph($number){
	global $post;
	

	echo get_the_paragraph($number);
	
}

function get_the_paragraph($number){
	global $post;
	
	$paragraphs = digressit_paragraphs(wpautop($post->post_content));
	
	return $paragraphs[$number];
	
	
}




function get_digressit_comments_function(){
	$options = get_site_option('digressit');

	if( isset($options['comments_function']) || function_exists($options['comments_function']) ){
		return $options['comments_function'];
	}
	else{
		return 'standard_digressit_comment_parser';
	}	
}

function get_digressit_content_parser_function(){
	$options = get_site_option('digressit');


	if( isset($options['content_parser']) || function_exists($options['content_parser']) ){
		return $options['content_parser'];
	}
	else{
		return 'standard_digressit_content_parser';
	}	
}






function json_remote_call($webservice, $parameters = null){

	if(is_array($parameters)){
		foreach($parameters as $key => $param){
			$params [$key] = $param;
		}
	}



	$params ['format'] = 'json';


	$data = http_build_query($params);

	//var_dump($data);


	$context_options = array (
		'http' => array (
			'method' => 'POST',
			'header'=> "Content-type: application/x-www-form-urlencoded\r\n",
			"Content-Length: " . strlen($data) . "\r\n",
			'content' => $data
			)
		);



	$context = stream_context_create($context_options);
	
	
	$response = null;
	if($json = file_get_contents($webservice, false, $context)){
		$response = json_decode($json);
	}
	else{
		$response = (object)array('responseCode' => false, 'errorMessage' => 'Server failed to respond. Please try again later');
	}
		
	return $response;
}



function collaborate_page($content){
	$blocks[] = $content;
	return $blocks;
}




function add_comment_change_notice() {	
	
	$comments= get_approved_comments($_GET['post']);
	
	if(count($comments)){
		add_action('admin_notices', 'change_content_warning' );
	}
}


function change_content_warning(){
	?>
	
	<div id="register-form" class="updated error" style="padding: 5px; width: 99% <?php echo $hidethis;?>" >
		Warning: There are comments attached to the structure of this page. Changing the structure
		of this post will break the alignment of comments to their paragraphs
	</div>
	
	<?php
	
}

function get_header_images(){	
	return do_action('add_header_image');
}









function current_browser() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

    // Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari.
    if (preg_match('/opera/', $userAgent)) {
        $name = 'opera';
    }
    elseif (preg_match('/webkit/', $userAgent)) {
        $name = 'safari';
    }
    elseif (preg_match('/msie/', $userAgent)) {
        $name = 'msie';
    }
    elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) {
        $name = 'mozilla';
    }
    else {
        $name = 'unrecognized';
    }

    // What version?
    if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) {
        $version = $matches[1];
    }
    else {
        $version = 'unknown';
    }

    // Running on what platform?
    if (preg_match('/linux/', $userAgent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/', $userAgent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/', $userAgent)) {
        $platform = 'windows';
    }
    else {
        $platform = 'unrecognized';
    }

    return array(
        'name'      => $name,
        'version'   => $version,
        'platform'  => $platform,
        'userAgent' => $userAgent
    );
}

function is_commentbrowser(){
	global $is_commentbrowser;
	return $is_commentbrowser;
}

function functions_wp_print_styles(){
	global $current_user, $override_default_theme, $browser;
	
	wp_register_style('digressit.frontpage', get_template_directory_uri().'/frontpage.css'); 
	wp_register_style('digressit.author', get_template_directory_uri().'/author.css'); 
	wp_register_style('digressit.comments', get_template_directory_uri().'/comments.css'); 
	wp_register_style('digressit.lightboxes', get_template_directory_uri().'/lightboxes.css'); 
	wp_register_style('digressit.sidebar', get_template_directory_uri().'/sidebar.css'); 
	wp_register_style('digressit.page', get_template_directory_uri().'/page.css'); 
	wp_register_style('digressit.search', get_template_directory_uri().'/search.css'); 
	wp_register_style('digressit.single', get_template_directory_uri().'/single.css');
	wp_register_style('digressit.theme', get_template_directory_uri().'/theme.css');

	wp_register_style('digressit.ie7', get_template_directory_uri().'/ie7.css');
	wp_register_style('digressit.ie8', get_template_directory_uri().'/ie8.css');
	

	
	if(is_page() || is_search()):		
		wp_enqueue_style('digressit.page');
	endif;


	if(is_search()):
		wp_enqueue_style('digressit.search');			
	endif;

	wp_enqueue_style('digressit.frontpage');
	wp_enqueue_style('digressit.author');
	wp_enqueue_style('digressit.comments');
	wp_enqueue_style('digressit.lightboxes');


	//var_dump(is_commentbrowser());
	
	if(is_single()):
	wp_enqueue_style('digressit.single');
	endif;
	
	wp_enqueue_style('digressit.sidebar');

	if(!$override_default_theme):
	wp_enqueue_style('digressit.theme');		
	endif;


	if($browser['name'] =='msie' && $browser['version'] == '7.0'):
	wp_enqueue_style('digressit.ie7');				
	endif;

	if($browser['name'] =='msie' && $browser['version'] == '8.0'):
	wp_enqueue_style('digressit.ie8');				
	endif;

	
}


function get_root_domain(){
	global $development_mode,$testing_mode, $production_mode;
	$development_mode = false;
	$testing_mode = false;
	$production_mode = false;

	//var_dump(DOMAIN_CURRENT_SITE);
	return ($_SERVER['HTTPS']) ? "https://". DOMAIN_CURRENT_SITE : "http://" . DOMAIN_CURRENT_SITE;
}


function functions_wp_print_scripts(){
	global $current_user, $post, $blog_id;
	wp_deregister_script('autosave');
	wp_enqueue_script('jquery');		
	
	$options = get_site_option('digressit');
	
	
	$url = parse_url(get_root_domain(). $_SERVER["REQUEST_URI"]);
	
	?>
	<script>	
		var siteurl = '<?php echo get_option("siteurl"); ?>';
		var baseurl = '<?php echo get_root_domain() ?>';
		var user_ID =  <?php echo $current_user->ID; ?>;
		<?php if(is_single()): ?>
		var post_ID = <?php echo $post->ID ?>;
		<?php endif; ?>
		var blog_ID = <?php echo $blog_id; ?>;
		var current_blog_id = <?php echo $blog_id; ?>;
		var request_uri = '<?php echo  $url['path']; ?>';
		<?php if(is_single()): ?>
			var is_single = true;
			var post_name = '<?php echo $post->post_name; ?>';
			var allow_general_comments = <?php echo !is_null($options["allow_general_comments"]) ? $options["allow_general_comments"] : 0; ?>;
			var allow_comments_search = <?php echo !is_null($options["allow_comments_search"]) ? $options["allow_comments_search"] : 0; ?>;
		
		
			var comment_count = <?php echo count($comment_array); ?>;
			var commment_text_signature = new Array(); 
			var commentbox_function = '<?php echo strlen($options['commentbox_parser']) ? $options['commentbox_parser'] : 'standard_digressit_commentbox_parser'; ?>';
		<?php else: ?>
			var is_single = false;
		<?php endif; ?>
		
	</script>	
	<?php

	wp_enqueue_script('digressit.functions',get_template_directory_uri().'/functions.js', 'jquery', false, true );	




	wp_enqueue_script('jquery.scrollto',	get_template_directory_uri().'/js/scrollto/jquery.scrollTo.js', 'jquery', false, true );		

	wp_enqueue_script('jquery.cookie',		get_template_directory_uri().'/js/cookie/jquery.cookie.js', 'jquery', false, true );		
	wp_enqueue_script('jquery.easing', 		get_template_directory_uri().'/js/easing/jquery.easing.js', 'jquery', false, true );		
	wp_enqueue_script('jquery.mousewheel',	get_template_directory_uri().'/js/mousewheel/jquery.mousewheel.js', 'jquery', false, true );		
	wp_enqueue_script('jquery.em',			get_template_directory_uri().'/js/em/jquery.em.js', 'jquery', false, true );		
	wp_enqueue_script('jquery.tinysort',			get_template_directory_uri().'/js/tinysort/jquery.tinysort.min.js', 'jquery', false, true );		

	wp_enqueue_script('jquery.ui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', 'jquery', false, true );	

}



/*
function browser() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

    // Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari.
    if (preg_match('/opera/', $userAgent)) {
        $name = 'opera';
    }
    elseif (preg_match('/webkit/', $userAgent)) {
        $name = 'safari';
    }
    elseif (preg_match('/msie/', $userAgent)) {
        $name = 'msie';
    }
    elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) {
        $name = 'mozilla';
    }
    else {
        $name = 'unrecognized';
    }

    // What version?
    if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) {
        $version = $matches[1];
    }
    else {
        $version = 'unknown';
    }

    // Running on what platform?
    if (preg_match('/linux/', $userAgent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/', $userAgent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/', $userAgent)) {
        $platform = 'windows';
    }
    else {
        $platform = 'unrecognized';
    }

    return array(
        'name'      => $name,
        'version'   => $version,
        'platform'  => $platform,
        'userAgent' => $userAgent
    );
}

*/



//if($browser['name'] =='msie' && $browser['version'] == '6.0'):
	//add_action('template_redirect', 'browser_not_supported' );
	//function browser_not_supported(){
	//	include(get_template_directory() . '/browser-not-supported.php');
	//	die();
	//}
//endif;




if( !is_user_logged_in()):
	//$_SESSION['captcha'] = md5((time() * rand()). "cAptcHaKey");
endif;

?>
