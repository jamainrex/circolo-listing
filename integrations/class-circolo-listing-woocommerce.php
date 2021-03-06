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
class Circolo_Listing_WooCommerce {

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

	public function init()
    {
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
    }

	public function order_add_meta_boxes() {
		add_meta_box( 'circolo_listing_field', __('Circolo Listing','woocommerce'), array( $this, 'order_add_listing_field_html' ), 'shop_order', 'side', 'core' );
	}

	public function order_add_listing_field_html()
    {
        global $post;

        $meta_field_data = get_post_meta( $post->ID, 'circolo_listings', true ) ? get_post_meta( $post->ID, 'circolo_listings', true ) : '';
		if( !empty( $meta_field_data ) && is_numeric( $meta_field_data ) ) {
			$circolo_listing = Circolo_Listing_Helper::get_post( (int) $meta_field_data );

			if( $circolo_listing ){
				echo '<a href="'. get_admin_url().'post.php?post='. $circolo_listing['ID'] .'&action=edit">'.$circolo_listing['post_title'].' - '.'[#'.$circolo_listing['ID'].']</a>';
			}
		}
    }

	public function new_product_tab($tabs){
		// Adds the new tab

		$tabs['subscription_tab'] = [
			'label' => __('Subscription', 'circolo_listings'),
			'target' => 'additional_product_data',
			'class' => ['hide_if_external'],
			'priority' => 25,
			'callback' 	=> array( $this, 'wc_new_product_tab_content')
		];

		return $tabs;
	}

	public function new_product_tab_content() {

		// The new tab content
	
		echo '<h2>Date Range of Product Subscription</h2>';
		echo '<p>Here\'s your new product tab.</p>';
		
	}

	public function product_date_range() {
		?>
		<div class="options_group show_if_simple show_if_external hidden" >
		<p class="form-field date_range_field">
				<label for="listing_number_of">
							<?php
							echo esc_html__( 'Date range', 'circolo_listings' );
							?>
						</label>
						<span class="wrap">
							<input id="listing_number_of" placeholder="# of" class="input-text" size="6" type="number" min="1" name="_listing_nubmer_of" value="" />
							<select id="listing_by" class="select short" name="_listing_by">
								<option value="day">Day</option>
								<option value="week">Week</option>
								<option value="month">Month</option>
								<option value="year">Year</option>
							</select>
						</span>
					</p>
	</div>
		<?php
	}

	public function limit_one_per_order( $passed_validation, $product_id ) {
		// if ( 31 !== $product_id ) {
		// 	return $passed_validation;
		// }
	
		if ( WC()->cart->get_cart_contents_count() >= 1 ) {
			WC()->cart->empty_cart();
			//wc_add_notice( __( 'This product cannot be purchased with other products. Please, empty your cart first and then add it again.', 'woocommerce' ), 'error' );
			//return false;
		}
	
		return $passed_validation;
	}

	
	public function order_processed( $order_id ) {
		// Getting an instance of the order object
		$order = wc_get_order( $order_id );

		if($order->is_paid())
			$paid = 'yes';
		else
			$paid = 'no';

			// iterating through each order items (getting product ID and the product object) 
		// (work for simple and variable products)
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item['product_id']; // simple product
	
			// Get the product object
			//$product = wc_get_product( $product_id );
				
			$posts = Circolo_Listing_Helper::get_post_associated_with_product_id( $product_id );
			if( isset( $posts[0] ) ) {
				$circolo_listing = $posts[0];
				update_post_meta(
					$circolo_listing->ID,
					'circolo_listing_order_id',
					$order->get_id()
				);

				// Update the post into the database
  				wp_update_post( array(
					'ID' => $circolo_listing->ID,
					'post_status'   => 'pending',
				) );

				$order->update_meta_data( 'circolo_listings', $circolo_listing->ID );
    			$order->save();

				update_post_meta( 
					$order->get_id(), 
					'circolo_listings', 
					$circolo_listing->ID
				);
			}
		}
		// Ouptput some data
		echo '<p>Order ID: '. $order_id . ' ??? Order Status: ' . $order->get_status() . ' ??? Order is paid: ' . $paid . '</p>';
	}

	public function payment_complete( $order_id ) {
		// Getting an instance of the order object
		$order = wc_get_order( $order_id );

		if($order->is_paid())
			$paid = 'yes';
		else
			$paid = 'no';

			// iterating through each order items (getting product ID and the product object) 
		// (work for simple and variable products)
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item['product_id']; // simple product
	
			// Get the product object
			//$product = wc_get_product( $product_id );
				
			$posts = Circolo_Listing_Helper::get_post_associated_with_product_id( $product_id );
			if( isset( $posts[0] ) ) {
				$circolo_listing = $posts[0];
				update_post_meta(
					$circolo_listing->ID,
					'circolo_listing_order_id',
					$order->get_id()
				);

				$circolo_listing_status = $order->get_status() == 'completed' ? 'publish' : 'pending';
				// Update the post into the database
  				wp_update_post( array(
					'ID' => $circolo_listing->ID,
					'post_status'   => $circolo_listing_status,
				) );

				$order->update_meta_data( 'circolo_listings', $circolo_listing->ID );
    			$order->save();

				update_post_meta( 
					$order->get_id(), 
					'circolo_listings', 
					$circolo_listing->ID
				);
			}
		}
		// Ouptput some data
		echo '<p>Order ID: '. $order_id . ' ??? Order Status: ' . $order->get_status() . ' ??? Order is paid: ' . $paid . '</p>';
	}

	// define the woocommerce_update_order callback 
	public function update_order( $order_id ) { 
		// Getting an instance of the order object
		$order = wc_get_order( $order_id );

		$circolo_listing_status = $order->get_status() == 'completed' ? 'publish' : 'pending';

		// Only if order status has been set to completed AND Get Order meta Listing ID
		if( $circolo_listing_status == 'publish' && $circolo_listing_id = get_post_meta( $order->get_id(), 'circolo_listings', true ) ) {
			
			// Update the post into the database
			wp_update_post( array(
				'ID' => $circolo_listing_id,
				'post_status'   => $circolo_listing_status,
			) );

			$approved_date = Circolo_Listing_Helper::current_time();
			$expiry_date = Circolo_Listing_Helper::calculate_expiry_date($approved_date);
			$approved_date_timestamp = Circolo_Listing_Helper::to_date_timestamp($approved_date);
			$expiry_date_timestamp = Circolo_Listing_Helper::to_date_timestamp($expiry_date);

			update_post_meta(
				$circolo_listing_id,
				'circolo_listing_date_approved',
				$approved_date
			);

			update_post_meta(
				$circolo_listing_id,
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

	public function order_review() {
        ob_start();

        $cart = WC()->cart->get_cart();
        $cart_item = current($cart);
        $product_id = $cart_item['product_id'];

        $product = wc_get_product( $product_id );

        require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/shortcode-order-review.php';
        wp_reset_postdata();
    
        echo '<div class="woocommerce">' . ob_get_clean() . '</div>';
    }

	public function order_total() {
		ob_start();

        require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/checkout-order-total.php';
        echo '<div id="order_review" class="woocommerce-checkout-review-order">' . ob_get_clean() . '</div>';
	}



	public function thank_you_page( $order_id ){
		$order = wc_get_order( $order_id );
		$url = site_url( 'create-a-listing' ).'/thank-you';
		
		if ( $order->get_status() != 'failed' ) {
			echo "<script type=\"text/javascript\">window.location = '".$url."'</script>";
			wp_safe_redirect( $url );
			exit;
		}
	}
        
}
