/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Timecontrol_List_Js", {

	TicketPreview :function(ticketId){
    	var vtigerInstance = Vtiger_Index_Js.getInstance();
    	vtigerInstance.showQuickPreviewForId(ticketId, 'HelpDesk', '','', true, '');
    },
	

} ,{
	
});