<?php 
global $commentbrowser, $blog_id, $current_user, $current_user_comments, $development_mode, $testing_mode, $production_mode;
global $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function,$is_commentbrowser;
global $browser, $post_paragraph_count;



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
		$digressit_options['digressit_enabled_for_digressit_type'] = 0;


		update_option('digressit', $digressit_options);
	}



}

function create_digressit_post_type() {
	global $digressit_options;
	if((int)$digressit_options['digressit_enabled_for_digressit_type']){
		register_post_type( 'digressit_type',
			array(
				'labels' => array(
				
				    'name' => _x('Digress.it', 'post type general name'),
				    'singular_name' => _x('Digress.it', 'post type singular name'),
				    'menu_name' => 'Digress.it'
				),
				'public' => true,
				'has_archive' => true,
				'rewrite' => array('slug' => 'digressit')
			)
		);
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



/**
 * Simple wrapper function that prints calls action 'secondary_menu'
 */
function digressit_get_secondary_menu(){
	do_action('secondary_menu');
}


/**
 * Simple wrapper that gets widgets by name
 */
function digressit_get_widgets($widget_name){
	return dynamic_sidebar($widget_name);
}


/**
 * Simple wrapper that gets widgets by the section they are on
 */
function digressit_get_dynamic_widgets(){
	do_action('add_dynamic_widget');
}


/**
 * 
 */
function digressit_get_single_default_widgets(){
	
	$digressit_options = get_option('digressit');
	if ( !is_active_sidebar('single-sidebar') && (int)$digressit_options['enable_sidebar'] !== 0) : 
		
		?>
		
		<div class="sidebar-widgets default-list-post">
		<div id="dynamic-sidebar" class="sidebar <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
		<?php
	
		ListPostsWithCommentCount::widget($args =array(), array('title' => 'Posts', 
															'auto_hide' => true, 
															'position' => 'left', 
															'order_by' => 'ID', 
															'order_type' => 'ASC', 
															'categorize' => false, 
															'categories' => null, 
															'show_category_titles' => false));
		?>
		</div>
		</div>
		<?php
	else:
		do_action('add_dynamic_widget');
	endif;
}


/**
 * 
 */
function digressit_get_stylized_content_header(){
	if(has_action('stylized_content_header')){
		do_action('stylized_content_header');
	}
}



/**
 * 
 */
function digressit_get_stylized_title(){
	global $post;
	echo '<div id="the_title"  class="the_title">';	
	if(has_action('stylized_title')){
		do_action('stylized_title');
	}
	else{			
		if(is_single() || is_page() || is_search()){
		
			echo '<h2><a href="'.get_permalink().'">'.get_the_title().'</a><div class="edit-this"><a href="'.get_edit_post_link($post->ID).'">edit</a></div></h2>';

		}			
	}
	echo '</div>';
}



/**
 * 
 */
function digressit_wp_head(){
?>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<?php

}



/**
 * Hooks into the {@the_content} of each posts and breaks the text into an array.
 * *
 * @since 3.0.0
 *
 * @param string $html Required. Comment amount in post if > 0, else total comments blog wide.
 * @param string $tags Optional
 * @return array An array of each text block with the proper html tags for comment count and extra tags for adding javascript hooks
 */
function standard_digressit_content_parser($html, $tags = 'div|table|object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8', $return_paragraphs = false){
	global $post;
	$matches = array();
	$html = strip_selected_tags($html, '<hr>');
	
	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|iframe|object|param|embed|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|iframe|object|param|embed|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = str_replace("</iframe>", "&nbsp;</iframe>", $html);

	$digressit_options = get_option('digressit');
	
	$blocks = array();
	$text_signatures = null;
	$permalink = get_permalink($post->ID);

	$defaults = array('post_id' => $post->ID);
	$total_comments = get_comments($defaults);
	$total_count = count($total_comments);
		
	if($digressit_options['parse_list_items'] == 1){
		$html = preg_replace('/<(\/?ul|ol)>/', '', $html);
		$html = preg_replace('/<li>/', '<p>*   ', $html);
	}
	
	$html = wpautop(force_balance_tags($html));	
	$html = str_replace('&nbsp', '', $html);
	$html = str_replace('&copy;', '(c)', $html);
	$html = preg_replace("/&#?[a-z0-9]{2,8};/i","",$html);
	
	libxml_use_internal_errors(true);
	if($result = @simplexml_load_string(trim('<content>'.$html.'</content>'))){
		$xml = $result->xpath('/content/'. $tags);
		foreach($xml as $match){
			$matches[] = $match->asXML();
		}
	}
	else
	{
		if(current_user_can('edit_posts')){		
			$matches[] = "There was a problem parsing your content. Please make sure that every HTML tag is properly nested and closed. 
			To validate your text, and to try and repair it, use the <a href='https://wordpress.org/extend/plugins/tidy-up/'>Tidy Up</a> plugin for WordPress.";
			if (!$result) {
			    $errors = libxml_get_errors();
			    foreach ($errors as $error) {
			        $error_messages .= display_xml_error($error, $xml). "<br>";
			    }
			    libxml_clear_errors();
			}				
			$matches[] = $error_messages;		
		}
		else{
			$matches[] = "Sorry! There was a problem loading the contents of this post. Please notify the site administrator.";
		}	
	}

	if ($return_paragraphs){
		return $matches;
	}

	foreach($matches as $key=>$paragraph){
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

		$morelink = null;
		if($number == 1){
			//$morelink = '<span class="morelink"></span>';
		}
		else{
			$morelink = null;
		}

		$matches = null;

		preg_match_all('/class=\"([^"]+)\"/is', $paragraph, $matches);		
		if(count($matches)){
			foreach($matches[1] as $match){
				if(strstr($match, 'wp-image')){		
					$paragraph = str_replace($match, 'lightbox lightbox-images '.$match, $paragraph);
				}
				$paragraph = str_replace(" class=\"$matches\" ", " class=\"lightbox lighbox-images $classes\" ", $paragraph);
			}
		}
		$block_content = "<div id='textblock-$number' class='textblock'>
			<span class='paragraphnumber'><a href='$permalink#$number'>$number</a></span>";
			
		if($digressit_options['enable_citation_button'] == 1){
		$block_content .= "<span class='paragraphembed'>
				<a href='#' rel='$number'>&ldquo;</a>
				<span class='embedcode' id='embedcode-$number'>
					<a href='#' class='closeme'>x</a>
					<b>Cite</b> <input type='text' value='".$post->guid."&digressit-embed=$number&format=html'><br>
					<b>Embed</b><br>
					<textarea><blockquote cite='$permalink#$number'>".force_balance_tags($paragraph)."</blockquote></textarea>
					<span class='text-copied'>Text copied</span>
				</span>
			</span>";
		}
			
				
		$block_content .=  "<span  title='There $numbertext for this paragraph' class='commenticonbox'><small class='commentcount commentcount".$digit_count."'>".$comment_count."</small></span>
			<span class='paragraphtext'>".force_balance_tags($paragraph)."</span>
		</div>" .  $morelink;
		
		$blocks[$paranumber] = $block_content;
    }

	global $post_paragraph_count;
	$post_paragraph_count = count($blocks);
	return $blocks;

}


/**
 * 
 */
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

	foreach($paragraph_blocks as $key=>$paragraph){
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


/**
 * Like strip_tags() but inverse; the strip_tags tags will be stripped, not kept.
 * strip_tags: string with tags to strip, ex: "<a><p><quote>" etc.
 * strip_content flag: TRUE will also strip everything between open and closed tag
 */
function strip_selected_tags($str, $tags = "", $stripContent = false){
    preg_match_all("/<([^>]+)>/i",$tags,$allTags,PREG_PATTERN_ORDER);
    foreach ($allTags[1] as $tag){
        if ($stripContent) {
            $str = preg_replace("/<".$tag."[^>]*>.*<\/".$tag.">/iU","",$str);
        }
        $str = preg_replace("/<\/?".$tag."[^>]*>/iU","",$str);
    }
    return $str;
}



/**
 * Returns true if $string is valid UTF-8 and false otherwise.
 */
function is_utf8($string) {
  
   // From http://w3.org/International/questions/qa-forms-utf-8.html
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);
}


/**
 * 
 */
function display_xml_error($error, $xml)
{
    $return  = $xml[$error->line - 1] . "\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line";

    if ($error->file) {
        $return .= "\n  File: $error->file";
    }
	$return  .= "<br>";

    return "$return\n\n\n\n";
}



/**
 * 
 */
function digressit_parser($content){
	global $digressit_options;
		
	if(is_single() && (int)$digressit_options['digressit_enabled_for_posts'] || is_page() && (int)$digressit_options['digressit_enabled_for_pages']){
		return implode("\n", (array)digressit_paragraphs($content));
	}
	else{
		return $content;
	}
	
}

/**
 * 
 */
function digressit_paragraphs($content){	
	return call_user_func(get_digressit_content_parser_function(), $content); 
}

/**
 * 
 */
function the_paragraph($number){
	global $post;
	

	echo get_the_paragraph($number);
	
}

/**
 * 
 */
function get_the_paragraph($number){
	global $post;
	
	$paragraphs = digressit_paragraphs(wpautop($post->post_content));
	
	return $paragraphs[$number];
	
	
}



/**
 * 
 */
function get_digressit_comments_function(){
	$digressit_options = get_option('digressit');
	

	if( isset($digressit_options['comments_parser']) || function_exists($digressit_options['comments_parser']) ){
		return $digressit_options['comments_parser'];
	}
	else{
		return 'standard_digressit_comment_parser';
	}	
}

/**
 * 
 */
function get_digressit_content_parser_function(){
	$digressit_options = get_option('digressit');


	if( isset($digressit_options['content_parser']) || function_exists($digressit_options['content_parser']) ){
		return $digressit_options['content_parser'];
	}
	else{
		return 'standard_digressit_content_parser';
	}	
}



/**
 * 
 */
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
		$response = (object)array('responseCode' => false, 'errorMessage' => __('Server failed to respond. Please try again later'));
	}
		
	return $response;
}


/**
 * 
 */
function get_header_images(){	
	return do_action('add_header_image');
}


/**
 *
 */
function digressit_page_sidebar_widgets(){
	if(is_page()){
		global $digressit_options;
		if(is_active_sidebar('page-sidebar') && $digressit_options['enable_sidebar'] != 0){
			?>
			<div class="sidebar-widgets">
			<div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
			<?php
			dynamic_sidebar('Page Sidebar');		
			?>
			</div>
			</div>
			<?php
		}
	}	
}


/**
 *
 */
function digressit_single_sidebar_widgets(){
	if(is_single()){	
		global $digressit_options;
		if(is_active_sidebar('single-sidebar') && (int)$digressit_options['enable_sidebar'] != 0){
			?>
			<div class="sidebar-widgets">
			<div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
			<?php dynamic_sidebar('Single Sidebar'); ?>
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


/**
 *
 */
function header_default_top_menu(){
	global $digressit_options;
	?>
	<ul>
		<li><a title="<?php echo($digressit_options['table_of_contents_label']); ?>" href="<?php bloginfo('url'); ?>"><?php echo($digressit_options['table_of_contents_label']); ?></a></li>
		<li><a title="<?php echo($digressit_options['comments_by_section_label']); ?>" href="<?php bloginfo('url'); ?>/comments-by-section"><?php echo($digressit_options['comments_by_section_label']); ?></a></li>
		<li><a title="<?php echo($digressit_options['comments_by_users_label']); ?>"  href="<?php bloginfo('url'); ?>/comments-by-contributor"><?php echo($digressit_options['comments_by_users_label']); ?></a></li>
		<li><a title="<?php echo($digressit_options['general_comments_label']); ?>"  href="<?php bloginfo('url'); ?>/general-comments"><?php echo($digressit_options['general_comments_label']); ?></a></li>
		<?php do_action('add_commentbrowser_link'); ?>		
	</ul>
<?php
}



/**
 *
 */
function digressit_body_class(){
	global $blog_id, $post;
	$request_root = parse_url($_SERVER['REQUEST_URI']);
	
	$blog_name_unique = ereg_replace("[^A-Za-z0-9]", "-", strtolower(get_bloginfo('name') ));
	$post_name_unique = 'post-name-'. $post->post_name;
	$current_page_name = '';
	if(function_exists('digressit_is_commentbrowser') && digressit_is_commentbrowser()){
		$current_page_name .= ' comment-browser '. $blog_name_unique ;
	}
	elseif(is_multisite() && $blog_id == 1 && digressit_is_frontpage()){
		$current_page_name = ' frontpage '. $blog_name_unique ;
	}
	else{
		$current_page_name .= basename(get_bloginfo('home'));
		if(is_home()){
			$current_page_name .= ' site-home '. $blog_name_unique ;
		}	
	}
	
	if(digressit_enabled() ){
		$current_page_name = $current_page_name . " digressit-enabled ";
	
	}
	
	return $current_page_name. " ". $post_name_unique;
}


/**
 *
 */
function digressit_live_content_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;

	$excluded_words = array('the','and');
	//every three letters we give results
	if(strlen($request_params['value']) > 3 && !in_array($request_params['value'], $excluded_words)){
		$posts = null;
		$message = null;		

		$sql = "SELECT * FROM $wpdb->posts p  
				WHERE p.post_status = 'publish' 
				AND ( p.post_type  = 'post' OR  p.post_type  = 'page' ) 
				AND ( p.post_content LIKE \"%".esc_sql($request_params['value'])."%\"  OR p.post_content LIKE \"%".esc_sql($request_params['value'])."%\" ) 
				GROUP BY p.ID LIMIT 3";
	
		$posts = $wpdb->get_results($sql);			
		//var_dump($posts);

		$message = null;
		foreach($posts as $p){
			$message .= "<div class='search-result'>".
						"<div class='post-title'><a href='".get_permalink($p->ID)."'>".$p->post_title."</a></div>".
						"</div>";
		}
		die(json_encode(array('status' => count($posts), "message" => $message)));
	}
	die(json_encode(array('status' => 0, "message" => '')));
}


/**
 *
 */
function digressit_live_comment_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;


	$excluded_words = array('the','and');
	//every three letters we give results
	if(strlen($request_params['value']) > 3 && !in_array($request_params['value'], $excluded_words)){
		$posts = null;
		$message = null;		
		
		$sql = "SELECT *
				FROM $wpdb->comments c, $wpdb->posts p
				WHERE p.ID = c.comment_post_ID
				AND c.comment_approved =1
				AND p.post_status = 'publish'
				AND (c.comment_content LIKE '%".esc_sql($request_params['value'])."%'
		              OR c.comment_content LIKE '".esc_sql($request_params['value'])."%' 
		              OR c.comment_content LIKE '%".esc_sql($request_params['value'])."')
		        GROUP BY comment_ID LIMIT 3";
		$posts = $wpdb->get_results($sql);			
		
		foreach($posts as $post){
			$message .= "<div class='search-result'>".
						"<div class='post-title'><a href='".get_permalink($post->ID)."#comment-".$post->comment_ID."'>".substr($post->comment_content, 0, 75)." [...]</a></div>".
						"</div>";
		}
		die(json_encode(array('status' => count($posts), "message" => $message)));
	}
	die(json_encode(array('status' => 0, "message" => '')));
}

/**
 *
 */
function frontpage_sidebar_widgets(){
	global $digressit_options;

	if(is_active_sidebar('frontpage-sidebar') && $digressit_options['enable_sidebar'] != 0){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
		<?php
		dynamic_sidebar('Frontpage Sidebar');
		?>
		</div>
		</div>
		<?php
	}
}

/**
 *
 */
function frontpage_load(){
	if(digressit_is_frontpage()){
		add_action('add_dynamic_widget', 'frontpage_sidebar_widgets');
	}
}


/**
 * 
 */
function digressit_core_print_styles(){
	global $current_user, $override_default_theme, $browser, $blog_id, $digressit_options;
	
	
	wp_register_style('digressit.core', get_digressit_media_uri('css/core.css'));

	
	if(strlen($digressit_options['custom_style_sheet']) > 10 && esc_url($digressit_options['custom_style_sheet'], array('http', 'https'))){
		wp_register_style('digressit.custom', $digressit_options['custom_style_sheet']);
	}

	//hacks for IE
	wp_register_style('digressit.ie7', get_digressit_media_uri('css/ie7.css'));
	wp_register_style('digressit.ie8', get_digressit_media_uri('css/ie8.css'));
	wp_enqueue_style('digressit.core');		
	

	if($browser['name'] =='msie' && $browser['version'] == '7.0'){
		wp_enqueue_style('digressit.ie7');				
	}

	if($browser['name'] =='msie' && $browser['version'] == '8.0'){
		wp_enqueue_style('digressit.ie8');				
	}

	wp_enqueue_style('digressit.custom');

	if($blog_id == 1 && file_exists(ABSPATH.'/wp-content/plugins/buddypress/bp-core/bp-core-cssjs.php') && function_exists('bp_core_add_admin_bar_css')){
		if ( defined( 'BP_DISABLE_ADMIN_BAR' ) )
			return false;

		if ( is_multisite()  || is_admin() ) {
			$stylesheet = get_blog_option( BP_ROOT_BLOG, 'stylesheet' );

			if ( file_exists( WP_CONTENT_DIR . '/themes/' . $stylesheet . '/_inc/css/adminbar.css' ) )
				wp_enqueue_style( 'bp-admin-bar', apply_filters( 'bp_core_admin_bar_css', WP_CONTENT_URL . '/themes/' . $stylesheet . '/_inc/css/adminbar.css' ) );
			else
				wp_enqueue_style( 'bp-admin-bar', apply_filters( 'bp_core_admin_bar_css', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/css/adminbar.css' ) );
		}
	}

}


/**
 * 
 */
function custom_digressit_logo(){
	global $digressit_options;

	$css_name = preg_replace("/[^a-zA-Z]/", "", get_bloginfo('name'));
	?>
	<style>

	#<?php echo $css_name; ?>-logo{
		background: url(<?php echo $digressit_options['custom_header_image']; ?>) no-repeat;
		height: 100px;
	}
	</style>
	<a href="<?php bloginfo('url'); ?>" ><div id="<?php echo $css_name; ?>-logo"></div></a>	
	<?php
}

/**
 * 
 */
function get_root_domain(){
	global $development_mode,$testing_mode, $production_mode;
	$development_mode = false;
	$testing_mode = false;
	$production_mode = false;

	return "http://" . DOMAIN_CURRENT_SITE; //isset($_SERVER['HTTPS']) ? "https://". DOMAIN_CURRENT_SITE : "http://" . DOMAIN_CURRENT_SITE;
}


function digressit_enabled(){
	global $digressit_options;
	
	if(	is_single() && (int)$digressit_options['digressit_enabled_for_posts'] || is_page() && (int)$digressit_options['digressit_enabled_for_pages']){
		
		return true;
				
	}

}

/**
 * 
 */
function digressit_core_print_scripts(){
	global $current_user, $post, $blog_id, $digressit_options;

	wp_deregister_script('autosave');
    if (!is_admin() && $digressit_options['debug_mode'] != 1) {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js');
    }
    wp_enqueue_script( 'jquery' );




	$url = parse_url(get_root_domain(). $_SERVER["REQUEST_URI"]);

	if(!is_admin()){
		?>
		<script>	
			var siteurl = '<?php echo get_option("siteurl"); ?>';
			var baseurl = '<?php echo get_root_domain() ?>';
			var user_ID =  <?php echo $current_user->ID; ?>;
			var post_ID = <?php echo $post->ID ?>;
			var blog_ID = <?php echo $blog_id; ?>;
			var current_blog_id = <?php echo $blog_id; ?>;
			var request_uri = '<?php echo  str_replace(get_option("siteurl"), '', get_permalink($post->ID)); ?>';
			<?php 	if(is_single() && (int)$digressit_options['digressit_enabled_for_posts'] || is_page() && (int)$digressit_options['digressit_enabled_for_pages']){ ?>
			var digressit_enabled = 1;
			var post_name = '<?php echo $post->post_name; ?>';
			var allow_general_comments = <?php echo !is_null($digressit_options["allow_general_comments"]) ? $digressit_options["allow_general_comments"] : 0; ?>;
			var allow_comments_search = <?php echo !is_null($digressit_options["allow_comments_search"]) ? $digressit_options["allow_comments_search"] : 0; ?>;
			var comment_count = <?php echo count($comment_array); ?>;
			var commment_text_signature = new Array(); 
			var commentbox_function = '<?php echo strlen($digressit_options['commentbox_parser']) ? $digressit_options['commentbox_parser'] : 'grouping_digressit_commentbox_parser'; ?>';
			<?php } else{ ?>
			var digressit_enabled = 0;
			<?php } ?>			
			var keyboard_navigation = <?php echo $digressit_options['keyboard_navigation'] ?>;

		</script>	
		<?php
	

		if($digressit_options['debug_mode'] == 1){
			wp_enqueue_script('digressit.core',		get_digressit_media_uri('js/digressit.core.js'), 'jquery', false, true );	
			wp_enqueue_script('jquery.easing', 		get_digressit_media_uri('js/jquery.easing.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.scrollto',	get_digressit_media_uri('js/jquery.scrollTo.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.cookie',		get_digressit_media_uri('js/jquery.cookie.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.mousewheel',	get_digressit_media_uri('js/jquery.mousewheel.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.em',			get_digressit_media_uri('js/jquery.em.js'), 'jquery', false, true );
			wp_enqueue_script('jquery.copy',			get_digressit_media_uri('js/jquery.copy.js'), 'jquery', false, true );
			wp_enqueue_script('superfish.js',			get_digressit_media_uri('js/superfish.js'), 'jquery', false, true );
		}		
		else{
			wp_enqueue_script('digressit.core',		get_digressit_media_uri('js/digressit.core.min.js'), 'jquery', false, true );				
		}
	}
}

?>
