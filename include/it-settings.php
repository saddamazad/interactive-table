<?php
if(isset($_POST['it_settings_options_submit'])){
	
	update_option( 'it_block_heading_position', $_POST['block_heading_position'] );
	update_option( 'it_block_associates_color', $_POST['block_associates_color'] );
	update_option( 'it_display_mode', $_POST['display_mode'] );
	update_option( 'it_highlight_mode', $_POST['highlight_mode'] );
	update_option( 'it_single_cell_slug_url', strtolower(str_replace(" ", "-", esc_attr($_POST['single_cell_slug_url']))) );
	update_option( 'it_read_more', $_POST['read_more'] );
	update_option( 'it_load_default_css', $_POST['load_default_css'] );
	
	echo "<div class='updated'><p>Successfully Updated</p></div>";
}
?>
<div class="wrap">
<h2 style="margin-bottom:15px;"><?php echo __('Settings'); ?></h2>
<form name="it_settings" method="post" action="edit.php?post_type=interactive_table&page=it-settings">
	<table class="form-table">
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Block Heading'); ?></th>
			<td style="padding-top:0;">
                <select name="block_heading_position">
                	<option value="Top" <?php if( get_option('it_block_heading_position') == 'Top' ) echo 'selected="selected"'; ?>>Top</option>
                	<option value="Left" <?php if( get_option('it_block_heading_position') == 'Left' ) echo 'selected="selected"'; ?>>Left</option>
                	<option value="Right" <?php if( get_option('it_block_heading_position') == 'Right' ) echo 'selected="selected"'; ?>>Right</option>
                	<option value="Bottom" <?php if( get_option('it_block_heading_position') == 'Bottom' ) echo 'selected="selected"'; ?>>Bottom</option>
                </select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Block Associates Color'); ?></th>
			<td style="padding-top:0;">
				<input type="text" name="block_associates_color" class="color-picker" value="<?php echo get_option('it_block_associates_color'); ?>" />
			</td>
		</tr>				
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Display Mode'); ?></th>
			<td style="padding-top:0;">
                <select name="display_mode">
                	<option value="Highlight" <?php if( get_option('it_display_mode') == 'Highlight' ) echo 'selected="selected"'; ?>>Highlight</option>
                	<option value="Filter" <?php if( get_option('it_display_mode') == 'Filter' ) echo 'selected="selected"'; ?>>Filter</option>
                </select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Highlight Mode'); ?></th>
			<td style="padding-top:0;">
                <select name="highlight_mode">
                	<option value="On Hover" <?php if( get_option('it_highlight_mode') == 'On Hover' ) echo 'selected="selected"'; ?>>On Hover</option>
                	<option value="On Click" <?php if( get_option('it_highlight_mode') == 'On Click' ) echo 'selected="selected"'; ?>>On Click</option>
                </select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Single cell slug URL'); ?></th>
			<td style="padding-top:0;">
            	<input type="text" name="single_cell_slug_url" value="<?php echo get_option('it_single_cell_slug_url'); ?>" />
                <br />
                <small>Re-save permalink again if you update this slug URL</small>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Read More'); ?></th>
			<td style="padding-top:0;">
                <select name="read_more">
                	<option value="Yes" <?php if( get_option('it_read_more') == 'Yes' ) echo 'selected="selected"'; ?>>Yes</option>
                	<option value="No" <?php if( get_option('it_read_more') == 'No' ) echo 'selected="selected"'; ?>>No</option>
                </select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:0;"><?php echo __('Load Default CSS'); ?></th>
			<td style="padding-top:0;">
                <select name="load_default_css">
                	<option value="Yes" <?php if( get_option('it_load_default_css') == 'Yes' ) echo 'selected="selected"'; ?>>Yes</option>
                	<option value="No" <?php if( get_option('it_load_default_css') == 'No' ) echo 'selected="selected"'; ?>>No</option>
                </select>
			</td>
		</tr>
	</table>			
	<p class="submit">
		<input type="submit" name="it_settings_options_submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
</form>	
</div>	