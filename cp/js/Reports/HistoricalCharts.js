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
        
        var ticketStatus = $('#ticket_status').val();
        if(typeof(ticketStatus) !== "undefined")
        {
            var chartData = $.parseJSON(ticketStatus);
            if(chartData === 0){
                this.RemoveTicketStatusWidget();
            }
            this.ticketStatusChart(chartData);
        }
        var ticketTime = $('#ticket_time').val();
        if(typeof(ticketTime) !== "undefined")
        {
            var chartData = $.parseJSON(ticketTime);
            if(chartData === 0){
                this.RemoveTicketTimeWidget();
            }
            this.ticketTimeChart(chartData);
        }
        
        var ticketCat = $('#ticket_type').val();
        if(typeof(ticketCat) !== "undefined")
        {
            var chartData = $.parseJSON(ticketCat);
            if(chartData === 0){
                this.RemoveTicketCatWidget();
            }
            this.ticketCatChart(chartData);
        }

    },

    RemoveWidget : function() {
        $("#AssetAllocationWidget").closest(".summaryWidgetContainer").hide();
    },
    
    RemoveTicketStatusWidget : function() {
        $("#ticketStatusWidget").closest(".summaryWidgetContainer").hide();
    },
    
    RemoveTicketTimeWidget : function() {
        $("#ticketTimeWidget").closest(".summaryWidgetContainer").hide();
    },
    
    RemoveTicketCatWidget : function() {
        $("#ticketTimeWidget").closest(".summaryWidgetContainer").hide();
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
        chart.responsive = {
			"enabled": false
		};
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
        legend.align = "center";
        legend.markerType = "square";
        legend.maxColumns = 1;
       // legend.position = "right";
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
    },
    
    ticketStatusChart : function(chartData){
        var chart;
        var legend;
        
        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.responsive = {
			"enabled": false
		};
        chart.radius = 75;
        chart.labelText = "[[percents]]%";
        chart.hideLabelsPercent = 100;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 15;
        chart.angle = 30;
        chart.outlineColor = "#ffffff";
        chart.outlineAlpha = 0.4;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3"];//,"#dadbb9","#e8cf84","#84b3e8","#d8adec"
        chart.startDuration = 0;
//        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        chart.urlField = "url";
		chart.urlTarget = "_blank";
		
        legend = new AmCharts.AmLegend();
        legend.markerType = "circle";
        legend.maxColumns = 4;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend, 'ticketstatuslegenddiv');
        
        // WRITE
        if($("#ticket_status_filtered_pie").length > 0) {
            chart.write("ticket_status_filtered_pie");
        }   
    },
    
    ticketTimeChart : function(chartData){
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.responsive = {
			"enabled": false
		};
        chart.radius = 75;
        chart.labelText = "[[percents]]%";
        chart.hideLabelsPercent = 100;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 15;
        chart.angle = 30;
        chart.outlineColor = "#ffffff";
        chart.outlineAlpha = 0.4;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa"];//,"#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"
        chart.startDuration = 0;
//        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        
        legend = new AmCharts.AmLegend();
        legend.markerType = "circle";
        legend.maxColumns = 4;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend, 'tickettimelegenddiv');
        
        // WRITE
        if($("#ticket_time_filtered_pie").length > 0) {
            chart.write("ticket_time_filtered_pie");
        }   
    },
    
    ticketCatChart : function(chartData){
        var chart;
        var legend;
        
        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.responsive = {
			"enabled": false
		};
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
//        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        chart.urlField = "url";
		chart.urlTarget = "_blank";
		
        legend = new AmCharts.AmLegend();
        legend.markerType = "circle";
        legend.maxColumns = 4;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend, 'ticketcatlegenddiv');
        
        // WRITE
        if($("#ticket_cat_filtered_pie").length > 0) {
            chart.write("ticket_cat_filtered_pie");
        }   
    }
    
};
jQuery(document).ready(function($) {
	Historical_Js.registerEvents();
});