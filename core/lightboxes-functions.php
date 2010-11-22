<?php

add_action('wp_print_styles', 'lightboxes_wp_print_styles');
add_action('wp_print_scripts', 'lightboxes_wp_print_scripts' );

function lightboxes_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/lightboxes.css'); ?>" type="text/css" media="screen" />
<?php
}

function lightboxes_wp_print_scripts(){	
	wp_enqueue_script('digressit.lightboxes', get_digressit_media_uri('js/digressit.lightboxes.js'), 'jquery', false, true );
}



function get_lightboxes(){
	if(file_exists(TEMPLATEPATH . '/lightboxes.php')){
		include(TEMPLATEPATH . '/lightboxes.php');
	}
    //do_action('add_lightbox');
}

?>