<?php
include_once("include/config.php");

global $api_username, $api_accesskey, $api_url;

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;
    
$data = $_REQUEST ;
$data['mode'] = 'logo';
$postParams = array(
    'operation'=>'get_schedule_appointment',
    'sessionName'=>$session_id,
    'element'=>json_encode($data)
);

$response = postHttpRequest($ws_url, $postParams);

$response = json_decode($response,true);

$logoFile = $response['result']['logo'];

if(isset($response['result']['15min']) && $response['result']['15min'] != ''){
    $min15Text = $response['result']['15min'];
} else {
    $min15Text = 'say hi';
}

if(isset($response['result']['30min']) && $response['result']['30min'] != ''){
    $min30Text = $response['result']['30min'];
} else {
    $min30Text = "let's keep it short";
}


if(isset($response['result']['1hr']) && $response['result']['1hr'] != ''){
    $hr1Text = $response['result']['1hr'];
} else {
    $hr1Text = "let's chat";
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Online Appointment Scheduling</title>
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <!-- <link rel="stylesheet" href="css/font-awesome.min.css"> -->
        <!-- <link href='css/font.css?family=Lato:400,300' rel='stylesheet' type='text/css'> -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
        <link href='https://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
        
        <script type="text/javascript" src="js/jstz.min.js"></script>
        <script type="text/javascript" src="js/json2.js"></script>
        
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="js/date-formatter.js"></script>
        <script type="text/javascript" src="js/moment.min.js"></script>
        <script type="text/javascript" src="js/moment.timezone.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.v3.min.js"></script>
        
        <link rel="stylesheet" href="css/datepicker.css" type="text/css" />
        <script type="text/javascript" src="js/datepicker.js"></script>
        <script type="text/javascript" src="js/jquery.placeholder.js"></script>
        <script type="text/javascript" src="js/eye.js"></script>
        <script type="text/javascript" src="js/utils.js"></script>
        <script type="text/javascript" src="js/layout.js"></script>
        <script type="text/javascript" src="js/time.js"></script>
        <script type="text/javascript" src="js/util.js"></script>
        <script type="text/javascript" src="js/ui.js"></script>
        <script type="text/javascript" src="js/localize.js"></script>
        
        <style type="text/css">
        * {
        	font-family: 'Lato', sans-serif;
        }
        </style>
        <!--[if IE 7]>
        <style type="text/css">
        .col-md-4{
        width: 30px;
        top: 30px;
        left: -12%;
        }
        .col-md-8{
        width: 50%;
        float: right;
        }
        .checkbox-main-grid{
        width: 60%;
        position: relative;
        margin-left: 20%;
        }
        .checkbox-main-grid ul{
        margin: 10px;
        }
        .checkbox-main-grid ul li{
        padding-left: 10px;
        }
        </style>	
        <![endif]-->
        <!--[if gt IE 7]>
        <style type="text/css">
        .col-md-4{
        width: 30px;
        }
        .col-md-8{
        width: 40%;
        margin-left: 30%;
        }
        .col-sm-8 textarea{
        	position: relative;
        	top: 10px;
        	margin-left: 10%;
        }
        </style>	
        <![endif]-->
    </head>
    
    <body onload="bodyLoad();">
    	<div id="mainwrap" class="container">	
    
    	<img src="<?php if($logoFile) echo $logoFile; else echo 'logo.png'; ?>" id="avatar" class="thumbnail"/>
    	<div class="text-center"><p class='lead' style='color: #777;font-size: 19px;font-weight:normal'>Welcome to my scheduling page. Please follow the instructions to book an appointment.</p></div>
    
    		<div class="col-sm-10 segment segment1 blockdiv" >
    			<div class="numberlt" id="one">1</div>
    			<div class="event-title" style="font-weight:normal;">Choose a Time Slot</div>
    			<div class="col-sm-4 show_slots">
    				<p class="timeslot-view"></p>
    				<div class="panel panel-default">
    					<div class="panel-heading font-bold">15 mins </div>
    					<div class="panel-body" style="height: 98px;">
        					<form class="bs-example form-horizontal">
        						<div class="form-group" style="margin-left:7px;">
        							<div class="radio">
        								<label>
        									<input class="c-p selected_meeting_time" type="radio" data="15" name="selected_meeting_time" value="say hi">
        									<i></i><?php echo $min15Text;?>
    									</label>
        							</div>
        						</div>
        					</form>
        				</div>
        			</div>
        			<p></p>
    			</div>
    			<div class="col-sm-4 show_slots">
    				<p class="timeslot-view"></p>
    				<div class="panel panel-default">
    					<div class="panel-heading font-bold">30 mins </div>
    					<div class="panel-body" style="height: 98px;">
    						<form class="bs-example form-horizontal">
    							<div class="form-group" style="margin-left:7px;">
    								<div class="radio">
    									<label>
    										<input class="c-p selected_meeting_time" type="radio" data="30" name="selected_meeting_time" value="let's keep it short">
    										<i></i><?php echo $min30Text;?>
    									</label>
    								</div>
    							</div>
    						</form>
    					</div>
    				</div>
    				<p></p>
    			</div>
    			<div class="col-sm-4 show_slots">
    				<p class="timeslot-view"></p>
    				<div class="panel panel-default">
    					<div class="panel-heading font-bold">1 hr  </div>
    					<div class="panel-body" style="height: 98px;">
    						<form class="bs-example form-horizontal">
    							<div class="form-group" style="margin-left:7px;">
    								<div class="radio">
    									<label>
    										<input class="c-p selected_meeting_time" type="radio" data="60" name="selected_meeting_time" value="let's chat">
    										<i></i><?php echo $hr1Text;?>
    									</label>
    								</div>
    							</div>
    						</form>
    					</div>
    				</div>
    				<p></p>
    			</div>
    			<div class="clearfix"></div>
    		</div>
    
    		<form action="" id="addEventForm" name="addEventForm" method="post">
    			<fieldset>
    				<div class="col-sm-10 segment segment2 me-disable "
    					style="display: table;display:none">
    					<div class="numberlt" id="two">2</div>
    					<div class="event-title" style="margin-bottom:4px;margin-top:2px;font-weight:normal;">
    						<span class="pull-left datetimezone">Select Date and Time</span>
    						<span class="timezone ">											
    							<span id="base_timezone"class="font-normal"></span>
    						</span>
    						<div class="clearfix"></div>
    					</div>
    					<div class="col-md-4 col-sm-12 col-xs-12">
    						<div id="datepick" style="height:215px;"></div>
    					</div>
    					<div class="col-md-8 col-sm-12 col-xs-12">
    						<p class="availability">Availability on</p>
    						<ul class="checkbox-main-grid">
    
    						</ul>
    					</div>
    					<div class="clearfix"></div>
    
    				</div>
    		
    
    				<div class="col-sm-10 segment segment3 me-disable" style="display:none">
    
    					<div class="numberlt" id="three">3</div>
    					<div class="event-title" style="margin-bottom:20;margin-top: 5px;font-weight:normal">
    						Contact Info</div>
    
    					<div class="col-sm-4">
    						<input type="text" id="topic" name="topic"
    							placeholder='Topic' class="required me-disable"
    							disabled="disabled" />
    						<input type="text" id="userName" name="userName"
    							placeholder='Name' class="required me-disable"
    							disabled="disabled" /> <input type="text" id="email"
    							name="email" placeholder='Email' class="required me-disable"
    							disabled="disabled" /> <input type="text" id="phoneNumber"
    							name="phoneNumber" placeholder='Phone Number' class="required me-disable"
    							disabled="disabled" />
    							
    							<select class="form-control meetingtypes" style="border: 1px solid #74B9EF;height:37px" title='Meeting Type' name="meetingType" id="meetingType">
    							 	<option selected disabled>Meeting Type</option>
                            		<option value="Meeting">Meeting</option>
                            		<option value=" Call"> Call</option>
<!--                             		<option value=" Skype"> Skype</option> -->
<!--                             		<option value=" Google Hangouts"> Google Hangouts</option> -->
                            	</select>
    					
    						<div class="clearfix"></div>
    						<input type="checkbox" id="confirmation" name="confirmation"  checked
    							class="me-disable" disabled="disabled" style="margin-top: 10px;" /> <label
    							style="margin-top: 7px;" for="confirmation" >Send me a confirmation email</label>
    					</div>
    
    					<div class="col-sm-8">
    						<textarea class="inputtext me-disable" rows="7" cols="90"
    							id="notes" name="notes" placeholder='Notes (Phone number/Skype details)' disabled="disabled"></textarea>
    					</div>
    					<div class="clearfix"></div>
    				</div>
    
    			</fieldset>
    			<div align="center" style="margin:0 auto;width:105px;">
        			<input type="submit" value='Confirm' id="confirm" class="me-disable" style="display:none"
        				disabled="disabled" />
    			</div>
    		</form>
    		  
    	</div>	
    
    <script>
    var User_Id = "<?php  echo $_REQUEST['user_name'];?>";
    var CALENDAR_WEEK_START_DAY=0;
    var selecteddate="";
    var SELECTED_TIMEZONE="";
    var current_date_mozilla="";
    
    var slot_array=null;
    var multi_user_ids=[];
    var mapobject={};
    var business_hours_array=[];
    var multiple_schedule_ids=false;
    var meeting_types=[];
    var slot_details=[];
    var buffer_time = 0;
    var CURRENT_DAY_OPERATION=null;
    var MEETING_DURATION_AND_NAMES=null;
    var BUFFERTIME=null;
    var LOCALES_JSON = {"appointment-scheduled":"Appointment Scheduled","cancel":"Cancel","slot-exists":"Something went wrong as your appointment was not scheduled. Please try again","phone_no":"Phone Number","minlength":"Please enter at least {0} characters.","solt-book-error":"Looks like this slot is booked already. Please try another one.","is-cancelled":"is cancelled","email-validation":"We need your email address to contact you","for":"for","something-wrong":"Something is wrong","sendconfirmation":"Send me a confirmation email","on-availability":"Availability on","holiday-today":"Today is holiday","welcome-message-bulk":"Welcome to our scheduling page. Please follow the instructions to book an appointment.","mins":"mins","scheduled-another-appointment":"Schedule Another Appointment","event-starts":"Event starts","cancelled-appointment":"Appointment Cancelled","schedule-new":"Schedule new appointment","meeting-type":"Meeting Type","contact-info":"Contact Info","email":"Email","your-appointment":"Your appointment","pl-enter-valid-slot":"Please enter valid slot number","cancel-appointment":"Cancel Appointment","no-valid-slot":"No slots available for","choose-time-slot":"Choose a Time Slot","select-a-person":"Select a Person","confirm":"Confirm","name-validation":"Please specify your name","you-scheduled":"has been scheduled ","with":"with","welcome-message-single":"Welcome to my scheduling page. Please follow the instructions to book an appointment.","cancelled-already":"Looks like this appointment is already cancelled.","reasong-for-cancel":"Reason for cancellation","online-appointment-scheduling":"Online Appointment Scheduling","your-appointment-with":"Your appointment with","name":"Name","no-working-hrs":"No working hours","select-slot":"Select a Slot","select-date-time":"Select Date and Time","email-format":"Your email address must be in the format of name@domain.com","online-appointment-cancellation":"Online Appointment Cancellation","user-phone-details":"Notes (Phone number/Skype details)","mins-on":"mins on"};
    var NAME="";
    var EMAIL="";
     </script>
    
    	<script type="text/javascript">
    		// Default selected slot is 60min
    		var Selected_Time = null;
    
    		// Default selected date will be current date
    		var Selected_Date = null;		
    
    		$(document).ready(
    
    				function(){
    					// Get current date
    					var newDate = new Date();
    					var currMonth = (newDate.getMonth() + 1);
    					if (currMonth < 10)
    						currMonth = "0" + currMonth;
    					var currentDate = newDate.getFullYear() + '-' + currMonth + '-' + newDate.getDate();
    					//console.log("in doc ready");
    					//console.log(currentDate);
                     	current_date_mozilla=currentDate;
    					// Set current date as selected date
    					Selected_Date = currentDate;
                      	var ms=86400000;
    					// Initialize date picker
    					var locale_dates;
    					if(!!navigator.userAgent.match(/Trident/g) || !!navigator.userAgent.match(/MSIE/g))
    					locale_dates = $.fn.datepicker.dates['en'];
    					$('#datepick').DatePicker({ flat : true, date : [
    							'2014-07-6', '2016-07-28'
    					], current : '' + currentDate, format : 'Y-m-d', calendars : 1,starts: CALENDAR_WEEK_START_DAY, mode : 'single', view : 'days', locale : locale_dates,
    					onRender: function(date) {
    						return {
    							
                                     disabled: (date.valueOf() < new Date().getTime()-ms),
    								 className: date.valueOf() < new Date().getTime()-ms ? 'datepickerNotInMonth' : false
    						}
    					},onChange : function(formated, dates)
    					{
        					
    						CURRENT_DAY_OPERATION=false;
    						//console.log("In date picker on change");
    						//console.log(formated + "  " + dates);
    						selecteddate=dates;
    						// On date change change selected date
    						Selected_Date = formated;
    						$('.user_in_visitor_timezone').html(SELECTED_TIMEZONE);
    						updateUserBusinessHoursInVisitorTimezone(dates);
    						
    						//setting the date to current_date_mozilla variable becoz it doesn't shppot new date format
    						current_date_mozilla=Selected_Date;
    					
    						// Check user select date
    						if ($('.segment2').hasClass('me-disable'))
    							return;
    
    						
    						// Date change in right column above available slot box
    						change_availability_date(dates);
    						
    						// Add loading img
    						$('.checkbox-main-grid').html('<img class="loading-img" src="img/21-0.gif" style="width: 40px;margin-left: 216px;"></img>');
    
    						//console.log(dates+"      "+Selected_Time);
    					
    						// Get available slots With new date
    						get_slots(dates, Selected_Time);
    					} });
    
    					// Setup form validation on the #register-form element
    					$('#addEventForm').validate({
    						// Specify the validation rules
    						rules : { userName : { required : true, minlength : 3 }, email : { required : true, email : true } },
    
    						// Specify the validation error messages
    						messages : {
    							userName : { required : LOCALES_JSON['name-validation'], minlength : jQuery.format(LOCALES_JSON['minlength']) },
    							email : { required : LOCALES_JSON['email-validation'],
    								email : LOCALES_JSON['email-format'] } },
    
    						submitHandler : function(form)
    						{
    							form.submit();
    						} 
    					});
      
    				});
    
    		function bodyLoad()
    		{
             	$(".segment1").removeClass("blockdiv");
    			SELECTED_TIMEZONE=jstz.determine().name();			
//     			$('#user_timezone').val(SELECTED_TIMEZONE);
//     			$('#base_timezone').html(SELECTED_TIMEZONE);
//     			$('#base_timezone').click(function()
//     			{
//     				$('#base_timezone').addClass('hidden');
//     				$('#user_timezone').removeClass('hidden');
//     			});
    			$("#current_local_time").html("Current Time: "+getConvertedTimeFromEpoch(new Date().getTime()/1000) );
    			//console.log("bodyonlod  : " + Selected_Date);
    		
    			// Set current date in calendar
    			$('#datepick').DatePickerSetDate(Selected_Date, true);
    
    			// Default date in right column above available slot box
    			change_availability_date(Selected_Date);
    			if(!!navigator.userAgent.match(/Trident/g) || !!navigator.userAgent.match(/MSIE/g)){
    				$(".datepickerViewDays > .datepickerDays > tr > td").each(function(){
    					if(parseInt($(this).find('span').text()) == Selected_Date.getDate()){
    						if(!$(this).hasClass("datepickerSelected") && !$(this).hasClass("datepickerNotInMonth")){
    							$(this).addClass("datepickerSelected");
    				    	    return false;
    				    	}
    				    }
    				});
    			}
    
    		    $('input[type=text], textarea').placeholder();
    		}
    	</script>
    </body>
</html>

