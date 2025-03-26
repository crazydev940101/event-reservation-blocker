<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ieproductions.com
 * @since      1.0.0
 *
 * @package    Event_Reservation_Blocker
 * @subpackage Event_Reservation_Blocker/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Event_Reservation_Blocker
 * @subpackage Event_Reservation_Blocker/public
 * @author     Ariel Cruz <ariel@ieproductions.com>
 */
class Event_Reservation_Blocker_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
		 * defined in Event_Reservation_Blocker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Reservation_Blocker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/event-reservation-blocker-public.css', array(), $this->version, 'all' );

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
		 * defined in Event_Reservation_Blocker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Reservation_Blocker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/event-reservation-blocker-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'erbDeleteEventNonce', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'erb_check_event_time_nonce' )
		));

	}

	// Handle checking event time against the current time
	function erb_check_event_time() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'erb_check_event_time_nonce' ) ) {
			wp_send_json_error(array('message' => 'Invalid nonce'));
			return;
		}
	
		// Get the current time in WordPress server timezone
		$current_time = current_time('mysql');  // Get current time in WordPress server timezone (YYYY-MM-DD HH:MM:SS format)
	
		// Convert the current server time to Mountain Time (MT)
		// Create a DateTime object from the current server time
		$server_time = new DateTime($current_time, new DateTimeZone(wp_timezone_string()));
		
		// Convert server time to Mountain Time (America/Denver)
		$server_time->setTimezone(new DateTimeZone('America/Denver'));
		
		// Get the current time in Mountain Time as a Unix timestamp
		$mountain_time = $server_time->getTimestamp();
	
		// Retrieve all the events (assuming events are stored in 'erb_events')
		$events = get_option( 'erb_events', array() );
	
		$matching_events = false;

		$event_date = [];
	
		// Loop through the events and check if the current time is within the event's time range
		foreach ( $events as $event ) {
			$event_start = isset( $event['start'] ) ? $event['start'] : '';
			$event_end = isset( $event['end'] ) ? $event['end'] : '';
	
			// Use array_push() to add event start and end to the $event_date array
			$event_date[] = [$event_start, $event_end];

			$matching_events = true;
		}
	
		// Return success or failure based on the comparison
		if ( $matching_events ) {
			wp_send_json_success(array('message' => 'Event is active!', 'event_date' => $event_date));
		} else {
			wp_send_json_error(array('message' => 'No active events at the moment.'));
		}
	}

	// PHP for User Meta for Reservation Form
	function set_default_cf7_values($tag, $function_name) {
		$current_user = wp_get_current_user();
	
		if ($current_user->ID) {
			// Check the name of the field to set default values
			if ($tag['name'] === 'd-first-name') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'first_name', true));
			}
			if ($tag['name'] === 'd-last-name') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'last_name', true));
			}
			if ($tag['name'] === 'd-email') {
				$tag['values'] = array($current_user->user_email);
			}
			if ($tag['name'] === 'd-phone') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'phone', true));
			}
			if ($tag['name'] === 'd-address1') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'address_1', true));
			}
			if ($tag['name'] === 'd-address2') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'address_2', true));
			}
			if ($tag['name'] === 'd-city') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'city', true));
			}
			if ($tag['name'] === 'd-zip') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'postal_code', true));
			}
			if ($tag['name'] === 'd-state') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'state', true));
			}
			if ($tag['name'] === 'd-country') {
				$tag['values'] = array(get_user_meta($current_user->ID, 'country', true));
			}
		}
	
		return $tag;
	}
	
}
