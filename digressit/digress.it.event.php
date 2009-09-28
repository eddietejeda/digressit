<?php

class Digress_It_Event extends Digress_It_Base{

		
	function __construct(){
		parent::__construct();
		$this->Digress_It_Event();
	}
	
	function Digress_It_Event()
	{
		
		if(isset($_REQUEST['digressit-event'])){
			header('Content-type: text/plain'); 
			header("Cache-Control: no-cache");
			header("Expires: -1");
						
			global $digressit_commentbrowser;
			switch($_REQUEST['digressit-event']):

				case 'comment_count':
					echo json_encode($digressit_commentbrowser->getAllCommentCount());
				break;

		
				case 'approved_comments':
					if($_REQUEST['current-count'] != 'null'){

						$diff = (int)$digressit_commentbrowser->getAllCommentCount() - (int)$_REQUEST['current-count'];
						if($diff > 0){
							$comments= $digressit_commentbrowser->getRecentComments( $diff, $cleaned = true );						
							echo json_encode($comments);
						}
					}
				break;
				
				case 'confirm_community_server':
					if($url = $_REQUEST['url']){
						if( substr($url, 0, 7) != 'http://' ){
							$url = 'http://'. $url;
						}						
						echo file_get_contents($url . '?digressit_action=status');
					}
				break;
				

			endswitch;

			flush();
			die();
		}
	}
}