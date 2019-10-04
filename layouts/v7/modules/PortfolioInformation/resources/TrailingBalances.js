jQuery.Class("TrailingBalances_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new TrailingBalances_JS();
        return instance;
    }
},{
    DisplayHistoricalChart: function(){

    },

    registerEvents : function() {
//        var chart = AjaxDynamicChart.getInstanceByView();
//        this.registerEventDateClick();
        var chart = TrailingBalancesZoomChart.getInstanceByView();
        var start_date = $("#balances_start_date").val();
        var end_date = $("#balances_end_date").val();
        chart.CreateChart("trailing_balances", "trailingBalancesZoom");

//        this.DisplayHistoricalChart();
    }
});

jQuery(document).ready(function($) {
    var instance = TrailingBalances_JS.getInstanceByView();
    instance.registerEvents();
//    chart.CreateChart("trailing12revenue", "2018-01-01", "2018-02-28");
    //var instance = TrailingRevenue_Js.getInstanceByView();
});