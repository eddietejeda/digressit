<?php

add_action('wp', 'frontpage_load');


function frontpage_sidebar_widgets(){
	if(is_active_sidebar(1)){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar">		
		<?php
		get_widgets('Frontpage Sidebar');
		?>
		</div>
		</div>
		<?php
	}
}

function frontpage_load(){
	if(is_frontpage()){
		add_action('add_dynamic_widget', 'frontpage_sidebar_widgets');
	}
}
?>
