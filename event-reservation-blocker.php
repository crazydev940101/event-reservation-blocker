<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ieproductions.com
 * @since             1.0.0
 * @package           Event_Reservation_Blocker
 *
 * @wordpress-plugin
 * Plugin Name:       Event Reservation Blocker
 * Plugin URI:        https://ieproductions.com
 * Description:       Custom plugin to block reservations during the event
 * Version:           1.0.0
 * Author:            Ariel Cruz
 * Author URI:        https://ieproductions.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       event-reservation-blocker
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
define( 'EVENT_RESERVATION_BLOCKER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-event-reservation-blocker-activator.php
 */
function activate_event_reservation_blocker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-event-reservation-blocker-activator.php';
	Event_Reservation_Blocker_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-event-reservation-blocker-deactivator.php
 */
function deactivate_event_reservation_blocker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-event-reservation-blocker-deactivator.php';
	Event_Reservation_Blocker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_event_reservation_blocker' );
register_deactivation_hook( __FILE__, 'deactivate_event_reservation_blocker' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-event-reservation-blocker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_event_reservation_blocker() {

	$plugin = new Event_Reservation_Blocker();
	$plugin->run();

}
run_event_reservation_blocker();
