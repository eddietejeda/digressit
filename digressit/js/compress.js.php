<?php
include('lib/jsmin-1.1.1.php');


$ui = 'digress.it.ui.js';
$extensions = 'digress.it.extensions.js';
$digressit = 'digress.it.js';
$sidebar = 'sidebar.js';


$jsdir =  PLUGINDIR.'/digressit/js/';			

$javascript = file_get_contents($jsdir . 'jquery/ui/ui.core.js');
$javascript .= file_get_contents($jsdir . 'jquery/ui/ui.accordion.js');
$javascript .= file_get_contents($jsdir . 'jquery/ui/ui.resizable.js');
$javascript .= file_get_contents($jsdir . 'jquery/ui/ui.draggable.js');
$javascript .= file_get_contents($jsdir . 'jquery/ui/effects.core.js');
$minified =  trim(JSMin::minify($javascript));
file_put_contents($jsdir . $ui, $minified);


$javascript .= file_get_contents($jsdir . 'jquery/external/create/jquery.create.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/cookie/jquery.cookie.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/em/jquery.em.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/easing/jquery.easing.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/eventdrag/jquery.event.drag.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/mousewheel/jquery.mousewheel.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/scrollto/jquery.scrollTo.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/tooltip/jquery.tooltip.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/resize/jquery.resize.js')."\n";
//$javascript .= file_get_contents($jsdir . 'jquery/external/utils/jquery.utils.js')."\n";
$javascript .= file_get_contents($jsdir . 'jquery/external/pulse/jquery.pulse.js')."\n";

$minified =  trim(JSMin::minify($javascript));
file_put_contents($jsdir . $extensions, $minified);


$javascript .= file_get_contents($jsdir . 'digress.it.src.js')."\n";
$minified =  trim(JSMin::minify($javascript));
file_put_contents($jsdir . $digressit, $minified);






?>