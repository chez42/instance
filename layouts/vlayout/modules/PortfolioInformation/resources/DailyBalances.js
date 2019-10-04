jQuery.Class("DailyBalances_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new DailyBalances_Module_Js();
        return instance;
    }
},{

    ClickEvents: function(){
        var self = this;
    },

    registerEvents : function() {
        this.ClickEvents();
    },

    firstLoad : function(){
        $('#FidelityBalances').DataTable( {
            "scrollY": "300px",
            "scrollX": "4000px",
            "scrollCollapse": true,
            "paging":         false
        });
    }
});

jQuery(document).ready(function($) {
    var instance = DailyBalances_Module_Js.getInstanceByView();
    instance.registerEvents();
    instance.firstLoad();
});
