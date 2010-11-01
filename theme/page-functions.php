<?php

add_action('wp', 'page_load');


function page_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/page.css" type="text/css" media="screen" />
<?php
}

function page_wp_print_scripts(){
	wp_enqueue_script('digressit.page', get_template_directory_uri().'/page.js', 'jquery', false, true );
}

function page_sidebar_widgets(){
	if(is_active_sidebar('page-sidebar')){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar">		
		<?php
		dynamic_sidebar('Page Sidebar');		
		?>
		</div>
		</div>
		<?php
	}
	
}

function page_load(){
	if(is_page() || is_archive()){
		add_action('add_dynamic_widget', 'page_sidebar_widgets');
	}
}








?>