<html>
    <head>
        {foreach key=index item=cssModel from=$STYLES}
            <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
        {/foreach}
    </head>
    <body>
    <div id="wrapper">
        {if $MAILING_INFO}
        <div id="mailinginfo">
            <div class="leftside" style="float:left; width:48%;">
                <img src="{if $LOGO neq ''}{$LOGO}{else}test/logo/Omniscient Logo small.png{/if}" />
            </div>
            <div class="rightside" style="float:right; width:48%;">
                <p style="width:60%; display:block; border-bottom:1px solid black"><span style="font-size:8px;">Prepared for</span></p>
                {if $MAILING_INFO}
                    {if $MAILING_INFO['name']}
                        <p style="margin:0;padding:0">{$MAILING_INFO['name']}</p>
                    {/if}
                    {if $MAILING_INFO['street']}
                        <p style="margin:0;padding:0">{$MAILING_INFO['street']}</p>
                    {/if}
                    {if $MAILING_INFO['city']}
                        <p style="margin:0;padding:0">{$MAILING_INFO['city']}</p>
                    {/if}
                    {if $MAILING_INFO['state']}
                        <p style="margin:0;padding:0">{$MAILING_INFO['state']}</p>
                    {/if}
                    {if $MAILING_INFO['zip']}
                        <p style="margin:0;padding:0">{$MAILING_INFO['zip']}</p>
                    {/if}
                {/if}
            </div>
        </div>
        <div style="page-break-after: always" />
        {/if}
        <div style="float:left; margin-top: -30px;">
          <span style="text-align:right; font-size:10px;">Prepared By: {$USER_NAME}</span>
        </div>
        <div id="charts" style="width:45%">
            <div id="pie_chart">
				{if $RENDER_PIE_CHART eq '1'}
                    {$PIE_IMAGE}
{*					<img src="storage/pdf/overview_pie.png" width="580" height="178" />*}
				{else}
					<div width="580" height="178"><p style="font-size: 16px; text-align: center;">No data available.</p></div> 
				{/if}
            </div>
            <div id="graph_chart">
                {if $RENDER_BAR_CHART eq '1'}
					<img src="storage/pdf/chart.png"  width="579" height="318"/>
				{else}
					<div width="580" height="318"><p style="font-size: 16px; text-align: center;">No data available.</p></div> 
				{/if}
            </div>
        </div>
        <div id="investments" style="width:50%">
            <table class="investments" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;">
                <tr><td colspan="5" align="center" ><h4>Portfolio Summary</h4></td></tr>
                <tr>
                  <td bgcolor="#cccccc" width="20%" >&nbsp;</td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Last Year</b></td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Trailing 3</b></td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Trailing 12</b></td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>YTD</b></td>
<!--                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Since <br>
                  Inception</b></td>
-->
                </tr>

                <tr>
                  <td align="right" style="font-size:12px;">Beginning Value</td>
                  <td align="right" style="font-size:12px;">{$LYR.beginning_value|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.beginning_value|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.beginning_value|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.beginning_value|number_format:2}</td>
                  {*<td align="right">{$INCEPTION.start_value|number_format:2}</td>*}
                </tr>
                <tr>
                  <td align="right" style="font-size:12px;">Net Contributions</td>
                  <td align="right" style="font-size:12px;">{$LYR.net_contributions|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.net_contributions|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.net_contributions|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.net_contributions|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.net_contributions|number_format:2}*}
                </tr>
                    <tr>
                  <td align="right" style="font-size:12px;">Net Income</td>
                  <td align="right" style="font-size:12px;">{$LYR.net_income|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.net_income|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.net_income|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.net_income|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.net_income|number_format:2}</td>-->*}
                </tr>
                <tr>
                  <td align="right" style="font-size:12px;">Capital Appreciation</td>
                  <td align="right" style="font-size:12px;">{$LYR.capital_appreciation|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.capital_appreciation|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.capital_appreciation|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.capital_appreciation|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.capital_appreciation|number_format:2}</td>-->*}
                </tr>
                <tr>
                  <td align="right" style="font-weight:bold; font-size:12px;">Ending Value</td>
                  <td align="right" style="font-weight:bold; font-size:12px;">{$LYR.ending_value|number_format:2}</td>
                  <td align="right" style="font-weight:bold; font-size:12px;">{$QTR.ending_value|number_format:2}</td>
                  <td align="right" style="font-weight:bold; font-size:12px;">{$TRAILING.ending_value|number_format:2}</td>
                  <td align="right" style="font-weight:bold; font-size:12px;">{$YTD.ending_value|number_format:2}</td>
                  {*<!--<td align="right" style="font-weight:bold">{$INCEPTION.end_value|number_format:2}</td>-->*}
                </tr>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" class="top_spacing">
                  <tr>
                  <td colspan="5" align="center" style="border-bottom:1px #333 solid; font-family:Arial, Helvetica, sans-serif" valign="top"><h4>Additional Details</h4></td>
                </tr>
                <tr>
                  <td align="left" bgcolor="#cccccc" width="20%">&nbsp;</td>
                  <td align="right" bgcolor="#cccccc" width="20%"><strong>Last Year</strong></td>
                  <td align="right" bgcolor="#cccccc" width="20%"><b>Trailing 3</b></td>
                  <td align="right" bgcolor="#cccccc" width="20%"><strong>Trailing 12</strong></td>
                  <td align="right" bgcolor="#cccccc" width="20%"><strong>YTD</strong></td>
<!--                  <td align="right" bgcolor="#cccccc"width="20%"><strong>Since <br>
                  Inception</strong></td>
-->
                </tr>
                <tr>
                  <td align="right" style="font-size:12px;">Contributions</td>
                  <td align="right" style="font-size:12px;">{$LYR.other_net_contributions|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.other_net_contributions|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.other_net_contributions|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.other_net_contributions|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.contributions|number_format:2}</td>-->*}
                </tr>
                <tr>
                  <td align="right" style="font-size:12px;">Withdrawals</td>
                  <td align="right" style="font-size:12px;">{$LYR.other_withdrawals|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.other_withdrawals|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.other_withdrawals|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.other_withdrawals|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.withdrawals|number_format:2}</td>-->*}
                </tr>
                {if $SHOW_EXPENSES || $SHOW_BREAKDOWN}
                <tr>
                    <td align="right" style="font-size:12px;">Expenses</td>
                    <td align="right" style="font-size:12px;">{$LYR.expenses|number_format:2}</td>
                    <td align="right" style="font-size:12px;">{$QTR.expenses|number_format:2}</td>
                    <td align="right" style="font-size:12px;">{$TRAILING.expenses|number_format:2}</td>
                    <td align="right" style="font-size:12px;">{$YTD.expenses|number_format:2}</td>
                </tr>
                {/if}
                {if $SHOW_BREAKDOWN}
                <tr>
                  <td align="right" style="font-size:12px;">Management Fee</td>
                  <td align="right" style="font-size:12px;">{$LYR.management_fee|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.management_fee|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.management_fee|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.management_fee|number_format:2}</td>
                </tr>
                <tr>
                  <td align="right" style="font-size:12px;">Other Expenses</td>
                  <td align="right" style="font-size:12px;">{$LYR.other_expenses|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.other_expenses|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.other_expenses|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.other_expenses|number_format:2}</td>
                </tr>
                {/if}
                <tr>
                  <td align="right" style="font-size:12px;">Income</td>
                  <td align="right" style="font-size:12px;">{$LYR.other_income|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.other_income|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.other_income|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.other_income|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.income|number_format:2}</td>-->*}
                </tr>   
                <tr>
                  <td align="right" style="font-size:12px;">Investment Return</td>
                  <td align="right" style="font-size:12px;">{$LYR.other_investment_return|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$QTR.other_investment_return|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$TRAILING.other_investment_return|number_format:2}</td>
                  <td align="right" style="font-size:12px;">{$YTD.other_investment_return|number_format:2}</td>
                  {*<!--<td align="right">{$INCEPTION.investment_return|number_format:2}</td>-->*}
                </tr>                
            </table>
            <table border="0" cellpadding="0" cellspacing="0" class="top_spacing">
              <tr>
                <td colspan="4" align="center" style="border-bottom:1px #333 solid; font-family:Arial, Helvetica, sans-serif;"  ><h4>Performance Summary</h4></td>
                </tr>
              <tr>
                <td align="right" bgcolor="#cccccc" style="font-size:12px;"><strong>Total portfolio</strong></td>
				<td align="right" bgcolor="#cccccc" style="font-size:12px;"><strong>Trailing 3</strong></td>
                <td align="right" bgcolor="#cccccc" style="font-size:12px;"><strong>Trailing 12</strong></td>
                <td align="right" bgcolor="#cccccc" style="font-size:12px;"><strong>YTD</strong></td>
                {if $SHOW_INCEPTION eq 1}
                    <th align="right" bgcolor="#cccccc" style="font-size:12px;"><strong>Inception</strong></th>
                {/if}
                </tr>
              <tr>
                <td align="right" style="font-size:12px;">Time Weighted Return</td>
                <td align="right" style="text-align: right; font-size:12px;">{$TWR.trailing_3}%</td>
                <td align="right" style="text-align: right; font-size:12px;"><span id="TWR_WARNING" style="color:red; display:inline; float:left;">{$DISCLAIMER_WARNING}</span>{$TWR.trailing_12}%</td>
                <td align="right" style="text-align: right; font-size:12px;"><span id="TWR_WARNING" style="color:red; display:inline;">{$DISCLAIMER_WARNING}</span>{$TWR.year_to_date}%</td>
                {if $SHOW_INCEPTION eq 1}
                    <td align="right" style="text-align: right; font-size:12px;"><span id="TWR_WARNING_INCEPTION" style="color:red; display:inline;">{$DISCLAIMER_WARNING}</span>{$TWR.inception}%</td>
                {/if}
              </tr>
              <tr>
                <td align="right" style="font-size:12px;">S&amp;P 500</td>
                <td align="right" style="text-align: right; font-size:12px;">{$REF.trailing_3}%</td>
                <td align="right" style="text-align: right; font-size:12px;">{$REF.trailing_12}%</td>
                <td align="right" style="text-align: right; font-size:12px;">{$REF.year_to_date}%</td>
                {if $SHOW_INCEPTION eq 1}
                    <td align="right" style="text-align: right; font-size:12px;">{$REF.inception}%</td>
                {/if}
                </tr>
              <tr>
                <td id="WARNING" colspan="5" style='color:red;'>{if $DISCLAIMER_WARNING eq "*"}* The inception is less than one year{/if}</td>
              </tr>
                <tr>
                    <td align="right" valign="top" style="font-size:12px;">Barcap Aggregate Bond</td>
                    <td align="right" style="text-align: right; font-size:12px;" valign="top">{$BAR.trailing_3}%</td>
                    <td align="right" style="text-align: right; font-size:12px;" valign="top">{$BAR.trailing_12}%</td>
                    <td align="right" style="text-align: right; font-size:12px;" valign="top">{$BAR.year_to_date}%</td>
                    {if $SHOW_INCEPTION eq 1}
                        <td align="right" style="text-align: right" valign="top">{$BAR.inception}%</td>
                    {/if}
                </tr>
                {if $SHOW_GOAL}
                <tr>
                    <td align="right" valign="top">Goal %</td>
                    <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
                    <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
                    <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
                    {if $SHOW_INCEPTION eq 1}
                        <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
                    {/if}
                </tr>
                {/if}
            </table>
              <table border="0" cellpadding="0" cellspacing="0" class="top_spacing">
                <tr><td colspan="3" align="center" style="font-family:Arial, Helvetica, sans-serif;">
                <h4>Accounts Used in Report</h4>
                </td></tr>
              </table>
              <table border="0" cellpadding="0" cellspacing="0">
                {foreach from=$PERFORMANCE_ACCOUNTS_USED item=v}
                    <tr><td style="font-size:10px;">{$v.account_number} {$v.account_nickname}</td></tr>
                {/foreach}
              </table>
        </div>
        <p style="font-size:8px;"><br />Performance is calculated based on individual transactions and are subject to differences in precision depending on custodial data</p>
        <div style="page-break-after:always"></div>
        <div style="clear:both;"></div>
        {if $SHOW_TRANSACTIONS eq 1}
            {include file='layouts/vlayout/modules/PortfolioInformation/pdf/transactions_pdf.tpl'}
        {/if}
    </div>
    <div class='disclaimer'>
        <div style="display:block; width:100%; margin-top:10px;">
            <p style="text-align:left; display:block; color:black; font-size:10px;">
                Market values are obtained from sources believed to be reliable but are not 					
                guaranteed.  No representaion is made as to this review&rsquo;s accuracy or completeness. The performance data quoted represents past performance and does not guarantee future results.
                <br />
                <br />
                The investment return and principal value of an investment will fluctuate thus an investor&rsquo;s shares, when redeemed, may be worth more or less than return data quoted herein.
            </p>
        </div>
    </div>
    </body>
</html>