/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("PortfolioInformation_Edit_Js",{},{
	
	//Will have the mapping of address fields based on the modules
	FieldsMapping : {'Contacts' :
			{
				'household_account' : 'account_id',
				'first_name' : 'firstname',
				'last_name' : 'lastname',
				'tax_id' : 'ssn',
				'email_address' : 'email',
				'address1' : 'mailingstreet',  
				'city' : 'mailingcity',
				'state' : 'mailingstate',
				'zip' : 'mailingzip'
			}
	},
	
    /* Function which will register event for Reference Fields Selection
    */
	registerReferenceSelectionEvent : function(container) {
        var thisInstance = this;

       jQuery('input[name="contact_link"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent,function(e,data){
            thisInstance.referenceSelectionEventHandler(data, container);
        });
	},
		
	/**
	 * Reference Fields Selection Event Handler
	 * On Confirmation It will copy the address details
	 */
	referenceSelectionEventHandler :  function(data, container) {
		var thisInstance = this;
		var message = 'Overwrite the existing fields with the selected Contact ('+data['selectedName']+') details?';
		app.helper.showConfirmationBox({'message' : message}).then(function(e){
			thisInstance.copyFieldDetails(data, container);
		},
		function(error,err){});
	},
	
	/**
	 * Function which will copy the address details - without Confirmation
	 */
	copyFieldDetails : function(data, container) {
		var thisInstance = this;
		var sourceModule = data['source_module'];
		console.log(data)
		thisInstance.getRecordDetails(data).then(
			function(response){
				thisInstance.mapFieldDetails(thisInstance.FieldsMapping[sourceModule], response['data'], container);
			},
			function(error, err){

			});
	},
	
	/**
	 * Function which will map the address details of the selected record
	 */
	mapFieldDetails : function(addressDetails, result, container) {
		var thisInstance = this;
		for(var key in addressDetails) {
            if(container.find('[name="'+key+'"]').length == 0) {
                var create = container.append("<input type='hidden' name='"+key+"'>");
            }
            if(key == 'household_account'){
            	var params = {
        			'record': result[addressDetails[key]],
    				'source_module': "Accounts"
            	};
        		thisInstance.getRecordDetails(params).then(
        			function(response){
        				container.find('[name="household_account_display"]').val(response.data.accountname).prop('disabled',true);
        				var parent = container.find('[name="household_account_display"]').closest('td');
        				if(parent.length == 0){
        					parent = container.find('[name="household_account_display"]').closest('.fieldValue');
        				}
        				
        				parent.find('.clearReferenceSelection').removeClass('hide');
        				parent.find('.referencefield-wrapper').addClass('selected');
        			}
    			);
            }
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
		}
	},
	
	
	registerBasicEvents: function (container) {
		this._super(container);
		this.registerReferenceSelectionEvent(container);
	},
	
	registerEvents : function(){
		this._super();
	},
	

	
})