jQuery.Class("OmniOverview_Module_Js",{
    currentInstance : false,
    pieInfo : [],
    graphInfo : [],
    getInstanceByView : function(){
        var instance = new OmniOverview_Module_Js();
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
            self.pieInfo.exporting.getImage("jpg").then(function(imgData){
                $("#pie_image").val(encodeURIComponent(imgData));
//                console.log(imgData);
                self.graphInfo.exporting.getImage("jpg").then(function(imgData){
                    $("#graph_image").val(encodeURIComponent(imgData));
//                    console.log(imgData);
                    $("#export").submit();
                });

//                $("#export").submit();
            });
        });

        $(document).on("change", "input[type=text]", function(e){
//                $("input[type=text]").change(function(e){
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
        am4core.options.commercialLicense = true;
        var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
        var chartData = $.parseJSON($("#holdings_values").val());

        chart.data = chartData;

// Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries3D());
        pieSeries.slices.template.stroke = am4core.color("#555354");
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "title";
        chart.fontSize = 16;

        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;

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

        self.pieInfo = chart;
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

        /*
        var colorSet = new am4core.ColorSet();
        var colors = [];
        $.each(chartData,function(){
            var element = jQuery(this);
            colors.push(element["0"].color);
        });

        colorSet.list = colors.map(function(color) {
            return new am4core.color(color);
        });
        pieSeries.colors = colorSet;*/

        self.graphInfo = chart;
    },

    registerEvents : function() {
        this.ClickEvents();
        this.CollapTable();
        this.createPie();
        this.createGraph();
    }
});

jQuery(document).ready(function($) {
    var instance = OmniOverview_Module_Js.getInstanceByView();
    instance.registerEvents();

    var pie = DynamicPie_Js.getInstanceByView();
    pie.registerEvents();
//    var chart = DynamicChart_JS.getInstanceByView();

//    pie.CreatePie("dynamic_pie_holder", "holdings_values");
    pie.CreatePie("sector_pie_holder", "sector_values", false);
//    pie.CreateGraph("dynamic_chart_holder", "t12_balances", "intervalenddateformatted", "intervalendvalue");

//    chart.CreateChart("dynamic_chart_holder", "t12_balances");
});