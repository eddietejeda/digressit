<?php 
global $commentbrowser, $blog_id, $current_user, $current_user_comments, $development_mode, $testing_mode, $production_mode;
global $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function,$is_commentbrowser;

global $browser;


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


//if(file_exists(TEMPLATEPATH . '/extensions.php')){
//	require_once(TEMPLATEPATH . '/extensions.php');	
//}


add_action('init', 'digressit_load');

add_action('admin_head-post.php', 'add_comment_change_notice');

add_action('wp', 'apply_content_parser');


function apply_content_parser(){
	if(is_single()){
		add_filter('the_content', 'digressit_parser', 10000);	
	}
}


function digressit_load(){
	//after extensions are loaded
	add_action('wp_print_scripts',  'functions_wp_print_scripts' );
	add_action('wp_print_styles',  'functions_wp_print_styles') ; 		
	
	
	$options = get_option('digressit');

	if(esc_url($options['custom_header_image'], array('http', 'https'))){
		add_action('add_header_image', 'custom_digressit_logo');
	}
}



function digressit_setup(){
	global $wpdb;
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu()
	add_theme_support( 'nav-menus' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	
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



function lightbox_custom_login(){
	//header('Location: /#login');
	//die();
}



function regexp_digressit_content_parser($html){
	$matches = array();
	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);

	$options = get_option('digressit');
	if($options['parse_list_items'] == 1){
		$html = preg_replace('/<(\/?ul|ol)>/', '', $html);
		$html = preg_replace('/<li>/', '<p>&bull;   ', $html);
	}
	$html = html_entity_decode(force_balance_tags($html));


	@preg_match_all('#<('.$tags.')>(.*?)</('.$tags.')>#si',$html,$matches_array);
	$matches = $matches_array[0];

		
	return  $matches;
}


function discrete_digressit_content_parser($content){
	global $wpdb, $image_path, $post;

	$matches = array();
	$paragraph_blocks = explode('[break]', $content);


	$blocks = null;
	$text_signatures = null;
	$permalink = get_permalink($post->ID);



	$defaults = array('post_id' => $post->ID);
	$total_comments = get_comments($defaults);
	$total_count = count($total_comments);

	foreach($paragraph_blocks as $key=>$paragraph)
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


		$block_content = "<div id='textblock-$number' class='textblock'>
			<span class='paragraphnumber'><a href='$permalink#$number'>$number</a></span>
			<span  title='There $numbertext for this paragraph' class='commenticonbox'><small class='commentcount commentcount".$digit_count."'>".$comment_count."</small></span>
			<span class='paragraphtext'>".force_balance_tags($paragraph)."</span>
		</div>" .  $morelink;
		
		$blocks[$paranumber] = $block_content;
    }

	
	return $blocks;
	
}

function standard_digressit_content_parser($html, $tags = 'div|table|object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8'){
	global $post;
	$matches = array();

	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);

	$options = get_option('digressit');
	
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
	
	
	$html = wpautop(force_balance_tags($html));

	//var_dump($html);
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
	$options = get_option('digressit');

	if( isset($options['comments_function']) || function_exists($options['comments_function']) ){
		return $options['comments_function'];
	}
	else{
		return 'standard_digressit_comment_parser';
	}	
}

function get_digressit_content_parser_function(){
	$options = get_option('digressit');


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












function functions_wp_print_styles(){
	global $current_user, $override_default_theme, $browser;
	
	$options = get_option('digressit');
	
	wp_register_style('digressit.frontpage', get_digressit_media_uri().'/frontpage.css'); 
	wp_register_style('digressit.author', get_digressit_media_uri().'/author.css'); 
	wp_register_style('digressit.comments', get_digressit_media_uri().'/comments.css'); 
	wp_register_style('digressit.lightboxes', get_digressit_media_uri().'/lightboxes.css'); 
	wp_register_style('digressit.sidebar', get_digressit_media_uri().'/sidebar.css'); 
	wp_register_style('digressit.page', get_digressit_media_uri().'/page.css'); 
	wp_register_style('digressit.search', get_digressit_media_uri().'/search.css'); 
	wp_register_style('digressit.single', get_digressit_media_uri().'/single.css');
	wp_register_style('digressit.theme', get_digressit_media_uri().'/theme.css');

	if(strlen($options['custom_style_sheet']) > 10 && esc_url($options['custom_style_sheet'], array('http', 'https'))){
		$override_default_theme = true;
		wp_register_style('digressit.custom', $options['custom_style_sheet']);
	}

	//hacks for IE
	wp_register_style('digressit.ie7', get_digressit_media_uri().'/ie7.css');
	wp_register_style('digressit.ie8', get_digressit_media_uri().'/ie8.css');
	

	
	if(is_page() || is_search()){	
		wp_enqueue_style('digressit.page');
	}


	if(is_search()){
		wp_enqueue_style('digressit.search');			
	}

	if(is_frontpage()){
		wp_enqueue_style('digressit.frontpage');
	}

	
	if(is_author()){
		wp_enqueue_style('digressit.author');
	}
	
	if(is_single() || is_commentbrowser() || is_author()){
		wp_enqueue_style('digressit.comments');
	}

	if(is_single() || is_page() || is_archive() || is_author() || is_search()){
		wp_enqueue_style('digressit.lightboxes');
	}

	
	if(is_single()){
		wp_enqueue_style('digressit.single');
	}
	
	if(is_single() || is_page() || is_archive() || is_author() || is_search()){	
		wp_enqueue_style('digressit.sidebar');
	}

	if($override_default_theme){
		wp_enqueue_style('digressit.custom');
	}else{
		wp_enqueue_style('digressit.theme');		
	}


	if($browser['name'] =='msie' && $browser['version'] == '7.0'){
		wp_enqueue_style('digressit.ie7');				
	}

	if($browser['name'] =='msie' && $browser['version'] == '8.0'){
		wp_enqueue_style('digressit.ie8');				
	}


	//var_dump(get_option('sidebars_widgets'));
	
}


function custom_digressit_logo(){
$options = get_option('digressit');

$css_name = preg_replace("/[^a-zA-Z]/", "", get_bloginfo('name'));
?>
<style>

#<?php echo $css_name; ?>-logo{
	background: url(<?php echo $options['custom_header_image']; ?>) no-repeat;
	height: 100px;
}
</style>
<a href="<?php bloginfo('url'); ?>" ><div id="<?php echo $css_name; ?>-logo"></div></a>	
<?php
}

function get_root_domain(){
	global $development_mode,$testing_mode, $production_mode;
	$development_mode = false;
	$testing_mode = false;
	$production_mode = false;

	return "http://" . DOMAIN_CURRENT_SITE; //isset($_SERVER['HTTPS']) ? "https://". DOMAIN_CURRENT_SITE : "http://" . DOMAIN_CURRENT_SITE;
}


function functions_wp_print_scripts(){
	global $current_user, $post, $blog_id;
	wp_deregister_script('autosave');
	wp_enqueue_script('jquery');		
	

	$options = get_option('digressit');


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
			var commentbox_function = '<?php echo strlen($options['commentbox_parser']) ? $options['commentbox_parser'] : 'grouping_digressit_commentbox_parser'; ?>';
		<?php else: ?>
			var is_single = false;
		<?php endif; ?>
	
	</script>	
	<?php
	

	
	if(!is_admin()){
		wp_enqueue_script('digressit.functions',get_digressit_media_uri().'/functions.js', 'jquery', false, true );	

		wp_enqueue_script('jquery.easing', 		get_template_directory_uri().'/js/easing/jquery.easing.js', 'jquery', false, true );		
		wp_enqueue_script('jquery.scrollto',	get_template_directory_uri().'/js/scrollto/jquery.scrollTo.js', 'jquery', false, true );		

		wp_enqueue_script('jquery.cookie',		get_template_directory_uri().'/js/cookie/jquery.cookie.js', 'jquery', false, true );		
		wp_enqueue_script('jquery.mousewheel',	get_template_directory_uri().'/js/mousewheel/jquery.mousewheel.js', 'jquery', false, true );		
		wp_enqueue_script('jquery.em',			get_template_directory_uri().'/js/em/jquery.em.js', 'jquery', false, true );		
		//wp_enqueue_script('jquery.tinysort',			get_template_directory_uri().'/js/tinysort/jquery.tinysort.min.js', 'jquery', false, true );		

		wp_enqueue_script('jquery.ui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', 'jquery', false, true );	
	}
}

?>
