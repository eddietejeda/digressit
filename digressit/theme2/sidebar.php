<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
		
		
<?php

$sidebars_widgets = get_option('sidebars_widgets');	

?>		
			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
			if ( function_exists('dynamic_sidebar')  && count($sidebars_widgets['sidebar-1']) ) {
				
				?>
				<div id="sidebar">
				<ul> 
				<?php dynamic_sidebar(1); ?>
				</ul>
				</div>
			<?php
			}
			?>


