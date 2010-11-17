<?php


add_action('wp_print_styles', 'digressit_author_print_styles');
add_action('wp_print_scripts', 'digressit_author_print_scripts' );

function digressit_author_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/author.css'); ?>" type="text/css" media="screen" />
<?php
}

function digressit_author_print_scripts(){
		
	wp_enqueue_script('digressit.authors', get_digressit_media_uri('js/digressit.author.js'), 'jquery', false, true );
}

?>