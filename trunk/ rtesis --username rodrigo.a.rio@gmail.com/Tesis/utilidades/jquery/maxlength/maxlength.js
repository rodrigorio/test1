(function($) 
{
	$.fn.maxlength = function(options)
	{
		var settings = jQuery.extend(
		{
			events:				      [], // Array of events to be triggerd
			maxCharacters:		  500, // Characters limit
			status:				      true, // True to show status indicator bewlow the element
			statusClass:		    "restantes male fte2", // The class on the status div
			statusText:			    "caracteres restantes" // The status text
		}, options );
		
		// Add the default event
		$.merge(settings.events, ['keyup']);

		return this.each(function() 
		{
			var item = $(this);
			var charactersLength = $(this).val().length;
			
			//si el title es igual al description entonces que tome charactersLength = 0
			if(item.attr("title") == item.val()){ charactersLength = 0; }
			
		    // Update the status text
			function updateStatus()
			{
				var charactersLeft = settings.maxCharacters - charactersLength;
				
				if(charactersLeft < 0) 
				{
					charactersLeft = 0;
				}

				item.next("div").html(charactersLeft + " " + settings.statusText);
			}

			function checkChars() 
			{
				var valid = true;
				
				// Too many chars?
				if(charactersLength >= settings.maxCharacters) 
				{
					// Too may chars, set the valid boolean to false
					valid = false;
					// Add the notifycation class when we have too many chars
					item.addClass(settings.notificationClass);
					// Cut down the string
					item.val(item.val().substr(0,settings.maxCharacters));
					// Show the alert dialog box, if its set to true
					showAlert();
				} 
				else 
				{
					// Remove the notification class
					if(item.hasClass(settings.notificationClass)) 
					{
						item.removeClass(settings.notificationClass);
					}
				}

				if(settings.status)
				{
					updateStatus();
				}
			}
									
			// Loop through the events and bind them to the element
			$.each(settings.events, function (i, n) {
				item.bind(n, function(e) {
					charactersLength = item.val().length;
					checkChars();
				});
			});

			// Insert the status div
			if(settings.status) 
			{
				var clases_div_caracteres = settings.statusClass;						
				item.after($("<div/>").addClass(clases_div_caracteres).html('-').hide()); /* SOY RE PRO */
				updateStatus();
			}

		});
	};
})(jQuery);
