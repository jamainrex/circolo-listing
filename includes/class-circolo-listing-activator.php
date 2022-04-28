<?php

/**
 * Fired during plugin activation
 *
 * @link       https://circolo.club
 * @since      1.0.0
 *
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/includes
 * @author     Jerex Lennon <skyguyverph@gmail.com>
 */
class Circolo_Listing_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {


		if ( !class_exists( 'WooCommerce' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( esc_html__( 'Please install and Activate WooCommerce.', 'circolo_listings' ), 'Plugin dependency check', array(
                'back_link' => true,
            ) );
        }
	}

}
