
jQuery( document ).ready(function() {
	var cols = jQuery('.heading.horizontal').attr('data-terms');
	var hLiWidth = (100/cols);

	//jQuery('.heading.horizontal li').css('width', hLiWidth+'%');
	jQuery('.column_list .horizontal th').css('width', hLiWidth+'%');
	jQuery('.column_list td').css('width', hLiWidth+'%');

	var v_cols = jQuery('.column_list.vertical').attr('data-vcols');
	var vLiWidth = (100/v_cols);

	jQuery('.column_list.vertical th').css('width', vLiWidth+'%');
	jQuery('.column_list.vertical td').css('width', vLiWidth+'%');
	
	/*if( (ajaxObj.display_mode == 'Filter') && ((ajaxObj.heading_position == 'Top') || (ajaxObj.heading_position == 'Left') || (ajaxObj.heading_position == 'Right')) ) {
		var allBtnRight = '';
		if( ajaxObj.heading_position == 'Right' ) allBtnRight = " right-align";
		jQuery('.dynamic_table_wrap').prepend('<p class="filter_all'+allBtnRight+'"><a href="javascript: void(0);">All</a></p>');
	}else if( (ajaxObj.display_mode == 'Filter') && (ajaxObj.heading_position == 'Bottom') ) {
		jQuery('.dynamic_table_wrap').append('<p class="filter_all"><a href="javascript: void(0);">All</a></p>');
	}*/
	
	jQuery( ".filter_all > a" ).on( "click", function() {
		jQuery( ".column_list tr:not(.heading)" ).show();
		jQuery( ".column_list tr.appended" ).remove();
		jQuery( ".column_list th" ).removeClass('blur');
		jQuery( ".column_list th" ).removeClass('highlight');

		jQuery( ".column_list td .v-col-content" ).show();
		jQuery( ".column_list tr .v-filtered-content" ).remove();
		jQuery( ".column_list td" ).removeClass("v-filtered");
	});
	
	jQuery( ".column_list th" ).mouseover(function() {
		if( (ajaxObj.display_mode == 'Highlight') && (ajaxObj.highlight_mode == 'On Hover') ) {
			var taxonomy = jQuery(this).attr('data-tax');
			
			jQuery( ".column_list td" ).each(function( index ) {
				//var postTax = jQuery( ".column_list li" ).attr('post-tax');
				//var postTaxArray = postTax.split(",");
				var postTax = jQuery( this ).attr('data-posttax');
				var postTaxArray = postTax.split(",");
				if(jQuery.inArray(taxonomy, postTaxArray) !== -1) {
					jQuery( this ).addClass('highlight');
				}
			});
		}else if( (ajaxObj.display_mode == 'Filter') || (ajaxObj.highlight_mode == 'On Click') ) {
			jQuery( this ).css('cursor', 'pointer');
		}
	});
	
	jQuery( ".column_list th" ).mouseout(function() {
		if( (ajaxObj.display_mode == 'Highlight') && (ajaxObj.highlight_mode == 'On Hover') ) {
			jQuery( ".column_list td" ).removeClass('highlight');
		}
	});
	
	jQuery( ".column_list th" ).click(function() {
		var heading_position = ajaxObj.heading_position;
		var highlight_mode = ajaxObj.highlight_mode;
		var v_posts_per_row = ajaxObj.v_posts_per_row;

		if( (ajaxObj.display_mode == 'Filter') && (jQuery(this).attr('data-tax') == 'all') ) {
			jQuery( ".column_list tr:not(.heading)" ).show();
			jQuery( ".column_list tr.appended" ).remove();
			jQuery( ".column_list th" ).removeClass('blur');
			jQuery( ".column_list th" ).removeClass('highlight');
	
			jQuery( ".column_list td .v-col-content" ).show();
			jQuery( ".column_list tr .v-filtered-content" ).remove();
			jQuery( ".column_list td" ).removeClass("v-filtered");
			if( (heading_position == 'Left') || (heading_position == 'Right') ) {
				jQuery( ".column_list td" ).addClass("v-filtered");
				jQuery(".v-filtered .v-col-content").removeAttr('style');
				setTimeout(function(){ jQuery(".v-filtered .v-col-content").css('transform', 'scale(1)'); }, 50);
			}
			
			if( (heading_position == 'Top') || (heading_position == 'Bottom') ) {
				jQuery( ".column_list td" ).addClass("horz-filtered");
				setTimeout(function(){ jQuery(".horz-filtered .horizontal_td_wrap").css('transform', 'scale(1)'); }, 50);
			}
		}else if( (ajaxObj.display_mode == 'Filter') && (heading_position == 'Top' || heading_position == 'Bottom') ) {
			jQuery( ".column_list th" ).removeClass('highlight');
			jQuery( ".column_list th" ).addClass('blur');
			jQuery( this ).removeClass('blur');
			jQuery( this ).addClass('highlight');
			var h_post_per_row = parseInt(jQuery( ".column_list" ).attr("data-terms"))+1; // +1 for All taxonomy filter
			//var v_posts_per_row = ajaxObj.v_posts_per_row;

			var horzRow = 1;
	
			var tax_posts = jQuery(this).attr('data-taxposts');
			var total_rows = Math.ceil(tax_posts/h_post_per_row);
			var extra_posts = (tax_posts % h_post_per_row);
			var extra_cols = 0;
			if(extra_posts > 0) {
				extra_cols = h_post_per_row - extra_posts;
			}
			
			var taxonomy = jQuery(this).attr('data-tax');

			jQuery( ".column_list tr.appended" ).remove();
			jQuery( ".column_list td" ).removeClass("horz-filtered");
			jQuery('.column_list td .horizontal_td_wrap').removeAttr('style');

			var filterTable = '';
			var counter = 0;
			var total_printed_posts = 0;

			jQuery( ".column_list td" ).each(function( index ) {
				var postTax = jQuery( this ).attr('data-posttax');
				var postTaxArray = postTax.split(",");

				if(jQuery.inArray(taxonomy, postTaxArray) !== -1) {
					if(counter == 0) filterTable += '<tr class="appended">';
					//jQuery( this ).toggleClass('highlight');
					filterTable += '<td data-posttax="'+postTax+'"><div class="filter_td_wrap">'+jQuery( this ).html()+'</div></td>';
					counter++;
					total_printed_posts++;

					if( (total_printed_posts == tax_posts) && (extra_cols > 0) && (total_rows == horzRow) && ( counter >= extra_posts) ) {
						for(var j=0; j<extra_cols; j++) {
							filterTable += '<td data-posttax="">&nbsp;</td>';
							counter++;
							total_printed_posts++;
						}
					}
					if(counter == h_post_per_row) {
						filterTable += '</tr>';
						counter = 0;
						horzRow++;
					}
				} else {
					//jQuery( this ).removeClass('highlight');
				}
			});

			jQuery( ".column_list tr:not(.heading)" ).hide();
			if( heading_position == 'Bottom' ) {
				jQuery( filterTable ).insertBefore( jQuery( ".column_list tr.heading" ) );
				setTimeout(function(){ jQuery(".appended td .filter_td_wrap").css('transform', 'scale(1)'); }, 50);
			} else {
				jQuery( filterTable ).insertAfter( jQuery( ".column_list tr.heading" ) );
				setTimeout(function(){ jQuery(".appended td .filter_td_wrap").css('transform', 'scale(1)'); }, 50);
			}
		} else if( (ajaxObj.display_mode == 'Filter') && (heading_position == 'Left' || heading_position == 'Right') ) {
			var total_rows = parseInt(jQuery( ".column_list" ).attr("data-terms"))+1;
			var num_of_posts = jQuery(this).attr('data-taxposts');
			var total_cols = 1;
			if( num_of_posts > total_rows ) {
				total_cols = Math.ceil(num_of_posts/total_rows);
			}

			jQuery( ".column_list th" ).removeClass('highlight');
			jQuery( ".column_list th" ).addClass('blur');
			jQuery( this ).removeClass('blur');
			jQuery( this ).addClass('highlight');
			
			jQuery('.column_list .v-filtered .v-col-content').removeAttr('style');
			jQuery( ".column_list td" ).removeClass("v-filtered");

			jQuery( ".column_list td .v-col-content" ).hide();
			jQuery( ".column_list td .v-filtered-content" ).remove();
			var taxonomy = jQuery(this).attr('data-tax');

			//var filterTable = '';
			var counter = 0;
			var column_switcher = 2; //first TD

			jQuery( ".column_list td" ).each(function( index ) {
				//jQuery( this ).children(".v-filtered-content").remove();
				var postTax = jQuery( this ).attr('data-posttax');
				var postTaxArray = postTax.split(",");

				if(jQuery.inArray(taxonomy, postTaxArray) !== -1) {
					//filterTable += '<div class="v-filtered">'+jQuery( this ).html()+'</div>';
					//var theRow = jQuery(".column_list tbody tr:nth-child(" + (counter+1) + ") td:nth-child(" + column_switcher + ")");
					jQuery(".column_list tbody tr:nth-child(" + (counter+1) + ") td:nth-child(" + column_switcher + ")").addClass("v-filtered");
					jQuery(".column_list tbody tr:nth-child(" + (counter+1) + ") td:nth-child(" + column_switcher + ")").append('<div class="v-filtered-content">'+jQuery( this ).children(".v-col-content").html()+'</div>');
					counter++;

					if( counter == total_rows ) {
						counter = 0;
						column_switcher++;
					}

				}
			});
			setTimeout(function(){ jQuery(".v-filtered .v-filtered-content").css('transform', 'scale(1)'); }, 50);
			setTimeout(function(){ jQuery(".v-filtered .v-col-content").css('transform', 'scale(1)'); }, 50);
			//alert(counter);
		} else if( (ajaxObj.display_mode == 'Highlight') && (highlight_mode == 'On Click') ) {
			var taxonomy = jQuery(this).attr('data-tax');
			jQuery( ".column_list td" ).removeClass('highlight');
			
			jQuery( ".column_list td" ).each(function( index ) {
				var postTax = jQuery( this ).attr('data-posttax');
				var postTaxArray = postTax.split(",");

				if(jQuery.inArray(taxonomy, postTaxArray) !== -1) {
					jQuery( this ).toggleClass('highlight');
				}/* else {
					jQuery( this ).removeClass('highlight');
				}*/
			});
		}
	});
});