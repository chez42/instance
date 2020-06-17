Vtiger_Widget_Js('Vtiger_ChartWidgetDisplay_Widget_Js',{},{

	generateChartData : function() {
		var container = this.getContainer();

		var jData = container.find('.widgetData').val();
		var data = JSON.parse(jData);

		var chartData = [];
		var xLabels = new Array();
		var yMaxValue = 0;
		for(var index in data) {
			var row = data[index];
			row[0] = parseInt(row[0]);
			xLabels.push(app.getDecodedValue(row[1]))
			chartData.push(row[0]);
			if(parseInt(row[0]) > yMaxValue){
				yMaxValue = parseInt(row[0]);
			}
		}
        // yMaxValue Should be 25% more than Maximum Value
		yMaxValue = yMaxValue + 2 + (yMaxValue/100)*25;
		return {'chartData':[chartData], 'yMaxValue':yMaxValue, 'labels':xLabels};
	},
    
     postLoadWidget: function() {
        this._super();
        var thisInstance = this;
        
        this.getContainer().on('jqplotDataClick', function(ev, gridpos, datapos, neighbor, plot) {
                var jData = thisInstance.getContainer().find('.widgetData').val();
                var data = JSON.parse(jData);
                var linkUrl = data[datapos]['links'];
                if(linkUrl) window.location.href = linkUrl;
        });
        
        this.getContainer().on("jqplotDataHighlight", function(evt, seriesIndex, pointIndex, neighbor) {
                $('.jqplot-event-canvas').css( 'cursor', 'pointer' );
        });
        this.getContainer().on("jqplotDataUnhighlight", function(evt, seriesIndex, pointIndex, neighbor) {
                $('.jqplot-event-canvas').css( 'cursor', 'auto' );
        });
    },

	loadChart : function() {
            $(function () {

            });
	}

});

