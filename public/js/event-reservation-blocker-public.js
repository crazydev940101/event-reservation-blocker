(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	jQuery(document).ready(function($){
		// When the page is ready, make an Ajax request to check if any events are active
		$.ajax({
			type: 'POST',
			url: erbDeleteEventNonce.ajaxurl,
			data: {
				action: 'erb_check_event_time',  // The action hook
				nonce: erbDeleteEventNonce.nonce
			},
			success: function(response) {
				if (response.success) {
					// Handle success (event is active)
					var msg = "Unfortunately, reservations are not available at the moment due to an ongoing event. Kindly visit the event page by <a href='/events'>clicking here</a> for more details"
					console.log(response.data.message);  // 'Event is active!'
					$('.reservation-form').empty(); // Use '.empty()' to remove all child elements
					$('.reservation-form').append('<p style="color:#FFF">' + msg + '</p>');
					// $('.reservation-form').append('<p>' + response.data.message + '</p>');
				} else {
					// Handle error (no active events)
					console.log(response.data.message);  // 'No active events at the moment.'
					// $('.reservation-form').append('<p>' + response.data.message + '</p>');
				}
			},
			error: function(xhr, status, error) {
				// Handle any Ajax errors
				console.log('Ajax request failed: ' + error);
			}
		});
	});

})( jQuery );
