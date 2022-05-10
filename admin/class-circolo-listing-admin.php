<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://circolo.club
 * @since      1.0.0
 *
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/admin
 * @author     Jerex Lennon <skyguyverph@gmail.com>
 */
class Circolo_Listing_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Circolo_Listing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Circolo_Listing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/circolo-listing-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Circolo_Listing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Circolo_Listing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/circolo-listing-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function create_categories_taxonomy() {
 
		// Labels part for the GUI
		
		$labels = array(
			'name' => _x( 'Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Categories' ),
			'popular_items' => __( 'Popular Categories' ),
			'all_items' => __( 'All Categories' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Category' ), 
			'update_item' => __( 'Update Category' ),
			'add_new_item' => __( 'Add New Category' ),
			'new_item_name' => __( 'New Category Name' ),
			'menu_name' => __( 'Categories' ),
			'name_admin_bar'  => __( 'Categories' ),
		); 
		
		// Now register the non-hierarchical taxonomy like tag
		
		register_taxonomy('listing_cat','circolo_listings',array(
			'hierarchical' => false,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'show_in_menu' => $this->plugin_name,
			'menu_position'       => 6,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'category' ),
		));
	}

	public function register_custom_post_types(){
		$customPostTypeArgs = array(
					'label'=>'Circolo Listings',
					'labels'=>
						array(
							'name'=>'Listings',
							'singular_name'=>'Listing',
							'add_new'=>'Add Listing',
							'add_new_item'=>'Add New Listing',
							'edit_item'=>'Edit Listing',
							'new_item'=>'New Listing',
							'view_item'=>'View Listing',
							'search_items'=>'Search Listing',
							'not_found'=>'No Listings Found',
							'not_found_in_trash'=>'No Listings Found in Trash',
							'menu_name' => 'Listings',
							'name_admin_bar'     => 'Listings',
						),
					'public'			  => true,
					'rewrite' 			  => array('slug' => 'listings'),
					'hierarchical'        => false,
        			'show_ui'             => true,
        			'show_in_menu'        => $this->plugin_name,
        			'show_in_nav_menus'   => true,
        			'show_in_admin_bar'   => true,
					'menu_position'       => 5,
					'has_archive'         => true,
					'exclude_from_search' => true,
					'publicly_queryable'  => true,
					'capability_type'     => 'post',
					'show_in_rest' => true,
					'supports'=>array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
					'taxonomies'=>array('category')
				);
		 
		// Post type, $args - the Post Type string can be MAX 20 characters
		register_post_type( 'circolo_listings', $customPostTypeArgs );
	}


	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page( 'Circolo Listing', 'Circolo', 'administrator', $this->plugin_name, array( $this, 'display_plugin_admin_dashboard' ), 
			'', 
		26 );
	}

	public function display_plugin_admin_dashboard() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/circolo-listing-admin-display.php';
	}

	public function add_custom_box() {
		$screens = [ 'circolo_listings' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				CIRCOLO_LISTING_SLUG . '_product_id',                 // Unique ID
				'Post Type',      // Box title
				array($this, 'post_type_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
			add_meta_box(
				CIRCOLO_LISTING_SLUG . '_order_id',                 // Unique ID
				'Order',      // Box title
				array($this, 'custom_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
		}
	}

	public function post_type_box_html() {
		ob_start();
        global  $post ;
        $id = $post->ID;
		$selected = get_post_meta( $id, CIRCOLO_LISTING_SLUG . '_product_id', true );
		//echo '<pre>'.print_r([$post->ID, $selected], true).'</pre>';
		$drop_down = $this->generate_products_dropdown( $selected );
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/meta-box-base.php';
        echo  ob_get_clean();
	}

	public function custom_box_html() {
		global $post;
		$value = get_post_meta( $post->ID, CIRCOLO_LISTING_SLUG . '_order_id', true );
		// Use nonce for verification to secure data sending
		wp_nonce_field( basename( __FILE__ ), 'circolo_nonce' );

		$wp_orders = wc_get_orders(array());
		//echo '<pre>'.print_r($wp_orders, true).'</pre>';
		?>

		<!-- my custom value input -->
		<input type="number" min="0" name="<?php echo CIRCOLO_LISTING_SLUG . '_order_id' ?>" value="<?php echo $value ?>">

		<?php
	}

	public function save_meta_fields( $post_id ) {

		// verify nonce
		if (!isset($_POST['circolo_nonce']) || !wp_verify_nonce($_POST['circolo_nonce'], basename(__FILE__)))
			return 'nonce not verified';
	  
		// check autosave
		if ( wp_is_post_autosave( $post_id ) )
			return 'autosave';
	  
		//check post revision
		if ( wp_is_post_revision( $post_id ) )
			return 'revision';
	  
		// check permissions
		if ( 'circolo_listings' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return 'cannot edit page';
			} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
				return 'cannot edit post';
		}

		if ( array_key_exists( 'circolo_listing_product_id', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_product_id',
				$_POST['circolo_listing_product_id']
			);
		}

		if ( array_key_exists( 'circolo_listing_order_id', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_order_id',
				$_POST['circolo_listing_order_id']
			);
		}
		
	  }

	  protected function get_post_products_custom_field( $value, $id = null )
	  {
		  $custom_field = get_post_meta( $id, $value, true );
		  
		  if ( !empty($custom_field) ) {
			  return ( is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) ) );
		  } else {
			  return false;
		  }
	  
	  }
	  
	  protected function generate_products_dropdown( $selected = array() ) : string
	  {
		  $products = Circolo_Listing_Helper::get_products();
		  $drop_down = '<select id="' . CIRCOLO_LISTING_SLUG . '_product_id" name="' . CIRCOLO_LISTING_SLUG . '_product_id" style="width: 100%">';
		  $drop_down .= '<optgroup label="Products">';
		  foreach ( $products as $product ) {
			  $drop_down .= '<option value="' . $product['ID'] . '"';
			  if ( (int) $product['ID'] === (int) $selected ) {
				  $drop_down .= ' selected="selected"';
			  }
			  $drop_down .= '>' . $product['post_title'] . ' - [#' . $product['ID'] . ']</option>';
		  }
		  $drop_down .= '</optgroup>';
		  $drop_down .= '</select>';
		  return $drop_down;
	  }
	  
	  /**
	   * @param $product_ids
	   *
	   * @return array|string
	   */
	  private function sanitize_product_ids( $product_ids )
	  {
		  
		  if ( is_array( $product_ids ) ) {
			  $return = [];
		  } else {
			  return '';
		  }
		  
		  foreach ( $product_ids as $key => $product_id ) {
			  if ( is_numeric( $product_id ) ) {
				  $return[] = $product_id;
			  }
		  }
		  return $return;
	  }

	function set_post_category( $post_id, $post, $update ) {
		// Only want to set if this is a new post!
		// if ( $update ){
		// 	return;
		// }
		
		// Only set for post_type = post!
		if ( 'circolo_listings' !== $post->post_type ) {
			return;
		}

		$product_id = get_post_meta( $post_id, CIRCOLO_LISTING_SLUG . '_product_id', true );
		
		// Get the default term using the slug, its more portable!
		//$term = get_term_by( 'slug', 'my-custom-term', 'category' );
	
		//wp_set_post_terms( $post_id, $term->term_id, 'category', true );
	}

	public function widget_area() {
			register_sidebar(
				array(
					'id' => 'circolo-listing-sidebar',
					'name' => esc_html__( 'Listing Sidebar', 'circolo_listings' ),
					'description' => esc_html__( 'Individual Listing Sidebar Contet', 'circolo_listings' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<div class="widget-title-holder"><h3 class="widget-title">',
					'after_title' => '</h3></div>'
				)
			);

			register_sidebar(
				array(
					'id' => 'circolo-listing-footer',
					'name' => esc_html__( 'Listing Footer', 'circolo_listings' ),
					'description' => esc_html__( 'Individual Listing Footer Content', 'circolo_listings' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<div class="widget-title-holder"><h3 class="widget-title">',
					'after_title' => '</h3></div>'
				)
			);
	}
}
