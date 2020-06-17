{*
<div class="breakbefore"></div>

<img src="storage/pdf/projected.png" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" name="portfolio_holdings_table" style="padding-top:10px;">
<tr style="font-size:12px; background-color:#9c9c9c;">
    <th width="150" align="left" valign="top" style="font-size:12px;">Symbol</th>
    <th align="left" valign="top">Description</th>
    {foreach from=$DISPLAY_MONTHS key=k item=v}
    <th align="right" valign="top">{$v} <br /><span style="font-size:9px;">{$DISPLAY_YEARS_PROJECTED.$v}</span></th>
    {/foreach}
    <th align="right" valign="top">Total</th>
    {foreach from=$MAIN_CATEGORIES_PROJECTED key=main_category_key item=main_category}
        <tr style="font-size:8px; background-color:#cccccc;">
            <td colspan="14"><strong>{$main_category.category}</strong> - ${$main_category.category_total|number_format:0}</td>
            <td align="right"></td>
        </tr>
        {foreach from=$SUB_SUB_CATEGORIES_PROJECTED key=sub_sub_category_key item=sub_sub_category}
            {if $sub_sub_category.category eq $main_category.category}
                <tr style="font-size:8px; background-color:#e1e1e1;">
                    <td colspan="14">&nbsp;&nbsp;&nbsp;&nbsp;<strong>{$sub_sub_category.sub_sub_category}</strong> - ${$sub_sub_category.sub_category_total|number_format:0}</td>
                    <td align="right"></td>
                </tr>
                {foreach from=$PROJECTED_SYMBOLS key=symbol_key item=symbol}
                    {if $symbol.sub_sub_category eq $sub_sub_category.sub_sub_category}
                        <tr style="font-size:8px;">
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$symbol.symbol}</td>
                            <td align="left">{$symbol.description}</td>
                            {foreach from=$DISPLAY_MONTHS key=month_key item=month}
                                {if $PROJECTED_SYMBOLS_VALUES[$symbol.symbol][$month].month eq $month}
                          <td align="right">${$PROJECTED_SYMBOLS_VALUES[$symbol.symbol][$month].amount|number_format:0}</td>
                                {else}
                                    <td>&nbsp;</td>
                                {/if}
                            {/foreach}
                            <td align="right">${$symbol.symbol_total|number_format:0}</td>
                        </tr>
                    {/if}
                {/foreach}
            {/if}
        {/foreach}
    {/foreach}
    <tr style="font-size:8px;">
        <td colspan="2">&nbsp;</td>
        {foreach from=$DISPLAY_MONTHS key=month_key item=month}
            <td align="right"><strong>${$PROJECTED_MONTHLY_TOTALS[$month].monthly_total|number_format:0}</strong></td>
        {/foreach}
        <td align="right"><strong>${$PROJECTED_MONTHLY_TOTALS['grand_total']|number_format:0}</strong></td>
    </tr>
</table>
*}