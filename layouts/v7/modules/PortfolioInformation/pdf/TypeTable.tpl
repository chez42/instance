<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Securities</h2>
</div>

<div class="block_wrapper">
<table id="holdings_report_table_2" class="holdings_report">
    <thead>
    <tr>
        <td colspan="7" style="text-align:left;">The balances view is an individualized view of each account and its holdings based on security type</td>
    </tr>
    <tr>
        <td class="report_heading">Symbol</td>
        <td class="report_heading">Description</td>
        <td class="report_heading">Account Number</td>
        <td class="report_heading">Qty</td>
        <td class="report_heading">Price</td>
        <td class="report_heading">Weight</td>
        <td class="report_heading">Total Value</td>
    </tr>
    </thead>
    <tbody>
    {foreach from=$PRIMARY key=pk item=pv}
        <tr class="primary">
            <td colspan="5" class="strong">{$pv.aclass}</td>
            <td class="right">{$pv.group_weight|number_format:2:".":","}%</td>
            <td class="right">${$pv.group_total|number_format:2:".":","}</td>
        </tr>
        {foreach from=$SECONDARY key=sk item=sv}
            {if $sv.aclass eq $pv.aclass}
                <tr class="secondary">
                    <td colspan="5" class="strong">&nbsp;&nbsp;{$sv.securitytype}</td>
                    <td class="right">{$sv.group_weight}%</td>
                    <td class="right">${$sv.group_total|number_format:2:".":","}</td>
                </tr>
                {foreach from=$INDIVIDUAL key=ik item=iv}
                    {if $sv.securitytype eq $iv.securitytype}
                        {if $sv.aclass eq $iv.aclass}
                            <tr class="position">
                                <td><label class="hover_symbol_holdings" id="{$iv.security_symbol}" data-account='{$iv.account_number}'>&nbsp;&nbsp;&nbsp;&nbsp;{$iv.security_symbol}</label></td>
                                <td>{$iv.description}</td>
                                <td>{$iv.account_number}</td>
                                <td class="right">{$iv.quantity|number_format:2:".":","}</td>
                                <td class="right">${$iv.last_price|number_format:2:".":","}</td>
                                <td class="right">{$iv.weight|number_format:2:".":","}%</td>
                                <td class="right">${$iv.current_value|number_format:2:".":","}</td>
                            </tr>
                        {/if}
                    {/if}
                {/foreach}
            {/if}
        {/foreach}
    {/foreach}
    </tbody>
</table>
{*<br /><br />
<p>Revenue Over the Past 12 Months</p>
{if $REVENUE_IMAGE eq 1}
    <img src="{$REVENUE_FILE}" style="width:50%; margin-top:20px;" />
{/if}*}
</div>