/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Field_Js("Calendar_Field_Js",{},{})


Vtiger_Field_Js('Calendar_Datetime_Field_Js',{},{

	getDateFormat : function(){
		return this.get('date-format');
	},
	
	getUi : function() {
		
		var strVale = this.getValue();
		var strArr = strVale.split(' ');
		var valArr = [];
		for(i=0; i < strArr.length; i++)
		   valArr.push(strArr[i]);
		
		var dateVal = valArr[0];
		var timeVal = valArr[1];
		
		if(valArr[2])
			timeVal += ' '+valArr[2];

		if(this.getName() == 'date_start'){
			
			var timeField = 'time_start';
			var timeFieldDetails = uimeta.field.get(timeField);
			
		}else if(this.getName() == 'due_date'){
			
			var timeField = 'time_end';
			var timeFieldDetails = uimeta.field.get(timeField);
			
		}
		
		var html = '<div class="referencefield-wrapper"><div class="input-group date">'+
						'<input class="inputElement dateField form-control" type="text" data-rule-date="true" data-format="'+ this.getDateFormat() +'" name="'+ this.getName() +'" value="'+ dateVal + '" />'+
						'<span class="input-group-addon"><i class="fa fa-calendar"></i></span>'+
					'</div></div>'+'<div class="referencefield-wrapper">'+'<div class="input-group time">'+
					'<input class="timepicker-default form-control inputElement" type="text" data-format="'+ timeFieldDetails['time-format'] +'" name="'+ timeFieldDetails['name'] +'" value="'+ timeVal + '" />'+
					'<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>'+
					'</div>'+'</div>';
		
		var element = jQuery(html);
		return this.addValidationToElement(element);
		
	}
	
});
Vtiger_Field_Js('Vtiger_Multireference_Field_Js',{},{

	getReferenceModules : function(){
		return this.get('referencemodules');
	},

	getUi : function(){
		
		var referenceModules = this.getReferenceModules();
		var value = this.getValue();
		
		var html = '<div class="referencefield-wrapper';
		if(value){
			html += ' selected';
		} else {
			html += '"';
		}
		html += '">';
		html += '<input name="popupReferenceModule" type="hidden" value="'+referenceModules[0]+'"/>';
		html += '<div class="input-group ">'
		html += '<input class="autoComplete inputElement sourceField" data-multiple="true" type="search" data-fieldtype="multireference" name="'+this.getName()+'"';
		if(value){
			html += ' value="'+value+'" ';
		}
		html += '/>';

		
		//popup search element
		html += '<span class="input-group-addon relatedPopup cursorPointer" title="'+referenceModules[0]+'">';
		html += '<i class="fa fa-search"></i>';
		html += '</span>';

		html += '</div>';
		html += '</div>';
		return this.addValidationToElement(html);
	}

});

Vtiger_Field_Js('Vtiger_Reminder_Field_Js',{},{

	/**
	 * Function to check whether the field is checked or not
	 * @return <Boolean>
	 */
	isChecked : function() {
		var value = this.getValue();
		if(value==1 || value == '1' || (value && (value.toLowerCase() == 'on' || value.toLowerCase() == 'yes'))){
			return true;
		}
		return false;
	},

	/**
	 * Function to get the ui
	 * @return - checkbox element
	 */
	getUi : function() {
		
		var value = this.getValue();
		
		var day = '';
		var hour = '';
		var min = '';
		
		if(value){
			var days = value.split(' days ');
			
			if(days.length > parseInt('1')){
				var day = days[0];
				var hours = days[1].split(' hours ');
			}else{
				var hours = days[0].split(' hours ');
			}
			
			if(hours.length > parseInt('1')){
				var hour = hours[0];
				var mins = hours[1].split(' minutes');
			}else{
				var mins = hours[0].split(' minutes');
			}
			var min = mins[0];
		}
		//var	html = '<input type="hidden" name="'+this.getName() +'" value="0"/><input class="inputElement" type="checkbox" name="'+ this.getName() +'" ';
		var	html = '<input type="hidden" name="set_reminder" value="0"/><input class="inputElement" type="checkbox" name="set_reminder" ';
		if(value) {
			html += 'checked';
		}
		html += ' />'
		html += '<div id="js-reminder-selections" style="float:left;margin: 0px 10px 5px 10px;visibility:';
		if(value) {
			html += 'visible';
		}else{
			html += 'collapse';
		}
		html += '">';
		html+= '<div style="float:left">'+
				'<div style="float:left">'+
					'<select class="select2" name="remdays">';
						for (i = 0; i <= 31; i++) {
							html += '<option value="'+i+'"';
							if(day == i)
								html += 'selected';
							html += '>'+i+'</option>';
						}
					html += '</select>'+
				'</div>'+
				'<div style="float:left;margin-top:5px">'+
					'&nbsp;Days&nbsp;&nbsp;'+
				'</div>'+
				'<div class="clearfix"></div>'+
			'</div>'+
			'<div style="float:left">'+
				'<div style="float:left">'+
					'<select class="select2" name="remhrs">';
						for (h = 0; h <= 23; h++) {
							html += '<option value="'+h+'" ';
							if(hour == h)
								html += 'selected';
							html += '>'+h+'</option>';
						}
					html += '</select>'+
				'</div>'+
				'<div style="float:left;margin-top:5px">'+
					'&nbsp;Hours&nbsp;&nbsp;'+
				'</div>'+
				'<div class="clearfix"></div>'+
			'</div>'+
			'<div style="float:left">'+
				'<div style="float:left">'+
					'<select class="select2" name="remmin">';
					for (m = 1; m <= 59; m++) {
						html += '<option value="'+m+'" ';
						if(min == m)
							html += 'selected';
						html += '>'+m+'</option>';
					}
				html+='</select>';
				html += '</div>';
				html += '<div style="float:left;margin-top:5px">';
				html += '&nbsp;minutes&nbsp;&nbsp;';
				html += '</div>';
				html += '<div class="clearfix"></div>';
			html+= '</div>';
		html+= '</div>';
		html += '<div class="clearfix"></div>';
		
		this.registerToggleReminderEvent();
		
		return this.addValidationToElement(html);
	},
	
	registerToggleReminderEvent : function() {
		
		$(document).on('change', 'input[name="set_reminder"]', function(e) {
			var element = jQuery(e.currentTarget);
			
			var reminderSelectors = element.parent().find('#js-reminder-selections');
			if(element.is(':checked')) {
				reminderSelectors.css('visibility','visible');
			} else {
				reminderSelectors.css('visibility','collapse');
			}
		})
	},

});

