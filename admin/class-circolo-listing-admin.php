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
					'public'			  =>false,
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
				'circolo_listing_product',                 // Unique ID
				'Post Type',      // Box title
				array($this, 'post_type_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
			add_meta_box(
				'circolo_listing_order',                 // Unique ID
				'Order',      // Box title
				array($this, 'custom_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
		}
	}

	public function post_type_box_html() {
		global $post;
		$value = get_post_meta( $post->ID, 'circolo_listing_product', true );
		// Use nonce for verification to secure data sending
		wp_nonce_field( basename( __FILE__ ), 'circolo_nonce' );

		?>

		<!-- my custom value input -->
		<input type="number" min="0" name="circolo_listing_product_value" value="<?php echo $value ?>">

		<?php
	}

	public function custom_box_html() {
		global $post;
		$value = get_post_meta( $post->ID, 'circolo_listing_order', true );
		// Use nonce for verification to secure data sending
		wp_nonce_field( basename( __FILE__ ), 'circolo_nonce' );

		$wp_orders = wc_get_orders(array());
		echo '<pre>'.print_r($wp_orders, true).'</pre>';
		?>

		<!-- my custom value input -->
		<input type="number" min="0" name="circolo_listing_order_value" value="<?php echo $value ?>">

		<?php
	}

	function save_meta_fields( $post_id ) {

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
	  
		if ( array_key_exists( 'circolo_listing_order_value', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_order',
				$_POST['circolo_listing_order_value']
			);
		}

		if ( array_key_exists( 'circolo_listing_product_value', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_product',
				$_POST['circolo_listing_product_value']
			);
		}

		
	  }
}
