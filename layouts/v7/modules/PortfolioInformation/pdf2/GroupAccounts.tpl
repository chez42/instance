{if !empty($PORTFOLIO_DATA)}
    <div id="rightside" style="float:right; width:48%; clear:both;font-family:Arial,Sans-Serif;font-size:16px;">
        <h2>Accounts Overview</h2>
    </div>
    <div id="account_info_wrapper" style = "font-family:Arial,Sans-Serif;font-size:16px;">
        <table class="holdings_report_account_info">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="right" style="border-bottom:1px dotted black;"><strong>All Accounts Total</strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="right">${$GLOBAL_TOTAL.global_total|number_format:2:".":","}</td>
            </tr>
            <tr>
                <td style="padding-top: 25px;">&nbsp;</td>
                <td style="padding-top: 25px;" class="center"><strong>Account Name</strong></td>
                <td style="padding-top: 25px;" class="center"><strong>Account Type</strong></td>
				<td style="padding-top: 25px;" class="right"><strong>Total Value</strong></td>
            </tr>
            {foreach from=$PORTFOLIO_DATA key=k item=v}
                <tr>
                    <td>{$v.account_number}
                    <td class="center">{$v.first_name} {$v.last_name}</td>
                    <td class="center">{$v.account_type}</td>
                    <td class="right">${$v.total_value|number_format:2:".":","}</td>
                </tr>
            {/foreach}
        </table>
    </div>
{/if}