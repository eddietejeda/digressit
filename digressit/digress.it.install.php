<?php
/*	
Plugin Name: digress.it
Plugin URI: http://digress.it
Description:  digress.it (the evolution of commentpress) allows readers to comment paragraph by paragraph in the margins of a text. You can use it to comment, gloss, workshop, debate and more!
Author: Eddie A Tejeda
Version: 2.1.7
Author URI: http://www.visudo.com
License: GPLv2 (http://creativecommons.org/licenses/GPL/2.0/)

Special thanks to:	
Matteo Bicocchi @ www.open-lab.com
The developers of JQuery @ www.jquery.com
Mark James, for the famfamfam iconset @ http://www.famfamfam.com/lab/icons/silk/
Joss Winn and Tony Hirst @ writetoreply.com
Jesse Wilbur, Ben Vershbow, Dan Visel and Bob Stein @ futureofthebook.org
*/

define("DIGRESSIT_VERSION", '2.1.7');
define("DIGRESSIT_COMMUNITY", 'digress.it');
define("DIGRESSIT_COMMUNITY_HOSTNAME", 'digress.it');



include_once('digress.it.base.php');
include_once('digress.it.post.php');
include_once('digress.it.commentbrowser.php');

$digressit_post = new Digress_It_Post();
$digressit_commentbrowser = new Digress_It_CommentBrowser();

if($_REQUEST['digressit-event']){
	include_once('digress.it.event.php');	
	$digressit_events = new Digress_It_Event();
}
if($_REQUEST['digressit-embed']){
	include_once('digress.it.embed.php');	
	$digressit_embed = new Digress_It_Embed();
}

include_once('digress.it.admin.php');
include_once('digress.it.widget.php');
include_once('digress.it.community.php');


$digressit_admin = new Digress_It_Admin();
$digressit_widget = new Digress_It_Widget();
$digressit_community = new Digress_It_Community();


register_activation_hook(__FILE__,  array($digressit_post,'on_activation') );
register_deactivation_hook(__FILE__,  array($digressit_post,'on_deactivation') );



if('digressit' == $_GET['plugin']){
	$mu_plugins_dir = WP_CONTENT_DIR . '/mu-plugins/';
	$digressit_mu_target = WP_CONTENT_DIR . '/plugins/digressit/digress.it.mu.install.php';
	$digressit_mu_link = WP_CONTENT_DIR . '/mu-plugins/digress.it.mu.install.php';

	if ( 'activate' == $_GET['action'] ) {
        check_admin_referer( 'activate-sitewide-plugin' );
		if($digressit_post->is_multiuser){
			if(is_writable( $mu_plugins_dir)){
				if(is_link($digressit_mu_link)){
					//we're good
				}
				elseif(!file_exists($digressit_mu_link)){
					if(symlink($digressit_mu_target, $digressit_mu_link)){
						//we're good
					}
					else{
						die( "There was a error creating the symlink of <b>$digressit_mu_target</b> in <b>$digressit_mu_link</b>. If the server doesn't have write permission try creating it manually");
					}
				}
				else{
					die( "There was a error creating the symlink of <b>$digressit_mu_target</b> in <b>$digressit_mu_link</b>. Maybe a theme named <b>digressit</b> already exists?");					
				}
			}
			else{
				die("Error: The webserver does not have write permission on <b>$mu_plugins_dir</b>. You can give the give the server user account temporary write permission on this directory or copy the file <b>$digressit_mu_target</b> to <b>$digressit_mu_link</b>.  ");
			}
		}
		        
	} else if ( 'deactivate' == $_GET['action'] ) {
       
	        check_admin_referer( 'deactivate-sitewide-plugin' );

			unlink($digressit_mu_link);

		}
}
	









?>