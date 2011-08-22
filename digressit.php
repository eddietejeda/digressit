<?php
/*	
Plugin Name: Digress.it
Plugin URI: http://digress.it
Description:  Digress.it allows readers to comment paragraph by paragraph in the margins of a text. You can use it to comment, gloss, workshop, debate and more!
Author: Eddie A Tejeda
Version: 3.2
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
global $using_mainpage_nav_walker;

$digressit_options = $digressit = $options = get_option('digressit');
$is_commentbrowser= false;
$plugin_name = str_replace("/", "", str_replace(basename( __FILE__),"",plugin_basename(__FILE__))); 
$plugin_dir = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/';
$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/themes';


load_plugin_textdomain('digressit', 'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/languages');

/* global variables */
define("DIGRESSIT_VERSION", '3.2');
define("DIGRESSIT_COMMUNITY", 'digress.it');
define("DIGRESSIT_COMMUNITY_HOSTNAME", 'digress.it');
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


//load core files
digressit_auto_load_dir(DIGRESSIT_CORE_DIR);
digressit_auto_load_dir(DIGRESSIT_EXTENSIONS_DIR);

$browser = digressit_current_browser();


/* activation and deactivation */
register_activation_hook(__FILE__,  'activate_digressit');
register_deactivation_hook(__FILE__, 'deactivate_digressit' );


register_theme_directory( $plugin_theme_link );


register_digressit_content_function('standard_digressit_content_parser');
register_digressit_content_function('discrete_digressit_content_parser');

register_digressit_comments_function('standard_digressit_comment_parser');

register_digressit_commentbox_js('grouping_digressit_commentbox_parser');
register_digressit_commentbox_js('nogrouping_digressit_commentbox_parser');	


add_action('init', 'digressit_init');
add_action('init', 'create_digressit_post_type' );
add_action('after_setup_theme', 'digressit_setup' );


/* admin functions */
add_action('admin_head-post.php', 'digressit_add_comment_change_notice');
add_action('admin_menu', 'digressit_add_admin_menu');
add_action('admin_init', 'digressit_theme_options_page_form');

/* ajax init functions */
add_filter('query_vars', 'ajax_query_vars', 0);
add_action('generate_rewrite_rules', 'ajax_add_rewrite_rules', 0 );
add_action('template_redirect', 'ajax_template' );


/* comments functions */
//add_action('init', 'commentbrowser_flush_rewrite_rules' );
add_filter('query_vars', 'commentbrowser_query_vars' );
add_action('generate_rewrite_rules', 'commentbrowser_add_rewrite_rules' );
add_action('template_redirect', 'commentbrowser_template_redirect' );
add_action('public_ajax_function', 'add_comment_ajax');
add_action('widgets_init', create_function('', 'return register_widget("CommentBrowserLinks");'));

/* these files can be found on core/comments.php */
add_action('add_commentbrowser', 'commentbrowser_comments_by_section');
add_action('add_commentbrowser', 'commentbrowser_comments_by_user'); //DEPRECATED
add_action('add_commentbrowser', 'commentbrowser_comments_by_contributor');
add_action('add_commentbrowser', 'commentbrowser_general_comments');


/* these theme files global and are always included in sub-themes */
add_action('wp_print_scripts',  'digressit_core_print_scripts', 1);
add_action('wp_print_styles',  'digressit_core_print_styles', 1) ; 		
add_action('wp_head',  'digressit_wp_head') ; 		

if(esc_url($digressit_options['custom_header_image'], array('http', 'https'))){
	add_action('add_header_image', 'custom_digressit_logo');
}
add_filter('the_content', 'digressit_parser', 10000);	
add_action('wp', 'frontpage_load');


/*ajax function */
add_action('public_ajax_function', 'live_content_search_ajax');	
add_action('public_ajax_function', 'live_comment_search_ajax');	


add_action('add_dynamic_widget', 'digressit_single_sidebar_widgets');
add_action('add_dynamic_widget', 'digressit_page_sidebar_widgets');


/* lightbox functions */
//add_action('add_lightbox', 'lightbox_login');
//add_action('add_lightbox', 'lightbox_generic_response');




/* mainpage functions */
add_action('wp', 'digressit_mainpage_load');


/* widgets functions */
add_action('widgets_init', create_function('', 'return register_widget("ListPostsWithCommentCount");'));




//load extensions
function digressit_auto_load_dir($path){
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if (!@is_dir($file) && strstr($file, '.php')) {
//				echo $path . '/' . $file;
				require_once($path . '/' . $file);
			}
		}
		closedir($handle);
	}
}

?>
