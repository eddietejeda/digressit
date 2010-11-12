<?php

add_action('init', 'frontpage_load');


function frontpage_sidebar_widgets(){
	$options = get_option('digressit');

	if(is_active_sidebar('frontpage-sidebar')){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar  <?php echo $options['auto_hide_sidebar']; ?> <?php echo $options['sidebar_position']; ?>">		
		<?php
		dynamic_sidebar('Frontpage Sidebar');
		?>
		</div>
		</div>
		<?php
	}
}

function frontpage_load(){
	//var_dump(is_frontpage());
	if(is_frontpage()){
		add_action('add_dynamic_widget', 'frontpage_sidebar_widgets');
	}
}
?>
