<?php

//this is a somewhat of an ABSTRACT class. Do not instansitate


class Digress_It_Base{

	var $wpdb;
		
	var $wp_path;
	var $xmlrpc_path;

	var $plugin_url;
	var $plugin_file;

	var $js_path;
	var $jquery_path;
	var $jquery_extensions_path;
	var $jquery_theme_path;
	var $jquery_elements_path;
	var $jquery_css_path;

	var $style_path;
	var $image_path;

	var $punctuations;
	var $browser;
	var $hostname;
	
	var $options;
	var $debugtime = null;
	
	var $is_multiuser = false;
	var $digressit_server = null;
	function __construct(){
		$this->Digress_It_Base();
	}
	
	function Digress_It_Base()
	{
		$start = microtime(true);
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->wp_path = get_bloginfo('wpurl');
		
		
		$this->xmlrpc_path = $this->wp_path . '/xmlrpc.php';
		$this->plugin_name = str_replace("/", "", str_replace(basename( __FILE__),"",plugin_basename(__FILE__))); 
		$this->plugin_url = WP_PLUGIN_URL .'/' . $this->plugin_name . '/';		
		$this->plugin_file = $this->plugin_url. plugin_basename(__FILE__); 


		$this->digressit_server = 'http://'. DIGRESSIT_COMMUNITY_HOSTNAME . '/';


		if(function_exists('wpmu_create_blog')){
			$this->is_multiuser = true;
		}
		else{
			$this->is_multiuser = false;			
		}


		$this->js_path = $this->plugin_url. 'js/'; 
		$this->jquery_path = $this->js_path . 'jquery/'; 
		$this->jquery_extensions_path =  $this->jquery_path. 'external/'; 
		$this->jquery_theme_path = $this->jquery_path . 'themes/'; 
		$this->jquery_elements_path = $this->jquery_path . 'elements/'; 
		$this->jquery_css_path = $this->jquery_path . 'css/'; 

		$this->style_path = $this->plugin_url . 'style/'; 
		$this->image_path = $this->plugin_url . 'theme/images/'; 

		$this->punctuations = null;


		$url = $_SERVER["SERVER_NAME"] ;
		preg_match("/^(http:\/\/)?([^\/]+)/i" , $url, $found);
		preg_match("/[^\.\/]+\.[^\.\/]+$/" , $found[2], $found);



		$this->hostname = $found[0];

		//$default_skin = $this->hostname == DIGRESSIT_COMMUNITY_HOSTNAME ? 'none' : 'skin1';
		//$default_stylesheet  =  $this->hostname == DIGRESSIT_COMMUNITY_HOSTNAME ? 'digress.it' : 'default';

		$default_skin = 'skin1';
		$default_stylesheet  = 'default';


		
		$installation_key  = null;
		$current = get_option('digressit');
		$installation_key = strlen($current['installation_key']) == 32 ? $current['installation_key'] : null;
		
		//upgrade from 2.1.1
		if(!get_option('digressit_installation_key')){
			add_option('digressit_installation_key', $installation_key);
		}

		if(!get_option('digressit_client_password')){
			$client_password = $this->random_string();
			add_option('digressit_client_password', $client_password);
		}
		
		if(!get_option('digressit_community_hostname')){
			add_option('digressit_community_hostname', 'http://digress.it/');
		}

		if($current['default_left_position'] == '48%'){
			$current['default_left_position'] = '440px';
			update_option('digressit', $current);
		}

		if($current['default_left_position'] == '440px'){
			$current['default_left_position'] = '42%';
			update_option('digressit', $current);
		}

		
		$monthly = 60 * 60 * 24 * 30;
		if(!isset($current['server_sync_interval'])){
			$current['server_sync_interval'] = $monthly;
			update_option('digressit', $current);
		}
		
		


		$sql = "SHOW COLUMNS FROM $wpdb->comments";	
		$columns = $wpdb->get_results($sql);

		$commentpress_installed = false;
		foreach($columns as $col){
			if($col->Field == 'comment_contentIndex'){
				$commentpress_installed = true;
			}
		}


		if($commentpress_installed && !get_option('commentpress_upgraded_to_digress_it')) {

			foreach($this->get_existing_comments() as $comment){
				if( isset($comment->comment_contentIndex) && !isset($comment->comment_text_signature) ){
					$sql = "UPDATE `$wpdb->comments` SET comment_text_signature = $comment->comment_contentIndex  WHERE comment_ID = $comment->comment_ID";	
					$wpdb->query($sql);
				}
			}
			add_option('commentpress_upgraded_to_digress_it', true);	
		}


		
		
		
		//enable widgets
		$sidebars_widgets = get_option('sidebars_widgets');	
		
		if(is_array($sidebars_widgets))	{
			$commentbrowserenabled = false;
			
			if(is_array($sidebars_widgets['sidebar-1'])){
				foreach($sidebars_widgets['sidebar-1'] as $s){
					if($s == 'commentbrowser'){
						$commentbrowserenabled = true;
					}
				}
			}
			if($commentbrowserenabled == false){
				$sidebars_widgets['sidebar-1'][] = 'commentbrowser';
			}
			//remove from inactive widgets
			$wp_inactive_widgets = array();
			if($sidebars_widgets['wp_inactive_widgets']){
				foreach($sidebars_widgets['wp_inactive_widgets'] as $s){
					if($s == 'commentbrowser'){

					}
					else{
						$wp_inactive_widgets[] = $s;
					}
				}
			}
			$sidebars_widgets['wp_inactive_widgets'] = $wp_inactive_widgets;
		
		}
		else{
			$sidebars_widgets['sidebar-1'][] = 'commentbrowser';			
		}	


		update_option('sidebars_widgets', $sidebars_widgets);


		
		

		$this->options = array(	
			'wp_path' => $this->wp_path,
			'debug_mode' => 0,
			'allow_text_selection' => 0,
			'default_skin' => $default_skin,
			'stylesheet' => $default_stylesheet,
			'default_left_position' => '42%',
			'default_top_position' => '75px',
			'allow_users_to_minimize' => 0,
			'allow_users_to_resize' => 0,
			'server_sync_interval' => $monthly,
			'allow_users_to_drag' => 1,
			'highlight_color' => '#FFFC00',			
			'front_page_post_type' => 'post',
			'front_page_numberposts' => 10,
			'frontpage_sidebar' => 1,
			'front_page_content' => '',
			'front_page_order' => 'ASC',
			'front_page_order_by' => 'date');
			
	//print_r($this->options);
	$this->debugtime[]['DigressIt_Base'] = microtime(true) - $start;

	}
	
	function get_existing_comments(){
		global $wpdb;
		$sql = "SELECT * FROM $wpdb->comments";
		return $result = $wpdb->get_results($sql);

	}
	
	/*
	 * @description: create the variables we're going to need throughout out little app
	 */
	function on_init()
	{
		$start = microtime(true);
		

		if($new = preg_replace('/\/(comment-page-\d)/', '', $this->current_url())){

			if($new != $this->current_url()){
				header('Location: '. $new);
				die();
			}
		}

		
		$this->translate();
		$this->browser = $this->browser() ;
		$this->debugtime[]['on_init'] = microtime(true) - $start;
		
		
	}




	
	/** 
	 * @description: modify the database.. add a field to 
	 * @todo: use the dbDelta function to do smooth upgrades 
	 *
	 */

	function on_activation() {	
		$this->install();
		$options = get_option('digressit');								
		switch_theme($this->plugin_name, $this->plugin_name);
	}
	
	
	
	function upgrade(){
		
		$options = get_option('digressit');								

		if( strlen($options['installation_key']) ){
			add_option('digressit_installation_key');
		}
		
		
		//upgrade from commentpress 1.4
		
		
		

		
	}
	
	function on_deactivation()
	{
		$this->uninstall();
		switch_theme('default', 'default');		
	}
	
	function uninstall(){
		
		$theme_link = WP_CONTENT_DIR . '/themes/' . $this->plugin_name;
		
		if(is_link($theme_link)){
			unlink($theme_link);
		}
		delete_option('digressit');
	}	
	
	function install(){
		global $wpdb, $post;
		
		
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


		$this->reset_options();

		$themes_dir = WP_CONTENT_DIR . '/themes/';
		$theme_link = $themes_dir . $this->plugin_name;
		$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $this->plugin_name.'/theme/';

		if(is_writable( $themes_dir)){
			if(is_link($theme_link)){
				//i think we're good
			}
			elseif(!file_exists($theme_link)){
				if(symlink($plugin_theme_link,$theme_link)){
					//we're good
				}
				else{
					die( "There was a error creating the symlink of <b>$plugin_theme_link</b> in <b>$theme_link</b>. If the server doesn't have write permission try creating it manually");
				}
			}
			else{
				die( "There was a error creating the symlink of <b>$plugin_theme_link</b> in <b>$theme_link</b>. Maybe a theme named DigressIt already exists?");					
			}
		}
		else{
			die("no write permission on $themes_dir please give the server write permission on this directory");
		}

	}
	

	function reset_options()
	{
		delete_option('digressit');						
		add_option('digressit', $this->options);	
	}
	
	/** 
	 * @description: save the settings set by the administrator
	 * @todo: do error checking
	 *
	 */
	function save_options($newoptions) 
	{		
		$current = get_option('digressit');

//TODO: check to see if we are admin to enable debug mode
		foreach($newoptions as $key=>$value)
		{			
			$current[$key] = $value;
		}
		
		if($current['default_skin'] == 'none'){
			$current['allow_users_to_drag'] = 0;
			$current['allow_users_to_iconize'] = 0;
			$current['allow_users_to_minimize'] = 0;
			$current['allow_users_to_resize'] = 0;
			$current['allow_users_to_save_position'] = 0;
		}

 		return update_option('digressit', $current);

	}
	


	// Generate a random character string
	function random_string($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
	    // Length of character list
	    $chars_length = (strlen($chars) - 1);

	    // Start our string
	    $string = $chars{rand(0, $chars_length)};

	    // Generate random string
	    for ($i = 1; $i < $length; $i = strlen($string))
	    {
	        // Grab a random character from our list
	        $r = $chars{rand(0, $chars_length)};

	        // Make sure the same two characters don't appear next to each other
	        if ($r != $string{$i - 1}) $string .=  $r;
	    }

	    // Return the string
	    return $string;
	}



	/** 
	 * @description: load in languages 
	 * @todo: add multiple language support
	 *
	 */
	function translate()
	{
		load_plugin_textdomain('digressit', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__))."/lang/");
	}
	

	//http://www.webcheatsheet.com/PHP/get_current_page_url.php
	function current_url() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	

	function post_request($parameters = null){
		
		$params = null;	
		
		if(is_array($parameters)){
			foreach($parameters as $key => $param){
				$params [$key] = $param;
			}
		}

		$data = http_build_query($params);
		
		$context_options = array (
		        'http' => array (
		            'method' => 'POST',
		            'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
		                . "Content-Length: " . strlen($data) . "\r\n",
		            'content' => $data
		            )
		        );

			

		
		$context = stream_context_create($context_options);
		
		$json = file_get_contents($this->digressit_server, false, $context);
		
		$response = json_decode($json);


		
		return $response;
	}
	

	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function get_text_signature_count($post_ID, $text_signature)
	{
		global $wpdb;
		
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE 	comment_approved = 1 AND comment_post_ID = %d", $post_ID) );
		$comment_count = count($this->get_text_signature_filter($comments, $text_signature));
		return ( $comment_count > 0) ? $comment_count : '';	
	}


	function get_tags($html, $tags = 'table|object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8', $technique = 'regexp')
	{
		
		$matches = array();
		$html = force_balance_tags($html);
		
		switch($technique){
		
			case 'simplexml':
				if($result = simplexml_load_string(trim('<content>'.$html.'</content>'))){
					$xml = $result->xpath('/content/'. $tags);
					foreach($xml as $match){
						$matches[] = $match->asXML();
					}
					break;	
				}
			
			case 'regexp':
				preg_match_all('#<('.$tags.')>(.*?)</('.$tags.')>#si',$html,$matches_array);
				$matches = $matches_array[0];
			
			break;
			
		}			
			
		return $matches;

	}
	
	function parse_content($content, $params = array('embed_code' => true)){
		$start = microtime(true);
		
		global $wpdb, $image_path, $post;

		$valid_paragraph_tags = 'object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8|table';
	
		$password_protected = (strlen($post->post_password) && strstr($content, 'wp-pass.php')) ? true : false;
		if($password_protected){
			$content = "<p>$content</p>";
		}

		$returned = $this->get_tags(force_balance_tags($content), $valid_paragraph_tags, 'simplexml');
		
		if( !count($returned) )
		{
			return $content;			
		}

		$blocks = null;
		$text_signatures = null;
		$permalink = get_permalink($post->ID);
		$total_approved = get_approved_comments($post->ID);

	
		foreach($returned as $key=>$paragraph)
		{
  
			$text_signature = $key+1;//$this->generate_text_signature($paragraph);
			$text_signatures[] = $text_signature;

			$count = $this->get_text_signature_count($post->ID, $text_signature);
			$comment_count = ($password_protected == true) ? 0 : $count;

			$icon =  'comment.png';

			$paranumber = $number = ( $key+1 );
			$paragraphnumber = '<span class="paragraphnumber">';
			
					
			
			
			if($params['embed_code']){

/*
				$embedcode .= '<div id="selected_paragraph"><div class="textblock" id="textblock-1">';
				$embedcode .= '<span class="paragraphnumber"><span  title="There are no comments for this paragraph" class="commenticonbox">';
				$embedcode .= '<img  class="commenticon" id="paragraph-1" src="http://wordpress.local/wp-content/plugins/digressit/theme/images/famfamfam/comment.png" />';
				$embedcode .= '<small class="commentcount"></small></span>';
				$embedcode .= '<span class="paragraphtext">'. $paragraph . '</div>';
				$embedcode .= "<br><b>Permalink:</b> <a href=\"" . $permalink ."#".$paranumber."\"> ".$permalink."#" .$paranumber."</a>";
				$embedcode .= "<br><b>Page Comments:</b> " .   $total_approved;
				$embedcode .= "<br><b>Paragraph Comments:</b> " . $comment_count;
*/

	

				$dataurl = get_bloginfo('home').'?p='.$post->ID.'&digressit-embed='.$number;
				$embedid = md5($dataurl);
			
				
							
				$resizejavascript = "this.style.height = (this.contentDocument.body.offsetHeight + 40) + 'px'";
				
				$embedcode = htmlentities('<object style="width: 100%;" onload="'.$resizejavascript.'" class="digressit-paragraph-embed" id="'.$embedid.'" data="'.$dataurl.'"></object><a href="'.get_permalink($post->ID).'#'.$number.'">@</a>');
				$paragraphnumber .= '<span class="embedcode">
				<b>Embed Code (<a href="javascript:return false" class="embed-link" id="embed-object-'.$number.'">object</a> | <a href="javascript:return false" class="embed-link" id="embed-html-'.$number.'">html</a>)</b><textarea id="textarea-embed-'.$number.'">'.$embedcode.'</textarea>
				<b>Permalink</b>:<br> <input type="text" value="'.get_permalink($post->ID).'#'.$number.'" />
				</span><a href="'.get_permalink($post->ID).'#'.$number.'">'.$number.'</a></span>'."\n";
			}
			
		 	$numbertext = ($comment_count == 1) ?  'is one comment' : 'are '.$comment_count.' comments';
		 	$numbertext = ($comment_count == 0) ?  'are no comments' : $numbertext;
			
			$commenticon =	'<span  title="There '.$numbertext.' for this paragraph" class="commenticonbox"><img  class="commenticon" id="paragraph-'.$text_signature.'" src="'.$this->image_path.'famfamfam/'.$icon.'" /><small class="commentcount">'.$comment_count.'</small></span>'."\n";

			//if it's not a P tag, we surround it by p tag
			preg_match('#^<('.$valid_paragraph_tags.')>#',$paragraph, $current_tag);
			if( $current_tag[1] != "p"){
				$paragraph = "<p>" . $paragraph;
			}

			$pattern = array('#^<p>#');
			$replace = array('<div class="textblock" id="textblock-'.$number.'">'.$paragraphnumber . $commenticon . '<span class="paragraphtext">');


			$closetag = "</span></div>";
			/*
			if( $current_tag[1] != "p"){
				$closetag = "</span></p>";
			}
			*/

			$blocks[$text_signature] = str_replace('</p>', '', preg_replace($pattern, $replace, $paragraph . $closetag));
	    }

		$this->debugtime[]['parse_content'] = microtime(true) - $start;

		return $blocks;
		
	}	
}



