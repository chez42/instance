<div id="investmentreturns" style="width:560px; margin-top:5px; margin-left:25px;">
<h2>Performance Review</h2>
  <table width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td colspan="2" align="center" style="border-bottom:1px #333 solid;" valign="top">
      <h4>
            {$PERFORMANCE.start_date|replace:'00:00:00':''} - 
            {$PERFORMANCE.end_date|replace:'00:00:00':''}</h4>
            </td>
    </tr>
    <tr style="background-color:#BBB;">
        <td>Beginning Value</td>
        <td style="font-weight: bold;">${$PERFORMANCE.beginning_value|number_format:2}</td>
    </tr>
    <tr style="background-color:#CCC;">
        <td style="font-weight: bold;">Net Contributions</td>
        <td>${$PERFORMANCE.net_contributions|number_format:2}</td>
    </tr>
    <tr style="background-color:#CCC;">
        <td style="font-weight: bold;">Capital Appreciation</td>
        <td>${$PERFORMANCE.capital_appreciation|number_format:2}</td>
    </tr>
    <tr style="background-color:#CCC;">
        <td style="font-weight: bold;">Income</td>
        <td>${$PERFORMANCE.income|number_format:2}</td>
    </tr>
    <tr style="background-color:#CCC;">
        <td style="font-weight: bold;">Expenses</td>
        <td>${$PERFORMANCE.expenses|number_format:2}</td>
    </tr>
    {if $SHOW_EXPENSES eq 1}
        <tr style="background-color:#CCC;">
            <td>&nbsp;&nbsp;Management Fees ({abs($PERFORMANCE.management_total)|number_format:2})</td>
            <td>&nbsp;</td>
        </tr>
        <tr style="background-color:#CCC;">
            <td>&nbsp;&nbsp;Other Expenses ({abs($PERFORMANCE.other_expenses)|number_format:2})</td>
            <td>&nbsp;</td>
        </tr>
    {/if}
    <tr style="background-color:#BBB;">
        <td style="font-weight: bold;">Ending Value</td>
        <td>${$PERFORMANCE.ending_value|number_format:2}</td>
    </tr>
    <tr style="background-color:#999;">
        <td style="font-weight: bold;">Investment Return</td>
        <td>${$PERFORMANCE.investment_return|number_format:2}</td>
    </tr>
</table>
</div><!--investmentreturns-->
<div style="clear:both; margin-top:20px;"></div>
<div  style="width:560px; margin-top:5px; margin-left:25px;">
<table width="100%">
  <tr>
    <th align="left">Total Portfolio</th>
    <th align="right">Trailing 3 Mos</th>
    <th align="right">Trailing 12 Mos</th>
    <th align="right">Year to Date</th>
    {if $SHOW_INCEPTION eq 1}
    <th align="right">Inception</th>
    {/if}
  </tr>
  
  <tr>      
    <td align="left">Time Weighted Return (net)</td>
    <td align="right" style="text-align: right"><div id="TWR_QTR_TYPE"></div><div id="TWR_QTR">{$TWR.trailing_3}%</div></td>
    <td align="right" style="text-align: right"><div id="TWR_TRAILING_TYPE"></div><div id="TWR_TRAILING">{$TWR.trailing_12}%</div></td>
    <td align="right" style="text-align: right"><div id="TWR_YTD_TYPE"></div><div id="TWR_YTD">{$TWR.year_to_date}%</div></td>
    {if $SHOW_INCEPTION eq 1}
    <td align="right" style="text-align: right"><div id="TWR_INCEPTION_TYPE"></div><div id="TWR_INCEPTION">{$TWR.inception}%</div></td>
    {/if}
  </tr>
  <tr>
    <td align="left">S&amp;P 500</td>
    <td align="right" style="text-align: right">{$REF.trailing_3}%</td>
    <td align="right" style="text-align: right"><div id="TWR_WARNING" style="color:red; display:inline;"></div>{$REF.trailing_12}%</td>
    <td align="right" style="text-align: right">{$REF.year_to_date}%</td>
    {if $SHOW_INCEPTION eq 1}
    <td align="right" style="text-align: right">{$REF.inception}%</td>
    {/if}
  </tr>
  <tr>
      <td id="WARNING" colspan="5" style='color:red;'></td>
  </tr>
  <tr>
      <td align="left" valign="top">Barcap Aggregate Bond</td>
      <td align="right" style="text-align: right" valign="top">{$BAR.trailing_3}%</td>
      <td align="right" style="text-align: right" valign="top">{$BAR.trailing_12}%</td>
      <td align="right" style="text-align: right" valign="top">{$BAR.year_to_date}%</td>
      {if $SHOW_INCEPTION eq 1}
      <td align="right" style="text-align: right" valign="top">{$BAR.inception}%</td>
      {/if}
  </tr>
  {if $SHOW_GOAL eq 1}
  <tr>
      <td align="left" valign="top">Goal %</td>
      <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
      <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
      <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
      {if $SHOW_INCEPTION eq 1}
      <td align="right" style="text-align: right" valign="top">{$PERFORMANCE.goal}%</td>
      {/if}
  </tr>
  {/if}
</table>
</div>
<div style="clear:both; margin-top:20px;"></div>
<div  style="width:560px; margin-top:5px; margin-left:25px;">
    <h2>Accounts used in Performance Report</h2>
    {foreach from=$PERFORMANCE_ACCOUNTS_USED item=v}
        <p style="font-size:10px;">{$v.account_number} {$v.account_nickname}</p>
    {/foreach}
</div>