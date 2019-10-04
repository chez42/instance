{*{assign var = "t3_performance_summed" value = $T3PERFORMANCE->GetPerformanceSummed()}
{assign var = "t6_performance_summed" value = $T6PERFORMANCE->GetPerformanceSummed()}
{assign var = "t12_performance_summed" value = $T12PERFORMANCE->GetPerformanceSummed()}

{assign var = "t3_performance" value = $T3PERFORMANCE->GetPerformance()}
{assign var = "t6_performance" value = $T6PERFORMANCE->GetPerformance()}
{assign var = "t12_performance" value = $T12PERFORMANCE->GetPerformance()}
*}
<input type="hidden" value='{$DYNAMIC_GRAPH}' id="estimate_graph_values" />

<div class="row-fluid ReportTitle detailViewTitle">
    <div class=" span12 ">
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown"><span></span>&nbsp;</span>
                </div>
            </div>
            <div class="span6">
                <div class="pull-right">
                    <div class="btn-toolbar">
							<span class="btn-group">
								<button class="btn ExportReport"><strong>Generate PDF</strong></button>
							</span>
                    </div>
                    <form method="post" id="export">
                        <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
                        <input type="hidden" value="PortfolioInformation" name="module" />
                        <input type="hidden" value="OmniIncome" name="view" />
                        <input type="hidden" value="" name="graph_image" id="graph_image" />
                        <input type="hidden" value="1" name="pdf" />
                        <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<h1 style="text-align:center;">{$START_MONTH} - {$END_MONTH}</h1>
<div id="overview_charts" style="display:block; width:100%;">
    <div id="dynamic_chart_holder" class="dynamic_chart_holder" style="display:block; width:100%; height:300px;"></div>
</div>

<table id="income_combined" class="collap_income table table-bordered">
    <thead>
    <tr>
        <th style="font-weight:bold;">Symbol</th>
        <th style="font-weight:bold;">Name</th>
{*        <th style="text-align:center;">Annual Payment Rate ($)</th>*}
{*        <th style="font-weight:bold;">Quantity</th>*}
        {foreach from=$MONTHLY_TOTALS item=v}
            <th style="font-weight:bold; text-align:center;">{$v->month}<br />{$v->year}</th>
        {/foreach}
        <th style="font-weight:bold; text-align:center;">Total</th>
    </tr>
    <thead>
    <tbody>
    {foreach from=$COMBINED_SYMBOLS item=symbol_values key=symbol}
        <tr>
            <td style="width:10%;">{$symbol}</td>
            <td style="width:10%;">{$symbol_values[0]->security_name}</td>
{*            <td style="text-align:right;">{if $symbol_values[0]->interest_rate eq 0}
                                {$symbol_values[0]->dividend_share|number_format:2:".":","}
                            {else}
                                {$symbol_values[0]->interest_rate|number_format:2:".":","}
                            {/if} </td>*}
{*            <td style="width:10%;">{$symbol_values[0]->quantity|number_format:2:".":","}</td>*}
                {foreach from=$MONTHLY_TOTALS item=ym}
                    <td style="text-align:right;">
                        {assign var='found' value=0}
                    {foreach from=$symbol_values item=v}
                        {if $ym->year EQ $v->year AND $ym->month EQ $v->month}
                            ${$v->amount|number_format:0:".":","}
                            {assign var='found' value=1}
                        {/if}
                    {/foreach}
                    {if $found != 1}
                        -
                    {/if}
                    </td>
                {/foreach}
            <td style="text-align:right;">${$YEAR_END_TOTALS[$symbol]|number_format:0:".":","}</td>
        </tr>
    {/foreach}
    <tr>
        <td colspan="2">&nbsp;</td>
    {foreach from=$MONTHLY_TOTALS item=v}
        <td style="font-weight:bold; text-align:right;">${$v->monthly_total|number_format:0:".":","}</td>
    {/foreach}
        <td style="font-weight:bold; text-align:right;">${$GRAND_TOTAL|number_format:0:".":","}</td>
    </tr>
    </tbody>
</table>