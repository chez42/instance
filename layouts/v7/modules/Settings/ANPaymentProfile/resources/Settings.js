/* ********************************************************************************
 * The content of this file is subject to the VTEAuthnet("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

var Settings_ANPaymentProfile_Js = {

    registerEvents: function () {
        var instance = new Vtiger_Index_Js();
        instance.registerAppTriggerEvent();
    }

};
jQuery(document).ready(function () {
    Settings_ANPaymentProfile_Js.registerEvents();
});