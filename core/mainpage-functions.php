<?php
/**
 *
 */
function digressit_mainpage_sidebar_widgets(){
    global $digressit_options;
    if(is_active_sidebar('mainpage-sidebar') && (int)$digressit_options['enable_sidebar'] != 0){
        ?>
        <div class="sidebar-widgets">
        <div id="dynamic-sidebar" class="sidebar  <?php echo $digressit_options['auto_hide_sidebar']; ?> <?php echo $digressit_options['sidebar_position']; ?>">        
        <?php digressit_get_widgets('Mainpage Sidebar'); ?>
        </div>
        </div>
        <?php
    }
}

/**
 *
 */
function digressit_mainpage_load(){
    if(digressit_is_mainpage() && !is_single()){
        add_action('add_dynamic_widget', 'digressit_mainpage_sidebar_widgets');
    }
}


/**
 *
 */
function digressit_mainpage_default_menu(){
    global $digressit_options, $post;
    ?>
    <ol class="navigation <?php echo $digressit_options['frontpage_list_style']; ?>">

     <?php
     
    if($digressit_options['front_page_menu'] == 'pages'){
        $frontpage_posts =  get_pages( $args );
    }
    else if($digressit_options['front_page_menu'] == ('custom')){
        $frontpage_posts = get_posts('post_type=digressit_type&numberposts=-1&orderby='.$digressit_options['front_page_order_by'].'&order=' . $digressit_options['front_page_order']);        
    }
    else{
        $frontpage_posts = get_posts('numberposts=-1&orderby='.$digressit_options['front_page_order_by'].'&order=' . $digressit_options['front_page_order']);
    }
    
//    var_dump(get_posts('post_type=digressit_type'));
    

    foreach($frontpage_posts as $pp){
        $comment_count = digressit_get_post_comment_count($pp->ID, null, null, null); ?>
        <li><a href="<?php echo get_permalink($pp->ID); ?>"><?php echo get_the_title($pp->ID); ?> (<?php echo $comment_count;  ?>)</a></li>
         <?php 
    }
     
     
     
    ?>
    </ol> 
    <?php digressit_mainpage_content_display($frontpage_posts); ?>
<?php
}



/**
 *
 */
class digressit_mainpage_nav_walker extends Walker_Nav_Menu{
    function start_el(&$output, $item, $depth, $args) {
        global $wp_query, $using_mainpage_nav_walker;        
        $using_mainpage_nav_walker = true;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $class_names = $value = '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = '';
        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $item_output = $args->before;
        $item_output .= '<a target="_top"'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '('.digressit_get_post_comment_count($item->object_id).')</a>';
        $item_output .= $args->after;
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

/**
 *
 */
function digressit_mainpage_content_display($frontpage_posts){ ?>
    <div class="previews">
        <div class="bubblearrow"></div>
        <div class="preview default">
        <?php
        global $digressit_options;
        $front_page_content = $digressit_options['front_page_content'];
        
        if((int)$front_page_content){
            $page = get_post($front_page_content);
            $content = $page->post_content;
            $content = apply_filters('the_content', $content);
            echo (strlen($content)) ? $content : '<p>This introduction can be changed by creating a new page titled "about"</p>';
            
        }
        else{
            $pages = get_pages();                                   
            foreach($pages as $key=>$page){ 
                if( $page->post_name == 'about'){
                    $content = $page->post_content;
                    $content = force_balance_tags(apply_filters('the_content', $content));
                    break;
                }
            }
            echo (strlen($content)) ? $content : '<p>This introduction can be changed by creating a new page titled "about"</p>';
            
        }
        ?>

        </div>

        <?php foreach($frontpage_posts as $p){ ?>
            <div class="preview">
                <?php 
                $p = (array)$p;

            
                if(isset($p['object_id'])){
                    $post_id = $p['object_id'];
                }
                else{
                    $post_id = $p['ID'];                    
                }
                $post_object =  get_post($post_id);
                $content = substr(strip_tags($post_object->post_content), 0 , 500);
                echo "<p>".$content;
                if(strlen($content) > 499){
                    echo " [...]";
                }
                echo "</p>";
                $comment_count = digressit_get_post_comment_count($post_object->ID, null, null, null);
                ?>

                <div class="comment-count">
                    <?php echo $comment_count ?> Comments
                </div>
            </div>
         <?php } ?>
        </div>
    <?php
    
}

?>