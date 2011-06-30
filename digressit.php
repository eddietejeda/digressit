<?php
/*	
Plugin Name: Digress.it
Plugin URI: http://digress.it
Description:  Digress.it allows readers to comment paragraph by paragraph in the margins of a text. You can use it to comment, gloss, workshop, debate and more!
Author: Eddie A Tejeda
Version: 3.2-beta
Author URI: http://eddietejeda.com
License: GPLv2 (http://creativecommons.org/licenses/GPL/2.0/)

Special thanks to:	
The developers of JQuery @ www.jquery.com
Joss Winn, Tony Hirst and Alex Bilbie @ University of Lincoln 
Jesse Wilbur, Ben Vershbow, Dan Visel and Bob Stein @ futureofthebook.org

Previous Versions:
Mark James, for the famfamfam iconset @ http://www.famfamfam.com/lab/icons/silk/
Matteo Bicocchi @ www.open-lab.com

*/

global $commentbrowser, $blog_id, $current_user, $current_user_comments, $development_mode, $testing_mode, $production_mode;
global $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function,$is_commentbrowser, $browser;

$browser = digressit_current_browser();
$digressit_options = get_option('digressit');

$is_commentbrowser= false;

$plugin_name = str_replace("/", "", str_replace(basename( __FILE__),"",plugin_basename(__FILE__))); 

load_plugin_textdomain('digressit', 'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/languages');

define("DIGRESSIT_VERSION", '3.2');
define("DIGRESSIT_COMMUNITY", 'digress.it');
define("DIGRESSIT_COMMUNITY_HOSTNAME", 'digress.it');
define("DIGRESSIT_REVISION", 229);
define("DIGRESSIT_DIR", WP_PLUGIN_DIR ."/". $plugin_name);
define("DIGRESSIT_CORE_DIR", DIGRESSIT_DIR . '/core');
define("DIGRESSIT_EXTENSIONS_DIR", DIGRESSIT_DIR . '/extensions');
define("DIGRESSIT_CORE_JS_DIR", DIGRESSIT_CORE_DIR . '/js');
define("DIGRESSIT_CORE_IMG_DIR", DIGRESSIT_CORE_DIR . '/images');
define("DIGRESSIT_CORE_CSS_DIR", DIGRESSIT_CORE_DIR . '/css');
define("DIGRESSIT_THEMES_DIR", DIGRESSIT_DIR . '/themes');
define("DIGRESSIT_URL", WP_PLUGIN_URL ."/". $plugin_name);
define("DIGRESSIT_CORE_URL", DIGRESSIT_URL . '/core');
define("DIGRESSIT_CORE_JS_URL", DIGRESSIT_CORE_URL . '/js');
define("DIGRESSIT_CORE_IMG_URL", DIGRESSIT_CORE_URL . '/images');
define("DIGRESSIT_CORE_CSS_URL", DIGRESSIT_CORE_URL . '/css');
define("DIGRESSIT_THEMES_URL", DIGRESSIT_URL . '/themes');

register_activation_hook(__FILE__,  'activate_digressit');
register_deactivation_hook(__FILE__, 'deactivate_digressit' );


$plugin_dir = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/';
$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/themes';


register_theme_directory( $plugin_theme_link );


register_digressit_content_function('standard_digressit_content_parser');
register_digressit_content_function('discrete_digressit_content_parser');
register_digressit_comments_function('standard_digressit_comment_parser');


register_digressit_commentbox_js('grouping_digressit_commentbox_parser');
register_digressit_commentbox_js('nogrouping_digressit_commentbox_parser');	


add_action('init', 'digressit_init');
add_action('after_setup_theme', 'digressit_setup' );




//load core files
if ($handle = opendir(DIGRESSIT_CORE_DIR)) {
	while (false !== ($file = readdir($handle))) {
		if (!@is_dir($file) && strstr($file, '-functions.php')) {
			require_once(DIGRESSIT_CORE_DIR . '/' . $file);
		}
	}
	closedir($handle);
}


//load extensions
if ($handle = opendir(DIGRESSIT_EXTENSIONS_DIR)) {
	while (false !== ($file = readdir($handle))) {
		if (@is_dir(DIGRESSIT_EXTENSIONS_DIR . "/". $file) && file_exists(DIGRESSIT_EXTENSIONS_DIR . '/' . $file  . '/' . $file.".php")) {
			require_once(DIGRESSIT_EXTENSIONS_DIR . '/' . $file  . '/' . $file.".php");
		}
	}
	closedir($handle);
}

if(isset($_REQUEST['digressit-embed'])){
	include_once('core/embed-functions.php');	
	$digressit_embed = new Digress_It_Embed();
}




/**
 * Loads default settings into 	add_option('digressit', $digressit_options), initializes theme 
 */
function activate_digressit(){
	global $wpdb;
	$digressit_options = get_option('digressit');

	//PRE-3.0
	$commentpress_upgraded_to_digress_it = get_option('commentpress_upgraded_to_digress_it');
	$digressit_community_hostname = get_option('digressit_community_hostname');
	$digressit_client_password = get_option('digressit_client_password');
	$digressit_installation_key = get_option('digressit_installation_key');
	
	$plugin_name = str_replace("/", "", str_replace(basename( __FILE__),"",plugin_basename(__FILE__))); 
	$plugin_url = WP_PLUGIN_URL .'/' . $plugin_name . '/';		
	$plugin_file = $plugin_url. plugin_basename(__FILE__); 


	$digressit_server = 'http://'. DIGRESSIT_COMMUNITY_HOSTNAME . '/';
	$is_multiuser = digressit_is_mu_or_network_mode();			


	$theme_url = $plugin_url. 'themes/'; 
	$js_path = $plugin_url. 'js/'; 
	$jquery_path = $js_path . 'jquery/'; 
	$jquery_extensions_path =  $jquery_path. 'external/'; 
	$jquery_theme_path = $jquery_path . 'themes/'; 
	$jquery_elements_path = $jquery_path . 'elements/'; 
	$jquery_css_path = $jquery_path . 'css/'; 

	$style_path = $plugin_url . 'style/'; 
	$image_path = $plugin_url . 'themes/images/'; 
	$punctuations = null;


	$url = $_SERVER["SERVER_NAME"] ;
	preg_match("/^(http:\/\/)?([^\/]+)/i" , $url, $found);
	preg_match("/[^\.\/]+\.[^\.\/]+$/" , $found[2], $found);

	$hostname = $found[0];
	$default_skin = 'skin1';
	$default_stylesheet  = 'default';

	$installation_key  = null;
	$installation_key = strlen($digressit_options['installation_key']) == 32 ? $digressit_options['installation_key'] : null;

	$digressit_options['debug_mode'] = 0;
	$digressit_options['allow_text_selection'] = 0;
	$digressit_options['default_skin'] = $default_skin;
	$digressit_options['stylesheet'] = $default_stylesheet;
	$digressit_options['default_left_position'] = '400px';
	$digressit_options['default_top_position'] = '175px';
	$digressit_options['allow_users_to_minimize'] = 0;
	$digressit_options['allow_users_to_resize'] = 0;
	$digressit_options['allow_users_to_drag'] = 1;
	$digressit_options['highlight_color'] = '#FFFC00';
	$digressit_options['parse_list_items'] = 0;
	$digressit_options['enable_chrome_frame']	= 1;
	$digressit_options['front_page_post_type'] = 'post';
	$digressit_options['front_page_numberposts'] = 10;
	$digressit_options['frontpage_sidebar'] = 0;
	$digressit_options['front_page_content'] = '';
	$digressit_options['front_page_order'] = 'ASC';
	$digressit_options['front_page_order_by'] = 'date';
	$digressit_options['allow_general_comments'] = 1;
	$digressit_options['allow_comments_search'] = 0;
	$digressit_options['enable_sidebar'] = 1;
	$digressit_options['enable_instant_content_search'] = 'false';
	$digressit_options['enable_instant_comment_search'] = 'false';
	$digressit_options['show_pages_in_menu'] = 0;
	$digressit_options['table_of_contents_label'] = 'Table of Contents';
	$digressit_options['comments_by_section_label'] = 'Comments by Section';
	$digressit_options['comments_by_users_label'] = 'Comments by Users';
	$digressit_options['general_comments_label'] = 'General Comments';
	$digressit_options['sidebar_position'] = 'sidebar-widget-position-left';
	$digressit_options['auto_hide_sidebar'] = 'sidebar-widget-auto-hide';
	$digressit_options['show_comment_count_in_sidebar'] = 1;
	$digressit_options['revision'] = DIGRESSIT_REVISION;
	$digressit_options['version'] = DIGRESSIT_VERSION;
	$digressit_options['custom_style_sheet'] = '';
	$digressit_options['custom_header_image'] = '';
	$digressit_options['use_cdn'] = 0;
	$digressit_options['cdn'] = 'http://c0006125.cdn2.cloudfiles.rackspacecloud.com';
	$digressit_options['frontpage_list_style'] = 'list-style-decimal';
	$digressit_options['commentpress_upgraded_to_digress_it'] = $digressit_installation_key;
	$digressit_options['digressit_community_hostname'] = $digressit_community_hostname;
	$digressit_options['digressit_client_password'] = $digressit_client_password;
	$digressit_options['digressit_installation_key'] = $digressit_installation_key;
	$digressit_options['content_parser'] = 'standard_digressit_content_parser';
	$digressit_options['comments_parser'] = 'standard_digressit_comment_parser';
	$digressit_options['commentbox_parser'] = 'grouping_digressit_commentbox_parser';

	$digressit_options['enable_dropdown_menu'] = 0;
	$digressit_options['enable_citation_button'] = 0;
	$digressit_options['keyboard_navigation'] = 0;
	$digressit_options['digressit_enabled_for_posts'] = 1;
	$digressit_options['digressit_enabled_for_pages'] = 0;
	
	
	
	
	if(get_option('digressit')){
		update_option('digressit', $digressit_options);		
	}
	else{
		add_option('digressit', $digressit_options);	
	}
		
	$digressit_options = get_option('digressit');
	
	update_option('thread_comments_depth', 2); //we default to just 2 threads.

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

	$themes_dir = WP_CONTENT_DIR . '/themes/';
	$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/themes/';
		
	$theme_link = $themes_dir . $plugin_name;
	
	if(is_link($theme_link)){
		unlink($theme_link);
	}
	/* Since: 2.9.0 */
	if(!function_exists( 'register_theme_directory')){
		if(is_writable( $themes_dir)){
			//echo "is_writable";
			$theme_link = $themes_dir . $plugin_name;
			//CREATE THE THEME DIRECTORY
			if(is_link($theme_link)){
				//i think we're good
				//die( "already link");
			}
			elseif(!file_exists($theme_link)){
				if(symlink($plugin_theme_link,$theme_link)){
					//we're good
					//update_option($digressit_options['theme_mode'], 'stylesheet');
					//die( "Created link");
				}
				else{
					//die( "There was an error creating the symlink of <b>$plugin_theme_link</b> in <b>$theme_link</b>. If the server doesn't have write permission try creating it manually");
				}
			}
			else{
				//die( "unknown error");
				//probably a windows person
				//die( "There was a error creating the symlink of <b>$plugin_theme_link</b> in <b>$theme_link</b>. Maybe a theme named DigressIt already exists?");					
			}
		
		
		}
		else{
			die(__('No write permission on: ').$themes_dir.__('. Please give the server write permission on this directory'));
		}
	}
	switch_theme('digressit-default', 'digressit-default');	
}


/**
 * Switches back to default theme
 */
function deactivate_digressit(){
	$themes_dir = WP_CONTENT_DIR . '/themes/';
	

	if ($handle = opendir($themes_dir)) {
		while (false !== ($file = readdir($handle))) {
			if (@is_dir($themes_dir.$file)) {

				switch($file){
					//3.2
					case 'twentyeleven':
						switch_theme('twentyeleven', 'twentyeleven');	
					break;
					//3.0
					case 'twentyten':
						switch_theme('twentyten', 'twentyten');	
					break;
					//pre 3.0
					case 'default':
						switch_theme('default', 'default');						
					break;
				}

			}
		}
		closedir($handle);
	}
}




/**
 * Basic theme setup function
 * *
 * @since 3.0.0
 */
function digressit_setup(){
	global $wpdb;
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu()
	add_theme_support( 'nav-menus' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	
	//we do this for backwarsd compatability. We are moving towards commentdata
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
		  'Main Page' => __('This replaces the menu that appears on the main page')  ,
		  'Top Menu' => __('This replaces the menu runs across the top of the page')  ,
		  'Sidebar' => __('This replaces the menu that loads in the sidebar')  
		)
	);
}


/**
 * Check to see if this revision requires an reset of options. 
 * @todo This function should be a bit smarter
 *
 */
function digressit_init(){

	global $digressit_options;

	/* upgrade to Digress.it 3.2 */
	if($digressit_options['version'] != DIGRESSIT_VERSION){		
		$digressit_options['enable_dropdown_menu'] = 0;
		$digressit_options['enable_citation_button'] = 0;
		$digressit_options['keyboard_navigation'] = 0;
		$digressit_options['digressit_enabled_for_pages'] = 0;
		$digressit_options['digressit_enabled_for_posts'] = 1;
		update_option('digressit', $digressit_options);
	}
}

/**
 * This function is to future-proof how media is handled. If we are using CDN it bybasses local media assets
 */
function get_digressit_media_uri($filepath){
	global $digressit_options;
	
	if((int)$digressit_options['use_cdn']){
		return $digressit_options['cdn'] ."/". basename($filepath);
	}
	else{
		return DIGRESSIT_CORE_URL ."/".$filepath;
	}
}


/**
 * Returns the system path where Digress.it is installed
 */
function get_digressit_theme_path(){
	return DIGRESSIT_THEMES_DIR."/".basename(get_template_directory());;
}

/**
 * Returns the URL path where Digress.it is installed
 */
function get_digressit_theme_uri(){
	return DIGRESSIT_THEMES_DIR . get_current_theme();
}

/**
 *
 */
function register_digressit_content_function($function_name){
	global $digressit_content_function;
	$digressit_content_function[$function_name] = $function_name;
}

/**
 *
 */
function register_digressit_comments_function($function_name){
	global $digressit_comments_function;
	$digressit_comments_function[$function_name] = $function_name;
}

/**
 *
 */
function register_digressit_commentbox_js($function_name){
	global $digressit_commentbox_function;
	$digressit_commentbox_function[$function_name] = $function_name;
}


/**
 *
 */
function digressit_print_input_text($name, $value, $attrs =null){
	echo "<input $attrs style='width: 50%' type='text' name='$name' value='$value'>";
}


/**
 *
 */
function digressit_print_dropdown($name, $digressit_options = array(), $selected, $id=''){
	if($id){
		$id = " id='$id' ";
	}
	
	echo "<select $id name='$name'>";
	foreach($digressit_options as $name => $value) {
		$selected_html = ($value == $selected) ? " selected='selected' " : '';
		echo "<option $selected_html value='$value'>$name</option>";
	}
	echo "</select>";
}


/**
 * Checks to see if this is Wordpress MU (pre WP 3.0) or WP 3.0+
 */
function digressit_is_mu_or_network_mode(){
	$is_multiuser = false;
	
	if(function_exists('wpmu_create_blog') || (function_exists('is_multisite') && is_multisite()) ){
		$is_multiuser = true;
	}
	return 	$is_multiuser;
}

/**
 * Checks to see if this page is the table of contents
 */
function digressit_is_frontpage(){
	global $is_frontpage, $is_mainpage, $blog_id;
	
	if(!function_exists('is_multisite')){
		return false;
	}
	
	if(is_multisite() && file_exists(get_template_directory(). '/frontpage.php')){
		if(is_home() || is_front_page()){
			if($blog_id == 1){		
				return true;
			}
		}
	}
	return false;
}


/**
 * Checks to see if this page is the table of contents
 */
function digressit_is_mainpage(){
	global $is_frontpage, $is_mainpage, $blog_id;
	
	if(is_multisite() && file_exists(get_template_directory(). '/frontpage.php')){
		if(is_home() || is_front_page()){
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


/**
 * A somewhat crude way to detect user browser
 */
function digressit_current_browser() {
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

/**
 * Checks to see if we are in the comment-browser section
 */
function digressit_is_commentbrowser(){
	global $is_commentbrowser;
	return $is_commentbrowser;
}




?>