<?php


add_action('wp_print_styles', 'digressit_default_stylesheets', 100);


function digressit_default_stylesheets(){
	wp_register_style('digressit.default', get_template_directory_uri()."/style.css");
	wp_enqueue_style('digressit.default');
}

?>