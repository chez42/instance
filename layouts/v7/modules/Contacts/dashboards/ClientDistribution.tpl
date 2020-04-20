{************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}

 
<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<div class="dashboardWidgetContent">
	{include file="dashboards/ClientDistributionContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
</div>

<script type="text/javascript">
	
	Vtiger_Barchat_Widget_Js('Vtiger_ClientDistribution_Widget_Js',{},{
	
		generateData : function() {
			var container = this.getContainer();
			var jData = container.find('#chart_data').val();
			var data = JSON.parse(jData);
			return {literal}{data}{/literal};
		},
		
		loadChart : function() {
		
			var container = this.getContainer();
			var chartData = this.generateData();
			chartData = chartData.data;
			
	        var chart;
	        var legend;
			
	        chart = new AmCharts.AmSerialChart();
	        
	        chart.dataProvider = chartData;
	        chart.theme = "light";
		
			chart.categoryField = "title";
			
			var valueAxis = new AmCharts.ValueAxis();
		        
			valueAxis.minimum = 0;
	        
			chart.addValueAxis(valueAxis);
			
			var graph = new AmCharts.AmGraph();
			
			graph.valueField = "value";
			graph.type = 'column';
			graph.urlField = "url";
			graph.urlTarget = "_blank";
			
			graph.lineAlpha = 0;
			graph.fillAlphas = 1;
			
			chart.addGraph(graph);
			
			chart.addTitle('Client Distribution');
			
			var catAxis = chart.categoryAxis;
	        catAxis.gridPosition = "start";
	        catAxis.gridCount = chartData.length;
	        catAxis.labelRotation = 45;
			catAxis.gridAlpha = 0;
			catAxis.tickPosition = "start";
			catAxis.tickLength = 20;
			
	        if($("#"+this.getPlotContainer(false).attr('id')).length > 0) {
	            chart.write(this.getPlotContainer(false).attr('id'));
	        }
		}
	});
	
</script>