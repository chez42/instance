$(function()
{
	// Total available slots on selected date with selecetd slot
	var Available_Slots = null;
	var selected_user_name = null;
	var MIDNIGHT_START_TIME = null;
	var MIDNIGHT_END_TIME = null;

	// Select slot duration 60/30/15min
	//$(".selected_meeting_time").die().live('click', function(e)
	$('body').on('click','.selected_meeting_time',function(e)
	{
		// e.preventDefault();

		$("#details").empty();
		Selected_Time = $(this).attr('data');
		$(".show_slots").find('input:radio').prop('checked', false);
		$(this, [
			'input:radio'
		]).prop('checked', true);
		appointmenttype = $('input[name="selected_meeting_time"]:checked').val();

		$(".activemin").removeClass("activemin");
		$(this).find('.minutes').addClass("activemin");

		// Make next part enable
		$('.segment2').removeClass('me-disable');
		$(".segment2").fadeIn("slow");
		$('#one').addClass('green-bg').html('<i class="fa fa-check"></i>');

		autoscrol(".segment2");

		var isFirefox = typeof InstallTrigger !== 'undefined';
		if (isFirefox)
		{
			$('#datepick').DatePickerSetDate(current_date_mozilla, true);
		}
		$('.checkbox-main-grid').html('<img class="loading-img" src="img/21-0.gif" style="width: 40px;margin-left: 216px;"></img>');
		if (!selecteddate)
		{
			selecteddate = new Date();
			CURRENT_DAY_OPERATION = true;

		}
		if (selecteddate)
		{

			get_slots(selecteddate, Selected_Time);
		}

	});

	// Confirm filled info with selected slot
	$('#confirm').click(function(e)
	{
		e.preventDefault();
		// Save scheduled slot
		save_web_event('addEventForm', this);
	});

	// Only single slot selection is allowed
	$('body').on('click','.selected-slot',function(e)
	{
		var currentId = $(this).attr('id');

		$('.selected-slot').each(function()
		{
			if ($(this).attr('id') != currentId)
				$(this).prop("checked", false);
		});

		// Make next part enable
		enableSegment3();

		$(".segment3").fadeIn("slow");
		$("#confirm").show();
		$('#two').addClass('green-bg').html('<i class="fa fa-check"></i>');
		autoscrol(".segment3");
		if(NAME == "")
		   NAME = getDetailsFromCookie("contact-detail-name");
		if(EMAIL=="")
		 EMAIL = getDetailsFromCookie("contact-detail-email");
		deleteCookie("contact-detail-name","",-1);
		deleteCookie("contact-detail-email","",-1);	
		$("#userName").val(NAME);
		$("#email").val(EMAIL);
	});

	$('#user_timezone').change(function()
	{

		SELECTED_TIMEZONE = $('#user_timezone').val();		
		updateUserBusinessHoursInVisitorTimezone();

		if (!selecteddate || !Selected_Time)
			return;
		$("#current_local_time").html("Current Time: " + getConvertedTimeFromEpoch(new Date().getTime() / 1000));
		$('.checkbox-main-grid').html('<img class="loading-img" src="img/21-0.gif" style="width: 40px;margin-left: 216px;"></img>');
		get_slots(selecteddate, Selected_Time);
	});
	$('#user_timezone_group').change(function()
	{

		SELECTED_TIMEZONE = $('#user_timezone_group').val();		
		//updateUserBusinessHoursInVisitorTimezone();

		if (!selecteddate || !Selected_Time)
			return;
		$("#current_local_time").html("Current Time: " + getConvertedTimeFromEpoch(new Date().getTime() / 1000));
		$('.checkbox-main-grid').html('<img class="loading-img" src="img/21-0.gif" style="width: 40px;margin-left: 216px;"></img>');
		getGeneralizeSlots(Selected_Time);
	});
    $('body').on('click','.show-general-slots',function(e)
	{
		$("#details").empty();
		Selected_Time = $(this).attr('data');
		$(".show_slots").find('input:radio').prop('checked', false);
		$(this, [
			'input:radio'
		]).prop('checked', true);
		appointmenttype = $('input[name="selected_meeting_time"]:checked').val();

		$(".activemin").removeClass("activemin");
		$(this).find('.minutes').addClass("activemin");

		// Make next part enable
		$('.segment2').removeClass('me-disable');
		$(".segment2").fadeIn("slow");
		$('#one').addClass('green-bg').html('<i class="fa fa-check"></i>');

		autoscrol(".segment2");

		var isFirefox = typeof InstallTrigger !== 'undefined';
		if (isFirefox)
		{
			$('#datepick').DatePickerSetDate(current_date_mozilla, true);
		}
		getGeneralizeSlots(Selected_Time);
	});
	

	function autoscrol(divclass)
	{

		//console.log($(divclass).offset().top);

		$("body,html").animate({ scrollTop : $(divclass).offset().top }, 1000);

	}

	function isEmpty(o)
	{
		for ( var p in o)
		{
			if (o.hasOwnProperty(p))
			{
				return false;
			}
		}
		return true;
	}
	function getDetailsFromCookie(name)
    {
		var nameEQ = name + "=";

		// Split document.cookie into array at each ";" and iterate through it
		var ca = document.cookie.split(';');
		for ( var i = 0; i < ca.length; i++)
		{
			var c = ca[i];

			// Check for ' ' and remove to get string from c
			while (c.charAt(0) == ' ')
				c = c.substring(1, c.length);
			// check if nameEQ starts with c, if yes unescape and return its value
			if (c.indexOf(nameEQ) == 0)
				return unescape(c.substring(nameEQ.length, c.length));
		}
		return null;
    }
   function deleteCookie(name, value, days)
   {
		// If days is not equal to null, undefined or ""
		if (days)
		{
			var date = new Date();
			// Set cookie variable's updated expire time in milliseconds
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = "; expires=" + date.toGMTString();
		}
		else
			// If days is null, undefined or "" set expires as ""
			var expires = "";
			document.cookie = name + "=" + escape(value) + expires + "; path=/";
	}

});