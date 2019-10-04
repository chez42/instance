/* ********************************************************************************
 * The content of this file is subject to the Custom Forms & Views ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("CustomFormsViews_Edit_Js",{
},{
    registerEvents : function() {
        var sPageURL = window.location.search.substring(1);
        if(sPageURL.indexOf('&customviewid=') != -1) {
            var customviewid = '';
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == 'customviewid') {
                    customviewid = sParameterName[1];
                }
            }
            var module=jQuery('#EditView').find('input[name="module"]').val();
            jQuery('#EditView').append('<input type="hidden" name="customviewid" value="'+customviewid+'"/>');
            // var instance = Vtiger_Edit_Js.getInstanceByModuleName(module);
            //var instance = Vtiger_Edit_Js.getInstance();
            // instance.registerEvents();
        }
    }
});
jQuery(document).ready(function() {
    var instance = new CustomFormsViews_Edit_Js();
    instance.registerEvents();
});