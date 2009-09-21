<?php

class Digress_It_Community extends Digress_It_Base{

		
	function __construct(){
		parent::__construct();
		$this->Digress_It_Community();
	}
	
	function Digress_It_Community()
	{
		$options  = get_option('digressit');		
		if($options['installation_key'] === false ){
		}
		else{
			add_action('admin_head-edit.php', array(&$this, 'add_plugin_notice') );
			
			if($_GET['page'] == 'digress.it.admin.php'){
				add_action('admin_head', array(&$this, 'add_plugin_notice') );				
			}
			
			$digressit_nextupdate = get_option('digressit_nextupdate');
						
			if(time() > $digressit_nextupdate && $options['installation_key'] == 32){
				$this->report_back();
			}
		}

	}
	
	

	function add_plugin_notice() {			
			add_action('admin_notices', array(&$this, 'print_form') );
	}
	
	
	
	function start(){
		
	}


	function report_back(){
		$params = null;
		$params['digressit-action'] = 'register';
		$params['name'] = get_bloginfo('name');
		$params['home'] = get_bloginfo('home');
		$params['description']  = get_bloginfo('description');
		$params['version']  = get_bloginfo('version');
		$params['url']  = get_bloginfo('url');
		$params['charset']  = get_bloginfo('charset');
		$params['language']  = get_bloginfo('language');
		$params['siteurl']  = get_bloginfo('siteurl');
		$params['pingback_url']  = get_bloginfo('pingback_url');
		$params['admin_email']  = get_bloginfo('admin_email');
		$params['password']  = get_option('digressit_client_password');
		$params['tags']  = get_the_tags();
		$params['categories']  = get_categories();
		global $digressit_commentbrowser;
		$params['total_comments']  = $digressit_commentbrowser->getAllCommentCount();




		$pages = get_pages();
		foreach($pages as $key=>$page){ 
			if( $page->post_name == 'about'){
				$content = $page->post_content;
				//$content = apply_filters('the_content', $content);
				$params['about']  = htmlentities($content);
				break;
			}
		}
		

		$options  = get_option('digressit');


		
		$response = $this->post_request($params);		

		if(strlen($response->key) == 32){

			$options['installation_key'] = $response->key;
			update_option('digressit', $options);
			update_option('digressit_lastupdate', time());
			update_option('digressit_nextupdate', time() + $options['server_sync_interval']);
		}
		
	}
	
	function print_form(){


		if($_POST['digressit-hostname-check'] == 'OK'){
			
			update_option('digressit_community_hostname', $_POST['digressit-hostname-submit']);
			
		}
		if($_POST['register'])
		{
			
			
			switch($_POST['register'])
			{
				
				case 'Yes!':	
					
					$this->report_back();

				break;

				case "No, and I never plan to join":	
					$options  = get_option('digressit');
					$options['installation_key'] = false;
					update_option('digressit', $options);
			
				break;
			}
		}		
		
		$options  = get_option('digressit');
		
		if($options['installation_key'] === null ){


			?>
			
				<script src="<?php echo $this->jquery_extensions_path.'cookie/jquery.cookie.js'; ?>"></script>
				<script>
				
				jQuery(document).ready(function(){
				
					var wp_path = "<?php $this->wp_path ?>";
					if( jQuery.cookie('digressit-register-notyet') )
					{
						//jQuery('#register-form').hide();
					}

					jQuery('#digressit-hostname-cancel').click(function(e){
					
						jQuery('#configure-digressit-community').hide();
					})
					


					jQuery('#register-notyet').click(function(e){
					
						jQuery.cookie('digressit-register-notyet', true, { path: '/', expires: 7} );
						jQuery('#register-form').hide();
					})
					
					jQuery('#register-configure').click(function(e){
						jQuery('#configure-digressit-community').show();					
					})



					jQuery('#digressit-hostname-check').click(function(e){

						var url = jQuery('#digressit-hostname').val().toString();

						if(url.substr(0, 7) != 'http://'){
							url = 'http://' + url;
						}
						

						jQuery.getJSON(wp_path + '?digressit-event=confirm_community_server&url=' + url, { digressit_action: "status" },
						  function(data){
							
							if(data.status == 'OK'){
								jQuery('#digressit-hostname-check').val('OK');
							}
							else{
								jQuery('#digressit-hostname-check').val('Error: Recheck');								
							}
						  });
					})
					
					
				
				});
						
				</script>

				<?php $hidethis = ($_COOKIE['digressit-register-notyet'] == 'true' && $_GET['page'] != 'digress.it.admin.php') ? ' ;display:none '  : '';  ?>
				<div id="register-form" class="updated error" style="padding: 5px; width: 50% <?php echo $hidethis;?>" >

					<?php global $community_name; ?>

					<form method="post" target="_self">
					<h3 style="margin-top:0px">Digress.it</h3>
					<p>Would you like your project to be featured on the digress.it community page? (<a href="http://digress.it/community">learn more</a>)</p>


					<input type="submit" name="register" id="register-yes"     value="Yes!"> 
					
					<?php if($_GET['page'] != 'digress.it.admin.php'): ?>
					<input type="button" name="register" id="register-notyet"  value="Not yet"> 
					<?php endif; ?>
					<input type="submit" name="register" id="register-no"      value="No, and I never plan to join"> 
					<input type="button" name="register" id="register-configure"      value="Configure"> 
					</form>
				</div>				
				
				<div id="configure-digressit-community" class="updated fade-ff0000 error" style="display: none; padding: 5px; width: 50%">
					<form method="post" target="_self">
					<p>Community Server:</p>
					<input type="text" name="digressit-hostname" id="digressit-hostname"  value="<?php echo get_option('digressit_community_hostname')?>"> 
					<input type="button" name="digressit-hostname-check" id="digressit-hostname-check"     value="Check Status"> 
					<input type="submit" name="digressit-hostname-submit" id="digressit-hostname-submit"     value="Save"> 
					<input type="button" name="digressit-hostname-cancel" id="digressit-hostname-cancel"     value="Cancel"> 
					</form>

				</div>
				
			<?php
		}	
	}
 
	
}
	
?>