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

$browser = current_browser();
$is_commentbrowser= false;

//get_currentuserinfo();

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
$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/themes/';


register_theme_directory( $plugin_theme_link );


register_digressit_content_function('standard_digressit_content_parser');
register_digressit_content_function('discrete_digressit_content_parser');
//register_digressit_content_function('regexp_digressit_content_parser');


register_digressit_comments_function('standard_digressit_comment_parser');


register_digressit_commentbox_js('grouping_digressit_commentbox_parser');
register_digressit_commentbox_js('nogrouping_digressit_commentbox_parser');	



add_action('admin_menu', 'digressit_add_admin_menu');
//add_action('init', 'digressit_localization' );
add_action('init', 'digressit_init');




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

if($_REQUEST['digressit-embed']){
	include_once('core/embed-functions.php');	
	$digressit_embed = new Digress_It_Embed();
}



/**
 * Check to see if this revision requires an reset of options. 
 * @todo This function should be a bit smarter
 *
 */
function digressit_init(){

	$digressit_options = get_option('digressit');

	/* upgrade to Digress.it 3.2 */
	if(is_null($digressit_options['enable_citation_button'])){		
		$digressit_options['enable_dropdown_menu'] = 1;
		$digressit_options['enable_citation_button'] = 0;
		$digressit_options['keyboard_navigation'] = 0;
		update_option('digressit', $digressit_options);
	}

	/*	
	 @deprecated This is a very bad way to handle upgrades
	if(!isset($digressit_options['revision']) || (int)$digressit_options['revision'] < 198 ){
		activate_digressit();
		
		echo "<meta http-equiv=\"refresh\" content=\"1\" >";
	}	
	
	*/
}



/**
 *
 */
function permalink_required_notice(){
		echo "<div id='permalink-required-notice' class='updated fade'><p>".__("Warning: Digress.it requires permalinks to be enabled. Please go to <a href='").get_bloginfo('url')."/wp-admin/options-permalink.php'>".__('Permalink Settings</a> and make sure that <b>Default</b> is not selected')."</p></div>";	
}





/**
 * This function is to future-proof how media is handled. If we are using CDN it bybasses local media assets
 */
function get_digressit_media_uri($filepath){
	$digressit_options = get_option('digressit');
	
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
function print_input_text($name, $value, $attrs =null){
	echo "<input $attrs style='width: 50%' type='text' name='$name' value='$value'>";
}


/**
 *
 */
function print_dropdown($name, $digressit_options = array(), $selected, $id=''){
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
function is_mu_or_network_mode(){

	$is_multiuser = false;

	if(function_exists('wpmu_create_blog') || (function_exists('is_multisite') && is_multisite()) ){
		$is_multiuser = true;
	}
	
	return 	$is_multiuser;
}

/**
 * Checks to see if this page is the table of contents
 */
function is_frontpage(){
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
function is_mainpage(){
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

/**
 * Checks to see if we are in the comment-browser section
 */
function is_commentbrowser(){
	global $is_commentbrowser;
	return $is_commentbrowser;
}




?>