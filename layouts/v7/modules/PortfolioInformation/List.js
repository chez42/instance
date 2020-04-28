/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("PortfolioInformation_List_Js", {
    
	triggerReportPdf : function(url) {
    	var listInstance = window.app.controller();
		
		if(!app.getAdminUser()){
			var selectedRecordCount = listInstance.getSelectedRecordCount();
			if (selectedRecordCount > 500) {
				app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
				return;
			}
		}
		
		var params = listInstance.getListSelectAllParams(true);
		if (params) {
			app.helper.showProgress();
			app.request.get({url: url, data: params}).then(function (error, data) {
				app.helper.hideProgress();
				if (!error) {
					app.helper.showModal(data, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerChangeForReportSelect(modalContainer);
                        }
                    });
				}
			});
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
        
    },
    
    
}, {
	 
	registerChangeForReportSelect: function(modalContainer){
		var self = this;
		
		$(".select_start_date").datepicker({
            format: 'yyyy-mm-dd',
            onClose: function (selectedDate) {
	        }
	    });
	
	    $(".select_end_date").datepicker({
	        format: 'yyyy-mm-dd',
	        onClose: function (selectedDate) {
	        }
	    });
	
	    $(".report_date_selection").change(function(e){
	        e.stopImmediatePropagation();
	
	        var selected = $(this).find(':selected');
	        
	        var start_date = selected.data('start_date');
	        var end_date = selected.data('end_date');
	        
	        $(this).closest('div').find('.select_start_date').val(start_date);
	        $(this).closest('div').find('.select_end_date').val(end_date);
	    });
		
		modalContainer.find('[name="reportselect"]').on('change',function(){
				
			 if($(this).val() == 'OmniOverview'){
				 
				 jQuery('.omniOverview').show();
				 jQuery('.assetClassReport').hide();
				 jQuery('.gh2Report').hide();
				 jQuery('.ghReport').hide();
				 
			 }else if($(this).val() == 'AssetClassReport'){
				 
				 jQuery('.omniOverview').hide();
				 jQuery('.assetClassReport').show();
				 jQuery('.gh2Report').hide();
				 jQuery('.ghReport').hide();
				 
			 }else if($(this).val() == 'GH2Report'){
				 
				 jQuery('.omniOverview').hide();
				 jQuery('.assetClassReport').hide();
				 jQuery('.gh2Report').show();
				 jQuery('.ghReport').hide();
				 
			 }else if($(this).val() == 'GHReport' || $(this).val() == 'GHReportActual' || $(this).val() == 'GHXReport'){
				 
				 jQuery('.omniOverview').hide();
				 jQuery('.assetClassReport').hide();
				 jQuery('.gh2Report').hide();
				 jQuery('.ghReport').show();
				 
			 }else{
					 
				 jQuery('.omniOverview').hide();
				 jQuery('.assetClassReport').hide();
				 jQuery('.gh2Report').hide();
				 jQuery('.ghReport').hide();
					 
			 }
				
		});
    	
    },
   
    
});