<?php

require_once(get_template_directory().'/widgets.php');
add_action('wp_print_styles', 'widgets_wp_print_styles');
add_action('wp_print_scripts', 'widgets_wp_print_scripts' );


function widgets_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/widgets.css" type="text/css" media="screen" />
<?php
}

function widgets_wp_print_scripts(){
	wp_enqueue_script('digressit.widgets', get_template_directory_uri().'/widgets.js', 'jquery', false, true );
}


?>