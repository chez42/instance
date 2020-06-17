/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Instances_Edit_Js",{
   
},{
	
	setDomainName: function(){
		
		$('[name="name"]').on("change", function(){
			$('[name="domain"]').val( "https://" + $(this).val().replace(/[^A-Za-z]+/g, '').toLowerCase() + ".360vew.com" );
		});
		
	},
	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerBasicEvents : function(container) {
		this._super(container);
		this.setDomainName();
	}
});