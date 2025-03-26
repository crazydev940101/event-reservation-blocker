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

		var event_reservation_blocker = false;
		var event_date_array = [];

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
					// console.log(1, response.data.event_date);  // 'Event is active!'
					event_date_array = response.data.event_date;
					event_reservation_blocker = true;
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

		// Init the business time
		var startTimeTue_ = 16;
		var endTimeTue_ =  21;
		var startTimeFri_ = 16;
		var endTimeFri_ =  23;

		var startTimeTue = startTimeTue_ * 60;
		var endTimeTue =  endTimeTue_ * 60 + 30;
		var startTimeFri = startTimeFri_ * 60;
		var endTimeFri =  endTimeFri_ * 60 + 30;
			
		// Function to init the current Date
		function normalizeDate(date) {
			var normalizedDate = new Date(date); // Create a new Date object based on the original one
			normalizedDate.setHours(0, 0, 0, 0); // Set hours, minutes, seconds, and milliseconds to 0
			return normalizedDate;
		}

		// Function to close the time
		function closedTime($hoursSelect, $minsSelect, $duringSelect) {
				$hoursSelect.empty();
				$minsSelect.empty();
				$duringSelect.empty();

				$hoursSelect.append('<option value="">Closed</option>');
				$hoursSelect.prop('disabled', true);
				$minsSelect.append('<option value="">Closed</option>');
				$minsSelect.prop('disabled', true);
				$duringSelect.append('<option value="">Closed</option>');
				$duringSelect.prop('disabled', true);
		}

		// Function to set the limitation to 2 hours before today.
		function compareToday(selectedDate,  $hoursSelect, $minsSelect, $duringSelect, st, et) {
			var twoHoursLater = new Date();
			if(normalizeDate(twoHoursLater).getTime() === normalizeDate(selectedDate).getTime()) {
					var minsS = twoHoursLater.getMinutes();
					var hoursS = twoHoursLater.getHours() + 2;
					var currentTime = hoursS * 60 + minsS;
					var startTime = st;
					var endTime = et;
				    if (currentTime >= startTime && currentTime <= endTime) {
					   // This ensures the time is between 4:00 PM and 11:30 PM
						if(hoursS > 11) {
							hoursS = hoursS - 12;
						}
					   $hoursSelect.val(hoursS < 10 ? '0' + hoursS : hoursS); 
					   $minsSelect.val(minsS < 10 ? '0' + minsS : minsS); 

				    } else if (currentTime > endTime) {
					  // This ensures the time is after 11:30 PM,
					   closedTime($hoursSelect, $minsSelect, $duringSelect);
					}
				}
		}

		// Function to set the limitation to 20 minutes before today.
		function compareTodayForPickerTime(selectedDate,  st, et) {
			var $hoursSelect = $('select[name="r-hours"]');
			var $minsSelect = $('select[name="r-mins"]');
			var $duringSelect = $('select[name="r-during"]');
			
			var twoHoursLater = new Date();
			if(normalizeDate(twoHoursLater).getTime() === normalizeDate(selectedDate).getTime()) {
					var minsS = twoHoursLater.getMinutes();
					var hoursS = twoHoursLater.getHours() + 2;
					var currentTime = hoursS * 60 + minsS;
					var startTime = st;
					var endTime = et;
				   if (currentTime >= startTime && currentTime <= endTime) {
					   // This ensures the time is between 4:00 PM and 11:30 PM
					   return twoHoursLater; 
				   } else if (currentTime > endTime) {
					  // This ensures the time is after 11:30 PM,
					   return 'null';
					}
				}
		}
		/* ------------------------------------------------------------------------------------------*/
		/* ------------------------------------------------------------------------------------------*/
		
		function updateIndividualTimeSlot(selectedDate, $hoursSelect, $minsSelect, $duringSelect, startTimeDay_, endTimeDay_, startTimeDay, endTimeDay ) {
			var eventTimes = event_date_array.map(function(event) {
				return {
					start: event[0],
					end: event[1]
				};
			});

			var hourslot = [];
			var minuteslot = [];

			eventTimes.forEach ( function (item) {
				if(normalizeDate(item.start).getTime() === normalizeDate(selectedDate).getTime()) {
					var start_hours_event = new Date(item.start).getHours() * 60;
					var end_hours_event = new Date(item.end).getHours() * 60;
					if((( start_hours_event < startTimeDay ) && ( end_hours_event < startTimeDay )) ||
					(( start_hours_event > endTimeDay ) && ( end_hours_event > endTimeDay ))){
						hourslot = [];
					}else {
						var j_s = new Date(item.start).getHours();
						var j_e = new Date(item.end).getHours();
						var j_s_m = new Date(item.start).getMinutes();
						var j_e_m = new Date(item.end).getMinutes();
					
						var offset_hours = j_e - j_s;
						if( offset_hours <= 1) {
							hourslot.push();								
						}else {
							for(var i = 1; i < offset_hours; i++) {
								var temp_start = Number(j_s) + i;
								if(temp_start >= j_s && temp_start <= j_e) {
									hourslot.push(temp_start);
								}
							}
							
						}

						if(j_s_m == 0) {
							hourslot.push(j_s);
						}

						if ((j_e === endTimeDay_) && (j_e_m >= 30)) {
							hourslot.push(endTimeDay_);
						}
						
						minuteslot.push( {
							'startH': j_s,
							'endH': j_e,
							'startM': j_s_m,
							'endM': j_e_m
						});
						
					}
				}else {
					hourslot = [];
				}
			})
			
			console.log(hourslot)
			console.log(minuteslot);

			// Loop to add hours (04 to 09)
			for (var i = startTimeDay_-12; i <= endTimeDay_-12; i++) {
				// console.log(555, hourslot);
				if(hourslot.length === 0) {
					$hoursSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
				}else {
					if( hourslot.includes(i+12) ){
						continue;
					}else {
						$hoursSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
					}
				}
			}

			// Available hours: 4:00 PM to 9:00 PM
			$duringSelect.append('<option value="PM">PM</option>'); // PM for Tuesday-Thursday

			for (var i = 0; i <= 59; i++) {
				 $minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
			}

			compareToday(selectedDate,  $hoursSelect, $minsSelect, $duringSelect, startTimeDay, endTimeDay);

			// Special handling for 9:00 PM
			$hoursSelect.on('change', function() {
				var selectedHour = $(this).val();

				if (minuteslot.length > 0) {
					minuteslot.forEach(element => {	
						if (element.startH === parseInt(selectedHour) + 12) {
							$minsSelect.empty();
							for (var i = 0; i <= element.startM; i++) {
								$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
							}
						}else if(element.endH === parseInt(selectedHour) + 12) {
							$minsSelect.empty();
							if(element.endH === endTimeDay_) {
								for (var i = element.endM; i <= 30; i++) {
									$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
								}
							} else {
								for (var i = element.endM; i <= 59; i++) {
									$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
								}
							}
						}else {
							if (endTimeDay_ == parseInt(selectedHour) + 12) {
								// If the hour is 9:00 PM, limit minutes to 00 and 30 only
								$minsSelect.empty();
								for (var i = 0; i <= 30; i++) {
									$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
								}
							}
						}
					});
				} else {
					if (endTimeDay_ == parseInt(selectedHour) + 12) {
						// If the hour is 9:00 PM, limit minutes to 00 and 30 only
						$minsSelect.empty();
						for (var i = 0; i <= 30; i++) {
							$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
						}
					} else {
						// Otherwise, show all minutes (00-59)
						$minsSelect.empty();
						for (var i = 0; i < 60; i++) {
							$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
						}
					}
				}

			});

			$hoursSelect.change();
		}

		// Function to update the available time slots based on the selected date
		function updateAvailableTimeSlots() {
			// Get the selected date from the datepicker
			var selectedDate = $('input[name="datepicker-2"]').val();

			// Clear the time dropdown options
			var $hoursSelect = $('select[name="r-hours"]');
			var $minsSelect = $('select[name="r-mins"]');
			var $duringSelect = $('select[name="r-during"]');

			// Reset to default values
			$hoursSelect.prop('disabled', false);
			$minsSelect.prop('disabled', false);
			$duringSelect.prop('disabled', false);
			$hoursSelect.empty();
			$minsSelect.empty();
			$duringSelect.empty();

			// Clear previous options in minutes and set default
			if (selectedDate === "") {
				// If no date is selected, clear minutes and disable the time selection

				$hoursSelect.prop('disabled', true);
				$minsSelect.prop('disabled', true);
				$duringSelect.prop('disabled', true);
				return; // Exit the function, since no valid date is selected
			}

			// Get the day of the week (0 = Sunday, 1 = Monday, 2 = Tuesday, ..., 6 = Saturday)
			var dayOfWeek = new Date(selectedDate).getDay();

			// Handle available time slots based on the selected day
			if (dayOfWeek == 0 || dayOfWeek == 1) { // Sunday (0) or Monday (1)
				// Closed on Sunday and Monday
				closedTime($hoursSelect, $minsSelect, $duringSelect);

			} else if (dayOfWeek >= 2 && dayOfWeek <= 4) { // Tuesday to Thursday
				// Available hours: 4:00 PM to 9:30 PM
				updateIndividualTimeSlot(selectedDate, $hoursSelect, $minsSelect , $duringSelect, startTimeTue_, endTimeTue_, startTimeTue, endTimeTue );
				
			} else if (dayOfWeek == 5 || dayOfWeek == 6) { // Friday and Saturday
				// Available hours: 4:00 PM to 11:30 PM
				updateIndividualTimeSlot(selectedDate, $hoursSelect, $minsSelect , $duringSelect, startTimeFri_, endTimeFri_, startTimeFri, endTimeFri );
				
			}
		}

		// Listen for changes in the datepicker
		$('input[name="datepicker-2"]').on('change', function() {
			updateAvailableTimeSlots();
		});

		// Listen for changes in the hour dropdown to dynamically update minutes
		$('select[name="r-hours"]').on('change', function() {
			var selectedHour = $(this).val();
			var $minsSelect = $('select[name="r-mins"]');

			if (selectedHour == "09" || selectedHour == "11") {
				// If the hour is 9:00 PM or 11:00 PM, limit minutes to 00 and 30 only
				$minsSelect.empty();
				$minsSelect.append('<option value="00">00</option>');
				$minsSelect.append('<option value="30">30</option>');
			} else {
				// Otherwise, show all minutes (00-59)
				$minsSelect.empty();
				for (var i = 0; i < 60; i++) {
					$minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
				}
			}
		});

		// Initial update based on current date
		updateAvailableTimeSlots();
	
		/* ------------------------------------------------------------------------------------------*/
		/* ------------------------------------------------------------------------------------------*/
		
		// Function to adjust the time (used for both page load and field change)
		function adjustTime(rH, rM, rD) {
			// Get the current selected time

			var $c_hoursSelect = $('select[name="c-hours"]');
			var $c_minsSelect = $('select[name="c-mins"]');
			var $c_duringSelect = $('select[name="c-during"]');

			var rHours = rH ? parseInt(rH) : parseInt($('select[name="r-hours"]').val());
			var rMins = rM ? parseInt(rM) : parseInt($('select[name="r-mins"]').val());
			var rDuring = rD ? rD : $('select[name="r-during"]').val();

			// If no valid selection exists, skip the rest of the time adjustment
			if (isNaN(rHours) || isNaN(rMins) || rDuring === '') {
			   $c_hoursSelect.empty();
			   $c_minsSelect.empty();
			   $c_duringSelect.empty();

			} else {

				$c_hoursSelect.empty();
				$c_minsSelect.empty();
				$c_duringSelect.empty();

				for (var i = 0; i <= 12; i++) {
					$c_hoursSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
				}
				for (var i = 0; i <= 59; i++) {
					$c_minsSelect.append('<option value="' + (i < 10 ? '0' + i : i) + '">' + (i < 10 ? '0' + i : i) + '</option>');
				}

				$c_duringSelect.append('<option value="AM">AM</option>');  // AM for Friday-Saturday
				$c_duringSelect.append('<option value="PM">PM</option>');  // PM for Friday-Saturday

				// Adjust the hour for AM/PM
				if (rDuring === 'PM' && rHours !== 12) {
					rHours += 12;  // Convert PM hours to 24-hour format
				}
				if (rDuring === 'AM' && rHours === 12) {
					rHours = 0;  // Convert 12 AM to 00 (midnight)
				}

				// Subtract 20 minutes
				rMins -= 20;

				if (rMins < 0) {
					// If minutes go below 0, subtract an hour and adjust minutes
					rMins = 60 + rMins;  // Add 60 to minutes to correct for the negative value
					rHours -= 1;         // Subtract an hour
				}

				if (rHours < 0) {
					rHours = 23;  // Wrap around to 23 if hours go below 0
				}

				// Determine AM/PM for the adjusted time
				var cDuring = (rHours >= 12 && rHours < 24) ? 'PM' : 'AM';
				var cHours = rHours % 12;  // Convert to 12-hour format
				if (cHours === 0) cHours = 12;  // Handle 12AM/PM correctly

				// Update the second set of fields with the adjusted time
				$('select[name="c-hours"]').val(cHours.toString().padStart(2, '0'));
				$('select[name="c-mins"]').val(rMins.toString().padStart(2, '0'));
				$('select[name="c-during"]').val(cDuring);
			}
		}

		// Run adjustTime when any of the fields are changed
		$('select[name="r-hours"], select[name="r-mins"], select[name="r-during"], input[name="c-showpickup[]"]').on('change', function() {
			adjustTime('', '', '');
		});

		$('input[name="datepicker-2"]').on('change', function() {
			// console.log(2, event_date_array);  // 'Event is active!'
			var selectedDate = $('input[name="datepicker-2"]').val();
			var dayOfWeek = new Date(selectedDate).getDay();
			if (dayOfWeek == 0 || dayOfWeek == 1) { // Sunday (0) or Monday (1)
				// Closed on Sunday and Monday
				 adjustTime('', '', '');
			} else if (dayOfWeek >= 2 && dayOfWeek <= 4) { // Tuesday to Thursday
				var todayReserve = compareTodayForPickerTime(selectedDate,  startTimeTue, endTimeTue);

				if(todayReserve && todayReserve !== 'null') {
					adjustTime(todayReserve.getHours(), todayReserve.getMinutes(), 'PM');
				} else if(todayReserve === 'null') {
					adjustTime('', '', '');	  
				} else {
					adjustTime('4', '0', 'PM');
				}
			} else if (dayOfWeek == 5 || dayOfWeek == 6) { // Friday and Saturday
				var todayReserve = compareTodayForPickerTime(selectedDate,  startTimeFri, endTimeFri);
				if(todayReserve && todayReserve !== 'null') {
					adjustTime(todayReserve.getHours(), todayReserve.getMinutes(), 'PM');
				} else if(todayReserve === 'null') {
					adjustTime('', '', '');	  
				} else {
					adjustTime('4', '0', 'PM');
				}
			}

		});

		// Also run adjustTime on page load to set the initial value
		adjustTime();  // This triggers the function as soon as the page loads
	});

})( jQuery );
