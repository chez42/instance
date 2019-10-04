/* ********************************************************************************
 * The content of this file is subject to the Custom Expenses/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTECustomExpenses_Js", {
    instance: false,
    getInstance: function () {
        if (VTECustomExpenses_Js.instance == false) {
            var instance = new VTECustomExpenses_Js();
            VTECustomExpenses_Js.instance = instance;
            return instance;
        }
        return VTECustomExpenses_Js.instance;
    },
},{
    
    registerEvents: function(){
        
    }
});

jQuery(document).ready(function () {
    // Does not load on other views
    // if(app.view() !='List') return;
    var instance = new VTECustomExpenses_Js();
    instance.registerEvents();
});
