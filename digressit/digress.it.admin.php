<?php

class Digress_It_Admin extends Digress_It_Base{

		
	function __construct(){
		parent::__construct();
		$this->Digress_It_Admin();
	}
	
	function Digress_It_Admin(){
		
		
		if(is_admin() && $_REQUEST['editor'] == 'true'){
			//add_action( 'wp', array( &$this, 'load_preview_editor') );				
		}
		
	}
	
	

	
	function load_preview_editor(){
?>

<div class="ui-widget debug-message">
	<div class="ui-state-error ui-corner-all" style="padding: .5em;"> 
		<p><span class="ui-icon ui-icon-alert" style="margin-right: .3em;"></span> 
		<strong>dfdfgd<input type="submit"></strong>
		</p>
	</div>

</div>


<?
		

	}

	function process_post(){

		if($_POST['Save'] == 'Save Changes'){
			$this->save_options($_POST);
			echo '<div id="message" class="updated fade"><p>Settings saved.</p></div>';            		
		}
		
		if($_POST['Reset'] == 'Reset Settings'){
			$this->reset_options();
			echo '<div id="message" class="updated fade"><p>Settings reset.</p></div>';            
		}
	}






	

	
	/** 
	 * @description: 
	 * @todo: 
	 *
	 */
	function on_options_menu() 
	{		
		echo '<div class="wrap">';

		if($_POST['digressit'] == 'update')
		{
			$this->process_post();
		}

		?>


		<script>
		jQuery(document).ready(function(){		

			jQuery('.checkbox_selector').click(function(e){
				var name = jQuery(this).attr('name').substr(0, jQuery(this).attr('name').length - 9);

				if(jQuery(this).attr('checked')){
					jQuery('#'+name).attr('value', '1');

				}
				else{
					jQuery('#'+name).attr('value', '0');

				}
			
			});			

		});
		</script>

		<div class="wrap">
			
			<div class="icon32" id="icon-edit"><br></div><h2><?php _e("Options", 'digressit'); ?></h2>
			
			<?php 	$options  = get_option('digressit'); ?>
			<form method="post" target="_self">

			<table  style="width: 400px; float: left; text-align: left">

				<tr valign="top">
					<th scope="row"><label for="front_page_post_type">Table of Contents</label></th>
					<td><select name="front_page_post_type">
							<option value="post" '<?php (($options['front_page_post_type'] == 'post') ? " selected " : null); ?>'>Posts</option>
							<option value="page" '<?php (($options['front_page_post_type'] == 'page') ? " selected " : null); ?>'>Pages</option>
						</select>
					</td>
				</tr>


				<tr valign="top">
					<th scope="row"><label for="front_page_numberposts">Number of Posts</label></th>
					<td><input name="front_page_numberposts" type="text" size="5" value="<?php echo $options['front_page_numberposts'] ; ?>" /></td>
				</tr>


				<tr valign="top">
					<th scope="row"><label for="front_page_order">Front Page Order</label></th>
					<td><select name="front_page_order">
							<option value="ASC" '<?php echo (($options['front_page_order'] == 'ASC') ? " selected " : null) ; ?>'>Ascending</option>
							<option value="DESC" '<?php echo (($options['front_page_order'] == 'DESC') ? " selected " : null); ?>'>Descending</option>
						</select>
					</td>
				</tr>


				<tr valign="top">
					<th scope="row"><label for="front_page_order_by">Order By</label></th>
					<td><select name="front_page_order_by">
							<option value="author" '<?php echo (($options['front_page_order_by'] == 'author') ? " selected " : null); ?>'>Sort by the numeric author IDs.</option>
							<option value="category" '<?php echo (($options['front_page_order_by'] == 'category') ? " selected " : null); ?>'>Sort by the numeric category IDs.</option>
							<option value="date" '<?php echo (($options['front_page_order_by'] == 'date') ? " selected " : null); ?>'>Sort by creation date.</option>
							<option value="ID" '<?php echo (($options['front_page_order_by'] == 'ID') ? " selected " : null); ?>'>Sort by numeric post ID.</option>
							<option value="menu_order" '<?php echo (($options['front_page_order_by'] == 'menu_order') ? " selected " : null); ?>'>Sort by the menu order.</option>
							<option value="modified" '<?php echo (($options['front_page_order_by'] == 'modified') ? " selected " : null); ?>'>Sort by last modified date.</option>
							<option value="name" '<?php echo (($options['front_page_order_by'] == 'name') ? " selected " : null); ?>'>Sort by stub.</option>
							<option value="parent" '<?php echo (($options['front_page_order_by'] == 'parent') ? " selected " : null); ?>'>Sort by parent ID.</option>
							<option value="rand" '<?php echo (($options['front_page_order_by'] == 'rand') ? " selected " : null); ?>'>Randomly sort results.</option>
							<option value="title" '<?php echo (($options['front_page_order_by'] == 'title') ? " selected " : null); ?>'>Sort by title</option>
						</select>
					</td>
				</tr>
				
				
				<tr valign="top">
					<th scope="row"><label for="front_page_content">Front Page Content</label></th>
					<td><select name="front_page_content">
					 <?php
					 global $post;
					 $myposts = get_posts('numberposts=-1');
					?><option value="">--Default--</a></option><?php
					 foreach($myposts as $post): ?>
					    <option <?php  echo ($post->ID == $options['front_page_content']) ? "selected" : null; ?> value="<?php echo $post->ID ?>"><?php echo substr(get_the_title($post->ID),0, 50 ); ?></a></option>
					 <?php endforeach; ?>

						</select>
					</td>
				</tr>

				
				<tr valign="top">
					<th scope="row"><label for="testing_page">Preview Pane</label></th>
					<td><select name="testing_page">
					 <?php
					 global $post;
					 $myposts = get_posts('numberposts=10');
					?><option value="">Front Page</a></option><?php
					 foreach($myposts as $post): ?>
					    <option <?php  echo ($post->ID == $options['testing_page']) ? "selected" : null; ?> value="<?php echo $post->ID ?>"><?php echo substr(get_the_title($post->ID),0, 50 ); ?></a></option>
					 <?php endforeach; ?>

						</select>
					</td>
				</tr>
				
				
				
				<?php if( $options['installation_key']): ?>
				<tr valign="top">
					<th scope="row"><label for="registration_key">Registration Key</label></th>
					<td><input type="text" style="width:21em" readonly value="<?php echo $options['installation_key']; ?>"></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="last_sync">Last Sync</label></th>
					<td><input type="text" style="width:21em" readonly value="<?php echo date( 'F j, Y, g:i a', get_option('digressit_lastupdate')); ?>"></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="next_sync">Next Sync</label></th>
					<td><input type="text" style="width:21em" readonly value="<?php echo date('F j, Y, g:i a', get_option('digressit_nextupdate')); ?>"></td>
				</tr>
				
				<?php endif; ?>
				
				
				
				
				
				

			</table>

			<table  style="width: 200px; float: left; text-align: left">
				<!--
				<tr valign="top">
					<th scope="row"><label for="allow_text_selection">Allow Text Selection and Commenting (*EXPERIMENTAL*)</label></th>
					<td><input type="hidden" id="allow_text_selection"  name="allow_text_selection" value='<?php echo ( $options['allow_text_selection'] ? "1" : "0"  ) ?>'><input name="allow_text_selection_checkbox" class="checkbox_selector"  value="1" type="checkbox" '<?php echo ( $options['allow_text_selection'] ? " checked" : ""  ) ?>'></td>
				</tr>
				-->
				<?php if($options['default_skin'] != 'none'): ?>
				<tr valign="top">
					<th scope="row"><label for="allow_users_to_drag"><?php _e("Allow Drag", 'digressit'); ?></label></th>
					<td><input type="hidden" id="allow_users_to_drag"  name="allow_users_to_drag" value='<?php echo ( $options['allow_users_to_drag'] ? "1" : "0"  ) ?>'><input name="allow_users_to_drag_checkbox" class="checkbox_selector" value="1"  type="checkbox" '<?php echo (($options['allow_users_to_drag']) ? " checked " : null); ?>'></td>
				</tr>


<!--
				<tr valign="top">
					<th scope="row"><label for="allow_users_to_iconize"><?php _e("Allow Iconize", 'digressit'); ?></label></th>
					<td><input type="hidden" id="allow_users_to_iconize"  name="allow_users_to_iconize" value='<?php echo ( $options['allow_users_to_iconize'] ? "1" : "0"  ) ?>'><input name="allow_users_to_iconize_checkbox" class="checkbox_selector" value="1" type="checkbox" '<?php echo (($options['allow_users_to_iconize']) ? " checked " : null); ?>'></td>
				</tr>
-->
				<tr valign="top">
					<th scope="row"><label for="allow_users_to_minimize"><?php _e("Allow Minimize", 'digressit'); ?></label></th>
					<td><input type="hidden" id="allow_users_to_minimize"  name="allow_users_to_minimize" value='<?php echo ( $options['allow_users_to_minimize'] ? "1" : "0"  ) ?>'><input name="allow_users_to_minimize_checkbox" class="checkbox_selector" value="1" type="checkbox" '<?php echo (($options['allow_users_to_minimize']) ? " checked " : null); ?>'></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="allow_users_to_resize"><?php _e("Allow Resize", 'digressit'); ?></label></th>
					<td><input type="hidden" id="allow_users_to_resize"  name="allow_users_to_resize" value='<?php echo ( $options['allow_users_to_resize'] ? "1" : "0"  ) ?>'><input name="allow_users_to_resize_checkbox" class="checkbox_selector" value="1" type="checkbox" '<?php echo (($options['allow_users_to_resize']) ? " checked " : null); ?>'></td>
				</tr>
<!--
				<tr valign="top">
					<th scope="row"><label for="allow_users_to_save_position"><?php _e("Save Position", 'digressit'); ?></label></th>
					<td><input type="hidden" id="allow_users_to_save_position"  name="allow_users_to_save_position" value='<?php echo ( $options['allow_users_to_save_position'] ? "1" : "0"  ) ?>'><input name="allow_users_to_save_position_checkbox" class="checkbox_selector"  value="1" type="checkbox" '<?php echo (($options['allow_users_to_save_position']) ? " checked " : null); ?>'></td>
				</tr>
-->
				<?php endif; ?>
				
<!--				
				<tr valign="top">
					<th scope="row"><label for="frontpage_sidebar">Sidebar in Front Page</label></th>
					<td><input type="hidden"  id="frontpage_sidebar" name="frontpage_sidebar" value='<?php echo ( $options['frontpage_sidebar'] ? "1" : "0"  ) ?>'><input name="frontpage_sidebar_checkbox"  value="1" type="checkbox" class="checkbox_selector" '<?php echo ( $options['frontpage_sidebar'] ? " checked" : ""  ) ?>'></td>
				</tr>
-->				
				<tr valign="top">
					<th scope="row"><label>Enable Chrome Frame</label></th>
					<td><input type="hidden" id="enable_chrome_frame"  name="enable_chrome_frame" value='<?php echo ( $options['enable_chrome_frame'] ? "1" : "0"  ) ?>'><input name="enable_chrome_frame_checkbox" class="checkbox_selector" value="1" type="checkbox" '<?php echo (($options['allow_users_to_resize']) ? " checked " : null); ?>'></td>
				</tr>
				


				<tr valign="top">
					<th scope="row"><label >Parse list items</label></th>
					<td><input type="hidden" id="parse_list_items"  name="parse_list_items" value='<?php echo ( $options['parse_list_items'] ? "1" : "0"  ) ?>'><input name="parse_list_items_checkbox" class="checkbox_selector" value="1" type="checkbox" '<?php echo (($options['parse_list_items']) ? " checked " : null); ?>'></td>
					
				</tr>


				<?php if(is_admin()): ?>
				<tr valign="top">
					<th scope="row"><label >Debug Mode</label></th>
					<td><input type="hidden" id="debug_mode"  name="debug_mode" value='<?php echo ( $options['debug_mode'] ? "1" : "0"  ) ?>'><input name="debug_mode_checkbox" class="checkbox_selector" value="1" type="checkbox" '<?php echo (($options['debug_mode']) ? " checked " : null); ?>'></td>
				</tr>
				<?php endif; ?>


				
			</table>
			
			<p class="submit" style="clear: both;">
				<input type="hidden" name="digressit" value="update" />
				<input type="submit" name="Save" value="Save Changes" class="button" />
				<input type="submit" name="Reset" value="Reset Settings" class="button" />
			</p>
			
			<iframe style="clear:both" border="1" name="iframe" id="iframe" src ="<?php echo bloginfo('wpurl'); ?>/?p=<? echo $options['testing_page']; ?>&editor=true" width="90%" height="400px">
			  <p>Your browser does not support iframes.</p>
			</iframe>

		</div>	 
		</div>

		</form>
<?php		


	}
}
