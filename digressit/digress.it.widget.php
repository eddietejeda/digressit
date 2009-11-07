<?php
class Digress_It_Widget extends Digress_It_Base{

	
	/* 
	 * @description: attach the Wordpress hooks to their respective class methods
	 */
	function __construct(){
		parent::__construct();
		$this->Digress_It_Widget();
	}
	
	function Digress_It_Widget()
	{
		add_action('wp_insert_post', array(&$this, 'on_insert_post'));
		add_action('plugins_loaded', array(&$this, 'dashboard_widget'));		
		add_action('admin_menu', array(&$this, 'load_meta_box'));		
	}
	
	function metabox() {
		global $post;


		if(!get_option('digressit_enabled_' . $post->ID)){
			add_option('digressit_enabled_' . $post->ID, 'publish');
		}

		


		$digressit_enabled = get_option('digressit_enabled_' . $post->ID);

		?>
		<p><input type="radio" value="publish" <?php echo ($digressit_enabled == 'publish') ? 'checked' : ''; ?> name="digressit_enabled" /> <?php _e('Enabled when published','digressit') ?></p>
		<p><input type="radio" value="private" <?php echo ($digressit_enabled == 'private') ? 'checked' : ''; ?> name="digressit_enabled" /> <?php _e('Enabled when set to private','digressit') ?></p>
		<p><input type="radio" value="draft" <?php echo ($digressit_enabled == 'draft') ? 'checked' : ''; ?>  name="digressit_enabled" /> <?php _e('Enabled when set to draft','digressit') ?></p>
		
		<?php
	}

	function load_meta_box() {
		add_meta_box('digressit','Digress.it',array(&$this, 'metabox'), 	'post', 'normal', 'high');
		
	}

	function on_insert_post($revisionid) {

		$postid = wp_is_post_revision($revisionid) ? wp_is_post_revision($revisionid) : $revisionid;	

		update_option('digressit_enabled_' . $postid,$_POST['digressit_enabled']);

	}
	


	// Class initialization
	function dashboard_widget() {
		if (isset($_GET['show_digressit_widget'])) {
			if ($_GET['show_digressit_widget'] == "true") {
				update_option( 'show_digressit_widget', 'noshow' );
			} else {
				update_option( 'show_digressit_widget', 'show' );
			}
		} 

		// Add the widget to the dashboard
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}

	// Register this widget -- we use a hook/function to make the widget a dashboard-only widget
	function register_widget() {
		//wp_register_sidebar_widget( 'digressit_posts', __( 'DigressIt Posts', 'digressit-posts' ), array(&$this, 'widget'), array( 'all_link' => 'http://digressit.org/', 'feed_link' => 'http://digressit.org/feed/', 'edit_link' => 'options.php' ) );
	}

	// Modifies the array of dashboard widgets and adds this plugin's
	function add_widget( $widgets ) {
		global $wp_registered_widgets;
		if ( !isset($wp_registered_widgets['digressit_posts']) ) return $widgets;
		array_splice( $widgets, 2, 0, 'digressit_posts' );
		return $widgets;
	}

	function widget($args = array()) {
		$show = get_option('show_digressit_widget');
		if ($show != 'noshow') {
			if (is_array($args))
				extract( $args, EXTR_SKIP );
			echo $before_widget.$before_title.$widget_name.$after_title;
			echo '<a href="http://digressit.org/"><img style="margin: 0 0 5px 5px;" src="http://digressit.org/images/digress-logo-rss.png" align="right" alt="DigressIt"/></a>';
			include_once(ABSPATH . WPINC . '/rss.php');
			$rss = fetch_rss('http://digressit.org/feed/');
			if ($rss) {
				$items = array_slice($rss->items, 0, 2);
				if (empty($items)) 
					echo '<li>No items</li>';
				else {
					foreach ( $items as $item ) { ?>
					<a style="font-size: 14px; font-weight:bold;" href='<?php echo $item['link']; ?>' title='<?php echo $item['title']; ?>'><?php echo $item['title']; ?></a><br/> 
					<p style="font-size: 10px; color: #aaa;"><?php echo date('j F Y',strtotime($item['pubdate'])); ?></p>
					<p><?php echo substr($item['summary'],0,strpos($item['summary'], "This is a post from")); ?></p>
					<?php }
				}
			}
			echo $after_widget;
		}
	}	
}
