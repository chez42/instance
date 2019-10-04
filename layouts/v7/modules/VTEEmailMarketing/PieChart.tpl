<script src="layouts/v7/modules/VTEEmailMarketing/resources/PieChart/analytics.js"></script>
<script src="layouts/v7/modules/VTEEmailMarketing/resources/PieChart/Chart.bundle.js"></script>
<script src="layouts/v7/modules/VTEEmailMarketing/resources/PieChart/utils.js"></script>

<div id="canvas-holder" style="width:100%">
    <canvas id="chart-area" class="chartjs-render-monitor"
            style="display: block; margin: 0 auto; max-width: 100%">
    </canvas>
</div>
<script type="text/javascript">
    Chart.plugins.register({
        afterDraw: function(chart) {

            var unopen_email = {$RECORD->get('unopened')};
            var failed_sent_mail = {$RECORD->get('failed_to_send')};
            var unsubcribes_email = {$RECORD->get('unsubcribes')};
            var open_email = {$RECORD->get('unique_open')};

            if (failed_sent_mail == 0 && unsubcribes_email == 0 && open_email == 0 && unopen_email == 0) {
                // No data is present
                var ctx = chart.chart.ctx;
                var width = chart.chart.width;
                var height = chart.chart.height
                chart.clear();

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = "30px black 'Helvetica Nueue'";
                ctx.fillStyle = 'black';
                ctx.fillText('No data to show. Chart will populate when the emails go out', width / 2, height / 2);
                ctx.restore();
            }
        }
    });
    $(function () {
        var unopen_email = {$RECORD->get('unopened')};
        var failed_sent_mail = {$RECORD->get('failed_to_send')};
        var unsubcribes_email = {$RECORD->get('unsubcribes')};
        var open_email = {$RECORD->get('unique_open')};
        var div = jQuery('#vte-campagin-pie-chart').find('#chart-area')[0].getContext("2d");
        var config = {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                        unsubcribes_email,
                        open_email,
                        failed_sent_mail,
                        unopen_email,
                    ],
                    backgroundColor: [
                        window.chartColors.yellow,
                        window.chartColors.green,
                        window.chartColors.orange,
                        window.chartColors.gray,

                    ],
                    label: 'Dataset 1'
                }],
                labels: [
                    "Unsubscribed",
                    "Unique Opens",
                    "Failed to Send",
                    "Unopened"
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                    text: 'Chart.js Doughnut Chart'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                                return parseInt(previousValue) + parseInt(currentValue);
                            });
                            var label = data.labels[tooltipItem.index];
                            var currentValue = dataset.data[tooltipItem.index];
                            var precentage = Math.floor(((currentValue/total) * 100)+0.5);
                            return precentage + "% (" + label + ")";
                        }
                    }
                }
            }
        };
        new Chart(div, config);
    });
    $(document).ready(function () {
        resizeChart = function () {
            var viewSummary = $('#detailView > .resizable-summary-view');
            var left = viewSummary.find('> .left-block').height();
            var height = left-14;
            var witdh = height * 2.2;
            viewSummary.find('#chart-area').height(height);
            viewSummary.find('#chart-area').width(witdh);
        };
        resizeChart();

        $(window).resize(function() {
            resizeChart();
        });
    })
</script>