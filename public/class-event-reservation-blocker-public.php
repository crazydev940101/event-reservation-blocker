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
		$current_time = current_time('timestamp');  // Current time in MySQL format (YYYY-MM-DD HH:MM:SS)

		// Retrieve all the events (assuming events are stored in 'erb_events')
		$events = get_option( 'erb_events', array() );

		$matching_events = false;

		// Loop through the events and check if the current time is within the event's time range
		foreach ( $events as $event ) {
			$event_start = isset( $event['start'] ) ? $event['start'] : '';
			$event_end = isset( $event['end'] ) ? $event['end'] : '';

			// Convert the event start and end times to Unix timestamps
			$event_start_timestamp = $event_start ? strtotime( $event_start ) : false;
			$event_end_timestamp = $event_end ? strtotime( $event_end ) : false;

			if ( $event_start_timestamp && $event_end_timestamp ) {
				// Check if current time is between event start and end times
				if ( $current_time >= $event_start_timestamp && $current_time <= $event_end_timestamp ) {
					$matching_events = true;
					error_log('Matching event found!');  // Log for debugging purposes
					break;
				}
        	}
		}

		// Return success or failure based on the comparison
		if ( $matching_events ) {
			wp_send_json_success(array('message' => 'Event is active!'));
		} else {
			wp_send_json_error(array('message' => 'No active events at the moment.'));
		}
	}

}
