<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://circolo.club
 * @since             1.0.0
 * @package           Circolo_Listing
 *
 * @wordpress-plugin
 * Plugin Name:       Circolo Listing
 * Plugin URI:        https://circolo.club
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Stamped Strategies Limited
 * Author URI:        http://www.stampedstrategies.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       circolo-listing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CIRCOLO_LISTING_VERSION', '1.0.0' );
define( 'CIRCOLO_LISTING_SLUG', 'circolo_listing' );
define( 'CIRCOLO_LISTING_PLUGIN_NAME', 'circolo-listing' );
define( 'CIRCOLO_LISTING_NAME', 'Circolo Listing' );
define( 'CIRCOLO_LISTING_TEMPLATE_PATH', 'circolo-listing/' );
define( 'CIRCOLO_LISTING_PATH', plugin_dir_path( __FILE__ ) );
define( 'CIRCOLO_LISTING_URL', plugin_dir_url( __FILE__ ) );
define( 'CIRCOLO_LISTING_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-circolo-listing-activator.php
 */
function activate_circolo_listing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-circolo-listing-activator.php';
	Circolo_Listing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-circolo-listing-deactivator.php
 */
function deactivate_circolo_listing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-circolo-listing-deactivator.php';
	Circolo_Listing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_circolo_listing' );
register_deactivation_hook( __FILE__, 'deactivate_circolo_listing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-circolo-listing.php';
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_circolo_listing() {

	$plugin = new Circolo_Listing();
	$plugin->run();

}
run_circolo_listing();
