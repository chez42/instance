{assign var = "individual_projected" value = $PROJECTED_INCOME->GetGroupedAccounts()}
{assign var = "monthly_total" value = $PROJECTED_INCOME->GetMonthlyTotals()}

<input type="hidden" value='{$PROJECTED_GRAPH}' id="estimate_graph_values" />

<h1 style="text-align:center;">Projected Income</h1>

<table id="projected_income" class="collap_income table table-bordered">
    <thead>
    <tr>
        <th style="text-align:center;">Symbol</th>
        <th style="text-align:center;">Name</th>
        <th style="text-align:center;">Payment Rate ($)</th>
        <th style="text-align:center;">Quantity</th>
        {foreach item=v from=$CALENDAR}
            <th style="text-align:center;">{$v->month_name}<br />{$v->year}</th>
        {/foreach}
        <th style="text-align:center;">Year Total</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$individual_projected item=holder key=account_number}
        {foreach from=$holder item=v}
            <tr>
                <td style="text-align:right;">{$v->security_symbol}</td>
                <td style="text-align:center;">{$v->security_name}</td>
                <td style="text-align:right;">{$v->interest_rate|number_format:2:".":","}</td>
                <td style="text-align:right;">{$v->quantity|number_format:0:".":","}</td>
                {foreach item=month from=$CALENDAR}
                    <td style="text-align:right;">
                        {foreach from=$v->pay_dates item=pd}
                            {if $pd->month eq $month->month}
                                {$v->estimate_payment_amount|number_format:0:".":","}
                            {/if}
                        {/foreach}
                    </td>
                {/foreach}
                <td style="text-align:right;">{$v->year_payment|number_format:0:".":","}</td>
            </tr>
        {/foreach}
    {/foreach}
    <tr>
        <td colspan="4">
            {foreach item=month from=$CALENDAR}
        <td style="font-weight:bold; text-align:right;">
            {foreach from=$monthly_total key=k item=pd}
                {if $k eq $month->month}
                    {$pd|number_format:0:".":","}
                {/if}
            {/foreach}
        </td>
        {/foreach}
        <td style="font-weight:bold; text-align:right;">{$GRAND_TOTAL|number_format:0:".":","}</td>
    </tr>
    </tbody>
</table>