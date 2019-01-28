<?php
/*
Plugin Name: Interactive Table
Plugin URI: http://www.rmweblab.com
Description: A dynamic, clean & responsive table for your contents.
Author: RM Web Lab
Version: 1.1.0
Author URI: http://rmweblab.com
*****/ 

class Interactive_Table {

    /* Constructor for the class */
    function __construct() {
        add_action('init', array(&$this, 'define_constants'), 8);
        add_action('init', array(&$this, 'register_it_custom_post_type'), 10);

		/* Add admin menu */
		add_action('admin_menu', array(&$this, 'it_settings_page'));

        add_action('init', array(&$this, 'load_functions'), 10);
		add_action('wp_enqueue_scripts', array(&$this, 'it_print_scripts'), 10);
		add_action( 'wp_head', array(&$this, 'it_dynamic_styles') );
		/*interactive table post details page*/
		add_filter('single_template', array(&$this, 'get_interactive_table_post_type_template'));

    }


    /**
     * Define plugin constants
     */
    public function define_constants() {	
		// Define contants
		define('INTERACTIVE_TABLE_ROOT', dirname(__FILE__));
		define('INTERACTIVE_TABLE_URL', plugins_url( 'interactive-table/' ));
    }

    /**
     * Plugins settings page
     */
    public function it_settings_page() {
		add_submenu_page( 'edit.php?post_type=interactive_table', 'Settings', 'Settings', 'manage_options', 'it-settings', array(&$this, 'it_settings_plug_page'));
	}
	
    /**
     * Plugins settings page
     */
    public function it_settings_plug_page() {
		require_once( INTERACTIVE_TABLE_ROOT . '/include/it-settings.php');
	}			

    /**
     * Include all the required functions
     *
     */
    public function load_functions() {			
        $include_path = INTERACTIVE_TABLE_ROOT . '/include/';		
		include_once($include_path . 'it-functions.php');
		include_once($include_path . 'it-metaboxes-config.php');
    }

    /**
     * Register Interactive Table CPT
     *
     */
	public function register_it_custom_post_type() {
        $include_path = INTERACTIVE_TABLE_ROOT . '/include/';		
		include_once($include_path . 'it-custom-post-type.php');
	}
	
    /**
     * InteractiveTable ajax script load.
     */	
	public function it_print_scripts() {
		wp_enqueue_script('jquery');
		
		$ajaxurl = admin_url('admin-ajax.php');
		$ajax_nonce = wp_create_nonce('InteractiveTable');
		$display_mode = get_option('it_display_mode');
		$highlight_mode = get_option('it_highlight_mode');
		$heading_position = get_option('it_block_heading_position');
		$args = array(
			'post_type' => 'interactive_table',
			'posts_per_page' => -1,
			'orderby'   => 'menu_order',
			'order'     => 'ASC',
		);
		
		$query = new WP_Query( $args );
		
		if($query->have_posts()) {
			$total_posts = $query->post_count;
			$terms = get_terms( 'it_cell_category' );
			$posts_per_row = ceil($total_posts/count($terms));
			/*while ( $query->have_posts() ) {
				$query->the_post();
			}
			wp_reset_postdata();*/
		}
		wp_localize_script( 'jquery', 'ajaxObj', array( 'ajaxurl' => $ajaxurl, 'ajax_nonce' => $ajax_nonce, 'display_mode' => $display_mode, 'heading_position' => $heading_position, 'v_posts_per_row' => $posts_per_row, 'highlight_mode' => $highlight_mode ) );
		
		if( get_option('it_load_default_css') != 'No' ) {
			wp_enqueue_style( 'table-style', plugins_url('/css/style.css', __FILE__ ) );
		}
		wp_enqueue_script( 'table-script', plugins_url('/js/it-script.js', __FILE__ ) );
	}
	
    /**
     * InteractiveTable load dynamic styles.
     */	
	public function it_dynamic_styles() {
	?>
		<style type="text/css">
			.column_list .highlight { background-color: <?php echo get_option('it_block_associates_color'); ?>; }
		</style>
    <?php
	}

    /**
     * InteractiveTable load single post template.
     */	
	public function get_interactive_table_post_type_template($single_template) {
		 global $post;
	
		 if ($post->post_type == 'interactive_table') {
			  $single_template = dirname( __FILE__ ) . '/templates/single-interactive_table.php';
		 }
		 return $single_template;
	}	
}


function color_picker_assets($hook_suffix) {
	// $hook_suffix to apply a check for admin page.
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'field-color-picker', plugins_url('/js/Field_Color.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'color_picker_assets' );


global $Interactive_Table;
$Interactive_Table = new Interactive_Table();