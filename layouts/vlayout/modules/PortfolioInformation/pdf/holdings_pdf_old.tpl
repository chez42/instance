    <div style="float:left; margin-top: -30px;">
  <span style="text-align:right; font-size:10px;">Prepared By: {$USER_NAME}</span>
</div>
<div style="display: block; margin: -80px auto 0px auto; width: 20%">
  <h2 style="padding-left: 20px;">Portfolio Holdings</h2></div>
  

<!--Create Holdings Table-->
<div style="margin-left:25px; margin-bottom:5px; height:500px;">
<table class="lvt small" cellspacing="0" cellpadding="3" border="0" width="100%" style="margin-top:30px;" >
    <thead>
        <tr style="background-color:#999;">
            <th align="left" width="220">Description</th>
            <th  width="75" align="left"> Symbol</th>
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
{foreach from=$MAIN_CATEGORIES key=MAIN_TITLE item=MAIN_VALUES}
    <tr style="background-color:#CCC;">
    <td><strong>{$MAIN_TITLE}</strong></td>
    <td>&nbsp;</td>
    <td align="right">{$MAIN_VALUES.weight|string_format:"%.01f"}%</td>
    <!--<td>&nbsp;</td>-->
    <td>&nbsp;</td>
    <td></td>
    <td align="right">${$MAIN_VALUES.total_value|number_format:0}</td>
    <td align="right">${$MAIN_VALUES.cost_basis|number_format:0}</td>
    <td align="right">${$MAIN_VALUES.gain_loss|number_format:0}</td>
    <td align="right">{$MAIN_VALUES.gain_loss_percent|string_format:"%.01f"}%</td>
    
    </tr>
    {foreach from=$SUB_SUB_CATEGORIES key=SUB_TITLE item=MAIN}
        {if $MAIN.asset_class eq $MAIN_TITLE}
            <tr style="background-color:#e5e5e5;">
                <td><strong>&nbsp;&nbsp;{$SUB_TITLE}</strong></td>
                <td>&nbsp;</td>
                 <td align="right"><strong>{$MAIN.weight|string_format:"%.01f"}%</strong></td>
                <!--<td>&nbsp;</td>-->
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong>${$MAIN.total_value|number_format:0}</strong></td>
                <td align="right"><strong>${$MAIN.cost_basis|number_format:0}</strong></td>
                <td align="right"><strong>${$MAIN.gain_loss|number_format:0}</strong></td>
                <td align="right"><strong>{$MAIN.gain_loss_percent|string_format:"%.01f"}%</strong></td>
                
            </tr>
            {foreach from=$FINAL_VALUE key=k item=v}
                {if $v.sub_sub_category eq $SUB_TITLE AND $v.asset_class eq $MAIN_TITLE AND $v.security_symbol}
                    <tr>
                        <td align="left"><strong>{$v.description}</strong> {$v.account_number_hash}</td>
                        <td>&nbsp;&nbsp;&nbsp;{$v.security_symbol}</td>
                        <td align="right">{$v.percent|string_format:"%.01f"}%</td>
                       <!-- <td align="left">{$v.asset_class}</td>-->
                        <td align="right">{if $v.quantity EQ 0} {else}{$v.quantity|number_format}{/if}</td>                   
                        <td align="right">{if $v.last_price EQ 0} {else}${$v.last_price|number_format:2}{/if}</td>                      
                        <td align="right">${$v.current_value|number_format:0}</td>
                        <td align="right">${$v.cost_basis|number_format:0}</td>
                        <td align="right">{if $v.gain_loss < 0}
                                (${$v.gain_loss|number_format:0|replace:'-':''})
                            {else}
                                ${$v.gain_loss|number_format:0}
                            {/if}
                        </td>
                        <td align="right">{$v.gain_loss_percent|string_format:"%.01f"}%</td>
                    </tr>
                {/if}
            {/foreach}
        {/if}
    {/foreach}
{/foreach}
    <tr><!--Grand Totals-->
        <td><strong>Total</strong></td>
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
</table>
</div>