jQuery.Class("AssetAllocationReport_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AssetAllocationReport_Module_Js();
        return instance;
    }
},{

    ClickEvents: function(){
        var self = this;
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = AssetAllocationReport_Module_Js.getInstanceByView();
    var pie = DynamicPie_Js.getInstanceByView();
    instance.registerEvents();
    pie.registerEvents();
    pie.CreatePie("dynamic_pie_holder", "estimate_pie_values");
});