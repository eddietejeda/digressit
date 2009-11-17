<?php
/*	
Plugin Name: digress.it
Plugin URI: http://digress.it
Description:  digress.it (the evolution of commentpress) allows readers to comment paragraph by paragraph in the margins of a text. You can use it to comment, gloss, workshop, debate and more!
Author: Eddie A Tejeda
Version: 2.3
Author URI: http://www.visudo.com
License: GPLv2 (http://creativecommons.org/licenses/GPL/2.0/)

Special thanks to:	
Matteo Bicocchi @ www.open-lab.com
The developers of JQuery @ www.jquery.com
Mark James, for the famfamfam iconset @ http://www.famfamfam.com/lab/icons/silk/
Joss Winn and Tony Hirst @ writetoreply.com
Jesse Wilbur, Ben Vershbow, Dan Visel and Bob Stein @ futureofthebook.org
*/

define("DIGRESSIT_VERSION", '2.3');
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


?>