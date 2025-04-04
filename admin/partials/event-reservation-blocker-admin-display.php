<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ieproductions.com
 * @since      1.0.0
 *
 * @package    Event_Reservation_Blocker
 * @subpackage Event_Reservation_Blocker/admin/partials
 */


function erb_admin_page() {
    // Call the cleanup function when the admin page loads
    erb_cleanup_expired_events();

    ?>
    <div class="wrap event-reservation-blocker">
        <h1>Event Reservation Blocker</h1>
        <p>To block a reservation, add start and end times for the event.</p>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="start_datetime">Start Date/Time</label></th>
                    <td><input type="datetime-local" id="start_datetime" name="start_datetime" required /></td>
                </tr>
                <tr>
                    <th><label for="end_datetime">End Date/Time</label></th>
                    <td><input type="datetime-local" id="end_datetime" name="end_datetime" required /></td>
                </tr>
            </table>
            <input type="submit" name="add_event" class="button-primary" value="Add Event">
        </form>
        <hr/>
        <h2>Added Blacklist Events</h2>
        <?php erb_display_events(); ?>
    </div>
    <?php
}


// Display events in a table
function erb_display_events() {
    $events = get_option( 'erb_events', array() );
    if ( ! empty( $events ) ) {
        echo '<table class="widefat fixed">';
        echo '<thead><tr><th>Start Date/Time</th><th>End Date/Time</th><th>Action</th></tr></thead><tbody>';

        foreach ( $events as $index => $event ) {
            // Format the start date/time
            $start_date = new DateTime( $event['start'] );
            $start_date_formatted = $start_date->format( 'Y-m-d, g:i A' );  // Example: March 22, 2025 2:49 PM

            // Format the end date/time
            $end_date = new DateTime( $event['end'] );
            $end_date_formatted = $end_date->format( 'Y-m-d, g:i A' );  // Example: March 22, 2025 4:30 PM

            echo '<tr>';
            echo '<td>' . esc_html( $start_date_formatted ) . '</td>';
            echo '<td>' . esc_html( $end_date_formatted ) . '</td>';
            echo '<td><button class="button delete-event" data-index="' . $index . '">Delete</button></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No events added yet.</p>';
    }
}

// Handle the event submission
function erb_handle_event_submission() {
    if ( isset( $_POST['add_event'] ) ) {
        $start_datetime = sanitize_text_field( $_POST['start_datetime'] );
        $end_datetime = sanitize_text_field( $_POST['end_datetime'] );

        // Save event to the database
        $events = get_option( 'erb_events', array() );
        $events[] = array( 'start' => $start_datetime, 'end' => $end_datetime );
        update_option( 'erb_events', $events );
    }
}

// Handle event deletion via Ajax
function erb_delete_event() {
    // Check nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'erb_delete_event_nonce' ) ) {
        wp_send_json_error();
        return;
    }
    
    // Get the event index and remove the event
    $event_index = intval( $_POST['event_index'] );
    $events = get_option( 'erb_events', array() );
    if ( isset( $events[ $event_index ] ) ) {
        unset( $events[ $event_index ] );
        update_option( 'erb_events', $events );

        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}

// Function to clean up events that have passed
function erb_cleanup_expired_events() {
    $events = get_option( 'erb_events', array() );

    // Get the current time in WordPress server timezone
    $current_time = current_time('mysql');  // Get current time in WordPress server timezone (YYYY-MM-DD HH:MM:SS format)
    
    // Create a DateTime object from the current server time
    $server_time = new DateTime($current_time, new DateTimeZone(wp_timezone_string()));

    // Convert server time to Mountain Time (America/Denver)
    $server_time->setTimezone(new DateTimeZone('America/Denver'));

    // Get the current time in Mountain Time as a Unix timestamp
	$mountain_time = $server_time->getTimestamp();

    // Loop through the events and check if the end time has passed
    foreach ( $events as $index => $event ) {
        $event_end = isset( $event['end'] ) ? $event['end'] : '';

        // Convert the event start and end times to DateTime objects
        $event_end_datetime = $event_end ? new DateTime($event_end, new DateTimeZone(wp_timezone_string())) : false;

        if ( $event_end_datetime ) {
            // Convert the event end time to DateTime
            $event_end_timestamp = $event_end_datetime->getTimestamp();

            // Check if the current time is past the event's end time
            if ( $mountain_time > $event_end_timestamp ) {
                // Event has passed, remove it
                unset( $events[ $index ] );
            }
        }
    }

    // Save the updated list of events
    update_option( 'erb_events', array_values($events) );
}



?>

