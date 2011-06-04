<?php

/**
 *
 */
class Digress_It_Embed{

	function __construct(){

		if(isset($_REQUEST['digressit-embed']) && $_REQUEST['digressit-embed'] == 'stylesheet'){
			header('Content-type: text/plain'); 
			die();
		}

		if(isset($_REQUEST['digressit-embed']) && $_REQUEST['digressit-embed'] > 0){
			$paranumber = (int)$_REQUEST['digressit-embed'] - 1;

			$id = addslashes($_REQUEST['p']);
			$thepost = &get_post($id);
			
			if(!$thepost || $thepost->post_status != 'publish'){
				return;
			}
			
			$content = $thepost->post_content;
			$content = apply_filters('the_content', $content);
			$content = standard_digressit_content_parser($content, 'div|table|object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8', true);
					
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
						<title><?php bloginfo('name'); ?> : <?php echo $thepost->post_title; ?> : paragraph <?php echo $paranumber; ?></title>
						<atom:link href="<?php echo $paragraph['permalink']; ?>" rel="self" type="application/rss+xml" />
						<link><?php bloginfo('siteurl'); ?></link>
						<description><?php bloginfo('description'); ?></description>
						<lastBuildDate><?php echo date("D M j G:i:s T Y");   ?></lastBuildDate>
						<generator><?php bloginfo('siteurl'); ?>?v=2.8.4</generator>

						<sy:updatePeriod>hourly</sy:updatePeriod>
						<sy:updateFrequency>1</sy:updateFrequency>

						<item>
							<title><?php echo htmlentities($thepost->post_title); ?> (paragraph <?php echo $paranumber; ?>) </title>
							<link><?php echo $paragraph['permalink']; ?></link>
							<dc:creator>admin</dc:creator>
							<pubDate><?php echo date("D M j G:i:s T Y");   ?></pubDate>
							<guid isPermaLink="false"><?php echo $paragraph['permalink']; ?></guid>
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
					<content><![CDATA[<?php echo $paragraph['content']; ?>]]></content>						
				</paragraph>
				<?php

				break;
			
				case 'html':
				default:
					header('Content-type: text/html'); 
					$paranumber++;
					echo "<blockquote cite='".$paragraph['permalink']."'>". $paragraph['content'] . '</blockquote>';
				break;
			
			
			}
			?>
			<?php

			die();
		}
	
	}
}