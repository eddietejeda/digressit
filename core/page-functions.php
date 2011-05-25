<?php

add_action('add_dynamic_widget', 'digressit_page_sidebar_widgets');


/**
 *
 */
function digressit_page_sidebar_widgets(){
	if(is_page()){
		$digressit_options = get_option('digressit');
		if(is_active_sidebar('page-sidebar') && $digressit_options['enable_sidebar'] != 0){
			?>
			<div class="sidebar-widgets">
			<div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">		
			<?php
			dynamic_sidebar('Page Sidebar');		
			?>
			</div>
			</div>
			<?php
		}
	}	
}

?>