<?php
if( get_option('it_single_cell_slug_url') ) {
	$single_cell_slug_url = get_option('it_single_cell_slug_url');
} else {
	$single_cell_slug_url = 'interactive-table';
}
$labels = array(
	'name' 					=> __( 'Interactive Table', 'IT' ),
	'singular_name' 		=> __( 'Interactive Table', 'IT' ),
	'menu_name'				=> _x( 'Interactive Table', 'Admin menu name', 'IT' ),
	'add_new' 				=> __( 'Add New', 'IT' ),
	'add_new_item' 			=> __( 'Add New', 'IT' ),
	'edit' 					=> __( 'Edit', 'IT' ),
	'edit_item' 			=> __( 'Edit Post', 'IT' ),
	'new_item' 				=> __( 'New Post', 'IT' ),
	'view' 					=> __( 'View Post', 'IT' ),
	'view_item' 			=> __( 'View Post', 'IT' ),
	'search_items' 			=> __( 'Search Posts', 'IT' ),
	'not_found' 			=> __( 'No Posts found', 'IT' ),
	'not_found_in_trash' 	=> __( 'No Posts found in trash', 'IT' ),
	'parent' 				=> __( 'Parent Post', 'IT' )
);
register_post_type('interactive_table', array('labels' => $labels,		
		'description' 			=> __( '', 'IT' ),
		'public' 				=> true,
		'show_ui' 				=> true,
		'capability_type' => 'post',
		'map_meta_cap'			=> true,
		'publicly_queryable' 	=> true,
		'exclude_from_search' 	=> false,
		'hierarchical' 			=> false, // Hierarchical causes memory issues - WP loads all records!
		'rewrite' => array('slug' => $single_cell_slug_url),
		'taxonomies' => array('itcategories'),
		'query_var' 			=> true,
		'supports' 				=> array('title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),
		'show_in_nav_menus' 	=> true,
	)
);

register_taxonomy( 'it_cell_category',
	apply_filters( 'interactive_table_taxonomy_objects_it_cell_category', array( 'interactive_table' ) ),
	apply_filters( 'interactive_table_taxonomy_args_it_cell_category', array(
		'hierarchical' 			=> true,
		'label' 				=> __( 'Cell Category', 'IT' ),
		'labels' => array(
				'name' 				=> __( 'Cell Category', 'IT' ),
				'singular_name' 	=> __( 'Cell Category', 'IT' ),
				'menu_name'			=> _x( 'Cell Category', 'Admin menu name', 'IT' ),
				'search_items' 		=> __( 'Search Cell Category', 'IT' ),
				'all_items' 		=> __( 'All Cell Categories', 'IT' ),
				'parent_item' 		=> __( 'Parent Cell Category', 'IT' ),
				'parent_item_colon' => __( 'Parent Cell Category:', 'IT' ),
				'edit_item' 		=> __( 'Edit Cell Category', 'IT' ),
				'update_item' 		=> __( 'Update Cell Category', 'IT' ),
				'add_new_item' 		=> __( 'Add New Cell Category', 'IT' ),
				'new_item_name' 	=> __( 'New Cell Category Name', 'IT' )
			),
		'show_ui' 				=> true,
		'show_admin_column'     => true,
		'query_var' 			=> true,
		'rewrite' => array( 'slug' => 'cell-category' ),
	) )
);