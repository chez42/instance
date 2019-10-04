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
		var timeVal = valArr[1]+' '+valArr[2];

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

