{*
<p>Prepared by: {$CURRENT_USER->get('first_name')} {$CURRENT_USER->get('last_name')}</p>
<p>Financial Advisor: {$ADVISOR->get('first_name')} {$ADVISOR->get('last_name')}</p>
{if $HOUSEHOLD}
    <p>Household: {$HOUSEHOLD->get('accountname')}</p>
{/if}
<div style="page-break-after: always" />
*}

<div id="holdings_table_wrapper">
    <table id="holdings_report_table" class="holdings_report">
        <thead>
        <tr>
            <td class="report_heading">Symbol</td>
            <td class="report_heading">Description</td>
            <td class="report_heading">Qty</td>
            <td class="report_heading">Price</td>
            <td class="report_heading" colspan="5">Asset Allocation</td>
            <td class="report_heading">Weight</td>
            <td class="report_heading">Total Value</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td style="">EQ</td>
            <td style="">FI</td>
            <td style="">CS</td>
            <td style="">OT</td>
            <td style="">UC</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$CATEGORIES key=ck item=cv}
            <tr class="primary make_dotted" style="">
                <td class="report_heading">{$ck}</td>
                <td colspan="9" class="right">{$cv.weight|number_format:2:".":","}%</td>
                <td class="right strong">${$cv.total|number_format:2:".":","}</td>
            </tr>
            {foreach from=$GROUPED key=ik item=iv}
                {if $ck eq $iv.category}
                    {*                    {assign var='equity' value=$iv.us_stock_value + $iv.intl_stock_value}
                                        {assign var="fi" value=$iv.us_bond_value + $iv.intl_bond_value + $iv.preferred_net_value}
                                        {assign var="other" value=$iv.convertible_net_value + $iv.other_net_value}
                                        {assign var="symbol" value=$iv.security_symbol}
                                        {if $iv.category eq 'Blended'}
                                        <tr>
                                            <td class="left"><label class="hover_symbol_holdings" id="{$iv.security_symbol}" data-account='{$iv.account_number}'>&nbsp;&nbsp;&nbsp;&nbsp;{$iv.security_symbol}</label></td>
                                            <td>{$iv.description}</td>
                                            <td class="right">{$iv.quantity|number_format:2:".":","}</td>
                                            <td class="right">${$iv.last_price|number_format:2:".":","}</td>
                                            <td class="right" style="">${$equity|number_format:2:".":","}</td>
                                            <td class="right" style="">${$fi|number_format:2:".":","}</td>
                                            <td class="right" style="">${$iv.cash_net_value|number_format:2:".":","}</td>
                                            <td class="right" style="">${$other|number_format:2:".":","}</td>
                                            <td class="right" style="">${$iv.unclassified_net_value|number_format:2:".":","}</td>
                                            <td class="right">{$iv.weight|number_format:2:".":","}%</td>
                                            <td class="right">${$iv.current_value|number_format:2:".":","}</td>
                                        </tr>*}
                    {assign var='equity' value=$POSITIONS[$iv.security_symbol].equity}
                    {assign var="fi" value=$POSITIONS[$iv.security_symbol].fixed}
                    {assign var='cash' value=$POSITIONS[$iv.security_symbol].cash}
                    {assign var="other" value=$POSITIONS[$iv.security_symbol].other}
                    {assign var="symbol" value=$iv.security_symbol}
{*                    {if $iv.category eq 'Blended'}*}
                        <tr>
                            <td class="left"><label class="hover_symbol_holdings" id="{$iv.security_symbol}" data-account='{$iv.account_number}'>&nbsp;&nbsp;&nbsp;&nbsp;{$iv.security_symbol}</label></td>
                            <td>{$iv.description}</td>
                            <td class="right">{$iv.quantity|number_format:2:".":","}</td>
                            <td class="right">${$iv.last_price|number_format:2:".":","}</td>
                            <td class="right" style="">{$equity|number_format:2:".":","}%</td>
                            <td class="right" style="">{$fi|number_format:2:".":","}%</td>
                            <td class="right" style="">{$cash|number_format:2:".":","}%</td>
                            <td class="right" style="">{$other|number_format:2:".":","}%</td>
                            <td class="right" style="">{$iv.unclassified_net_value|number_format:2:".":","}</td>
                            <td class="right">{$iv.weight|number_format:2:".":","}%</td>
                            <td class="right">${$iv.current_value|number_format:2:".":","}</td>
                        </tr>
{*                    {else}
                        <tr>
                            <td class="left"><label class="hover_symbol_holdings" id="{$iv.security_symbol}" data-account='{$iv.account_number}'>&nbsp;&nbsp;&nbsp;&nbsp;{$iv.security_symbol}</label></td>
                            <td>{$iv.description}</td>
                            <td class="right">{$iv.quantity|number_format:2:".":","}</td>
                            <td class="right">${$iv.last_price|number_format:2:".":","}</td>
                            {if $iv.category eq 'Equity'}
                                <td class="right" style="" colspan="5">{$equity|number_format:2:".":","}%</td>
                            {/if}
                            {if $iv.category eq 'Fixed Income'}
                                <td class="right" style="" colspan="5">{$fi|number_format:2:".":","}%</td>
                            {/if}
                            {if $iv.category eq 'Cash'}
                                <td class="right" style="" colspan="5">{$iv.cash_net_value|number_format:2:".":","}%</td>
                            {/if}
                            {if $iv.category eq 'Other'}
                                <td class="right" style="" colspan="5">{$other|number_format:2:".":","}%</td>
                            {/if}
                            {if $iv.category eq 'Unclassified'}
                                <td class="right" style="" colspan="5">{$iv.unclassified_net_value|number_format:2:".":","}%</td>
                            {/if}
                            <td class="right">{$iv.weight|number_format:2:".":","}%</td>
                            <td class="right">${$iv.current_value|number_format:2:".":","}</td>
                        </tr>
                    {/if}*}
                {/if}
            {/foreach}
        {/foreach}
        <tr>
            <td colspan="4"><strong>Asset Allocation Values</strong></td>
            <td class="right" style=""><strong>${$ASSET_CLASS.equities|number_format:2:".":","}</strong></td>
            <td class="right" style=""><strong>${$ASSET_CLASS.fixed|number_format:2:".":","}</strong></td>
            <td class="right" style=""><strong>${$ASSET_CLASS.cash|number_format:2:".":","}</strong></td>
            <td class="right" style=""><strong>${$ASSET_CLASS.other|number_format:2:".":","}</strong></td>
            <td class="right" style=""><strong>${$ASSET_CLASS.unclassified|number_format:2:".":","}</strong></td>
            <td class="right"><strong>{$TOTAL_WEIGHT}%</strong></td>
            <td class="right"><strong>${$GLOBAL_TOTAL.global_total|number_format:2:".":","}</strong></td>
        </tr>
        </tbody>
    </table>
    <div class="account_notes">
        <h3>Account Notes:</h3>
        {if $UNSETTLED_CASH != 0}
            <p>There is a total of ${$UNSETTLED_CASH|number_format:2:".":","} in unsettled cash</p>
        {/if}
    </div>
</div>
<div style="page-break-after: always" />