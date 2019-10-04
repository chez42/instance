/* ********************************************************************************
 * The content of this file is subject to the Item Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEItemLookup_Edit_Js",{
    instance:false,
    getInstance: function(){
        if(VTEItemLookup_Edit_Js.instance == false){
            var instance = new VTEItemLookup_Edit_Js();
            VTEItemLookup_Edit_Js.instance = instance;
            return instance;
        }
        return VTEItemLookup_Edit_Js.instance;
    }
},{

    registerEvents: function(){

    }
});
jQuery(document).ready(function() {
    var instance = new VTEItemLookup_Edit_Js();
    instance.registerEvents();
});