{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is: vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent">
	{include file="dashboards/DashBoardWidgetContentsOpenTicket.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
</div>


<script type="text/javascript">
	Vtiger_Pie_Widget_Js('Vtiger_OpenTickets_Widget_Js',{},{
		/**
		 * Function which will give chart related Data
		 */
		generateData : function() {
			var container = this.getContainer();
			var jData = container.find('.widgetData').val();
			var data = JSON.parse(jData);
			var chartData = [];
			for(var index in data) {
				var row = data[index];
				var rowData = [row.name, parseInt(row.count), row.id];
				chartData.push(rowData);
			}
			return {literal}{'chartData':chartData}{/literal};
		},
		loadChart : function() {
			var chartData = this.generateData();
			
			chartData = chartData.chartData;
			
			var finalchart = [];
			var links = this.generateLinks();
			
			$.each(chartData, function(ind, ele){
				var data =[];
				data['title'] = ele[0];
				data['value'] = ele[1];
				data['user'] = ele[2];
				data['url'] = links[ind];
				finalchart.push(data);
			});
			
			if(!finalchart) return false;
			
	        var chart;
	        var legend;
	
	        chart = new AmCharts.AmPieChart();
	        
	        chart.dataProvider = finalchart;
	        chart.titleField = "title";
	        chart.valueField = "value";
			chart.theme = "light";
			chart.labelRadius = 30;
	        chart.textColor= "#FFFFFF";
	        chart.depth3D = 15;
	        chart.angle = 30;
	        chart.outlineColor = "#363942";
	        chart.outlineAlpha = 0.8;
	        chart.outlineThickness = 1;
	        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
	        chart.startDuration = 0;
	
			chart.urlField = "url";
			chart.urlTarget = "_blank";
	
			chart.radius = "40%";
			
	        /*legend = new AmCharts.AmLegend();
	        legend.position = "bottom";
			legend.labelText = "[[title]]:";
			legend.valueWidth = 25;
			legend.column = 4;
	        chart.addLegend(legend);*/
	
	        if($("#"+this.getPlotContainer(false).attr('id')).length > 0) {
	            chart.write(this.getPlotContainer(false).attr('id'));
	        }
	        
	}
	});
</script>