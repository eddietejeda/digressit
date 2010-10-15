<?php


add_action('widgets_init', create_function('', 'return register_widget("ListPosts");'));



class ListPosts extends WP_Widget {
	/** constructor */
	function ListPosts() {
		parent::WP_Widget(false, $name = 'Sidebar List Posts');	
	}

	function widget($args = array(), $defaults) {		
		extract( $args );
		global $post;
		$currentpost = $post;;
		
		
		if($defaults['categorize']){
			$categories=  get_categories(); 
		}
		else{
			$categories = array('1');
		}
		
		?>
		<div id="digress-it-list-posts">
		<a href="<?php echo get_option('siteurl'); ?>">
			<div class="rule-dashboard">
				<div class="rule-home-text">
					<?php echo $instance['title']; ?>
				</div>
			</div>
		</a>
		
		<div class="sidebar-optional-graphic"></div>
		<div class="sidebar-pullout"></div>
		
		<div id="searchform">
			<input id="live-post-search" class="ajax-live live-post-search comment-field-area" type="text" value="Search">
		</div>

		<?php
		foreach ($categories as $key => $cat) {
			if(isset($cat->name) && $cat->name == 'Uncategorized'){
				continue;
			}
			?>

			<?php $cat_id = null; ?>
			<?php if(isset($cat->name)): ?>
			<h3><?php echo $cat->name; ?></h3>
			<?php $cat_id = $cat->cat_ID; ?>
			<?php endif; ?>
			
			<?php

			//var_dump($cat);
			$args = array(
				'numberposts' => -1,
				'post_type' => 'post',
				'post_status' => 'publish',
				'post_type' => 'post',
				'category' => $cat_id,
				);
			$posts = get_posts($args);
			//var_dump($posts);
			?>
			<?php foreach($posts as $post ): ?>
			<?php 
			
			setup_postdata($post); 

			//TODO
			$rule_discussion_status = 'upcoming';
			if($currentpost->post_name == $post->post_name){
				$rule_discussion_status = 'current';
			}
			
			?>
			
			<?php 
			
			$commentcount = null;
			$commentcount = get_post_comment_count($post->ID);
						
			$commentbubblecolor = ($rule_discussion_status == 'current') ? '-dark' : '-grey';
			
			if($commentcount < 10){
				$commentcountclass  = 'commentcount1 sidebar-comment-count-single'.$commentbubblecolor;
			}
			else if($commentcount < 100 && $commentcount > 9){
				$commentcountclass  = 'commentcount2 sidebar-comment-count-double'.$commentbubblecolor;
			}
			else{
				$commentcountclass  = 'commentcount3 sidebar-comment-count-triple'.$commentbubblecolor;					
			}
			
			?>
			<div id="sidebar-item-<?php echo $post->ID; ?>" class="sidebar-item sidebar-<?php echo $rule_discussion_status; ?>">
				
				<span class="<?php echo $commentcountclass; ?>"><?php echo $commentcount; ; ?></span>
				
				<span class="sidebar-text"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></span>
				
			</div>
			<?php endforeach; ?>
		<?php
		}
		?>
			</div>
		<?php

    }

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];
		$instance['auto_hide'] = $new_instance['auto_hide'];
		$instance['position'] = $new_instance['position'];
		$instance['order_by'] = $new_instance['order_by'];
		$instance['order_type'] = $new_instance['order_type'];

		$instance['categorize'] = $new_instance['categorize'];
		$instance['categories'] = $new_instance['categories'];
		$instance['show_category_titles'] = $new_instance['show_category_titles'];

		return $instance;
	}



	/** @see WP_Widget::form */
	function form($instance) {				
		global $blog_id, $wpdb;

		
		$defaults = array( 	
			
			'title' => 'Posts',
			'auto_hide' => true,
			'position' => 'left',
			'order_by' => 'ID',
			'order_type' => 'DESC',
			'categorize' => true,
			'categories' => null,
			'show_category_titles' => true	
		);
		
		
		$instance['title'] = $new_instance['title'];
		$instance['auto_hide'] = $new_instance['auto_hide'];
		$instance['position'] = $new_instance['position'];
		$instance['order_by'] = $new_instance['order_by'];
		$instance['order_type'] = $new_instance['order_type'];

		$instance['categorize'] = $new_instance['categorize'];
		$instance['categories'] = $new_instance['categories'];
		$instance['show_category_titles'] = $new_instance['show_category_titles'];

		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e('Title:', 'digressit'); ?></strong></label>
			<textarea id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" style="width:100%;" ><?php echo $instance['title']; ?></textarea>
		</p>



		<p>
			<label for="<?php echo $this->get_field_id( 'auto_hide' ); ?>"><?php _e('Auto Hide?', 'digressit'); ?></label>
			<input class="checkbox" type="checkbox" <?php checked( $instance['auto_hide'], true ); ?> id="<?php echo $this->get_field_id( 'auto_hide' ); ?>" name="<?php echo $this->get_field_name( 'auto_hide' ); ?>" /> 
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'categorize' ); ?>"><?php _e('Group by Category?', 'digressit'); ?></label>
			<input class="checkbox" type="checkbox" <?php checked( $instance['categorize'], true ); ?> id="<?php echo $this->get_field_id( 'categorize' ); ?>" name="<?php echo $this->get_field_name( 'categorize' ); ?>" /> 
		</p>

		<p>
			
			<label for="<?php echo $this->get_field_id( 'categories' ); ?>"><strong><?php _e('Visible Categories', 'digressit'); ?></strong></label>
			<?php 
			$categories=  get_categories(array(    'hide_empty' => 0) ); 
//			var_dump($categories);
			foreach ($categories as $key => $cat) :
				?>
				<p><input class="checkbox" type="checkbox" <?php checked( $instance['categories'][$cat->cat_ID], true ); ?> 
							name="<?php echo $this->get_field_name( 'categories' ); ?>[<?php echo $cat->cat_ID ?>]" /> <?php echo $cat->cat_name; ?></p>
				<?php
			 ?>
			<?php endforeach; ?>
		</p>

		
		<?php
		

	}

}


?>