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

		wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );

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
		
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );

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
				CIRCOLO_LISTING_SLUG . '_owner',                 // Unique ID
				'Listing Information',      // Box title
				array($this, 'owner_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
			
			add_meta_box(
				CIRCOLO_LISTING_SLUG . '_product_id',                 // Unique ID
				'Listing Type',      // Box title
				array($this, 'post_type_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
			add_meta_box(
				CIRCOLO_LISTING_SLUG . '_order_id',                 // Unique ID
				'Listing Status',      // Box title
				array($this, 'custom_box_html'),  // Content callback, must be of type callable
				$screen                            // Post type
			);

			add_meta_box(
				CIRCOLO_LISTING_SLUG . '_images',                 // Unique ID
				'Images',      // Box title
				array($this, 'images_metabox_callback'),  // Content callback, must be of type callable
				$screen                            // Post type
			);
		}
	}

	public function owner_box_html() {
		ob_start();
        global  $post ;
        $id = $post->ID;
		$selected = get_post_meta( $id, CIRCOLO_LISTING_SLUG . '_owner', true );
		//echo '<pre>'.print_r([$post->ID, $selected, $selectedCountry ], true).'</pre>';
		$drop_down = $this->generate_users_dropdown( $selected );

		$selectedCountries = get_post_meta( $id, CIRCOLO_LISTING_SLUG . '_country', true );
		//var_dump($selectedCountries);
		//$_selectedCountries = is_serialized( $selectedCountries ) ? maybe_unserialize( $selectedCountries ) : [];
		//var_dump($_selectedCountries);
		$countries_drop_down = $this->generate_countries_dropdown( $selectedCountries );
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/meta-box-users.php';
        echo  ob_get_clean();
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
		ob_start();
		global $post;
		$order_id = get_post_meta( $post->ID, CIRCOLO_LISTING_SLUG . '_order_id', true );
		$date_approved = get_post_meta( $post->ID, CIRCOLO_LISTING_SLUG . '_date_approved', true );
		$force_approved = get_post_meta( $post->ID, CIRCOLO_LISTING_SLUG . '_force_approved', true );

		$date_approved_timestamp = get_post_meta( $post->ID, CIRCOLO_LISTING_SLUG . '_date_approved_timestamp', true );
		$date_expiry_timestamp = get_post_meta( $post->ID, CIRCOLO_LISTING_SLUG . '_date_expire_timestamp', true );
		// Getting an instance of the order object
		if(is_numeric($order_id) && $order = wc_get_order( $order_id ) ) {
			if($order->is_paid())
				$paid = 'yes';
			else
				$paid = 'no';

			$order_link = '<a href="'. get_admin_url().'post.php?post='. $order_id .'&action=edit">'. $order_id .'</a>';
			echo '<p>Order ID: '. $order_link . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $paid . '</p>';
			
			if( $date_approved ) {
				$expiry_date = Circolo_Listing_Helper::calculate_expiry_date($date_approved);
				echo '<p>Date Approved: ' . $date_approved . '</p>';
				echo '<p>Date Expire: ' . $expiry_date . '</p>';
			}
		} else {
			if( $force_approved && $date_approved )
			{
				$expiry_date = Circolo_Listing_Helper::calculate_expiry_date($date_approved);
				echo '<p>Date Approved: ' . $date_approved . '</p>';
				echo '<p>Date Expire: ' . $expiry_date . '</p>';
			}else {
				echo '<p>Pending</p>';
			}
		}

		//$_approved_date = Circolo_Listing_Helper::current_time();
		//$current_timestamp = current_time( 'timestamp' );
		//echo '<pre>'.print_r($date_approved_timestamp, true).'</pre>';
		//echo '<pre>'.print_r($date_expiry_timestamp, true).'</pre>';
		//echo '<pre>'.print_r($current_timestamp, true).'</pre>';

		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/meta-box-status.php';
        echo  ob_get_clean();
		//echo '<a class="edit-timestamp" href="#">Date</a>';
		//echo '<input type="date" class="datetime listing-date-approved" name="_date_approved" value="'.$date_approved.'" />';
	}

	public function save_status_fields( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'circolo_status_nonce' ] ) && wp_verify_nonce( $_POST[ 'circolo_status_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
				return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Correct post type
		if ( 'circolo_listings' != $_POST['post_type'] ) // here you can set the post type name
			return;

		if ( array_key_exists( 'force-approve', $_POST ) ) {
			update_post_meta(
				$post_id,
					'circolo_listing_force_approved',
					1
				);
			
			$approved_date = Circolo_Listing_Helper::current_time();
			$expiry_date = Circolo_Listing_Helper::calculate_expiry_date($approved_date);
			//$expire_date = $approved_date->addDays(90);
			$approved_date_timestamp = Circolo_Listing_Helper::to_date_timestamp($approved_date);
			$expiry_date_timestamp = Circolo_Listing_Helper::to_date_timestamp($expiry_date);
			update_post_meta(
				$post_id,
				'circolo_listing_date_approved',
				$approved_date
			);

			update_post_meta(
				$post_id,
				'circolo_listing_date_expire',
				$expire_date
			);

			update_post_meta(
				$post_id,
				'circolo_listing_date_approved_timestamp',
				$approved_date_timestamp
			);

			update_post_meta(
				$post_id,
				'circolo_listing_date_expire_timestamp',
				$expiry_date_timestamp
			);
		}
	}

	public function images_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'circolo_images_nonce' ] ) && wp_verify_nonce( $_POST[ 'circolo_images_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
				return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Correct post type
		if ( 'circolo_listings' != $_POST['post_type'] ) // here you can set the post type name
			return;

		
		for ($x = 1; $x <= 3; $x++) {
			if ( array_key_exists( 'file-'.$x , $_POST ) && is_numeric( $_POST['file-'.$x] ) && absint( $_POST['file-'.$x] ) > 0 ) {
				update_post_meta(
					$post_id,
					'_image_'.$x,
					$_POST['file-'.$x]
				);
			}
		}
	}

	public function save_listing_owner_field( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'circolo_owner_nonce' ] ) && wp_verify_nonce( $_POST[ 'circolo_owner_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
				return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Correct post type
		if ( 'circolo_listings' != $_POST['post_type'] ) // here you can set the post type name
			return;

		
		if ( array_key_exists( 'circolo_listing_owner', $_POST ) ) {
			$post_info = get_post( $post_id );
			$owner = $post_info->post_author;

			if( (int) $owner != (int) $_POST['circolo_listing_owner'] ){
				update_post_meta(
					$post_id,
					'circolo_listing_owner',
					(int) $_POST['circolo_listing_owner']
				);
	
				// Update Post Author
				$arg = array(
					'ID' => $post_id,
					'post_author' => (int) $_POST['circolo_listing_owner'],
				);
				wp_update_post( $arg );
			}
			
		}

		if ( array_key_exists( 'circolo_listing_country[]', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_country',
				maybe_serialize( $_POST['circolo_listing_country[]'] )
			);
		}
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

		if ( array_key_exists( 'circolo_listing_owner', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_owner',
				$_POST['circolo_listing_owner']
			);
		}

		if ( array_key_exists( 'circolo_listing_country', $_POST ) ) {
			update_post_meta(
				$post_id,
				'circolo_listing_country',
				maybe_serialize( $_POST['circolo_listing_country[]'] )
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

	  protected function generate_users_dropdown( $selected = array() ) : string
	  {
		  $users = Circolo_Listing_Helper::get_users();
		  //var_dump($users);
		  //return '';
		  $drop_down = '<select id="' . CIRCOLO_LISTING_SLUG . '_owner" name="' . CIRCOLO_LISTING_SLUG . '_owner" style="width: 100%">';
		  $drop_down .= '<option value="">-- SELECT OWNER --</option>'; 
		  foreach ( $users as $user ) {
			  $drop_down .= '<option value="' . $user->ID . '"';
			  if ( (int) $user->ID === (int) $selected ) {
				  $drop_down .= ' selected="selected"';
			  }
			  $drop_down .= '>' . $user->display_name . ' - [ '. $user->user_email .' #' . $user->ID . ']</option>';
		  }
		  $drop_down .= '</select>';
		  return $drop_down;
	  }

	  protected function generate_countries_dropdown( $selected = array() ) : string
	  {
		  $countries = Circolo_Listing_Helper::get_countries();
		  $drop_down = '<select class="circolo-listing-countries" id="' . CIRCOLO_LISTING_SLUG . '_country" name="' . CIRCOLO_LISTING_SLUG . '_country[]" multiple="multiple">';
		  $drop_down .= '<option value="">-- SELECT COUNTRY --</option>'; 
		  foreach ( $countries as $key => $country ) {
			  $drop_down .= '<option value="' . $key . '"';
			  if ( in_array( $key, $selected ) ) { //$key === $selected
				  $drop_down .= ' selected="selected"';
			  }
			  $drop_down .= '>' . $country . '</option>';
		  }
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

	public function set_post_category( $post_id, $post, $update ) {
		// Only want to set if this is a new post!
		// if ( $update ){
		// 	return;
		// }
		
		// Only set for post_type = post!
		if ( 'circolo_listings' !== $post->post_type ) {
			return;
		}

		$product_id = get_post_meta( $post_id, CIRCOLO_LISTING_SLUG . '_product_id', true );
		
		if( $product_id ) {
			// Get the default term using the slug, its more portable!
			$categories = get_the_terms( $product_id, 'product_cat' );
			if( isset( $categories[0] ) ){
				$prod_cat = $categories[0];
				$category = Circolo_Listing_Helper::get_listing_category_by_slug($prod_cat->slug);
				wp_set_post_categories($product_id, [$category]);
			}
		}
		
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

	// Add To Cart
	public function ajax_add_to_cart() {
			$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
			$quantity = 1;
			$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
			$product_status = get_post_status($product_id);
			$categories = get_the_terms( $product_id, 'product_cat' );
			$product_category = isset( $categories[0] ) ? $categories[0] : 0;

			if ( $passed_validation && WC()->cart->add_to_cart($product_id, $quantity) && 'publish' === $product_status) {

				do_action('woocommerce_ajax_added_to_cart', $product_id);

				if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
					wc_add_to_cart_message(array($product_id => $quantity), true);
				}

				$circolo_listing = Circolo_Listing_Helper::save_new_listing($product_id, $product_category->slug);
				//echo wp_send_json($circolo_listing);
				//wp_die();

				$data = array(
					'success' => true,
					'listing' => $circolo_listing,
					'redirect_url' => site_url( 'create-a-listing' ).'/enter-listing-details/?post_id='.$circolo_listing,
				);

				echo wp_send_json($data);
			} else {

				$data = array(
					'error' => true,
					'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

				echo wp_send_json($data);
			}

			wp_die();
	}

	public function save_listing() {
		//   echo '<pre>'.print_r($_POST, true).'</pre>';
		//   echo '<pre>'.print_r($_FILES, true).'</pre>';
        //   wp_die();
		$errors = [];
        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "circolo_listing_save" && isset($_POST['pid'])) {
            //get the old post:
            $post_to_edit = get_post((int)$_POST['pid']); 
			$owner_id = get_current_user_id();

            $args = [
                'ID' => (int)$_POST['pid'],
                'post_title' => $_POST['title'],
                'post_content' => $_POST['description'],
				'post_excerpt' => $_POST['short_description'],
				'post_author'  => $owner_id,
            ];
    
            //save the edited post and return its ID
            $pid = wp_update_post($args); 

			update_post_meta(
				(int)$_POST['pid'],
				'circolo_listing_owner',
				$owner_id
			);

            //image upload
                if (!function_exists('wp_generate_attachment_metadata')){
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
                }
                if ($_FILES) {
					for( $x = 0; $x <= 4; $x++ ){
						if( isset( $_POST['file-'.$x] ) )
							update_post_meta((int)$_POST['pid'],'_image_'.$x, $_POST['file-'.$x]);
						else
							update_post_meta((int)$_POST['pid'],'_image_'.$x, "");
					}

                    foreach ($_FILES as $file => $array) {
						if( $file == 'thumbnail' )
							continue;

						if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
							//$errors[] = "upload error : " . $_FILES[$file]['error'];
							continue;
						}
		
						$fname = explode('-', $file);

                         
                        $attach_id = media_handle_upload( $file, (int)$_POST['pid'] );

						if( $attach_id > 0 && isset( $fname[1] ) && is_numeric( $fname[1] ) ) {
							update_post_meta((int)$_POST['pid'],'_image_'.$fname[1],$attach_id);
						}

						if ($attach_id > 0 && isset( $fname[1] ) && is_numeric( $fname[1] ) && $fname[1] == 0){
							//and if you want to set that image as Post  then use:
							update_post_meta((int)$_POST['pid'],'_thumbnail_id',$attach_id);
						}
                    }   
                }
                

            //if( isset( $_FILES['upload'] ) )
            //   Circolo_Listing_Helper::add_custom_image((int)$_POST['pid'], $_FILES['upload']); /*Call image uploader function*/

            if($pid && empty($errors)) {
				$data = array(
					'success' => true,
					'redirect_url' => site_url( 'create-a-listing' ).'/review-for-approval',
				);

				echo wp_send_json($data);
            } else {
				$data = array(
					'error' => true,
					'messages' => $errors
				);

				echo wp_send_json($data);
			}
        }

		wp_die();
	}

	public function images_metabox_callback() {
		ob_start();
		global  $post ;
		$id = $post->ID;
		$listingImages = [];
        for ($x = 1; $x <= 3; $x++) {
            $image = get_post_meta($id, '_image_'.$x);
            //echo "Is Error: " . is_wp_error( $image[0] );
            if( !empty($image[0]) && !is_wp_error( $image[0] ) ){
                $listingImages[$x] = $image;
            }
        }

		//echo '<pre>'.print_r($listingImages, true).'</pre>';

		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/meta-box-images.php';
		echo  ob_get_clean();
	}

	public function images_styles_scripts(){
		global $post;
		if( 'circolo_listings' != $post->post_type )
			return;

		?>  
		<style type="text/css">
		.gallery_area {
			float:right;
		}
		.image_container {
			float:left!important;
			width: 100px;
			background: url('https://i.hizliresim.com/dOJ6qL.png');
			height: 100px;
			background-repeat: no-repeat;
			background-size: cover;
			border-radius: 3px;
			cursor: pointer;
		}
		.image_container img{
			height: 100px;
			width: 100px;
			border-radius: 3px;
		}
		.clear {
			clear:both;
		}
		#gallery_wrapper {
			width: 100%;
			height: auto;
			position: relative;
			display: inline-block;
		}
		#gallery_wrapper input[type=text] {
			width:300px;
		}
		#gallery_wrapper .gallery_single_row {
			float: left;
			display:inline-block;
			width: 100px;
			position: relative;
			margin-right: 8px;
			margin-bottom: 20px;
		}
		.dolu {
			display: inline-block!important;
		}
		#gallery_wrapper label {
			padding:0 6px;
		}
		.button.remove {
			background: none;
			color: #f1f1f1;
			position: absolute;
			border: none;
			top: 4px;
			right: 7px;
			font-size: 1.2em;
			padding: 0px;
			box-shadow: none;
		}
		.button.remove:hover {
			background: none;
			color: #fff;
		}
		.button.add {
			background: #c3c2c2;
			color: #ffffff;
			border: none;
			box-shadow: none;
			width: 100px;
			height: 100px;
			line-height: 100px;
			font-size: 4em;
		}
		.button.add:hover, .button.add:focus {
			background: #e2e2e2;
			box-shadow: none;
			color: #0f88c1;
			border: none;
		}
		</style>
		<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
		<link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
		<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>
		<script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<script type="text/javascript">
			function remove_img(value) {
				var parent=jQuery(value).parent().parent();
				var img = parent.find('.gallery_img_img');
				var input = parent.find('.meta_image_url');
				//console.log("Parent: ", parent);
				//console.log("Input: ", input);
				img.attr('src',"");
				input.val(0);
				//parent.remove();
			}
			var media_uploader = null;
			function open_media_uploader_image(obj){
				media_uploader = wp.media({
					frame:    "post", 
					state:    "insert", 
					multiple: false
				});
				media_uploader.on("insert", function(){
					var json = media_uploader.state().get("selection").first().toJSON();
					var image_url = json.url;
					var image_id = json.id;
					var html = '<img class="gallery_img_img" src="'+image_url+'" height="55" width="55" onclick="open_media_uploader_image_this(this)"/>';
					console.log(image_url);
					jQuery(obj).append(html);
					jQuery(obj).find('.meta_image_url').val(image_id);
				});
				media_uploader.open();
			}
			function open_media_uploader_image_this(obj){
				media_uploader = wp.media({
					frame:    "post", 
					state:    "insert", 
					multiple: false
				});
				media_uploader.on("insert", function(){
					var json = media_uploader.state().get("selection").first().toJSON();
					//console.log("Insert: ", json);
					var image_url = json.url;
					var image_id = json.id;
					//console.log(image_url);
					jQuery(obj).attr('src',image_url);
					jQuery(obj).siblings('.meta_image_url').val(image_id);
				});
				media_uploader.open();
			}
	
			function open_media_uploader_image_plus(){
				media_uploader = wp.media({
					frame:    "post", 
					state:    "insert", 
					multiple: true 
				});
				media_uploader.on("insert", function(){
	
					var length = media_uploader.state().get("selection").length;
					var images = media_uploader.state().get("selection").models
	
					for(var i = 0; i < length; i++){
						var image_url = images[i].changed.url;
						var box = jQuery('#master_box').html();
						jQuery(box).appendTo('#img_box_container');
						var element = jQuery('#img_box_container .gallery_single_row:last-child').find('.image_container');
						var html = '<img class="gallery_img_img" src="'+image_url+'" height="55" width="55" onclick="open_media_uploader_image_this(this)"/>';
						element.append(html);
						element.find('.meta_image_url').val(image_url);
						console.log(image_url);		
					}
				});
				media_uploader.open();
			}
			jQuery(function() {
				jQuery("#img_box_container").sortable(); // Activate jQuery UI sortable feature
			});
		</script>
		<?php
	}

	
	public function add_subscribers_to_dropdown( $query_args ) {
		
		//echo '<pre>'.print_r($query_args, true).'</pre>';
		// Use this array to specify multiple roles to show in dropdown
		$query_args['role__in'] = array( 'subscriber', 'administrator' );
	
		// Use this array to specify multiple roles to hide in dropdown
		$query_args['role__not_in'] = array( 'editor' );
	
		// Unset the 'who' as this defaults to the 'author' role
		unset( $query_args['who'] );
	
		return $query_args;
	}
}
