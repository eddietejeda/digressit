<?php
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


	$is_multiuser = is_mu_or_network_mode();			


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

	//$digressit_options['wp_path'] = $wp_path;
	$digressit_options['debug_mode'] = 0;
	$digressit_options['allow_text_selection'] = 0;
	$digressit_options['default_skin'] = $default_skin;
	$digressit_options['stylesheet'] = $default_stylesheet;
	$digressit_options['default_left_position'] = '400px';
	$digressit_options['default_top_position'] = '175px';
	$digressit_options['allow_users_to_minimize'] = 0;
	$digressit_options['allow_users_to_resize'] = 0;
	//$digressit_options['server_sync_interval'] = $monthly;
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

	$digressit_options['enable_dropdown_menu'] = 1;
	$digressit_options['enable_citation_button'] = 0;
	$digressit_options['keyboard_navigation'] = 0;
	
	
	

	
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
 * Creates menu in the admin page. Also detects permalink status
 */
function digressit_add_admin_menu() {
	global $wp_rewrite;
	add_submenu_page( 'themes.php', 'Digress.it', 'Digress.it', 'administrator', 'digressit.php', 'digressit_theme_options_page');

	if(!$wp_rewrite->permalink_structure){
		add_action( 'admin_notices', 'permalink_required_notice' );
	}
}



/**
 * Creates the theme options page. Prints out HTML
 * @todo secure forms
 */
function digressit_theme_options_page() {
	global $wpdb, $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function, $blog_id;

	if($_GET['page'] == 'digressit.php' && isset($_POST['reset']) && $_POST['reset'] == 'Reset Options'){
		delete_option('digressit');
		activate_digressit();		
	}
	elseif(isset($_POST['update-digressit-options'])){
		$digressit_options = get_option('digressit');
		
		foreach($_POST as $key => $value){
			$digressit_options[$key] = $value;
		}
		
		delete_option('digressit');
		add_option('digressit', $digressit_options);
	}

	$digressit_options = get_option('digressit');
	?>

	<style>
		#wpcontent input[type=text],#wpcontent select {
		border:1px solid #DDDDDD;
		font-size:14px;
		margin:2px;
		width:auto;
		}
 		.form-table tr{
			border-bottom: 1px solid #eee;
		}	
	</style>

  	<div class="wrap" style="position: relative; font-size: 110%;">
	
		<form method="post" action="<?php $PHP_SELF; ?>">

		<h2><?php _e('Digress.it Options', 'digressit');  ?></h2>

		<table class="form-table" style="vertical-align: top; width: 800px; padding: 0; margin: 0" >
	



		<?php   
	
			$pages = null;
			foreach(get_pages() as $page){
				$pages[$page->post_title] = $page->ID;			
			}
		
		?>
		<tr>
			<td colspan="2"><h2><?php _e('Presentation', 'digressit'); ?></h2></td>
		</tr>
		
		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Front page content', 'digressit');  ?></b></td>
			<td>
			
				<?php print_dropdown('front_page_content', $pages, $digressit_options['front_page_content']); ?>
				<p><?php _e("The content of this page will be the first thing a visitor to your website will see.", 'digressit'); ?></p>
			</td>
		</tr>
		

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Table of Contents Label' , 'digressit');  ?></b></td>
			<td><?php print_input_text('table_of_contents_label', $digressit_options['table_of_contents_label']); ?></td>
		</tr>




		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Front Page Order', 'digressit');  ?></b></td>
			<td><?php print_dropdown('front_page_order_by', array('id' => 'id', 'date' => 'date'), $digressit_options['front_page_order_by']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Front Page Order by', 'digressit');  ?></b></td>
			<td><?php print_dropdown('front_page_order', array('ASC' => 'ASC', 'DESC' => 'DESC'), $digressit_options['front_page_order']); ?></td>
		</tr>



		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Comments by Section Label', 'digressit');  ?></b></td>
			<td><?php print_input_text('comments_by_section_label', $digressit_options['comments_by_section_label']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Comments by Users Label', 'digressit');  ?></b></td>
			<td><?php print_input_text('comments_by_users_label', $digressit_options['comments_by_users_label']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('General Comments Label', 'digressit');  ?></b></td>
			<td><?php print_input_text('general_comments_label', $digressit_options['general_comments_label']); ?></td>
		</tr>
		

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Keyboard Navigation', 'digressit');  ?></b></td>
			<td><?php print_dropdown('keyboard_navigation', array('No' => 0, 'Yes' => 1), $digressit_options['keyboard_navigation']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Enable Citation Button', 'digressit');  ?></b></td>
			<td><?php print_dropdown('enable_citation_button', array('No' => 0, 'Yes' => 1), $digressit_options['enable_citation_button']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Frontpage List Style', 'digressit');  ?></b></td>
			<td><?php print_dropdown('frontpage_list_style', array( __('Numbers', 'digressit') => 'list-style-decimal', 
																	__('None', 'digressit') => 'list-style-none',
																	__('Lower Alphabet', 'digressit') => 'list-style-lower-alpha',
																	__('Upper Alphabet', 'digressit') => 'list-style-upper-alpha',
																	__('Lower Roman', 'digressit') => 'list-style-lower-roman',
																	__('Upper Roman', 'digressit') => 'list-style-upper-roman',
																	__('Square', 'digressit') => 'list-style-square',
																	__('Circle', 'digressit') => 'list-style-circle'
																), $digressit_options['frontpage_list_style']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Allow General Comments', 'digressit');  ?></b></td>
			<td><?php print_dropdown('allow_general_comments', array('No' => 0, 'Yes' => 1), $digressit_options['allow_general_comments']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Enable Instant Content Search', 'digressit');  ?></b></td>
			<td><?php print_dropdown('enable_instant_content_search', array('No' => 'false', 'Yes' => 'true'), $digressit_options['enable_instant_content_search']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Parse List Items', 'digressit');  ?></b></td>
			<td><?php print_dropdown('parse_list_items', array('No' => 0, 'Yes' => 1), $digressit_options['parse_list_items']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Show Pages in Menu', 'digressit');  ?></b></td>
			<td><?php print_dropdown('show_pages_in_menu', array('No' => 0, 'Yes' => 1), $digressit_options['show_pages_in_menu']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Enable Drop Down Menu', 'digressit');  ?></b></td>
			<td><?php print_dropdown('enable_dropdown_menu', array('No' => 0, 'Yes' => 1), $digressit_options['enable_dropdown_menu']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Enable Sidebar', 'digressit');  ?></b></td>
			<td><?php print_dropdown('enable_sidebar', array('No' => 0, 'Yes' => 1), $digressit_options['enable_sidebar']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Sidebar Position', 'digressit');  ?></b></td>
			<td><?php print_dropdown('sidebar_position', array('Left' => 'sidebar-widget-position-left', 'Right' => 'sidebar-widget-position-right'), $digressit_options['sidebar_position']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Auto-hide Sidebar', 'digressit');  ?></b></td>
			<td><?php print_dropdown('auto_hide_sidebar', array('No' => 'sidebar-widget-no-auto-hide', 'Yes' => 'sidebar-widget-auto-hide'), $digressit_options['auto_hide_sidebar']); ?></td>
		</tr>
		
		<tr valign="top">
			<td style="width: 200px"><b><?php _e('In Sidebar Show', 'digressit');  ?></b></td>
			<td><?php print_dropdown('show_comment_count_in_sidebar', array('Comment Count' => '1', 'Section Number' => 0), $digressit_options['show_comment_count_in_sidebar']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Custom Header Image URL', 'digressit');  ?></b></td>
			<td>
				
				<?php print_input_text('custom_header_image', $digressit_options['custom_header_image']); ?>
				<p><?php _e('This image will override the current header and will become the logo to your site. 
					Be sure to get copy the entire URL in this field. You can also 
					<a href="'.bloginfo('url').'/wp-admin/media-new.php">upload your logo</a> and get the URL from there.
					<b>Note:</b> The image needs to be a maximum of 60px tall.', 'digressit'); ?>
			</td>
		</tr>

		<tr>
			<td style="width: 200px"><b><?php _e('Custom Style Sheet', 'digressit');  ?></b></td>
			<td>
				<?php print_input_text('custom_style_sheet', $digressit_options['custom_style_sheet']); ?>
				<p><?php _e('If you would like to customize the theme, you can upload a stylesheet that can be be loaded after the required stylesheets. 
					For heavy customizations you should use the "Digress.it Wireframe" theme provided.
					For more information on this feature follow the instructions provided at', 'digressit'); ?> <a href="http://digress.it/help">http://digress.it/help</a>. </p>
			</td>
		</tr>

		<tr>
			<td colspan="2"><h2><?php _e('Advanced','digressit'); ?></h2></td>
		</tr>

		
		
		<?php if(is_super_admin()): ?>
		<tr>
			<td style="width: 200px"><b><?php _e('Debug Mode', 'digressit');  ?></b></td>
			<td><?php print_dropdown('debug_mode', array('No' => 0, 'Yes' => '1'), $digressit_options['debug_mode']); ?></td>
		</tr>
		
		<tr>
			<td style="width: 200px"><b><?php _e('Use CDN', 'digressit');  ?></b></td>
			<td>
			<?php print_dropdown('use_cdn', array('Yes' => '1', 'No' => 0), $digressit_options['use_cdn']); ?>
			<p><?php _e('This is an experimental feature. The idea is that you can host the media files on a really fast file server. Enabling this now
				has the risk of downloading files that are out of date. Use at your own discretion.', 'digressit'); ?></p>				
				
			</td>
		</tr>
		
		<tr>
			<td style="width: 200px"><b><?php _e('CDN');  ?></b></td>
			<td><?php print_input_text('cdn', $digressit_options['cdn'], 'disabled'); ?>

			</td>
		</tr>
		
		<?php endif; ?>
		
		
		<tr>
			<td style="width: 200px"><b><?php _e('Content Parsing Function', 'digressit');  ?></b></td>
			<td><?php print_dropdown('content_parser', $digressit_content_function, $digressit_options['content_parser']); ?></td>
		</tr>

		<tr>
			<td style="width: 200px"><b><?php _e('Comments Parsing Function', 'digressit');  ?></b></td>
			<td><?php print_dropdown('comments_parser', $digressit_comments_function, $digressit_options['comments_parser']); ?></td>
		</tr>
	
		<tr>
			<td style="width: 200px"><b><?php _e('Comment Box Parsing Function', 'digressit');  ?></b></td>
			<td><?php print_dropdown('commentbox_parser', $digressit_commentbox_function, $digressit_options['commentbox_parser']); ?></td>
		</tr>


		</table>

		<input type="hidden" name="update-digressit-options" value="1" />

		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'digressit') ?>" />
		<input type="submit" name="reset" class="button-primary" value="<?php _e('Reset Options', 'digressit') ?>" />
		</p>

		</form>
	</div>
	

	

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="XYBB4WEBLRHMN">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>

	<?php 
}



?>