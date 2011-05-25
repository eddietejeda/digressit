<?php

add_action('wp', 'frontpage_load');

/**
 *
 */
function frontpage_sidebar_widgets(){
	$digressit_options = get_option('digressit');

	if(is_active_sidebar('frontpage-sidebar') && $digressit_options['enable_sidebar'] != 0){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
		<?php
		dynamic_sidebar('Frontpage Sidebar');
		?>
		</div>
		</div>
		<?php
	}
}

/**
 *
 */
function frontpage_load(){
	if(is_frontpage()){
		add_action('add_dynamic_widget', 'frontpage_sidebar_widgets');
	}
}
?>