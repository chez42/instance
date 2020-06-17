<html>
    <head>
        {foreach key=index item=cssModel from=$STYLES}
            <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
        {/foreach}
    </head>
    <body>
    <div id="wrapper">
        <div style="float:left; margin-top: -30px;">
          <span style="text-align:right; font-size:10px;">Prepared By: {$USER_NAME}</span>
        </div>
        <div id="charts">            
            <div id="pie_chart">
                <img src="storage/pdf/overview_pie.png" width="580" height="178" />
            </div>
            <div id="graph_chart">
                <img src="storage/pdf/chart.png"  width="579" height="318"/>
            </div>
        </div>
        <div id="investments">
            <table class="investments" border="0" cellpadding="0" cellspacing="0">
                <tr><td colspan="5" align="center" ><h4>Portfolio Summary</h4></td></tr>
                <tr>
                  <td bgcolor="#cccccc" width="20%" >&nbsp;</td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Last <br>
                  Year</b></td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Trailing <br>
                  3 Mos</b></td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Trailing <br>
                  12 Mos</b></td>
                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Year <br>
                  To Date</b></td>                  
<!--                  <td align="right" bgcolor="#cccccc"  width="20%" ><b>Since <br>
                  Inception</b></td>
-->
                </tr>

                <tr>
                  <td align="right">Beginning Value</td>
                  <td align="right">{$LYR.beginning_value|number_format:2}</td>
                  <td align="right">{$QTR.beginning_value|number_format:2}</td>
                  <td align="right">{$TRAILING.beginning_value|number_format:2}</td>
                  <td align="right">{$YTD.beginning_value|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.start_value|number_format:2}</td>-->
                </tr>
                <tr>
                  <td align="right">Net Contributions</td>
                  <td align="right">{$LYR.net_contributions|number_format:2}</td>
                  <td align="right">{$QTR.net_contributions|number_format:2}</td>
                  <td align="right">{$TRAILING.net_contributions|number_format:2}</td>
                  <td align="right">{$YTD.net_contributions|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.net_contributions|number_format:2}</td>-->
                </tr>
                    <tr>
                  <td align="right">Net Income</td>
                  <td align="right">{$LYR.net_income|number_format:2}</td>
                  <td align="right">{$QTR.net_income|number_format:2}</td>
                  <td align="right">{$TRAILING.net_income|number_format:2}</td>
                  <td align="right">{$YTD.net_income|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.net_income|number_format:2}</td>-->
                </tr>
                <tr>
                  <td align="right">Capital Appreciation</td>
                  <td align="right">{$LYR.capital_appreciation|number_format:2}</td>
                  <td align="right">{$QTR.capital_appreciation|number_format:2}</td>
                  <td align="right">{$TRAILING.capital_appreciation|number_format:2}</td>
                  <td align="right">{$YTD.capital_appreciation|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.capital_appreciation|number_format:2}</td>-->
                </tr>
                <tr>
                  <td align="right" style="font-weight:bold">Ending Value</td>
                  <td align="right" style="font-weight:bold">{$LYR.ending_value|number_format:2}</td>
                  <td align="right" style="font-weight:bold">{$QTR.ending_value|number_format:2}</td>
                  <td align="right" style="font-weight:bold">{$TRAILING.ending_value|number_format:2}</td>
                  <td align="right" style="font-weight:bold">{$YTD.ending_value|number_format:2}</td>
                  <!--<td align="right" style="font-weight:bold">{$INCEPTION.end_value|number_format:2}</td>-->
                </tr>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" class="top_spacing">
                  <tr>
                  <td colspan="5" align="center" style="border-bottom:1px #333 solid; font-family:Arial, Helvetica, sans-serif" valign="top"><h4>Additional Details</h4></td>
                </tr>
                <tr>
                  <td align="left" bgcolor="#cccccc" width="20%">&nbsp;</td>
                  <td align="right" bgcolor="#cccccc" width="20%"><strong>Last<br>Year</strong></td>
                  <td align="right" bgcolor="#cccccc" width="20%"><b>Trailing<br>3 Mos</b></td>
                  <td align="right" bgcolor="#cccccc" width="20%"><strong>Trailing<br>12 Mos</strong></td>
                  <td align="right" bgcolor="#cccccc" width="20%"><strong>Year <br> To Date</strong></td>                  
<!--                  <td align="right" bgcolor="#cccccc"width="20%"><strong>Since <br>
                  Inception</strong></td>
-->
                </tr>
                <tr>
                  <td align="right">Net Contributions</td>
                  <td align="right">{$LYR.other_net_contributions|number_format:2}</td>
                  <td align="right">{$QTR.other_net_contributions|number_format:2}</td>
                  <td align="right">{$TRAILING.other_net_contributions|number_format:2}</td>
                  <td align="right">{$YTD.other_net_contributions|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.contributions|number_format:2}</td>-->
                </tr>
                <tr>
                  <td align="right">Withdrawals</td>
                  <td align="right">{$LYR.other_withdrawals|number_format:2}</td>
                  <td align="right">{$QTR.other_withdrawals|number_format:2}</td>
                  <td align="right">{$TRAILING.other_withdrawals|number_format:2}</td>
                  <td align="right">{$YTD.other_withdrawals|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.withdrawals|number_format:2}</td>-->
                </tr>
                {if $SHOW_EXPENSES || $SHOW_BREAKDOWN}
                <tr>
                    <td align="right">Expenses</td>
                    <td align="right">{$LYR.expenses|number_format:2}</td>
                    <td align="right">{$QTR.expenses|number_format:2}</td>
                    <td align="right">{$TRAILING.expenses|number_format:2}</td>
                    <td align="right">{$YTD.expenses|number_format:2}</td>
                </tr>
                {/if}
                {if $SHOW_BREAKDOWN}
                <tr>
                  <td align="right">Management Fee</td>
                  <td align="right">{$LYR.management_fee|number_format:2}</td>
                  <td align="right">{$QTR.management_fee|number_format:2}</td>
                  <td align="right">{$TRAILING.management_fee|number_format:2}</td>
                  <td align="right">{$YTD.management_fee|number_format:2}</td>
                </tr>
                <tr>
                  <td align="right">Other Expenses</td>
                  <td align="right">{$LYR.other_expenses|number_format:2}</td>
                  <td align="right">{$QTR.other_expenses|number_format:2}</td>
                  <td align="right">{$TRAILING.other_expenses|number_format:2}</td>
                  <td align="right">{$YTD.other_expenses|number_format:2}</td>
                </tr>
                {/if}
                <tr>
                  <td align="right">Income</td>
                  <td align="right">{$LYR.other_income|number_format:2}</td>
                  <td align="right">{$QTR.other_income|number_format:2}</td>
                  <td align="right">{$TRAILING.other_income|number_format:2}</td>
                  <td align="right">{$YTD.other_income|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.income|number_format:2}</td>-->
                </tr>   
                <tr>
                  <td align="right">Investment Return</td>
                  <td align="right">{$LYR.other_investment_return|number_format:2}</td>
                  <td align="right">{$QTR.other_investment_return|number_format:2}</td>
                  <td align="right">{$TRAILING.other_investment_return|number_format:2}</td>
                  <td align="right">{$YTD.other_investment_return|number_format:2}</td>
                  <!--<td align="right">{$INCEPTION.investment_return|number_format:2}</td>-->
                </tr>                
            </table>
            <table border="0" cellpadding="0" cellspacing="0" class="top_spacing">
              <tr>
                <td colspan="4" align="center" style="border-bottom:1px #333 solid; font-family:Arial, Helvetica, sans-serif;"  ><h4>Performance Summary</h4></td>
                </tr>
              <tr>
                <td align="right" bgcolor="#cccccc"><strong>Total portfolio</strong></td>
				<td align="right" bgcolor="#cccccc"><strong>Trailing <br>
                3 Mos</strong></td>
                <td align="right" bgcolor="#cccccc"><strong>Trailing<br>
                12 Mos</strong></td>
                <td align="right" bgcolor="#cccccc"><strong>Year <br>
                        to Date</strong></td>
                {if $SHOW_INCEPTION eq 1}
                    <th align="right" bgcolor="#cccccc"><strong>Inception</strong></th>
                {/if}
                </tr>
              <tr>
                <td align="right">Time Weighted Return</td>
                <td align="right" style="text-align: right">{$TWR.trailing_3}%</td>
                <td align="right" style="text-align: right"><span id="TWR_WARNING" style="color:red; display:inline; float:left;">{$DISCLAIMER_WARNING}</span>{$TWR.trailing_12}%</td>
                <td align="right" style="text-align: right"><span id="TWR_WARNING" style="color:red; display:inline;">{$DISCLAIMER_WARNING}</span>{$TWR.year_to_date}%</td>
                {if $SHOW_INCEPTION eq 1}
                    <td align="right" style="text-align: right"><span id="TWR_WARNING_INCEPTION" style="color:red; display:inline;">{$DISCLAIMER_WARNING}</span>{$TWR.inception}%</td>
                {/if}
              </tr>
              <tr>
                <td align="right">S&amp;P 500</td>
                <td align="right" style="text-align: right">{$REF.trailing_3}%</td>
                <td align="right" style="text-align: right">{$REF.trailing_12}%</td>
                <td align="right" style="text-align: right">{$REF.year_to_date}%</td>
                {if $SHOW_INCEPTION eq 1}
                    <td align="right" style="text-align: right">{$REF.inception}%</td>
                {/if}
                </tr>
              <tr>
                <td id="WARNING" colspan="5" style='color:red;'>{if $DISCLAIMER_WARNING eq "*"}* The inception is less than one year{/if}</td>
              </tr>
                <tr>
                    <td align="right" valign="top">Barcap Aggregate Bond</td>
                    <td align="right" style="text-align: right" valign="top">{$BAR.trailing_3}%</td>
                    <td align="right" style="text-align: right" valign="top">{$BAR.trailing_12}%</td>
                    <td align="right" style="text-align: right" valign="top">{$BAR.year_to_date}%</td>
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
                {foreach from=$PERFORMANCE_ACCOUNTS_USED item=account_number}
                    <tr><td style="font-size:10px;">{$account_number}</td></tr>
                {/foreach}
              </table>
        </div>
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
                guaranteed.  No representaion is made as to this review\'s accuracy or completeness. The performance data quoted represents past performance and does not guarantee future results. 
                <br />
                <br />
                The investment return and principal value of an investment will fluctuate thus an investor\'s shares, when redeemed, may be worth more or less than return data quoted herein.
            </p>
        </div>
    </div>
    </body>
</html>