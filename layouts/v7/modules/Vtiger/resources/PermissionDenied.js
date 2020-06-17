jQuery.Class("PermissionDenied_js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new PermissionDenied_js();
        return instance;
    }
},{
    

    registerEvents : function() {
        var vtigerInstance = Vtiger_Index_Js.getInstance();
    	vtigerInstance.registerEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = PermissionDenied_js.getInstanceByView();
    instance.registerEvents();
});