<?php
add_action('add_meta_boxes', 'it_cell_specification_metaboxes');
function it_cell_specification_post_box() {
	echo '<input type="hidden" name="it_cell_specification_noncename" id="it_cell_specification_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	global $post;

	?>
	<table style="width:100%;">
		<tr>
			<!--<td><?php //_e('Short Description', 'IT');?>:</td>-->
			<td>
                <?php
					$content = get_post_meta($post->ID, '_it_short_description', true);
					$editor_id = 'it_short_description';
                	wp_editor( $content, $editor_id, array( 'textarea_rows' => 5) );
				?>
            </td>
		</tr>
	</table>       
	<?php
}

function it_cell_specification_metaboxes() {
	add_meta_box('it_cell_specification', __('Short Description', 'IT'), 'it_cell_specification_post_box', 'interactive_table', 'normal', 'high');
}



add_action( 'save_post', 'it_cell_specification_add_or_save', 10, 2 );
function it_cell_specification_add_or_save($post_id, $post){
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if (!isset($_POST['it_cell_specification_noncename']) || !wp_verify_nonce($_POST['it_cell_specification_noncename'], plugin_basename(__FILE__))) {
		return $post->ID;
	}           
	
	  // Check permissions
	  if ( 'interactive_table' == $_POST['post_type'] ) 
	  {
		if ( !current_user_can( 'edit_pages', $post_id ) )
			return;
	  }    
	  
	  
	if ($_POST['it_short_description']) {
		add_post_meta($post_id, '_it_short_description', $_POST['it_short_description'], TRUE) or update_post_meta($post_id, '_it_short_description', $_POST['it_short_description']);
	} else {
		delete_post_meta($post_id, '_it_short_description');
	}
}