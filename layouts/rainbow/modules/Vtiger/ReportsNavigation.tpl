{literal}
<style>
.stellarnav > ul > li > a {padding:10px 3px;}
.stellarnav>ul>li>a:hover{
	background-color:white;
}
.stellarnav li a:hover{
	 background-color: transparent !important;
 }
</style>
{/literal}
<div class="stellarnav" style="z-index:10; margin:0; padding:0;">
    <ul>
        <li><a href="#" style = "font-weight:800;">Reports</a>
            <ul>
                <li><a href="#">Performance</a>
                    <ul>
                        <li><a href="#" class="report_selection" data-report="GainLoss">Gain/Loss</a></li>
                        <li><a href="#" class="report_selection" data-report="GHReport">GH1 Report (Estimated Income)</a></li>
                        <li><a href="#" class="report_selection" data-report="GHReportActual">GH1 Report (Actual Income)</a></li>
                        <li><a href="#" class="report_selection" data-report="GH2Report">GH2 Report</a></li>
                        <li><a href="#" class="report_selection" data-report="OmniOverview">Overview</a></li>
                    </ul>
                </li>
                <li><a href="#" class="report_selection" data-report="AssetClassReport">Asset Class Report</a></li>
                <li><a href="#">Income</a>
                    <ul>
                        <li><a href="#" class="report_selection" data-report="LastYearIncome">Income - Last Year</a></li>
                        <li><a href="#" class="report_selection" data-report="MonthOverMonth">Month Over Month</a></li>
                        <li><a href="#" class="report_selection" data-report="OmniProjected">Income Projected</a></li>
                        <li><a href="#" class="report_selection" data-report="OmniIncome">Income - Trailing 12</a></li>
                    </ul>
                </li>
                <li><a href="#">Intervals</a>
                    <ul>
                        <li><a href="#" class="report_selection" data-report="OmniIntervalsDaily">Intervals Daily</a></li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>