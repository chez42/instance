jQuery.Class("HistoricalInformation_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new HistoricalInformation_Js();
        return instance;
    }
},{
    /**
     * Function to register event for filling in the pie chart
     */
    FillCharts : function(){
        var pie = $("#asset_pie").val();
        var revenue_value = $("#trailing_12_revenue").val();
        var aum_value = $("#trailing_12_aum").val();

        if(typeof(pie) !== "undefined")
        {
            var chartData = $.parseJSON(pie);
            if(chartData === 0){
            	if(app.getViewName() == 'Detail')
            		$("#AssetAllocationWidget").html('<div class="text-center">No data found.</div>');
            	else
            		this.RemoveWidget();
            }
            this.AssetChart(chartData);
        }
        /*
                if(typeof(revenue_value) !== "undefined")
                {
                    var chartData = $.parseJSON(revenue_value);
                    this.RevenueChart(chartData, 'filtered_revenue_graph');
                }

                if(typeof(aum_value) !== "undefined")
                {
                    var values = $.parseJSON(aum_value);
                    var chartData = values;
                    this.LineChart(chartData, 'filtered_assets_graph');
                }*/
    },

    RemoveWidget : function() {
        $("#AssetAllocationWidget").closest(".summaryWidgetContainer").hide();
    },

    registerEvents : function() {
        this.FillCharts();
    },

    AssetChart : function(chartData){
        var chart;
        var legend;

        var self = this;

        am4core.options.commercialLicense = true;
        var chart = am4core.create("filtered_pie", am4charts.PieChart3D);
//        var chartData = $.parseJSON($("#holdings_values").val());

        chart.data = chartData;
        var pieSeries = chart.series.push(new am4charts.PieSeries3D());
        pieSeries.slices.template.stroke = am4core.color("#555354");
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "title";
        pieSeries.fontSize = 14;

        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;

        pieSeries.labels.horizontalCenter = 'middle';
        pieSeries.labels.verticalCenter = 'middle';

        pieSeries.labels.template.disabled = true;
        /*
        pieSeries.alignLabels = true;
        pieSeries.labels.template.bent = true;
        pieSeries.labels.template.radius = 3;
        pieSeries.labels.template.padding(0,0,0,0);*/

        pieSeries.ticks.template.disabled = true;
//        pieSeries.labels.template.disabled = true;
//        pieSeries.ticks.template.disabled = true;

//        pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
//        pieSeries.slices.template.states.getKey("hover").properties.scale = 1.1;

        var colorSet = new am4core.ColorSet();
        var colors = [];
        $.each(chartData,function(){
            var element = jQuery(this);
            colors.push(element["0"].color);
        });

        colorSet.list = colors.map(function(color) {
            return new am4core.color(color);
        });
        pieSeries.colors = colorSet;
        chart.legend = new am4charts.Legend();

        var legendContainer = am4core.create("legenddiv", am4core.Container);
        legendContainer.width = am4core.percent(100);
        legendContainer.height = am4core.percent(100);
        chart.legend.parent = legendContainer;
    }
});
//REMOVED THE READY REQUIREMENT SO THIS LOADS IN A WIDGET EVEN AFTER A REFRESH
//jQuery(document).ready(function($) {
var instance = HistoricalInformation_Js.getInstanceByView();
instance.registerEvents();
//});