var Historical_Js = {
    /**
     * Function to register event for filling in the pie chart
     */
    FillCharts : function(){
        var pie = $("#asset_pie").val();
        
        if(typeof(pie) !== "undefined")
        {
            var chartData = $.parseJSON(pie);
            if(chartData === 0){
                this.RemoveWidget();
            }
            this.AssetChart(chartData);
        }

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

        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.radius = 75;
        chart.labelText = "[[percents]]%";
        chart.hideLabelsPercent = 100;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 15;
        chart.angle = 30;
        chart.outlineColor = "#ffffff";
        chart.outlineAlpha = 0.4;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;
        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};

        legend = new AmCharts.AmLegend();
        legend.align = "left";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "right";
        legend.marginRight = 20;
        legend.valueText = "$[[value]]";
        legend.valueWidth = 100;
        legend.switchable = false;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend, 'legenddiv');
        
        // WRITE
        if($("#filtered_pie").length > 0) {
            chart.write("filtered_pie");
        }   
    }
};
jQuery(document).ready(function($) {
	Historical_Js.registerEvents();
});