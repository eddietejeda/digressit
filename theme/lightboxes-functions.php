<?php

add_action('wp_print_styles', 'lightboxes_wp_print_styles');
add_action('wp_print_scripts', 'lightboxes_wp_print_scripts' );

function lightboxes_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/lightboxes.css" type="text/css" media="screen" />
<?php
}

function lightboxes_wp_print_scripts(){	
	wp_enqueue_script('digressit.lightbubbles', get_template_directory_uri().'/lightboxes.js', 'jquery', false, true );
}



function get_lightboxes(){
	include(TEMPLATEPATH . '/lightboxes.php');
    do_action('add_lightbox');
}

?>