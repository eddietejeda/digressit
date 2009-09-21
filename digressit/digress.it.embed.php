<?php


class Digress_It_Embed extends Digress_It_Post{


	function __construct(){
		parent::__construct();
		$this->Digress_It_Embed();
	}
	
	function Digress_It_Embed(){
		$start = microtime(true);

		if($_REQUEST['digressit-embed'] == 'stylesheet'):
		header('Content-type: text/plain'); 
		die();
		endif;

		if($_REQUEST['digressit-embed'] > 0):


			$paranumber = (int)$_REQUEST['digressit-embed'];

			$id = addslashes($_REQUEST['p']);
			$thepost = &get_post($id);
			
			if(!$thepost || $thepost->post_status != 'publish'){
				return;
			}
			
			$content = $thepost->post_content;
			$content = apply_filters('the_content', $content);
			$content = $this->parse_content($content, array('embed' => false) );
					
			$selected_paragraph = $content[$paranumber];
			
			$pagelinkedto = $permalink = get_permalink($thepost->ID);
			$comments = get_approved_comments($thepost->ID);
			$total_comment_count = count($comments);
			$paragraph_comment_count = 0; 
			foreach($comments as $c){
				if($c->comment_text_signature == $paranumber){
					$paragraph_comment_count++;
				}
			}

			$paragraph['content'] = strip_tags($selected_paragraph);
			$paragraph['page_comments'] = $total_comment_count;
			$paragraph['paragraph_comments'] = $paragraph_comment_count;
			$paragraph['permalink'] = $permalink."#" .$paranumber;
			
			switch($_REQUEST['format']){


				case 'text':
					header('Content-type: text/plain'); 
					echo trim($paragraph['content']);
				break;

				case 'json':
					header('Content-type: application/json'); 
					echo json_encode($paragraph);
				break;

				case 'rss':
					header('Content-type: application/xml'); 
					?>
					<rss version="2.0"
						xmlns:content="http://purl.org/rss/1.0/modules/content/"
						xmlns:dc="http://purl.org/dc/elements/1.1/"
						xmlns:atom="http://www.w3.org/2005/Atom"
						xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
						>
					<channel>
						<title>Comments on <?php bloginfo('name'); ?></title>
						<atom:link href="<?php echo htmlentities($this->current_url()); ?>" rel="self" type="application/rss+xml" />
						<link><?php bloginfo('siteurl'); ?></link>
						<description><?php bloginfo('description'); ?></description>
						<lastBuildDate><?php echo date("D M j G:i:s T Y");   ?></lastBuildDate>
						<generator><?php bloginfo('siteurl'); ?>?v=2.8.4</generator>

						<sy:updatePeriod>hourly</sy:updatePeriod>
						<sy:updateFrequency>1</sy:updateFrequency>

						<item>
							<title><?php echo htmlentities($thepost->post_title); ?> on paragraph number #<?php echo $paranumber; ?> </title>
							<link><?php echo $pagelinkedto.'#'.$paranumber; ?></link>
							<dc:creator>admin</dc:creator>
							<pubDate><?php echo date("D M j G:i:s T Y");   ?></pubDate>
							<guid isPermaLink="false"><?php bloginfo('siteurl'); ?>?p=<?php echo $id; ?>#<?php echo $paranumber; ?></guid>
							<description><![CDATA[<?php echo strip_tags($paragraph['content']); ?>]]></description>
							<content:encoded><![CDATA[<?php echo $paragraph['content']; ?>]]></content:encoded>
						</item>
						
					</channel>
					</rss>
					<?php


				break;


				case 'xml':
				header('Content-type: text/xml'); 				
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				?>
				<paragraph>
					<permalink><?php echo htmlentities($paragraph['permalink']); ?></permalink>
					<page_comments><?php echo $paragraph['page_comments']; ?></page_comments>
					<content><![CDATA[<?php echo $paragraph['page_comments']; ?>]]></content>						
				</paragraph>
				<?php

				break;
			
				case 'html':
				default:
					header('Content-type: text/html'); 
					$paranumber++;
					echo "<div id='selected_paragraph'>". $paragraph['content'] . '</div>';
				break;
			
			
			}
			?>
			<?php

			$this->debugtime[]['print_embed_code_js'] = microtime(true) - $start;
			die();
		endif;
	
	}
}