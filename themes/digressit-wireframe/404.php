<?php
/**
 * @package Digressit
 * @subpackage Digressit_Wireframe
 */
global $using_mainpage_nav_walker, $digressit_options;
?>


<?php get_header(); ?>
<div id="container">
    <?php 
    digressit_get_dynamic_widgets();
    digressit_get_stylized_title();
    ?>

    <div id="content" role="main">
        <div id="mainpage">        
            <h2 class="title"><?php _e('404 - The Server can not find it!', 'digressit'); ?></h2>
            <div class="entry">
              <p><?php _e('The post or the page that you are looking for, is not available at this time. It could have been moved / deleted.', 'digressit'); ?></p>
              <p><?php _e('Please browse through the archives / search through the site.', 'digressit'); ?></p>
            </div>

            <div class="clear"></div>
        </div>
    </div>
</div>
<?php get_footer(); ?>