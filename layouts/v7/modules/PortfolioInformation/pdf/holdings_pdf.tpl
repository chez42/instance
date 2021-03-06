{if $SETTINGS.positions neq '1'}<div style="float:left; margin-top: -30px;"></div>{/if}
<div style="display: block; margin: 0px auto 0px auto; width: 20%">
	<h2 style="padding-left: 20px;">Portfolio Holdings</h2>
</div>	
<table width="100%" style="background-color:#FFFFFF;">
	<tr>
		<td align="center" valign="top"><h4>Asset Allocation</h4></td>
		<td align="center" valign="top"><h4>Trailing 12 Assets Under Management</h4></td>
		<td align="center" valign="top"><h4>Trailing 12 Income</h4></td>
	</tr>
	<tr>
		<td align="center" valign="top" bgcolor="#FFFFFF">{if $RENDER_PIE_CHART eq '1'}<img src="storage/pdf/positions_pie.png" width="250" height="235" style="margin-top:-5px;" />{else}<p>&nbsp;</p><p style="font-size:16px;">No data available</p>{/if}</td>
		<td align="center" valign="top" bgcolor="#FFFFFF">{if $RENDER_LINE_CHART eq '1'}<img src="storage/pdf/positions_line.png" width="325" height="220" style="padding-top:10px;"/>{else}<p>&nbsp;</p><p style="font-size:16px;">No data available</p>{/if}</td>
		<td align="center" valign="top" bgcolor="#FFFFFF">{if $RENDER_BAR_CHART eq '1'}<img src="storage/pdf/account_historical.png" width="300" height="220" />{else}<p>&nbsp;</p><p style="font-size:16px;">No data available</p>{/if}</td>
	</tr>
</table>
{foreach from=$ACCOUNTS item=account_number name=accounts}
{if $ACCOUNT_TOTALS[{$account_number}].account_number_hash|strlen > 0}
<p style="font-size:10px;">Account: {$ACCOUNT_TOTALS[{$account_number}].account_number_hash}</p>
<!--Create Holdings Table-->
<!--
<div style="margin-left:25px; margin-bottom:5px; height:500px;">
    <img src="storage/pdf/positions_pie.png" width="250" height="220" />
    <img src="storage/pdf/positions_line.png" width="650" height="200" />
    <img style="display:block; margin-top:100px" src="storage/pdf/account_historical.png" width="900" height="250" />
</div>
-->
<div style="margin-left:25px; margin-bottom:5px;">
<table class="lvt small" cellspacing="0" cellpadding="3" border="0" width="100%" style="margin-top:30px;" >
    <thead>
        <tr style="background-color:#999;">
            <th align="left" width="220">Description</th>
            <th width="75" align="left"> Symbol</th>
            <th width="75" align="right">Weight</th>
            <!--<th align="left">Asset Class</th>-->
            <th width="75" align="right">Quantity</th>
            <th width="75" align="right"> Price</th>
            <th width="110" align="right">Current Value</th>
            <th width="110" align="right">Cost Basis</th>
            <th width="75" align="right">Unrealized Gain</th>
            <th width="75" align="right">G/L (%)</th>
        </tr>
    </thead>

    
{foreach from=$SIMPLE_CATEGORIES item=CATEGORY_VALUE}
    {if $CATEGORY_VALUE.account_number eq $account_number}
    <tr style="background-color:#CCC;">
            <td colspan="2"><strong>{$CATEGORY_VALUE.asset_class}</strong></td>
            <td colspan="3" align="right"><strong>{$CATEGORY_VALUE.weight|string_format:"%.01f"}%</strong></td>
            <td align="right"><strong>${$CATEGORY_VALUE.total_value|number_format:2}</strong></td>
            <td align="right"><strong>${$CATEGORY_VALUE.cost_basis|number_format:2}</strong></td>
            <td align="right"><strong>${$CATEGORY_VALUE.ugl|number_format:2}</strong></td>
            <td align="right"><strong>{$CATEGORY_VALUE.gl|string_format:"%.01f"}%</strong></td>
    </tr>
        {foreach from=$SIMPLE_SUB_CATEGORIES item=SUB_CATEGORY_VALUE}
            <p>&nbsp;</p>
            {if $SUB_CATEGORY_VALUE.account_number eq $account_number 
                AND $SUB_CATEGORY_VALUE.asset_class eq $CATEGORY_VALUE.asset_class}
                <tr style="background-color:#e5e5e5;">
                    <td colspan="2"><strong>&nbsp;&nbsp;{$SUB_CATEGORY_VALUE.sub_sub_category}</strong></td>
                    <td colspan="3" align="right"><strong>{$SUB_CATEGORY_VALUE.weight|string_format:"%.01f"}%</strong></td>
                    <td align="right"><strong>${$SUB_CATEGORY_VALUE.total_value|number_format:2}</strong></td>
                    <td align="right"><strong>${$SUB_CATEGORY_VALUE.cost_basis|number_format:2}</strong></td>
                    <td align="right"><strong>${$SUB_CATEGORY_VALUE.ugl|number_format:2}</strong></td>
                    <td align="right"><strong>{$SUB_CATEGORY_VALUE.gl|string_format:"%.01f"}%</strong></td>
                </tr>
                {foreach from=$SORTED_POSITIONS item=POSITION}
                    {if $POSITION.account_number eq $account_number AND 
                        $POSITION.asset_class eq $CATEGORY_VALUE.asset_class AND
                        $POSITION.sub_sub_category eq $SUB_CATEGORY_VALUE.sub_sub_category}
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{$POSITION.description}</td>
                            <td>{$POSITION.security_symbol}</td>
                            <td align="right">{$POSITION.percent|string_format:"%.01f"}%</td>
                            <td align="right">{$POSITION.quantity}</td>
                            <td align="right">${$POSITION.last_price|number_format:2}</td>
                            <td align="right">${$POSITION.current_value|number_format:2}</td>
                            <td align="right">${$POSITION.cost_basis|number_format:2}</td>
                            <td align="right">${$POSITION.gain_loss|number_format:2}</td>
                            <td align="right">{$POSITION.gain_loss_percent|string_format:"%.01f"}%</td>
                        </tr>
                    {/if}
                {/foreach}
            {/if}
        {/foreach}
    {/if}
{/foreach}
<tr style="background-color:#bebebe;"><!--Grand Totals-->
        <td><strong>Total</strong></td>
        <td>&nbsp;</td>
        <td align="right"><strong>{$ACCOUNT_TOTALS[{$account_number}].weight|string_format:"%.01f"}%</strong></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right"><strong>${$ACCOUNT_TOTALS[{$account_number}].total_value|number_format:2}</strong></td>
        <td align="right"><strong>${$ACCOUNT_TOTALS[{$account_number}].cost_basis|number_format:2}</strong></td>
        <td align="right"><strong>{if $ACCOUNT_TOTALS[{$account_number}].ugl < 0}
                (${$ACCOUNT_TOTALS[{$account_number}].ugl|number_format:2|replace:'-':''})
            {else}
                {$ACCOUNT_TOTALS[{$account_number}].ugl|number_format:2}
        {/if}</strong></td>
        <td align="right"><strong>{$ACCOUNT_TOTALS[{$account_number}].gl|string_format:"%.01f"}%</strong></td>
    </tr>
    
<!--The Grand Total only shows if it is the last account in the loop-->
{if $smarty.foreach.accounts.last}
    <tr style="background-color:#a2a2a2;"><!--Grand Totals-->
        <td><strong>Portfolio Total </strong></td>
        <td>&nbsp;</td>
        <td align="right"><strong>{$GRAND_TOTAL.weight|string_format:"%.01f"}%</strong></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right"><strong>${$GRAND_TOTAL.total_value|number_format:2}</strong></td>
        <td align="right"><strong>${$GRAND_TOTAL.cost_basis|number_format:2}</strong></td>
        <td align="right"><strong>{if $GRAND_TOTAL.ugl < 0}
                (${$GRAND_TOTAL.ugl|number_format:2|replace:'-':''})
            {else}
                {$GRAND_TOTAL.ugl|number_format:2}
        {/if}</strong></td>
        <td align="right"><strong>{$GRAND_TOTAL.ugl_percent|string_format:"%.01f"}%</strong></td>
    </tr>
{/if}
</table>
</div>
{/if}
{/foreach}
