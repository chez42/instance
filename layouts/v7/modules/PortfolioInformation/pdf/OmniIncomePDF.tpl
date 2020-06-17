{*{assign var = "t3_performance_summed" value = $T3PERFORMANCE->GetPerformanceSummed()}
{assign var = "t6_performance_summed" value = $T6PERFORMANCE->GetPerformanceSummed()}
{assign var = "t12_performance_summed" value = $T12PERFORMANCE->GetPerformanceSummed()}

{assign var = "t3_performance" value = $T3PERFORMANCE->GetPerformance()}
{assign var = "t6_performance" value = $T6PERFORMANCE->GetPerformance()}
{assign var = "t12_performance" value = $T12PERFORMANCE->GetPerformance()}
*}
<input type="hidden" value='{$DYNAMIC_GRAPH}' id="estimate_graph_values" />

<h1 style="text-align:center;">Income Overview</h1>
<table id="income_combined" cellspacing="0">
    <thead>
    <tr>
        <th>Symbol</th>
        {foreach from=$MONTHLY_TOTALS item=v}
            <th>{$v->month}<br />{$v->year}</th>
        {/foreach}
        <th style="font-weight:bold; text-align:center;">Total</th>
    </tr>
    <thead>
    <tbody>
    {foreach from=$COMBINED_SYMBOLS item=symbol_values key=symbol}
        <tr>
            <td style="width:10%;">{$symbol}</td>
            {foreach from=$MONTHLY_TOTALS item=ym}
                <td style="text-align:right;">
                    {assign var='found' value=0}
                    {foreach from=$symbol_values item=v}
                        {if $ym->year EQ $v->year AND $ym->month EQ $v->month}
                            ${$v->amount|number_format:2:".":","}
                            {assign var='found' value=1}
                        {/if}
                    {/foreach}
                    {if $found != 1}
                        -
                    {/if}
                </td>
            {/foreach}
            <td style="text-align:right;">${$YEAR_END_TOTALS[$symbol]|number_format:2:".":","}</td>
        </tr>
    {/foreach}
    <tr>
        <td>&nbsp;</td>
        {foreach from=$MONTHLY_TOTALS item=v}
            <td style="font-weight:bold; text-align:right;">${$v->monthly_total|number_format:2:".":","}</td>
        {/foreach}
            <td style="font-weight:bold; text-align:right;">${$GRAND_TOTAL|number_format:2:".":","}</td>
    </tr>
    </tbody>
</table>