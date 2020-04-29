<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Asset Allocation</h2>
</div>
<input type="hidden" value='{$DYNAMIC_PIE}' id="estimate_pie_values" />
<div id="dynamic_pie" style="display:block; width:100%;
                             box-shadow:
                             -6px -6px 8px -4px rgba(224, 224, 235,0.75),
                             6px -6px 8px -4px rgba(224, 224, 235,0.75),
                             6px 6px 8px -4px rgba(224, 224, 235,0.25),
                             6px 6px 8px -4px rgba(224, 224, 235,0.25);
                             border-radius:5px;
                             background-color:#f0f0f5;
                             margin-bottom:25px;">
	 <div id="dynamic_pie_holder" class="report_top_pie" style="height: 320px; margin-bottom:25px;"></div>
  
</div>
{if $DYNAMIC_PIE neq ''} 
	<script src="{$SITEURL}layouts/v7/lib/jquery/jquery.min.js"></script>
	
	<script src="{$SITEURL}libraries/amcharts/amcharts/amcharts.js"></script>
	<script src="{$SITEURL}libraries/amcharts/amcharts/pie.js"></script>
	<script type="text/javascript">
		CreatePieWithDetails("dynamic_pie_holder", "estimate_pie_values");
		function CreatePieWithDetails(holder, value_source, showLegend){
			if($("#"+holder).length == 0)
				return;

			if(showLegend == undefined)
				showLegend = true;

			var chart;
			var legend;

			var chartData = $.parseJSON($("#"+value_source).val());

			chart = new AmCharts.AmPieChart();
			{*chart.export = {enabled:"true",
				libs: {
					path: "libraries/amcharts/amcharts/plugins/export/libs/"
				},
				backgroundColor: "transparent",
				backgroundAlpha: 0.3 ,
				menu:[],
				fileName:holder
			};*}
			chart.dataProvider = chartData;
			chart.titleField = "title";
			chart.valueField = "value";
			chart.colorField = 'color';
			{*chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};*}
			chart.labelRadius = -30;
			chart.radius = 125;
			chart.labelText = "[[percents]]%";
			chart.textColor= "#FFFFFF";
			chart.color = "#FFFFFF";
			chart.depth3D = 14;
			chart.angle = 25;
			chart.outlineColor = "#363942";
			chart.outlineAlpha = 0.8;
			chart.outlineThickness = 1;
			chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
			chart.startDuration = 0;

			if(showLegend == true) {
				legend = new AmCharts.AmLegend();
				legend.align = "left";
				legend.markerType = "square";
				legend.maxColumns = 1;
				legend.position = "right";
				legend.marginRight = 20;
				legend.valueText = "$[[value]]";
				legend.valueWidth = 100;
				legend.switchable = false;
				legend.labelText = "[[title]]";
				chart.addLegend(legend);//, 'report_top_pie_legend');
			}
			chart.write(holder);
		}
	</script>
{/if}