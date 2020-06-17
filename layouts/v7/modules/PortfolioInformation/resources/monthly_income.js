jQuery.Class("Monthly_Income_Js",{},{
	
    registerCreateIncomeChartEvent : function CreateIncomeChart(chartData, element){
        var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var graph = new AmCharts.AmGraph();
        graph.valueField = "value";
        graph.balloonText="[[category]]: $[[value]]";
        graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph.type = "column";
        graph.lineAlpha = 0;
        graph.fillAlphas = 0.6;
        graph.fillColors = "#00FF00";
        chart.addGraph(graph);
        chart.angle = 30;
        chart.depth3D = 15;

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;
        chart.write(element);
    },

    registerRenderChartEvent : function(){
    	
    	var thisInstance = this;
    	
        var history_value = ($(document).find("[name=history_chart]").val());
        
        if(typeof(history_value) !== 'undefined'){
            var history_values = $.parseJSON(history_value);
            var historyChartData = history_values;
            if(jQuery(document).find("#history_chart").length > 0)
            	thisInstance.registerCreateIncomeChartEvent(historyChartData, "history_chart");
        }

        var future_value = ($(document).find("[name=future_chart]").val());
        
        if(typeof(future_value) !== "undefined"){
            var future_values = $.parseJSON(future_value);
            var futureChartData = future_values;
            if(jQuery(document).find("#future_chart").length > 0)
                thisInstance.registerCreateIncomeChartEvent(futureChartData, "future_chart");
        }
        
        var future_value = ($(document).find("[name=incomechart]").val());
        
        if(typeof(future_value) !== "undefined"){
            var future_values = $.parseJSON(future_value);
            var futureChartData = future_values;
            if(jQuery(document).find("#income_chart").length > 0)
                thisInstance.registerCreateIncomeChartEvent(futureChartData, "income_chart");
        }
    },
    
    registerJqueryStepsEvent : function(){

    	var monthlyIncomeContainer = jQuery(document).find('.PortfolioInformationMonthlyIncomeReport');
    	
    	jQuery(monthlyIncomeContainer).find('.monthly_reports_idealforms').idealforms({
			silentLoad: false,
			steps: {
				buildNavItems : false,
				after : function(currentStep){
					
					var totalSteps = this.$steps.length-1;
					
					if(currentStep == 0 || currentStep < totalSteps){
						jQuery(".next").show();
					}else{
						jQuery(".next").hide();
					}
					
					if(currentStep > 0 && currentStep <= totalSteps)
						$('.previous').show();
					else
						$('.previous').hide();
				}, 
				
			},
		});
		
		jQuery(monthlyIncomeContainer).find('.previous').click(function(){
		  $('.monthly_reports_idealforms').idealforms('prevStep');
		});
		jQuery(monthlyIncomeContainer).find('.next').click(function(){
		  $('.monthly_reports_idealforms').idealforms('nextStep');
		});
    },
	registerEvents : function(){
    	this.registerJqueryStepsEvent();
    	this.registerRenderChartEvent();
    }
});

jQuery(document).ready(function($) {
	var instance = new Monthly_Income_Js();
	instance.registerEvents();
});
