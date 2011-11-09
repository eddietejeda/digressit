<?php 
include(DIGRESSIT_THEMES_DIR . '/digressit-wireframe/functions.php'); 
$digressit_options = get_option('digressit');
$digressit_options['auto_hide_sidebar'] = 'sidebar-widget-auto-hide';
delete_option('digressit');
add_option('digressit', $digressit_options);

?>