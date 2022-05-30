<?php

use CIRCOLO\Circolo_Listing_Restrict_Content;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * 
 * @link       https://circolo.club
 * @since      1.0.0
 *
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/public
 * @author     Jerex Lennon <skyguyverph@gmail.com>
 */
class Circolo_Listing_Public {

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

	private  $should_track_pageview ;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function init()
    {
        Circolo_Listing_Shortcodes::register_shortcodes();
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/circolo-listing-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/circolo-listing-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'circolo_ajax', array(
			'url' => admin_url( 'admin-ajax.php' )
		) );
	}

	/**
     * @param $unfiltered_content
     *
     * @return string
     */
    public function restrict_content( $unfiltered_content ) : string
    {
        //ensure that our filter only runs one time
        if ( !in_the_loop() && !is_singular() && !is_main_query() && in_array( get_post_type(), Circolo_Listing_Helper::get_post_types() ) ) {
            //echo '<pre>'.print_r('Here!', true).'</pre>';
			return $unfiltered_content;
        }
        
        if ( !Circolo_Listing_Helper::is_protected() ) {
			//echo '<pre>'.print_r('Here2!', true).'</pre>';
            return $unfiltered_content;
        }
        
        //check and see if inline shortcode exists, if it does skip
        
        if ( strpos( $unfiltered_content, '[/wc-circolo-listing-inline]' ) ) {
            // Handle shortcode access
            return $unfiltered_content;
        } elseif ( strpos( $unfiltered_content, '[products ids=' ) ) {
            //check and see if the products shortcode exists, if it does skip.  This is to account for Elementor Elements being protected
            return $unfiltered_content;
        } else {
            $restrict = new Circolo_Listing_Restrict_Content();
            $show_paywall = apply_filters( 'wc_circolo_listing_force_bypass_paywall', $restrict->can_user_view_content() );
			//var_dump( $show_paywall );
            if ( $show_paywall == false ) {
                return $restrict->show_content( $unfiltered_content );
            }
            remove_filter( current_filter(), __FUNCTION__ );
            return $restrict->show_paywall( $unfiltered_content );
        }
    
    }

	public function set_product_ids()
    {
        global $product_id;
        $product_id = get_post_meta( get_the_ID(), CIRCOLO_LISTING_SLUG . '_product_id', true );
    }

	public function restrict_listing()
    {
		global $wp_query;
		//ensure that our filter only runs one time
        if ( in_array( get_post_type(), Circolo_Listing_Helper::get_post_types() ) ) {

            $restrict = new Circolo_Listing_Restrict_Content();

			if( !$restrict->check_if_logged_in() ){
				wp_redirect( 'home');
				exit();
			}

			$has_access = apply_filters( 'wc_circolo_listing_force_bypass_paywall', $restrict->can_user_view_content() );
			if ( $has_access == false ) {
				// $wp_query->set_404();
				// status_header( 404 );
				// get_template_part( 404 );
				// exit();
			}
        }

        
    }

	public function should_disable_comments()
    {
        $is_protected = Circolo_Listing_Helper::is_protected( get_the_ID() );
        
        if ( $is_protected ) {
            add_filter( 'comments_open', function () {
                return false;
            } );
            add_filter( 'get_comments_number', function () {
                return 0;
            } );
        }
    
    }

	/**
     * @return bool
     */
    protected function is_admin() : bool
    {
        $current_user = wp_get_current_user();
        if ( user_can( $current_user, 'administrator' ) ) {
            return true;
        }
        return false;
    }

	public function the_author( $display_name ) {
		$owner_id = get_post_meta( get_the_ID(), CIRCOLO_LISTING_SLUG . '_owner', true );
            
            if( isset( $owner_id ) && is_numeric( $owner_id ) ){
                $owner_obj = get_user_by('id', $owner_id);
                if( $owner_obj ) {
					$display_name = $owner_obj->display_name;
				}
            }

		return $display_name;
	}

}
