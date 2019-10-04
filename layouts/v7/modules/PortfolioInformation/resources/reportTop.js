jQuery(document).ready(function($){
    jQuery.CreateChart = function CreateChart(chartData){
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();
        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = -30;
        chart.radius = 80;
        chart.labelText = "[[percents]]%";
        chart.textColor= "#FFFFFF";
        chart.depth3D = 0;
        chart.angle = 0;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;

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
        chart.write("report_top_pie");  
    }
    var value = ($(this).find(".chartdata").val());
    var values = $.parseJSON(value);
    var chartData = values;
    $.CreateChart(chartData);
    
    $("#settings").click(function(){
        var account_number = $('[name=account_number]').val();
        $.post("index.php", {'module':'ReportSettings', 'view':'Settings', 'account_number':account_number}, function(response){
            var headerInstance = new Vtiger_Header_Js();
                headerInstance.handleQuickCreateData(response,{callbackFunction:function(data){
            }});
        });
    });
    
    $(".account_reconcile").click(function(e){
        e.stopImmediatePropagation();
        var progressInstance = jQuery.progressIndicator();
        var account_number = $(".account_reconcile").attr('data-number');
        $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'ResetAccountTransactions', account_number:account_number}, function(response){
            progressInstance.hide();
            window.location.reload();
        });
    });
});