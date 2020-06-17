<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:23
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\HelpDesk\dashboards\OpenTickets.tpl" */ ?>
<?php /*%%SmartyHeaderCode:91295ee9c3bfcafce9-02934671%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a0b63f50b993ba0eaa8994378d09cbd8da78320d' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\HelpDesk\\dashboards\\OpenTickets.tpl',
      1 => 1589643624,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '91295ee9c3bfcafce9-02934671',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3bfce2ed',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3bfce2ed')) {function content_5ee9c3bfce2ed($_smarty_tpl) {?>
<div class="dashboardWidgetHeader">
	<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/WidgetHeader.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
<div class="dashboardWidgetContent">
	<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/DashBoardWidgetContentsOpenTicket.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/DashboardFooterIcons.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
			return {'chartData':chartData};
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
</script><?php }} ?>