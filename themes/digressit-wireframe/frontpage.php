<?php get_header(); ?>

<div id="container">
<?php get_dynamic_widgets(); ?>

<?php get_stylized_title(); ?>
<div id="content">
	<div id="frontpage">
		<div class="entry">
		<?php
		if(is_active_sidebar('frontpage-content')){
			dynamic_sidebar('Frontpage Content');
		}
		elseif(has_action('custom_digressit_frontpage')){
			do_action('custom_digressit_frontpage');
		}		
		else{
			?>			
			<h2><?php _e('Congratulations on installing the Multi-site edition of Digress.it'); ?></h2>
			<p><?php _e('This is the frontpage to your Multi-site community.'); ?></p><br>
			<p><?php _e('To edit this page you can take one of the following actions:'); ?><p>
			<ul style="width: 75%">
				<li><?php _e('Login as an the admin and edit your <a href="'.bloginfo('url').'/wp-admin/options-reading.php">Reading Settings</a>. Choose "A static page" option and select the page you want to display here'); ?></li>
				<li><?php _e('You can also edit (as admin) your "<a href="'.bloginfo('url').'/wp-admin/widgets.php">Widgets</a>." Just drag your widgets to the "Frontpage Content" slot.'); ?></li>
				<li><?php _e('And for the programmatically inclined, you can use the "custom_digressit_frontpage" <a href="http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters">hook</a>'); ?></li>
			</ul>
			<?php
		}
		?>		
		</div>
		<div class="clear"></div>
	</div>
</div>
</div>
<?php get_footer(); ?>