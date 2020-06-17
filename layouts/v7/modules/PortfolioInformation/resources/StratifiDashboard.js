jQuery.Class("StratifiDashboard_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new StratifiDashboard_JS();
        return instance;
    }
},{
    ConfigurePiechart: function(){
        var provider = $("#widget_container").data("pie");

    },

    GetChartDataAndRender: function(chart, recordID, group_type){
        //var provider = $("#widget_container").data("pie");
    },

    HandleEvents:function(){
        var self = this;
    },

    registerEvents : function() {
        this.HandleEvents();
    }
});

//jQuery(document).ready(function($) {
var instance = StratifiDashboard_JS.getInstanceByView();
instance.registerEvents();
//});


//concertglobal.com\rsandnes@mail.concertwm.com
//mail.concertwm.com

