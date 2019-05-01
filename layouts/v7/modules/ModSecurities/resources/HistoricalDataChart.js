jQuery.Class("HistoricalDataChart_Js",{
	currentInstance : false,
        
	getInstanceByView : function(){
            var instance = new HistoricalDataChart_Js();
	    return instance;
	}
},{
    /**
     * Function to register event for Faq Popup
     */
    DisplayChart : function(){
	var cd = $.parseJSON($(".price_data").val());
        try{
            if($("#chartdiv").length){
        var chartData = [];
        for(i = 0; i < cd.length; i++){
            var tmp = cd[i];
            var newDate = new Date(tmp.date);
            chartData.push({
                date: newDate,
                value: tmp.value,
                volume: tmp.volume
            });
        }

        chart = new AmCharts.AmStockChart();
        chart.pathToImages = "libraries/amcharts/amstockchart/images/";

        // DATASETS //////////////////////////////////////////
        var dataSet = new AmCharts.DataSet();
        dataSet.color = "#05447d";
        dataSet.fieldMappings = [{
                fromField: "value",
                toField: "value"
        }, {
                fromField: "volume",
                toField: "volume"
        }];
        dataSet.dataProvider = chartData;
        dataSet.categoryField = "date";

        // set data sets to the chart
        chart.dataSets = [dataSet];

        // PANELS ///////////////////////////////////////////                                                  
        // first stock panel
        var stockPanel1 = new AmCharts.StockPanel();
        stockPanel1.showCategoryAxis = false;
        stockPanel1.title = "Value";
        stockPanel1.backgroundColor = "red";
        stockPanel1.percentHeight = 70;
        
        // graph of first stock panel
        var graph1 = new AmCharts.StockGraph();
        graph1.valueField = "value";
        graph1.fillAlphas = "0.2";
        
        stockPanel1.addStockGraph(graph1);

        // create stock legend                
        var stockLegend1 = new AmCharts.StockLegend();
        stockLegend1.valueTextRegular = " ";
        stockLegend1.markerType = "none";
        stockPanel1.stockLegend = stockLegend1;


        // second stock panel
        var stockPanel2 = new AmCharts.StockPanel();
        stockPanel2.title = "Volume";
        stockPanel2.percentHeight = 30;
        var graph2 = new AmCharts.StockGraph();
        graph2.valueField = "volume";
        graph2.type = "column";
        graph2.fillAlphas = 1;
        stockPanel2.addStockGraph(graph2);

        // create stock legend                
        var stockLegend2 = new AmCharts.StockLegend();
        stockLegend2.valueTextRegular = " ";
        stockLegend2.markerType = "none";
        stockPanel2.stockLegend = stockLegend2;

        // set panels to the chart
//        chart.panels = [stockPanel1];
        chart.panels = [stockPanel1, stockPanel2];


        // OTHER SETTINGS ////////////////////////////////////
        var scrollbarSettings = new AmCharts.ChartScrollbarSettings();
        scrollbarSettings.graph = graph1;
        scrollbarSettings.updateOnReleaseOnly = true;
//        scrollbarSettings.selectedGraphFillColor = '#d6e7f2';
//        scrollbarSettings.selectedBackgroundColor = 'black';
        chart.chartScrollbarSettings = scrollbarSettings;

        var cursorSettings = new AmCharts.ChartCursorSettings();
        cursorSettings.valueBalloonsEnabled = true;
        chart.chartCursorSettings = cursorSettings;


        // PERIOD SELECTOR ///////////////////////////////////
        var periodSelector = new AmCharts.PeriodSelector();
        periodSelector.periods = [{
                period: "DD",
                count: 10,
                label: "10 days"
        }, {
                period: "MM",
                count: 1,
                label: "1 month"
        }, {
                period: "YYYY",
                count: 1,
                label: "1 year",
                selected: true
        }, {
                period: "YTD",
                label: "YTD"
        }, {
                period: "MAX",
                label: "MAX"
        }];
        periodSelector.dateFormat = "MM-DD-YYYY";
        chart.periodSelector = periodSelector;


        var panelsSettings = new AmCharts.PanelsSettings();
        panelsSettings.usePrefixes = true;
        chart.panelsSettings = panelsSettings;
        
        chart.write('chartdiv');
            }
        }catch(error){
//            alert("ERROR");
        }
    },
    
    ContentsDivChange : function(){
        var self = this;
        $(document).on('hover', '.mainContainer', function (e) {
              self.DisplayChart();
        });
    },
    
    registerEvents : function() {
        this.DisplayChart();
        this.ContentsDivChange();
    }
    
});

jQuery(document).ready(function($) {
	var instance = HistoricalDataChart_Js.getInstanceByView();
	instance.registerEvents();
});