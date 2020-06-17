jQuery.Class("AssetAllocationV4_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AssetAllocationV4_JS();
        return instance;
    }
},{
    DisplayHistoricalChart: function(){

    },

    registerEvents : function() {
//        var chart = AjaxDynamicChart.getInstanceByView();
//        this.registerEventDateClick();
        var chart_instance = AssetAllocationPieV4.getInstanceByView();
        var chart = chart_instance.ConfigurePieChartAM4("asset_allocationv4");
        chart_instance.GetChartDataAndRenderAM4(chart);
        $("#asset_allocationv4").show();
//        chart_instance.GetChartDataAndRender(chart);
//alert(char);
/*        var start_date = $("#balances_start_date").val();
        var end_date = $("#balances_end_date").val();
        chart.CreateChart("asset_allocationv4", "asset_allocation_overview");*/

//        this.DisplayHistoricalChart();
    }
});

jQuery(document).ready(function($) {
    var instance = AssetAllocationV4_JS.getInstanceByView();
    instance.registerEvents();
//    chart.CreateChart("trailing12revenue", "2018-01-01", "2018-02-28");
    //var instance = TrailingRevenue_Js.getInstanceByView();
});