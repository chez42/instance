/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function updateClock(force) {
	var clock_counter = document.getElementById('clock_counter');
	clock_counter.value++;
	var clock_display_separator = document.getElementById('clock_display_separator');
	if (clock_counter.value % 2) {
		clock_display_separator.style.visibility = 'hidden';
	} else {
		clock_display_separator.style.visibility = 'visible';
	}
	var clock_display_separator2 = document.getElementById('clock_display_separator2');
	if (clock_counter.value % 2) {
		clock_display_separator2.style.visibility = 'hidden';
	} else {
		clock_display_separator2.style.visibility = 'visible';
	}
	
	var seconds = parseInt(clock_counter.value)%60;
	if (clock_counter.value % 60 == 0 || force) {
		var hours = parseInt(clock_counter.value / 60 / 60);
		var minutes = parseInt(clock_counter.value / 60) % 60;
		
		if (hours < 10) {
			hours = '0' + hours;
		}
		if (minutes < 10) {
			minutes = '0' + minutes;
		}
		var clock_display_hours = document.getElementById('clock_display_hours');
		var clock_display_minutes = document.getElementById('clock_display_minutes');
		clock_display_hours.replaceChild(document.createTextNode(hours),clock_display_hours.firstChild);
		clock_display_minutes.replaceChild(document.createTextNode(minutes),clock_display_minutes.firstChild);
	}
	if (seconds < 10) {
		seconds = '0' + seconds;
	}
	var clock_display_seconds = document.getElementById('clock_display_seconds');
	clock_display_seconds.replaceChild(document.createTextNode(seconds),clock_display_seconds.firstChild);
}

Vtiger_Detail_Js("HelpDesk_Detail_Js", {}, {
    
    /**
     * This function is used to transform href(GET) request of CovertFAQ
     * function to POST request because we are hitting action URL, So it should
     * be post request with valid token
     * */
    regiterEventForConvertFAQ: function () {
        var eleName = '#'+app.getModuleName()+'_detailView_moreAction_LBL_CONVERT_FAQ';
        var ele = jQuery(eleName).find('a');
        ele.on('click',function(e){
            var url = ele.attr('href');
            e.preventDefault();
            var form = jQuery("<form/>",{method:"post",action:url});
            form.append(jQuery("<input/>",{type:"hidden",name:csrfMagicName,value:csrfMagicToken}));
            form.appendTo('body').submit();
        });
    },

	/**
	 * Ajax Edit Save Event
	 * @param {type} currentTdElement
	 * @returns {undefined}
	 */
	registerAjaxEditSaveEvent : function(contentHolder){
		var thisInstance = this;
		if(typeof contentHolder === 'undefined') {
			contentHolder = this.getContentHolder();
		}

		contentHolder.on('click','.inlineAjaxSave',function(e){
			e.preventDefault();
			e.stopPropagation();
			var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = thisInstance.getInlineWrapper(currentTarget); 
			var detailViewValue = jQuery('.value',currentTdElement);
			var editElement = jQuery('.edit',currentTdElement);
			var actionElement = jQuery('.editAction', currentTdElement);
			var fieldBasicData = jQuery('.fieldBasicData', editElement);
			var fieldName = fieldBasicData.data('name');
			var fieldType = fieldBasicData.data("type");
			var previousValue = jQuery.trim(fieldBasicData.data('displayvalue'));

			var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			var ajaxEditNewValue = fieldElement.val();

			 // ajaxEditNewValue should be taken based on field Type
			if(fieldElement.is('input:checkbox')) {
				if(fieldElement.is(':checked')) {
					ajaxEditNewValue = '1';
				} else {
					ajaxEditNewValue = '0';
				}
				fieldElement = fieldElement.filter('[type="checkbox"]');
			} else if(fieldType == 'reference'){
				currentTdElement.prev('td').find('.referenceSelect').addClass('hide');
				if(currentTdElement.prev('td').find('.referenceSelect').length)
					currentTdElement.prev('td').find('.muted').removeClass('hide');
				ajaxEditNewValue = fieldElement.data('value');
			}

			// prev Value should be taken based on field Type
			var customHandlingFields = ['owner','ownergroup','picklist','multipicklist','reference','boolean']; 
			if(jQuery.inArray(fieldType, customHandlingFields) !== -1){
				previousValue = fieldBasicData.data('value');
			}

			// Field Specific custom Handling
			if(fieldType === 'multipicklist'){
				var multiPicklistFieldName = fieldName.split('[]');
				fieldName = multiPicklistFieldName[0];
			} 

			var fieldValue = ajaxEditNewValue;

			//Before saving ajax edit values we need to check if the value is changed then only we have to save
			if(previousValue == ajaxEditNewValue) {
				detailViewValue.css('display', 'inline-block');
				editElement.addClass('hide');
				editElement.removeClass('ajaxEdited');
				jQuery('.editAction').removeClass('hide');
				actionElement.show();
			}else{
				var fieldNameValueMap = {};
				fieldNameValueMap['value'] = fieldValue;
				fieldNameValueMap['field'] = fieldName;
				var form = currentTarget.closest('form');
				var params = {
					'ignore' : 'span.hide .inputElement,input[type="hidden"]',
					submitHandler : function(form){
						var preAjaxSaveEvent = jQuery.Event(Vtiger_Detail_Js.PreAjaxSaveEvent);
						app.event.trigger(preAjaxSaveEvent,{form:jQuery(form),triggeredFieldInfo:fieldNameValueMap});
						if(preAjaxSaveEvent.isDefaultPrevented()) {
							return false;
						}

						jQuery(currentTdElement).find('.input-group-addon').addClass('disabled');
						app.helper.showProgress();
						thisInstance.saveFieldValues(fieldNameValueMap).then(function(err, response) {
							app.helper.hideProgress();
							if (err !== null) {
								app.event.trigger('post.save.failed', err);
								jQuery(currentTdElement).find('.input-group-addon').removeClass('disabled');
								return true;
							}
							jQuery('.vt-notification').remove();
							var postSaveRecordDetails = response;
							if(fieldBasicData.data('type') == 'picklist' && app.getModuleName() != 'Users') {
								var color = postSaveRecordDetails[fieldName].colormap[postSaveRecordDetails[fieldName].value];
								if(color) {
									var contrast = app.helper.getColorContrast(color);
									var textColor = (contrast === 'dark') ? 'white' : 'black';
									var picklistHtml = '<span class="picklist-color" style="background-color: ' + color + '; color: '+ textColor + ';">' +
															postSaveRecordDetails[fieldName].display_value + 
														'</span>';
								} else {
									var picklistHtml = '<span class="picklist-color">' +
															postSaveRecordDetails[fieldName].display_value + 
														'</span>';
								}
								detailViewValue.html(picklistHtml);
							} else if(fieldBasicData.data('type') == 'multipicklist' && app.getModuleName() != 'Users') {
								var picklistHtml = '';
								var rawPicklistValues = postSaveRecordDetails[fieldName].value;
								rawPicklistValues = rawPicklistValues.split('|##|');
								var picklistValues = postSaveRecordDetails[fieldName].display_value;
									picklistValues = picklistValues.split(',');
								for(var i=0; i< rawPicklistValues.length; i++) {
									var color = postSaveRecordDetails[fieldName].colormap[rawPicklistValues[i].trim()];
									if(color) {
										var contrast = app.helper.getColorContrast(color);
										var textColor = (contrast === 'dark') ? 'white' : 'black';
										picklistHtml = picklistHtml +
														'<span class="picklist-color" style="background-color: ' + color + '; color: '+ textColor + ';">' +
															 picklistValues[i] + 
														'</span>';
									} else {
										picklistHtml = picklistHtml +
														'<span class="picklist-color">' + 
															 picklistValues[i] + 
														'</span>';
									}
									if(picklistValues[i+1]!==undefined)
										picklistHtml+=' , ';
								}
								detailViewValue.html(picklistHtml);
							} else if(fieldBasicData.data('type') == 'currency' && app.getModuleName() != 'Users') {
								detailViewValue.find('.currencyValue').html(postSaveRecordDetails[fieldName].display_value);
								contentHolder.closest('.detailViewContainer').find('.detailview-header-block').find('.'+fieldName).html(postSaveRecordDetails[fieldName].display_value);
							}else {
								detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
								//update namefields displayvalue in header
								if(contentHolder.hasClass('overlayDetail')) {
									contentHolder.find('.overlayDetailHeader').find('.'+fieldName)
									.html(postSaveRecordDetails[fieldName].display_value);
								} else {
									contentHolder.closest('.detailViewContainer').find('.detailview-header-block')
									.find('.'+fieldName).html(postSaveRecordDetails[fieldName].display_value);
								}
							}
							
							fieldBasicData.data('displayvalue',postSaveRecordDetails[fieldName].display_value);
							fieldBasicData.data('value',postSaveRecordDetails[fieldName].value);
							jQuery(currentTdElement).find('.input-group-addon').removeClass("disabled");
							
							if(fieldName == 'parent_id'){
								$('.tab-item.active:not(.block)').trigger('click');
							}
							
							detailViewValue.css('display', 'inline-block');
							editElement.addClass('hide');
							editElement.removeClass('ajaxEdited');
							jQuery('.editAction').removeClass('hide');
							actionElement.show();
							var postAjaxSaveEvent = jQuery.Event(Vtiger_Detail_Js.PostAjaxSaveEvent);
							app.event.trigger(postAjaxSaveEvent, fieldBasicData, postSaveRecordDetails, contentHolder);
							//After saving source field value, If Target field value need to change by user, show the edit view of target field.
							if(thisInstance.targetPicklistChange) {
								var sourcePicklistname = thisInstance.sourcePicklistname;
								thisInstance.targetPicklist.find('.editAction').trigger('click');
								thisInstance.targetPicklistChange = false;
								thisInstance.targetPicklist = false;
								thisInstance.handlePickListDependencyMap(sourcePicklistname);
								thisInstance.sourcePicklistname = false;
							}
						});
					}
				};
				validateAndSubmitForm(form,params);
			}
		});
	},
    
    registerEvents : function() {
        this._super();
        this.regiterEventForConvertFAQ();
    }
});