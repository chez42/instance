// On change of date, change right column above available slot box
function change_availability_date(selected_date)
{

	var date = new Date(selected_date);

	$('.availability').html(LOCALES_JSON['on-availability'] + " " + date.getDayName() + ", " + date.getMonthName() + ", " + date.getDate());
}

// Enable segment 3 after selection of slot
function enableSegment3()
{
	// Make input enable
	$('.me-disable').removeAttr("disabled");

	// Make next part enable
	$('.me-disable').removeClass('me-disable');
}

// Reset all, set current date in calendar, clear all slots, clear form and make
// segment 3 disable
function resetAll()
{
	// Get current date
	var newDate = new Date();
	var currMonth = (newDate.getMonth() + 1);
	if (currMonth < 10)
		currMonth = "0" + currMonth;
	var currentDate = newDate.getFullYear() + '-' + currMonth + '-' + newDate.getDate();

	// Set current date as selected date
	Selected_Date = currentDate;

	// Set current date in calendar
	$('#datepick').DatePickerSetDate(Selected_Date, true);

	// Default date in right column above available slot box
	change_availability_date(Selected_Date);

	// Empty div where all slots listed
	$('.checkbox-main-grid').html('');

	// Clear form
	document.getElementById("addEventForm").reset();

	Available_Slots = null;
}

// Send selected slot and selected date and get available slots from all sync
// calendar.
function get_slots(s_date, s_slot)
{
	MIDNIGHT_START_TIME = null;
	MIDNIGHT_END_TIME = null;
	var selected_epoch_start = getSelectedTimeFromDate(s_date);
	// Current timezone name
	var timezoneName = SELECTED_TIMEZONE;

	// selected date in current epoch time
	var epochTime = getEpochTimeFromDate(s_date); // milliseconds

	var d = new Date(s_date);
	var currMonth = (d.getMonth() + 1);
	if (currMonth < 10)
		currMonth = "0" + currMonth;
	var currentDate = d.getFullYear() + '-' + currMonth + '-' + d.getDate();
	//console.log(d);
	var secs = epochTime + d.getSeconds() + (60 * d.getMinutes()) + (60 * 60 * d.getHours());
	// gets the midnight of selected date. selected date will be stored in
	// global variable i.e current_date_mozilla
	MIDNIGHT_START_TIME = selected_epoch_start = getMidnightEpoch();
	MIDNIGHT_END_TIME = selected_epoch_end = selected_epoch_start + 86400;
	var start_time = getEpochTimeFromDate(d);
	d.setDate(d.getDate() + 1)
	var end_time = getEpochTimeFromDate(d);
	var timezone = getTimezoneOffset();

	// Send request to get available slot
	var initialURL = 'include/getData.php?mode=getslots&user_id=' + User_Id + '&curdate='+currentDate+'&date=' + s_date + '&slot_time=' + s_slot + "&timezone_name=" + timezoneName + "&epoch_time=" + epochTime + "&startTime=" + selected_epoch_start + "&endTime=" + selected_epoch_end + "&timezone=" + timezone;
	
	$.ajax({
		url : initialURL,
		type : 'POST',
		success : function(data){
        var data = JSON.parse(data);
		// No slots available for selected day
		if (data.length == 0)
		{
			displayNoSlotsMsg();
			return;
		}
		//console.log(data.slots);
		Available_Slots = data.slots;
		//SELECTED_TIMEZONE = data.timezone;
		$('#user_timezone').val(data.timezone);
		$('#base_timezone').html(data.timezone.replace('_', ' '));
		// Update in UI
		displaySlots();
       },
       error : function(data){
		console.log(data);
	   }
	});
  
}

// Add no slots available msg in grid of checkbox
function displayNoSlotsMsg()
{
	// Empty div where all slots listed, to display new slots
	$('.checkbox-main-grid').html('');

	var date = new Date(selecteddate);

	$('.availability').html(LOCALES_JSON['no-valid-slot'] + " " + date.getDayName() + ", " + date.getMonthName() + ", " + date.getDate());

}

// Add slots in grid checkbox in checkbox list
function displaySlots(groupView)
{
	var i = 0, j = 0, k = 0;

	// Empty div where all slots listed, to display new slots
	$('.checkbox-main-grid').html('');

	//console.log(SELECTED_TIMEZONE);
	var after_now = [];
	var date = new Date();
	if(groupView)
		BUFFERTIME = 0;
	if (BUFFERTIME == null)
	{
		BUFFERTIME = buffer_time;
	}
	
	var current_date_time = date.getTime() + parseInt(BUFFERTIME);
	
	for (var s = 0; s < Available_Slots.length; s++)
	{
		after_now.push(Available_Slots[s]);
	}
//	console.log(after_now.length);
	Available_Slots = "";
	Available_Slots = after_now;

	if (Available_Slots.length == 0)
	{
		displayNoSlotsMsg();
		return;
	}

	change_availability_date(selecteddate);
	// Number of row
	var numRow = Available_Slots.length / 6;

	numRow++;

	var addList = function()
	{
		var listSlot = "";
		for (i = 0; i <= numRow; i++)
		{
			if (k < Available_Slots.length){
				if(groupView)
				    listSlot = listSlot + '<li><input class="selected-slot-users" type="checkbox" id="startTime_' + k + '"name="startTime_' + k + '" value="' + Available_Slots[k][0] + '" /><label for="' + Available_Slots[k][0] + '">' + Available_Slots[k][0] + '</label></li>';
                else
					listSlot = listSlot + '<li><input class="selected-slot" type="checkbox" id="startTime_' + k + '"name="startTime_' + k + '" value="' + Available_Slots[k][0] + '" /><label for="' + Available_Slots[k][0] + '">' + Available_Slots[k][0] + '</label></li>';
            }
			k++;
		}
		return listSlot;
	}

	// 5 columns
	for (j = 0; j < 7; j++)
	{
		// Add number of rows, slots with time conversion
		$('.checkbox-main-grid').append('<li><ul class="checkbox-grid">' + addList() + '<ul><li>');
	}
}

// Validates the form fields
function isValid(formId)
{

	$(formId).validate();
	return $(formId).valid();
}

// Save selected slot with user details
function save_web_event(formId, confirmBtn)
{
	// Check if the form is valid
	if (!isValid('#' + formId))
	{
		$('#' + formId).find("input").focus();
		return false;
	}

	// Get details
	var data = $('#' + formId).serializeArray();

	// Make json
	var web_calendar_event = {};
	$.each(data, function()
	{
		if (web_calendar_event[this.name])
		{
			if (!web_calendar_event[this.name].push)
			{
				web_calendar_event[this.name] = [
					web_calendar_event[this.name]
				];
			}
			web_calendar_event[this.name].push(this.value || '');
		}
		else
		{
			web_calendar_event[this.name] = this.value || '';
		}
	});

	// Add selected parameter which are out of form
	web_calendar_event["name"] = appointmenttype;
	// web_calendar_event["date"] = Selected_Date;
	web_calendar_event["slot_time"] = Selected_Time;
	web_calendar_event["domainUserId"] = User_Id;
	web_calendar_event["selectedSlotsString"] = [];
	web_calendar_event["timezone"] = SELECTED_TIMEZONE;
	web_calendar_event["midnight_start_time"] = MIDNIGHT_START_TIME;
	web_calendar_event["midnight_end_time"] = MIDNIGHT_END_TIME;
	web_calendar_event["timezone_offset"] = getTimezoneOffset();
	// Get selected slots in UI from available slots list.
	var i = 0;
	for ( var prop in web_calendar_event)
	{
		if (prop.indexOf("startTime") != -1)
		{
			var res = prop.split("_");

			var result = {};
			result["start"] = Available_Slots[res[1]][0];
			result["end"] = Available_Slots[res[1]][1];
			web_calendar_event["selectedSlotsString"][i] = result;
			i++;
				
			web_calendar_event["date"] = Selected_Date;
		}
	}
	
	if (web_calendar_event["selectedSlotsString"].length == 0)
	{
		alert("Please select appointment time.");		
		return false;
	}

	$('#confirm').attr('disabled', 'disabled');
	$('#three').addClass('green-bg').html('<i class="fa fa-check"></i>');
	// Add selected slots to input json
	web_calendar_event["selectedSlotsString"] = JSON.stringify(web_calendar_event["selectedSlotsString"]);

	$(confirmBtn).val('Please wait');
	$(confirmBtn.form).find('input, textarea, button, select').attr('disabled','disabled');
	var input = {};
	input.data = JSON.stringify(web_calendar_event);
	var url = 'include/getData.php?mode=save';
    
	// Send request to save slot, if new then contact, event
	$.ajax({
		url : url,
		type : 'POST',
		data : input,
        async: false,
		success : function(res){
			var res = JSON.parse(res);
			if(res.success){
				// style="border-bottom: 1px solid #ddd;"
				var dates = JSON.parse(web_calendar_event.selectedSlotsString);
				var d = dates[0];
				
				var start = Selected_Date + ', ' + d.start;
				
				$('#mainwrap').addClass("appointment-wrap");
				var appointment_success_img1 = "img/appointment_confirmation.png";
				var temp = '<div class="appointment-body">'
	
				+ '<div id="info" ><h3 style="border-bottom: 1px solid #ddd;padding-bottom:8px;margin-bottom:15px;"><img style="margin-right: 8px;margin-top: -4px;" src=' + appointment_success_img1 + '><b>' +LOCALES_JSON['appointment-scheduled']+ '</b></h3>' + '<p >'+LOCALES_JSON['your-appointment']+' (' + appointmenttype + ') '+LOCALES_JSON['you-scheduled']+' '+LOCALES_JSON['for']+' ' + web_calendar_event.slot_time + ' '+LOCALES_JSON['mins-on']+' ' + start + '. </div>' + '<div class="row">' + '<div class="col-md-12">' + '<div class="row">' + '<div class="col-md-12">' + '<div class="left">' + '<a class="btn btn-primary" id="create_new_appointment" style="margin-top:20px;">'+LOCALES_JSON['scheduled-another-appointment']+'</a>' + '</div>' + '</div>' + '</div>' + '</div>';
				var powered_by_img = '';
				resetAll();
	
				$(".container").html(temp).after(powered_by_img);
			}
		},
		error : function(res){
			//console.log(res);
			$(confirmBtn).val('Confirm');
			$(confirmBtn.form).find('input, textarea, button, select').removeAttr('disabled');
			
			if(res.responseText == "slot booked"){
				alert(LOCALES_JSON['solt-book-error']);
				get_slots(selecteddate, Selected_Time);
				$('#confirm').attr('disabled', false);											
			}else{
				alert(LOCALES_JSON['slot-exists'] + "Error: " + res.statusText);
				resetAll();
				location.reload(true);
			}		
		}
	});
}

function convertToHumanDate(format, date)
{

	if (!format)
		format = "ddd, mmmm d yyyy, h:MM TT";

	if (!date)
		return;

	if ((date / 100000000000) > 1)
	{
		return new Date(parseInt(date)).format(format, 0);
	}
	// date form milliseconds
	var d = new Date(parseInt(date) * 1000).format(format);

	return d
}

function convertToHumanDateUsingMoment(format, date)
{

	if (!format)
		format = "ddd, MMM DD YYYY, HH:mm";

	if (!date)
		return;
	var date = moment.unix(date);
	var time_s = date.tz(SELECTED_TIMEZONE).format(format);
	return time_s;
}

$(function(){
	$('body').on('click','#create_new_appointment',function(e)
	{
		location.reload(true);
	});	
});

/**
 * if value morethan 50 adds .. at the end
 */
function addDotsAtEnd(title)
{
	if (title)
	{
		if (title.length > 10)
		{
			var subst = title.substr(0, 10);
			subst = subst + "....";
			return subst;
		}
	}

	return title;
}


function displayTime(mins){
	var time ="";
	if(mins){
	  var hours = Math.floor(mins/60);
	  var minutes = mins % 60;
	  if(hours > 0){
		  if(hours > 1){
		  	time += hours + " hrs ";
		  }else{
		  	time += hours +" hr ";
		  }
	  }
	  if(minutes > 0){
		  if(minutes > 1){
		  	time += minutes + " " + LOCALES_JSON['mins'];
		  }else{
		  	time += minutes + " min";
		  }
	  }
	}
  return time;
}

function getPanelBodyMaxHeight()
{
	var max = 0;
	$('.panel-body').each(function()
	{
		var height = $(this).height();
		if (height > max)
		{
			max = height;
		}
	});
	return max;
}

function generateNewDataArray(data)
{
	var finalJsonArray = [];
	for ( var slotDetail in data)
	{
		var json = JSON.parse(data[slotDetail]);

		var json_meeting_names = [];
		if (json.title.indexOf(",") > -1)
		{
			json_meeting_names = json.title.split(",");
		}
		else
		{
			json_meeting_names.push(json.title);
		}
		if (json_meeting_names.length > 0)
		{
			var newJson = {};
			newJson.time = json.time;
			newJson.meeting_names = json_meeting_names;
			finalJsonArray.push(JSON.stringify(newJson));

		}
		else
		{
			finalJsonArray.push(JSON.stringify(json));
		}

	}
	return finalJsonArray;
}
function getGeneralizeSlots(slot_time){
		
		$('#two').removeClass("green-bg").html('2');
		$(".segment3").addClass("hide");
		$(".segment4").fadeOut("fast");
		$("#confirm").hide();


		var isFirefox = typeof InstallTrigger !== 'undefined';
		if (isFirefox)
		{
			$('#datepick').DatePickerSetDate(current_date_mozilla, true);
		}
		//$('.checkbox-main-grid').html('<img class="loading-img" src="img/21-0.gif" style="width: 40px;margin-left: 216px;"></img>');
		if (!selecteddate)
		{
			selecteddate = new Date();
			CURRENT_DAY_OPERATION = true;

		}
		var start_time = "";
		var end_time = "";
	    
	    // gets the midnight of selected date. selected date will be stored in
	    // global variable i.e current_date_mozilla
	    MIDNIGHT_START_TIME = start_time = getMidnightEpoch();
	    MIDNIGHT_END_TIME = end_time = start_time + 86400;
	    var timezone = getTimezoneOffset();
	    var itr = (24*60)/parseInt(slot_time);
	    var slots = [];
	    for(var i=0; i< itr ; i++){
	    	var slot = [];
	    	slot.push(start_time);
	    	start_time = start_time + (parseInt(slot_time)*60);
	    	slot.push(start_time);
	    	slots.push(slot);
	    }
	    Available_Slots = slots;
	    displaySlots(true);
}

