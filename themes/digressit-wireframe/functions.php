<?php
/**
 * @package Digressit
 * @subpackage Digressit_Wireframe
 */

if('wp-signup.php' == basename($_SERVER['SCRIPT_FILENAME'])){
    add_action('wp_head', 'digressit_wp_signup');
    
    function digressit_wp_signup(){
    ?>
    <style>
    #content{
        margin: -32px auto 0 !important;
    }
    </style>
    <?php
    }    
}

?>