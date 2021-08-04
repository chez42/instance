jQuery.Class("PerformanceReport_Module_Js",{
    currentInstance : false,
    pieInfo : [],
    graphInfo : [],
    getInstanceByView : function(){
        var instance = new PerformanceReport_Module_Js();
        return instance;
    }
},{
    CollapTable: function(){
        $('.collap_performance').aCollapTable({
// the table is collapased at start
            startCollapsed: true,
// the plus/minus button will be added like a column
            addColumn: true,
// The expand button ("plus" +)
            plusButton: '<span class="i">+</span>',
// The collapse button ("minus" -)
            minusButton: '<span class="i">-</span>'
        });
    },

    ClickEvents: function(){
        var self = this;

        $(".ExportReport").click(function(e){
			e.stopImmediatePropagation();
			$("#export").submit();
        });

        $(document).on("change", "input[type=text]", function(e){
            var id = $(this).data("id");
            var value = $(this).val();
            var field = $(this).prop("name");
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateFileField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });
    },

    createPie : function() {
        var self = this;
        
    },

    createGraph : function() {
        var self = this;
        am4core.options.commercialLicense = true;
        var chart = am4core.create("dynamic_chart_holder", am4charts.XYChart);
        var chartData = $.parseJSON($("#t12_balances").val());

        chart.data = chartData;

        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.dataFields.category = "intervalenddateformatted";
        categoryAxis.renderer.minGridDistance = 40;
        categoryAxis.fontSize = 11;


        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
/*        valueAxis.max = 24000;
        valueAxis.strictMinMax = true;*/
        valueAxis.renderer.minGridDistance = 30;

/*        // axis break
        var axisBreak = valueAxis.axisBreaks.create();
        axisBreak.startValue = 2100;
        axisBreak.endValue = 22900;
        axisBreak.breakSize = 0.005;

// make break expand on hover
        var hoverState = axisBreak.states.create("hover");
        hoverState.properties.breakSize = 1;
        hoverState.properties.opacity = 0.1;
        hoverState.transitionDuration = 1500;

        axisBreak.defaultState.transitionDuration = 1000;*/

        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.categoryX = "intervalenddateformatted";
        series.dataFields.valueY = "intervalendvalue";
        series.columns.template.tooltipText = "${valueY.value}";
        series.columns.template.tooltipY = 0;
        series.columns.template.strokeOpacity = 0;

// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        });


        self.graphInfo = chart;
    },

    registerEvents : function() {
        this.ClickEvents();
        this.CollapTable();
        this.createPie();
       // this.createGraph();
    }
});

jQuery(document).ready(function($) {
    var instance = PerformanceReport_Module_Js.getInstanceByView();
    instance.registerEvents();
});