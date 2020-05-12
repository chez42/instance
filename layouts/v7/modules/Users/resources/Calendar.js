/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Settings_Users_PreferenceDetail_Js("Settings_Users_Calendar_Js",{},{
    
	
	registerEventForMsExchange : function(){
		$(document).on('click', '.syncNow', function(e) {
			var module = $(this).data('module');
            var params = {
                module : 'MSExchange',
                view : 'Sync',
                source_module : module
            }
            app.helper.showProgress();
            app.request.post({data: params}).then(function(error, data){
                app.helper.hideProgress();
				if(data.success){
					app.helper.showSuccessNotification({"message":'Sync Successfully'});
                } else {
                	app.helper.showErrorNotification({message : data.error});
				}
            });
        });
		
		$(document).on("click", ".revokeMSAccount", function(e){
			var module = $(this).data('module');
			var params = {
				module : 'MSExchange',
				view : 'List',
				operation : 'deleteSync',
				sourcemodule : module
			};
			app.helper.showProgress();
			app.request.post({data: params}).then(function(error, data){
				app.helper.hideProgress();
				window.location.reload();
			});       
		});
	},
	
	registerEventForBusinessHours : function(){
		
		$(document).on("click", ".bus_hours", function(e){
			var day = $(this).data("day");
			$('.'+day+'_pick').toggle();
			if ($('.'+day+'_pick').is(":visible")){
				$(this).removeClass('btn-danger');
				$(this).addClass('btn-success');
			}
			if ($('.'+day+'_pick').is(":hidden")){
				$(this).removeClass('btn-success');
				$(this).addClass('btn-danger');
				$('.'+day+'_pick').find('.inputElement').each(function(){
					$(this).val('').trigger('change');
				});
			}
		});
		
	},
	
	registerEventForCopyAppointmentUrl : function(){
		
		$(document).on("click", ".copytoclipboard", function(){
			
			var text = $(this).data('url');
			var textArea = document.createElement( "textarea" );
			textArea.value = text;
			document.body.appendChild( textArea );
			textArea.select();
			try {
				var successful = document.execCommand( 'copy' );
				if(successful)
					app.helper.showSuccessNotification({"message":'Url copy successfully'});
				else
					app.helper.showErrorNotification({message : 'Oops, unable to copy'});
			} catch (err) {
				app.helper.showErrorNotification({message : 'Oops, unable to copy'});
			}
			document.body.removeChild( textArea );
			
		});
		
		
	},
	
	registerEventForSubmitCalendarForm : function(){
		
		$('.CalendarEditViewSave').on('click', function(e){
			e.preventDefault();
			
			if($('[name="user_principal_name"]').val()){
				
				var params = {
						'module': app.getModuleName(),
						'action' : "CheckExchange",
						'record' : app.getRecordId(),
						'user_principal_name' : $('[name="user_principal_name"]').val(),
					}
				app.helper.showProgress();
				app.request.post({data:params}).then(
					function(err,data) {
						if(data){
							if(data.success){
								$('.CalendarEditView').submit();
								app.helper.hideProgress();
							}else{
								app.helper.hideProgress();
								app.helper.showErrorNotification({
									title:app.vtranslate(data.message),
									message :app.vtranslate(data.error)+' For MsExchange'
								});
							}
						}
					}
				);
			}
		});
		
	},
	
	/**
	 * register Events for my preference
	 */
	registerEvents : function(){
		this._super();
		this.registerEventForMsExchange();
		this.registerEventForBusinessHours();
		this.registerEventForCopyAppointmentUrl();
		Settings_Users_PreferenceEdit_Js.registerChangeEventForCurrencySeparator();
		Settings_Users_PreferenceEdit_Js.registerNameFieldChangeEvent();
		this.registerEventForSubmitCalendarForm();
		//this.registerEventForCopyMsData();
	},
	
	
	registerEventForCopyMsData : function(){
		jQuery(document).on('click', '.copyCalendar', function(){
			
			var module = $(this).data('module');
			
			var principalName = jQuery('[name="user_principal_name"]').val();
			var direction = jQuery('[name="sync_direction"]').val();
			var autoSync = jQuery('[name="automatic_calendar_sync"]').prop('checked');
			
			if($(this).prop('checked') && module == 'Task'){
				
				jQuery('[name="task_user_principal_name"]').val(principalName);
				jQuery('[name="task_sync_direction"]').val(direction).trigger('change');
				jQuery('[name="automatic_task_sync"]').prop('checked', autoSync);
				
			}else if($(this).prop('checked') && module == 'Contacts'){
				
				jQuery('[name="contact_user_principal_name"]').val(principalName);
				jQuery('[name="contact_sync_direction"]').val(direction).trigger('change');
				
			}
			
		});
	},
	
});