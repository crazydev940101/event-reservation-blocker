<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ieproductions.com
 * @since      1.0.0
 *
 * @package    Event_Reservation_Blocker
 * @subpackage Event_Reservation_Blocker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Event_Reservation_Blocker
 * @subpackage Event_Reservation_Blocker/admin
 * @author     Ariel Cruz <ariel@ieproductions.com>
 */
class Event_Reservation_Blocker_Admin {

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
		 * defined in Event_Reservation_Blocker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Reservation_Blocker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/event-reservation-blocker-admin.css', array(), $this->version, 'all' );

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
		 * defined in Event_Reservation_Blocker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Reservation_Blocker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/event-reservation-blocker-admin.js', array( 'jquery' ), $this->version, false );
		
		wp_localize_script( $this->plugin_name, 'erbDeleteEventNonce', array(
			'nonce' => wp_create_nonce( 'erb_delete_event_nonce' )
		) );

	}

	/**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function erb_add_menu_page() {

		/**
		* Add hook for admin menu
		*/

        add_menu_page(
            __('Event Reservation Blocker'),
            __('Event Reservation Blocker'),
            'manage_options',
            $this->plugin_name,
            array($this, 'erb_display_add_menu_page'),
			'dashicons-warning',
			6
        );
    }

	public function erb_display_add_menu_page() {

		/**
		* Get admin page for Excel File
		*/

		include_once( 'partials/event-reservation-blocker-admin-display.php' );

		erb_admin_page();

    }

	public function erb_function_event_submission() {

		/**
		* Create table
		*/

		include_once( 'partials/event-reservation-blocker-admin-display.php' );

		erb_handle_event_submission();

	}

	public function erb_function_delete_event() {

		/**
		* Create table
		*/

		include_once( 'partials/event-reservation-blocker-admin-display.php' );

		erb_delete_event();

	}

}
