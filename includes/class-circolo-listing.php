<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://circolo.club
 * @since      1.0.0
 *
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/includes
 * @author     Jerex Lennon <skyguyverph@gmail.com>
 */
class Circolo_Listing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Circolo_Listing_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CIRCOLO_LISTING_VERSION' ) ) {
			$this->version = CIRCOLO_LISTING_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = CIRCOLO_LISTING_PLUGIN_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_woocommerce_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Circolo_Listing_Loader. Orchestrates the hooks of the plugin.
	 * - Circolo_Listing_i18n. Defines internationalization functionality.
	 * - Circolo_Listing_Admin. Defines all hooks for the admin area.
	 * - Circolo_Listing_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-circolo-listing-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-circolo-listing-i18n.php';

		/**
		 * The class responsible for helpers functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-circolo-listing-helper.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-circolo-listing-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the WooCommerce Integration
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'integrations/class-circolo-listing-woocommerce.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-circolo-listing-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-circolo-listing-restrict-content.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-circolo-listing-shortcodes.php';

		$this->loader = new Circolo_Listing_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Circolo_Listing_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Circolo_Listing_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Circolo_Listing_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin, 'register_custom_post_types');
		//$this->loader->add_action( 'init', $plugin_admin, 'create_categories_taxonomy', 0 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'addPluginAdminMenu', 9);
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_custom_box');
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_fields' );
		$this->loader->add_action( 'new_to_publish', $plugin_admin, 'save_meta_fields' );
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'widget_area' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'set_post_category', 10,3 );

		//$this->loader->add_action( 'plugins_loaded', Circolo_Listing_PageTemplater::class, 'get_instance' );

		$this->loader->add_action('wp_ajax_woocommerce_ajax_add_to_cart', $plugin_admin, 'ajax_add_to_cart');
		$this->loader->add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', $plugin_admin, 'ajax_add_to_cart');

		$this->loader->add_action('wp_ajax_circolo_listing_save', $plugin_admin, 'save_listing');
		$this->loader->add_action('wp_ajax_nopriv_circolo_listing_save', $plugin_admin, 'save_listing');
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Circolo_Listing_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_public, 'init' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'set_product_ids' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'restrict_listing' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'restrict_content' );

		
	}

	private function define_woocommerce_hooks() {
		$plugin_wc = new Circolo_Listing_WooCommerce( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_wc, 'init' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_wc, 'order_add_meta_boxes' );
		$this->loader->add_action( 'woocommerce_product_options_general_product_data', $plugin_wc, 'product_date_range' );
		//$this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'wc_new_product_tab' );
		$this->loader->add_action( 'woocommerce_before_checkout_form', $plugin_wc, 'order_review', 10 );
		$this->loader->add_action('woocommerce_checkout_order_processed', $plugin_wc, 'order_processed', 10, 1);
		//$this->loader->add_action('woocommerce_payment_complete', $plugin_wc, 'payment_complete', 10, 1);
		$this->loader->add_action( 'woocommerce_update_order', $plugin_wc, 'update_order', 10, 1 ); 
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_wc, 'limit_one_per_order', 10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Circolo_Listing_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
