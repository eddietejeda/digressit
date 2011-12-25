<?php
global $blog_id;

/* 
 * Enable the sidebars widgets
 */
if ( function_exists('register_sidebar') ) {

    if(is_multisite() && ($blog_id == 1)){
    
        register_sidebar(array(
            'id' => 'frontpage-content',        
            'name' => 'Frontpage Content',
            'before_widget' => '<div id="%1$s-content" class="%2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));
    }

    register_sidebar(array(
        'id' => 'mainpage-content',        
        'name' => 'Mainpage Content',
        'before_widget' => '<div id="%1$s-content" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'single-content',        
        'name' => 'Single Content',
        'before_widget' => '<div id="%1$s-content" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'page-content',        
        'name' => 'Page Content',
        'before_widget' => '<div id="%1$s-content" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));



    if(is_multisite() && ($blog_id == 1)){
        register_sidebar(array(
            'name' => 'Frontpage Sidebar',
            'id' => 'frontpage-sidebar',        
            'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));
    }

    register_sidebar(array(
        'name' => 'Mainpage Sidebar',
        'id' => 'mainpage-sidebar',        
        'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ))    ;

    register_sidebar(array(
        'id' => 'single-sidebar',        
        'name' => 'Single Sidebar',
        'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'page-sidebar',        
        'name' => 'Page Sidebar',
        'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));


    if(is_multisite() && ($blog_id == 1)){
    
        register_sidebar(array(
            'id' => 'frontpage-topbar',        
            'name' => 'Frontpage Topbar',
            'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));
    }

    register_sidebar(array(
        'id' => 'mainpage-topbar',        
        'name' => 'Mainpage Topbar',
        'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'single-topbar',        
        'name' => 'Single Topbar',
        'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'page-topbar',        
        'name' => 'Page Topbar',
        'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));
}





/* 
 * A simple widget that displays coment browser links
 */
class CommentBrowserLinks extends WP_Widget {
    /** constructor */
    function CommentBrowserLinks() {
        parent::WP_Widget(false, $name = 'Comment Browser Links');    
    }

    function widget($args = array(), $defaults) {        
        extract( $args );
        global $digressit_options;
        ?>
        <h4><?php _e('Comment Browser', 'digressit'); ?></h4>
        <ul>
            <li><a href="<?php bloginfo('url'); ?>/comments-by-section"><?php echo $digressit_options['comments_by_section_label']; ?></a></li>
            <li><a href="<?php bloginfo('url'); ?>/comments-by-contributor"><?php echo $digressit_options['comments_by_users_label']; ?></a></li>
            <li><a href="<?php bloginfo('url'); ?>/general-comments"><?php echo $digressit_options['general_comments_label']; ?></a></li>
            <?php do_action('add_commentbrowser_link'); ?>
        </ul>
        <?php
    }

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {                
        global $blog_id, $wpdb;
        return $instance;
    }

}


/**
 * A simple search form widget
 */
class LiveContentSearch extends WP_Widget {
    function LiveContentSearch() {
        parent::WP_Widget(false, $name = 'Live Content Search');    
    }

    function widget($args = array(), $defaults) {        
        extract( $args );
        global $post;
        ?>
        <div id="searchform">
            <input id="live-content-search" class="ajax-live live-content-search comment-field-area" type="text" value="<?php _e('Search'); ?>">
        </div>
        <?php
    }

}


/* 
 * A simple widget that lists the posts
 */
class ListPostsWithCommentCount extends WP_Widget {
    function ListPostsWithCommentCount() {
        parent::WP_Widget(false, $name = 'List Posts with Comment Count');    
    }

    function widget($args = array(), $defaults) {        
        extract( $args );
        global $post, $digressit_options;
        $currentpost = $post;
        
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
                    <?php echo $defaults['title']; ?>
                </div>
            </div>
        </a>
        
        <div class="sidebar-optional-graphic"></div>
        <div class="sidebar-pullout"></div>

        <?php
        $section_number = 1;
        foreach ($categories as $key => $cat) {
            if(isset($cat->name) && $cat->name == 'Uncategorized'){
                //continue;
            }
            ?>

            <?php $cat_id = null; ?>
            <?php if(isset($cat->name)): ?>
            <h3><?php echo $cat->name; ?></h3>
            <?php $cat_id = $cat->cat_ID; ?>
            <?php endif; ?>
            
            <?php
            $args = array(
                'numberposts' => -1,
                'post_type' => 'post',
                'post_status' => 'publish',
                'post_type' => 'post',
                'order_by' => $digressit_options['front_page_order_by'],
                'order' => $digressit_options['front_page_order'],
                'category' => $cat_id,
                );
                                
            $posts = get_posts($args);
            ?>
            <?php foreach($posts as $post ): ?>
            <?php 
            
            setup_postdata($post); 

            //@TODO
            $rule_discussion_status = 'upcoming';
            if($currentpost->post_name == $post->post_name){
                $rule_discussion_status = 'current';
            }
            
            ?>
            
            <?php 
            
            $sidebar_number = null;
            if(isset($digressit_options['show_comment_count_in_sidebar'] ) && (int)$digressit_options['show_comment_count_in_sidebar'] == 0){
                $sidebar_number = $section_number;
                $commentcountclass  = 'section-number';                    
                $section_number++;
            }
            else{            
                $commentcount = null;
                $commentcount = digressit_get_post_comment_count($post->ID);
                        
                $commentbubblecolor = ($rule_discussion_status == 'current') ? '-dark' : '-grey';
            
                if($commentcount < 10){
                    $commentcountclass  = 'commentcount commentcount1 sidebar-comment-count-single'.$commentbubblecolor;
                }
                else if($commentcount < 100 && $commentcount > 9){
                    $commentcountclass  = 'commentcount commentcount2 sidebar-comment-count-double'.$commentbubblecolor;
                }
                else{
                    $commentcountclass  = 'commentcount commentcount3 sidebar-comment-count-triple'.$commentbubblecolor;                    
                }
                $sidebar_number = $commentcount;
            }
            ?>
            <div id="sidebar-item-<?php echo $post->ID; ?>" class="sidebar-item sidebar-<?php echo $rule_discussion_status; ?>">
                <span class="<?php echo $commentcountclass; ?>"><?php echo $sidebar_number; ; ?></span>
                <span class="sidebar-text"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></span>
            </div>
            <?php endforeach; ?>
        <?php
        }
        ?>
            </div>
        <?php

    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        //var_dump($new_instance);
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
        
        
        $instance = wp_parse_args( (array) $instance, $defaults ); 
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e('Title:', 'digressit'); ?></strong></label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo $instance['title']; ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" style="width:100%;" >
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'categorize' ); ?>"><?php _e('Categorize?', 'digressit'); ?></label>
            <input class="checkbox" type="checkbox" <?php echo ($instance['categorize'] == 'on') ? " checked " : ""; ?> id="<?php echo $this->get_field_id( 'categorize' ); ?>" name="<?php echo $this->get_field_name( 'categorize' ); ?>" /> 
        </p>
        <?php
        

    }

}

?>