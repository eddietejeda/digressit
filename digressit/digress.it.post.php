<?php


class Digress_It_Post extends Digress_It_Base{

	
	
	function __construct()
	{
        parent::__construct();	
		$this->Digress_It_Post();
	}
	
	/* 
	 * @description: attach the Wordpress hooks to their respective class methods
	 */
	function Digress_It_Post(){
		add_action('wp', array(&$this, 'on_wp'));

		add_filter('wp_head', array(&$this, 'on_wp_head'));

		add_filter('the_content', array(&$this, 'on_the_content'), 10000);
		add_action('comment_post',array(&$this, 'on_comment_post'));

		add_action('save_post',array(&$this, 'on_save_post'));		
				
		add_action('admin_menu', array(&$this, 'on_admin_menu'));

		add_action('wp', array(&$this, 'print_styles_and_js'));
	
	}
	

	function print_styles_and_js(){
		add_action('wp_print_scripts', array( &$this, 'on_wp_print_scripts') );
		add_action('wp_print_styles',  array( &$this, 'on_wp_print_styles') ); 		
	}

	function on_save_post($revisionid){
		
		$postid = wp_is_post_revision($revisionid) ? wp_is_post_revision($revisionid) : $revisionid;	
		$post = get_post($postid);		
		
		if($post->post_type == 'post'){
			$content = apply_filters('the_content', $post->post_content);
			$content = str_replace(']]>', ']]&gt;', $content);

			if(get_option('digressit_content'.$postid)){
				update_option('digressit_content' . $postid, $this->parse_content($content));
			}
			else{
				add_option('digressit_content' . $postid, $this->parse_content($content));
			}
		}
	}
	
	
	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function on_admin_menu() 
	{
		global $digressit_admin;
		add_submenu_page('themes.php', 'Digress.it', 'Digress.it', 8, 'digress.it.admin.php', array($digressit_admin, 'on_options_menu') ); 
	}

	function on_wp_head(){
		
		
		$options = get_option('digressit'); 
		$digressit_theme_mode = get_option('digressit_theme_mode'); 


		if($options['enable_chrome_frame'] == true):
		?>
		<meta http-equiv="X-UA-Compatible" content="chrome=1">


		<?php 		
		endif; 


		$chosen_style =  $options['stylesheet'];
		


		if(get_template() == $this->plugin_name):
		?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo  get_template(); ?>/styles/<?php echo $chosen_style; ?>.css" />		
		<?php
		endif;

	}

	
	/** 
	 * @description: print the stylesheets 
	 */
	function on_wp_print_styles(){		

		$browser = $this->browser;

		wp_enqueue_style( 'digressit.stylesheet', $this->style_path."style.css");

		if(is_single())
		{		
			wp_enqueue_style('jquery.ui.theme',	$this->jquery_path.'themes/smoothness/ui.theme.css');
			
		}

		if($browser['name'] == "msie"){				
			$version = substr($browser['version'], 0, 1);
			wp_enqueue_style( 'digressit.ie', $this->style_path . "ie".$version.".css");
		}
		if($browser->browser == "safari"){
			wp_enqueue_style( 'digressit.webkit', $this->style_path . "webkit.css");
		}
		
	}
	
	
	/** 
	 * @description: print the javascript code 
	 */	
	function on_wp_print_scripts(){

		if(is_single())
		{
			//$plugindir =  PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
			$plugindir =  PLUGINDIR. $this->plugin_name;			
			$options = get_option('digressit');
			$debug = null;		
			$browser = $this->browser;	
			
			
			if($options['debug_mode'] != 1)
			{
				$debug = '.min';
			}	


			//wp_deregister_script( 'jquery' ); 
			//wp_register_script( 'jquery', $this->jquery_path . 'jquery-1.3.2'.$debug.'.js'); 
			wp_enqueue_script('jquery');
			echo $this->get_settings_js();				
			global $digressit_commentbrowser;
			$js = "<script>var total_comment_count = " . $digressit_commentbrowser->getAllCommentCount() . " ; </script>\n";
			echo $js;

					
			if($options['debug_mode'] == 1)
			{				

				if($browser['name'] == "msie"){
				?>

				<?php					
				}

				?>
				<div class="ui-widget debug-message">
					<div class="ui-state-error ui-corner-all" style="padding: .5em;"> 
						<p><span class="ui-icon ui-icon-alert" style="margin-right: .3em;"></span> 
						<strong>Alert:</strong> You are in debug mode.</p>
					</div>

				</div>
				<?php


				wp_enqueue_script('jquery.ui.core',			$this->jquery_path.'ui/ui.core.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.ui.resizable',	$this->jquery_path.'ui/ui.resizable.js', array('jquery.ui.core'), '1.3.2'); 
				wp_enqueue_script('jquery.ui.draggable',	$this->jquery_path.'ui/ui.draggable.js', array('jquery.ui.core'), '1.3.2'); 
				wp_enqueue_script('jquery.effects.core',	$this->jquery_path.'ui/effects.core.js', array('jquery'), '1.3.2'); 


				wp_enqueue_script('jquery.create',		$this->jquery_extensions_path.'create/jquery.create.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.cookie',		$this->jquery_extensions_path.'cookie/jquery.cookie.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.em',			$this->jquery_extensions_path.'em/jquery.em.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.easing', 		$this->jquery_extensions_path.'easing/jquery.easing.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.event.drag',	$this->jquery_extensions_path.'eventdrag/jquery.event.drag.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.mousewheel',	$this->jquery_extensions_path.'mousewheel/jquery.mousewheel.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.scrollto',	$this->jquery_extensions_path.'scrollto/jquery.scrollTo.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.tooltip',		$this->jquery_extensions_path.'tooltip/jquery.tooltip.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.resize',		$this->jquery_extensions_path.'resize/jquery.resize.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.pulse',		$this->jquery_extensions_path.'pulse/jquery.pulse.js', array('jquery'), '1.3.2'); 

				wp_enqueue_script('digressit',		$this->js_path.'digress.it.src.js', array('jquery'), '1.3.2'); 
				
				include('js/compress.js.php');
				


			}
			else
			{
				
				wp_enqueue_script('jquery.digressit.ui',			$this->js_path.'digress.it.ui.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('jquery.digressit.extensions',	$this->js_path.'digress.it.extensions.js', array('jquery'), '1.3.2'); 
				wp_enqueue_script('digressit', 						$this->js_path.'digress.it.js', array('jquery'), '1.3.2'); 
				
			}
			
			if(is_admin())
			{
				wp_enqueue_script('jquery.ui.core',		$this->js_path.'customizer.js', array('jquery'), '1.3.2'); 
			}			
		}
		
		
		
		
	}




	/** 
	 * @description: 
	 *
	 */
	function on_the_content($content){
		global $wpdb, $image_path, $post;


		$postid = $post->ID;

		$enabled = true; //we assume it enabled for previous versions
				
		if(get_option('digressit_enabled_'. $postid)){

			if(get_option('digressit_enabled_'. $postid) == $post->post_status){
				$enabled = true;
			}
			else{
				$enabled = false;
			}
			
		}
				
		if(is_single() && $enabled )
		{

			$digressit_content = array();
			
			if(!$content){

								$digressit_content[] = '<div id="textblock-1" class="textblock"><span class="paragraphnumber"><span class="embedcode" style="display: none;">
								<b>Embed Code (<a id="embed-object-1" class="embed-link" href="javascript:return false">object</a> | <a id="embed-html-1" class="embed-link" href="javascript:return false">html</a>)</b><textarea id="textarea-embed-1" rows="5">&lt;object style="width: 100%;" onload="this.style.height = (this.contentDocument.body.offsetHeight + 40) + \'px\'" class="digressit-paragraph-embed" id="67dc5a225499cfed3d1d554c4a20d9a2" data="'.get_bloginfo('home').'?p=284&amp;digressit-embed=1"&gt;&lt;/object&gt;&lt;a href="'.get_permalink($postid).'#1"&gt;@&lt;/a&gt;</textarea>
								<b>Permalink</b>:<br> <input type="text" value="'.get_permalink($postid).'#1">
								</span><a href="'.get_permalink($postid).'#1">1</a></span>
				<span class="commenticonbox" title="There are 0 comments for this paragraph"><img src="'.get_bloginfo('home').'/wp-content/plugins/digressit/theme/images/famfamfam/comment.png" id="paragraph-1" class="commenticon"><small class="commentcount">2</small></span>
				<span class="paragraphtext"></span></div>';
				
				
			}
			else{
				$digressit_content = $this->parse_content($content);							
			}



			
			$blocks = $digressit_content;
			
			
			$text_signatures = null;
			foreach($blocks as $key => $block){
				$text_signatures[] = $key;
			}
			
		    $glue = "\n";
		    $updated = implode($glue, $blocks);


			$js = $this->get_approved_comments_js($post->ID);
			$updated .= $js;

			if(is_array($this->footnotes)){
				$updated = $updated. implode(' ',$this->footnotes);
			}
			return $updated;
		
			
		}
		else
		{
			return $content;
		}
	}


	/** 
	 * @description: when a comment is submitted, it gets the cookie on the browse that corresponds to the paragraph 
	 * the user select and attach that tidbit to the comment, using the ID
	 */
	function on_comment_post($comment_ID)
	{
		global $wpdb, $post;	
		$text_signature = $_COOKIE['text_signature'];			
		$result = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->comments SET comment_text_signature = %s WHERE comment_ID = %d", $text_signature, $comment_ID) );
		
		//var_dump($result);

		//$this->on_save_post($post->ID);
	}


	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function generate_text_signature($text, $position=null)
	{
  
		$words = explode(' ', ereg_replace("[^A-Za-z]", " ", html_entity_decode($text)));
		$unique_words = array_unique  ( $words  );
    
		$text_signature = null;

		foreach($unique_words as $key => $word)
		{
			$text_signature .= substr($word, 0, 1);
			if($key > 254)
			{
				break;
			}        
		}
		return ($position) ?  $position.":".$text_signature : $text_signature;
	}






	/** 
	 * @description: 
	 *
	 */
	function get_text_signature_filter($comments, $text_signature, $confidence = 90)
	{
		if(!is_array($comments))
		{
			return null;
		}
		$updated = null;


		foreach($comments as $comment)
		{
			if( $comment->comment_text_signature == $text_signature)
			{
				$updated[] = $comment;    			
			}		
		}

		return $updated;  
	}





	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function on_get_comment_ids_by_text_signature($post_ID, $text_signature)
	{
		global $wpdb;
		
		$comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d", $post_ID) );
		if(!$comments)
		{
			return null;
		}
	
		$comments_filtered = $this->get_text_signature_filter($comments, $text_signature);
	
		if(!$comments_filtered)
		{
			return null;
		}

		$ids = array();
	
		foreach($comments_filtered as $comment)
		{
			$ids[] = $comment->comment_ID;
		}
		return $ids;
	}

	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function on_get_text_signature_by_comment_id($comment_ID)
	{
		global $wpdb;		
		$comment_text_signature = $wpdb->get_var( $wpdb->prepare("SELECT comment_text_signature FROM $wpdb->comments WHERE comment_ID = %s", $comment_ID) );
		return $comment_text_signature;
	}

	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function get_text_signatures_js($text_signatures)
	{
		$js = "<script type=\"text/javascript\">\n";
		$js .=  "var text_signatures = new Array(); \n";
		foreach($text_signatures as $key=>$text_signature)
		{
			$js .= "text_signatures[$key] = '". $text_signature."';\n";
		}
		$js .= "</script>\n";
		return $js;	
	}



	/** 
	 * @description: why use ajax when we can just print all the data we need on the source? here we get the
	 * settings and hand them over to javascript
	 * @todo: could there be a way to 
	 */
	function get_settings_js()
	{
		$exclude = array('admin_mode','debug_mode','installation_key','front_page_post_type', 'Submit', 'action', 'stylesheet', 'current_theme', 'front_page_numberposts', 'highlight_color');

		$js = "<script type=\"text/javascript\">\n";
		$js .= "var image_path = \"".$this->image_path."\";\n";

		foreach( $this->get_settings()  as $variable=>$value)
		{
			if( !in_array  ( $variable  , $exclude )){
				if( is_numeric($value)){
					$js .= "var $variable = $value;\n";				
				}
				else{
					$js .= "var $variable = '$value';\n";
				}
			}
		}
		$js .= "</script>";
		return $js;
	}

	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function get_approved_comments_js($postID)
	{
		$comment_array = get_approved_comments($postID);
		global $digressit_commentbrowser, $post;
		
		$js = "\n<script type=\"text/javascript\">\n";
		$js .= "var post_ID = " . $postID . " ; \n";
		$js .= "var comment_count = " . count($comment_array) . " ; \n";
		$js .= "var commment_text_signature = new Array(); \n";


		if(!strlen($post->post_password) && !strstr($post->post_content, 'wp-pass.php')){
			foreach($comment_array as $comment){
				$js .= "commment_text_signature[".$comment->comment_ID."] = '". $comment->comment_text_signature."';\n";
				if(strlen($comment->commment_text_selection)){
					$js .= "commment_text_selection[".$comment->comment_ID."] = '". $comment->commment_text_selection."';\n";
				}
			}
		}
		else{
			$js .= "var comment_count = 0; \n";			
		}
		$js .= "</script>\n";
		return $js;
	}



	/** 
	 * @description: return an array with all the known options
	 * @todo: is there a function that gets all the options for a specific plugin?
	 */
	function get_settings() 
	{	
		$settings = get_option('digressit');
		$settings['wp_path'] = $this->wp_path;		
		return $settings;
	}



	/** 
	 * @description: print the posts and their comment count in a list format
	 * @todo: find out if this method, or method like it exists in wordpress. these custom functions are ugly
	 *
	 */
	function on_list_posts($params='numberposts=20')
	{
		global $post;
		$myposts = get_posts($params);	
		foreach($myposts as $post)
		{
			$count = count(get_approved_comments($post->ID));  ?>
			
			<li class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?> (<?php echo $count; ?>)</a></li>
		
		<?php
		}
	}
	
	
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
}