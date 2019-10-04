/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_Index_Js("DataExportTracking",{

},{

    registerEvents : function() {
        this._super();
    }

});
jQuery(document).ready(function(){
    var instance = new DataExportTracking();
    instance.registerEvents();
});