
<div id="wrapper">
    {if $SETTINGS.overview eq '1'}
        {include file='layouts/vlayout/modules/PortfolioInformation/overview_pdf.tpl'}
    {/if}
    {if $SETTINGS.account_details eq '1'}

    <div id="lefttable"  style="width:560px; margin-top:5px; margin-left:25px;">
        <table border="0">
            <thead>
                <tr><th colspan="2">Account Details</th></tr>
            </thead>
            <tbody>
                <tr><td align="left" class="lvtColLeft">Account Name</td><td>{$ACCT_DETAILS.name}</td></tr>
                <tr><td align="left" class="lvtColLeft">Account Number</td><td>{$ACCT_DETAILS.number}</td></tr>
                <tr><td align="left" class="lvtColLeft">Custodian</td><td>{$ACCT_DETAILS.custodian}</td></tr>
                <tr><td align="left" class="lvtColLeft">Account Type</td><td>{$ACCT_DETAILS.type}</td></tr>
                <tr><td align="left" class="lvtColLeft">Total Value</td><td>${$ACCT_DETAILS.total|number_format:2}</td></tr>
                <tr><td align="left" class="lvtColLeft">Market Value</td><td>${$ACCT_DETAILS.market_value|number_format:2}</td></tr>
                <tr><td align="left" class="lvtColLeft">Cash Value</td><td>${$ACCT_DETAILS.cash_value|number_format:2}</td></tr>
                <tr><td align="left" class="lvtColLeft">Management Fee</td><td>{$ACCT_DETAILS.management_fee}</td></tr>
                <tr><td align="left" class="lvtColLeft">Annual Management Fees (Trailing 12)</td><td>${$ACCT_DETAILS.annual_fee|number_format:2}</td></tr>
            </tbody>
        </table>
    </div>
    {/if}
<!--    {if $SETTINGS.pie_chart eq '1'}
    <div id="charts"  style="margin-top:5px; margin-left:25px;">
        <div id="pie_chart"><img src="storage/pdf/positions_pie.png" width="872" height="284" /></div>
    </div>
    {/if}-->
</div>
{if $SETTINGS.other_accounts eq '1'}
<div style="margin-top:5px; margin-left:25px;">
<table width="50%">
    <thead>
        <tr>
            <th>Account Number</th>
            <th>Total Value</th>
            <th>Market Value</th>
            <th>Cash Value</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$OTHER_ACCOUNTS key=k item=v}
        <tr>
            <td>{$v.account_number}</td>
            <td>${$v.total_value|number_format:2}</td>
            <td>${$v.market_value|number_format:2}</td>
            <td>${$v.cash_value|number_format:2}</td>
        </tr>
        {/foreach}
        <tr>
            <td><strong>Totals</strong></td>
            <td>${$OTHER_TOTALS.total_value|number_format:2}</td>
            <td>${$OTHER_TOTALS.market_value|number_format:2}</td>
            <td>${$OTHER_TOTALS.cash_value|number_format:2}</td>
        </tr>
    </tbody>
</table>
</div>
{/if}
{if $SETTINGS.positions eq '1'}
<!--Create Positions Table-->
<div style="margin-top:5px; margin-left:25px;">
<h2>Positions</h2>
<table class="lvt small" cellspacing="1" cellpadding="3" border="0" width="100%" style="margin-top:30px;" >
    <thead>
    <tr>
        <th>Security Symbol</th>
        <th>Description</th>
        <th><a>Security Type</th>
        <th>Asset Class</th>
        <th>Quantity</th>
        <th>Last Price</th>
        <th>Current Value</th>
        <th>% of Portfolio</th>
        <th>Cost Basis</th>
        <th>Gain / Loss</th>
        <th>G/L (%)</th>
    </tr>
    </thead>
    <tr>
        {foreach from=$FINAL_VALUE key=k item=v}
        {if $v.security_symbol}
            <tr>
                <td>{$v.security_symbol}</td>
                <td>{$v.description}</td>
                <td>{$v.security_type}</td>
                <td>{$v.asset_class}</td>
                {if $v.quantity EQ 0}
                    <td align="right">&nbsp;</td>
                {else}
                    <td align="right">{$v.quantity|number_format}</td>
                {/if}
                {if $v.last_price EQ 0}
                    <td align="right">&nbsp;</td>
                {else}
                    <td align="right">${$v.last_price|number_format:2}</td>
                {/if}
                <td align="right">${$v.current_value|number_format:2}</td>
                <td align="right">{$v.percent|string_format:"%.01f"}%</td>
                <td align="right">${$v.cost_basis|number_format:2}</td>
                <td align="right">{if $v.gain_loss < 0}
                        (${$v.gain_loss|number_format:2|replace:'-':''})
                    {else}
                        ${$v.gain_loss|number_format:2}
                    {/if}
                </td>
                <td align="right">{$v.gain_loss_percent|string_format:"%.01f"}%</td>
            </tr>
        {/if}
        {/foreach}
</table>
</div>
{/if}
{if $SETTINGS.holdings eq '1'}
    {include file='layouts/vlayout/modules/PortfolioInformation/pdf/holdings_pdf.tpl'}
{/if}

{if $SETTINGS.monthly_income eq '1'}
    {include file='layouts/vlayout/modules/PortfolioInformation/pdf/monthly_history_pdf.tpl'}
    {include file='layouts/vlayout/modules/PortfolioInformation/pdf/monthly_projected_pdf.tpl'}
{/if}

{if $SETTINGS.performance eq '1'}
    {include file='layouts/vlayout/modules/PortfolioInformation/pdf/performance_pdf.tpl'}
{/if}
<div class='disclaimer'>
    <div style="display:block; width:100%; margin-top:10px;">
        <p style="text-align:left; display:block; color:black; font-size:10px;">
            Market values are obtained from sources believed to be reliable but are not 					
            guaranteed.  No representaion is made as to this review\'s accuracy or completeness. The performance data quoted represents past performance and does not guarantee future results. 
            <br />
            <br />
            The investment return and principal value of an investment will fluctuate thus an investor\'s shares, when redeemed, may be worth more or less than return data quoted herein.
        </p>
    </div>
</div>
