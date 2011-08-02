<?php

add_action('wp_print_styles', 'digressit_default_stylesheets', 100);
add_action('init', 'digressit_default_lightboxes', 100);

/*
 *
 */
if('wp-signup.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	add_action('wp_head', 'digressit_wp_signup');
	
	function digressit_wp_signup(){
	?>
	<style>
	#content{
		margin: -32px auto 0 !important;
	}
	</style>
	<?php
	}
	
	
}

/*
 *
 */
function digressit_default_stylesheets(){
	//wp_register_style('digressit.default', get_template_directory_uri()."/style.css");
	//wp_enqueue_style('digressit.default');
}

/*
 *
 */
function digressit_default_lightboxes(){
/*
	add_action('add_lightbox', 'lightbox_login_ajax');
	add_action('add_lightbox', 'lightbox_register');
	add_action('add_lightbox', 'lightbox_site_register');
	add_action('add_lightbox', 'lightbox_registering');
	add_action('add_lightbox', 'lightbox_generic_response');
*/
}
?>