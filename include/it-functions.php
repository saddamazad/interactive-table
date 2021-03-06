<?php
function dynamic_table_shortcode( $content = null ) {
	$args = array(
		'post_type' => 'interactive_table',
		'posts_per_page' => -1,
		'orderby'   => 'menu_order',
		'order'     => 'ASC',
	);
	
	$query = new WP_Query( $args );
	
	if($query->have_posts()) {
		echo '<div class="dynamic_table_wrap">';
		$terms = get_terms( 'it_cell_category' );
		$count = 0;
		$heading_pos = get_option('it_block_heading_position');
		if( $heading_pos == 'Top' ) {
			$class = 'horizontal pos-top';
			$col_class = 'horizontal';
		} elseif( $heading_pos == 'Left' ) {
			$class = 'vertical pos-left';
			$col_class = 'vertical';
		} elseif( $heading_pos == 'Right' ) {
			$class = 'vertical pos-right';
			$col_class = 'vertical';
		} elseif( $heading_pos == 'Bottom' ) {
			$class = 'horizontal pos-bottom';
			$col_class = 'horizontal';
		} else {
			$class = '';
			$col_class = '';
		}
		
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$termOrderArr = array();
			$empOrderArr = array();
			foreach ( $terms as $term ) {
				// Get the custom fields based on the $presenter term ID  
				$cell_cat_custom_fields = get_option( "taxonomy_term_$term->term_id" );
				//echo $cell_cat_custom_fields['cell_cat_order'];
				if( $cell_cat_custom_fields['cell_cat_order'] != '' ) {
					$termOrderArr[$term->term_id] = $cell_cat_custom_fields['cell_cat_order'];
				} else {
					$empOrderArr[$term->term_id] = '';
					//continue;
				}
			}
			asort($termOrderArr);
			// merge the two arrays
			$termsArr = $termOrderArr + $empOrderArr;
			$serializedTermIds = array_keys($termsArr);
			//print_r($serializedTermIds);
		}

		$extra_count = 0;
		if(	get_option('it_display_mode') == 'Filter' ) {
			$extra_count = 1;
		}
		
		$total_posts = $query->post_count;
		$extra_cols = $total_posts%(count($terms)+$extra_count); // +1 for All taxonomy filter
		//echo $extra_cols;
		$posts_per_row = ceil($total_posts/(count($terms)+$extra_count)); // +1 for All taxonomy filter
		//echo '<br>'.$posts_per_row;
		echo '<table class="column_list '.$col_class.'" data-terms="'.count($terms).'" data-vcols="'.$posts_per_row.'">';
		$post_count = 0;
		$i = 0;
		$term_count = 1;
		$hasExtra = false;
		$skipped_post_id = '';
		$td_count = 0;
		$horzRow = 1;

		$total_rows = ceil($total_posts/(count($terms)+$extra_count)); // +1 for All taxonomy filter
		$extra_tds = count($terms) - $extra_cols;
		//$extra_columns = ceil($total_posts/count($terms));

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) && (($heading_pos == 'Top')) ) {
			echo '<tr class="heading '.$class.' terms-'.count($terms).'" data-terms="'.count($terms).'">';
			if(	get_option('it_display_mode') == 'Filter' ) {
				echo '<th data-tax="all" data-taxposts="'.$total_posts.'"><span>' . __('All', 'IT') . '</span></th>';
			}
			foreach ( $termsArr as $termId => $termOrder ) {
				$theTerm = get_term_by('id', $termId, 'it_cell_category');
				echo '<th data-tax="'.$theTerm->slug.'" data-taxposts="'.$theTerm->count.'"><span>' . $theTerm->name . '</span>'.term_description($termId, 'it_cell_category').'</th>';
				$count++;
			}
			echo '</tr>';
		}

		global $post;
		while ( $query->have_posts() ) {
			$query->the_post();

			$read_more = get_option('it_read_more');

			if( ($col_class == 'vertical') ) {
				$post_terms = array();
				$term_list = wp_get_post_terms($post->ID, 'it_cell_category', array("fields" => "all"));
				foreach($term_list as $term_single) {
					$post_terms[] = $term_single->slug;
				}
				$allTerms = join(",", $post_terms);

				if( ($post_count == 0) && ($heading_pos == 'Left') ) {
					echo '<tr>';
					if(	(get_option('it_display_mode') == 'Filter') && ($i == 0) ) {
						echo '<th data-tax="all" data-taxposts="'.$total_posts.'"><span>' . __('All', 'IT') . '</span></th>';
					} else {
						$theTerm = get_term_by('id', $serializedTermIds[$i-$extra_count], 'it_cell_category');
						echo '<th data-tax="'.$theTerm->slug.'" data-taxposts="'.$theTerm->count.'"><span>' . $theTerm->name . '</span>'.term_description($theTerm->term_id, 'it_cell_category').'</th>';
					}
					$i++;
					
					if( $hasExtra && ($skipped_post_id != '') ) {
						$skipped_post_terms = array();
						$skipped_term_list = wp_get_post_terms($skipped_post_id, 'it_cell_category', array("fields" => "all"));
						foreach($skipped_term_list as $skipped_term_single) {
							$skipped_post_terms[] = $skipped_term_single->slug;
						}
						$skippedAllTerms = join(",", $skipped_post_terms);
						if($read_more == 'No') {
							$content_post = get_post($skipped_post_id);
							$content = $content_post->post_content;
							$content = apply_filters('the_content', $content);
							$content = str_replace(']]>', ']]&gt;', $content);
							//$content = get_the_content();
							$title_link = '';
							$title_link_close = '';
						} else {
							$content = get_post_meta($skipped_post_id, '_it_short_description', true).'<br /><a href="'.get_permalink($skipped_post_id).'">'.__('Read More', 'IT').'</a>';
							$title_link = '<a href="'.get_permalink($skipped_post_id).'">';
							$title_link_close = '</a>';
						}

						echo '<td data-posttax="'.$skippedAllTerms.'"><div class="v-col-content"><span class="cell-title">' .$title_link. get_the_title($skipped_post_id) .$title_link_close. '</span>'.$content.'</div></td>';
						$post_count++;
						$td_count++;
					}
				} elseif( ($post_count == 0) && ($heading_pos == 'Right') ) {
					echo '<tr>';
					if( $hasExtra && ($skipped_post_id != '') ) {
						$skipped_post_terms = array();
						$skipped_term_list = wp_get_post_terms($skipped_post_id, 'it_cell_category', array("fields" => "all"));
						foreach($skipped_term_list as $skipped_term_single) {
							$skipped_post_terms[] = $skipped_term_single->slug;
						}
						$skippedAllTerms = join(",", $skipped_post_terms);
						if($read_more == 'No') {
							$content_post = get_post($skipped_post_id);
							$content = $content_post->post_content;
							$content = apply_filters('the_content', $content);
							$content = str_replace(']]>', ']]&gt;', $content);
							//$content = get_the_content();
							$title_link = '';
							$title_link_close = '';
						} else {
							$content = get_post_meta($skipped_post_id, '_it_short_description', true).'<br /><a href="'.get_permalink($skipped_post_id).'">'.__('Read More', 'IT').'</a>';
							$title_link = '<a href="'.get_permalink($skipped_post_id).'">';
							$title_link_close = '</a>';
						}

						echo '<td data-posttax="'.$skippedAllTerms.'"><div class="v-col-content"><span class="cell-title">' .$title_link. get_the_title($skipped_post_id) .$title_link_close. '</span>'.$content.'</div></td>';
						$post_count++;
						$td_count++;
					}
				}
	
				if($read_more == 'No') {
					$content = get_the_content();
					$title_link = '';
					$title_link_close = '';
				} else {
					$content = get_post_meta($post->ID, '_it_short_description', true).'<br /><a href="'.get_permalink().'">'.__('Read More', 'IT').'</a>';
					$title_link = '<a href="'.get_permalink($post->ID).'">';
					$title_link_close = '</a>';
				}

				// check if there is extra column and we are in the last column of the row
				if( ($extra_cols > 0) && ($post_count == ($posts_per_row-1)) && ($extra_cols >= $term_count) ) {
					echo '<td data-posttax="'.$allTerms.'"><div class="v-col-content"><span class="cell-title">' .$title_link. get_the_title() .$title_link_close. '</span>'.$content.'</div></td>';
				} elseif( ($extra_cols > 0) && ($post_count == ($posts_per_row-1)) && ($extra_cols < $term_count) ) {
					echo '<td data-posttax="">&nbsp;</td>';
					$hasExtra = true;
					$skipped_post_id = $post->ID;
				} else {
					echo '<td data-posttax="'.$allTerms.'"><div class="v-col-content"><span class="cell-title">' .$title_link. get_the_title() .$title_link_close. '</span>'.$content.'</div></td>';
				}

				$post_count++;
				$td_count++;
				
				// check if we are in the last row
				if(($extra_cols > 0) && ($term_count == (count($terms)+$extra_count))) { // +1 for All taxonomy filter
					//$next_post = get_next_post();
					$total_cols = ((count($terms)+$extra_count) * $posts_per_row); // +1 for All taxonomy filter
					// check if there is no more post and number of td is less than posts per row
					if( 1 == ($total_cols-$td_count) ) {
						echo '<td data-posttax="">&nbsp;</td>';
						$post_count++;
					}
				}

				if( ($post_count == $posts_per_row) && ($heading_pos == 'Right') ) {
					if(	(get_option('it_display_mode') == 'Filter') && ($i == 0) ) {
						echo '<th data-tax="all" data-taxposts="'.$total_posts.'"><span>' . __('All', 'IT') . '</span></th>';
					} else {
						$theTerm = get_term_by('id', $serializedTermIds[$i-$extra_count], 'it_cell_category');
						echo '<th data-tax="'.$theTerm->slug.'" data-taxposts="'.$theTerm->count.'"><span>' . $theTerm->name . '</span>'.term_description($theTerm->term_id, 'it_cell_category').'</th>';
					}
					$i++;
				}

				if( ($post_count == $posts_per_row) ) {
					echo '</tr>';
					$post_count = 0;
					$term_count++;
					//$td_count = 0;
				}
			} elseif( ($col_class == 'horizontal') ) {
				if( $post_count == 0 ) {
					echo '<tr>';
				}
				
				$post_terms = array();
				$term_list = wp_get_post_terms($post->ID, 'it_cell_category', array("fields" => "all"));
				foreach($term_list as $term_single) {
					$post_terms[] = $term_single->slug;
				}
				$allTerms = join(",", $post_terms);
				if($read_more == 'No') {
					$content = get_the_content();
					$title_link = '';
					$title_link_close = '';
				} else {
					$content = get_post_meta($post->ID, '_it_short_description', true).'<br /><a href="'.get_permalink().'">'.__('Read More', 'IT').'</a>';
					$title_link = '<a href="'.get_permalink($post->ID).'">';
					$title_link_close = '</a>';
				}
	
				echo '<td data-posttax="'.$allTerms.'"><div class="horizontal_td_wrap"><span class="cell-title">' .$title_link. get_the_title() .$title_link_close. '</span>'.$content.'</div></td>';

				$post_count++;

				if( ($extra_cols > 0) && ($total_rows == $horzRow) && ( $post_count >= $extra_cols) ) {
					for($j=0; $j<($extra_tds+1); $j++) { //$extra_tds
						echo '<td data-posttax="">&nbsp;</td>';
						$post_count++;
					}
				}
	
				if( $post_count == (count($terms)+$extra_count) ) { //count($terms)  // +1 for All taxonomy filter
					echo '</tr>';
					$post_count = 0;
					$horzRow++;
				}
			}
		}
		wp_reset_postdata();
	
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) && (($heading_pos == 'Bottom')) ) {
			echo '<tr class="heading '.$class.' terms-'.count($terms).'" data-terms="'.count($terms).'">';
			if(	get_option('it_display_mode') == 'Filter' ) {
				echo '<th data-tax="all" data-taxposts="'.$total_posts.'"><span>' . __('All', 'IT') . '</span></th>';
			}
			foreach ( $termsArr as $termId => $termOrder ) {
				$theTerm = get_term_by('id', $termId, 'it_cell_category');
				echo '<th data-tax="'.$theTerm->slug.'" data-taxposts="'.$theTerm->count.'"><span>' . $theTerm->name . '</span>'.term_description($termId, 'it_cell_category').'</th>';
				$count++;
			}
			echo '</tr>';
		}

		echo '</table>';
		//echo $td_count;
		echo '</div>';
	}
}
add_shortcode('dynamic_table', 'dynamic_table_shortcode');


// A callback function to add a custom field to our "it_cell_category" taxonomy  
function cell_category_taxonomy_custom_fields($tag) {  
   // Check for existing taxonomy meta for the term we're editing  
	$t_id = $tag->term_id; // Get the ID of the term we're editing  
	$term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check  
?>  
  
<tr class="form-field">  
	<th scope="row" valign="top">  
		<label for="cell_cat_order"><?php _e('Order'); ?></label>  
	</th>  
	<td>  
		<input type="text" name="term_meta[cell_cat_order]" id="term_meta[cell_cat_order]" size="25" style="width:60%;" value="<?php echo $term_meta['cell_cat_order'] ? $term_meta['cell_cat_order'] : ''; ?>"><br />  
		<span class="description"><?php _e('Cell Category Order.'); ?></span>  
	</td>  
</tr>  
  
<?php  
}

function add_custom_tax_field_oncreate( $term ){
	echo "<div class='form-field term-order-wrap'>";
	echo "<label for='term_meta[cell_cat_order]'>Order</label>";
	echo "<input id='term_meta[cell_cat_order]' value='' size='10' type='text' name='term_meta[cell_cat_order]'/>";
	echo '<p class="description">Cell Category Order.</p>';
	echo "<div>";
}
// Add the fields to the "it_cell_category" taxonomy, using our callback function
add_action( 'it_cell_category_add_form_fields', 'add_custom_tax_field_oncreate' );
add_action( 'it_cell_category_edit_form_fields', 'cell_category_taxonomy_custom_fields', 10, 2 );  

// A callback function to save our extra taxonomy field(s)  
function save_taxonomy_custom_fields( $term_id ) {  
    if ( isset( $_POST['term_meta'] ) ) {  
        $t_id = $term_id;  
        $term_meta = get_option( "taxonomy_term_$t_id" );  
        $cat_keys = array_keys( $_POST['term_meta'] );  
            foreach ( $cat_keys as $key ){  
            if ( isset( $_POST['term_meta'][$key] ) ){  
                $term_meta[$key] = $_POST['term_meta'][$key];  
            }  
        }  
        //save the option array  
        update_option( "taxonomy_term_$t_id", $term_meta );  
    }  
}  
// Save the changes made on the "it_cell_category" taxonomy, using our callback function
add_action( 'create_it_cell_category', 'save_taxonomy_custom_fields' );
add_action( 'edited_it_cell_category', 'save_taxonomy_custom_fields', 10, 2 );
?>