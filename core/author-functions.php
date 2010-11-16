<?php


add_action('wp_print_styles', 'author_wp_print_styles');
add_action('wp_print_scripts', 'author_wp_print_scripts' );

function author_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/author.css" type="text/css" media="screen" />
<?php
}

function author_wp_print_scripts(){
		
	wp_enqueue_script('digressit.authors', get_template_directory_uri().'/author.js', 'jquery', false, true );
}

?>