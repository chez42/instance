{*
<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Month End Values | Period 12 Months</h2>
</div>
<div id="HoldingsChartsWrapper">
    <table id="HoldingsCharts">
        <tr>
            <td>End of Month Values</td>
        </tr>
        <tr>
            <td>{if !empty($MONTHLY_VALUES)}
                    <img src="{$GRAPH_FILE}" style="width:50%;" />
                {/if}</td>
        </tr>
        <tr>
            <td>This chart depicts the total month end values of the accounts listed in the "Accounts Overview" section of this report.
                The month end values include the change in values of investments owned at the statement period, income, dividends, interest
                and any transfers or withdrawals from the included account(s).</td>
        </tr>
    </table>
</div>
<div style="page-break-after: always" />
*}

<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Month End Values | Period 12 Months</h2>
</div>
<div id="HoldingsChartsWrapper">
    <table id="HoldingsCharts">
        <tr>
            <td>Month End Values | Period 12 Months</td>
            <td>Asset Mix Prior 12 Months</td>
        </tr>
        <tr>
            <td>{if !empty($MONTHLY_VALUES)}
                    <img src="{$GRAPH_FILE}" style="width:50%;" />
                {/if}</td>
            <td>{if $AUM_IMAGE eq 1}
                    <img src="{$AUM_FILE}" style="width:50%;" />
                {/if}</td>
        </tr>
        <tr>
            <td>This chart depicts the total month end values of the accounts listed in the "Accounts Overview" section of this report.
                The month end values include the change in values of investments owned at the statement period, income, dividends, interest
                and any transfers or withdrawals from the included account(s).</td>
            <td>The month-end asset mix chart illustrates the change in the portfolios exposure to different asset classes over the course
                of the previous 12 months.  These changes may be caused by change in the values of the individual investments or trading
                in the portfolio.</td>
        </tr>
    </table>
</div>
{*
<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Asset Mix Prior 12 Months</h2>
</div>
<div id="HoldingsChartsWrapper">
    <table id="HoldingsCharts">
        <tr>
            <td>End of Month Values</td>
        </tr>
        <tr>
            <td>{if $AUM_IMAGE eq 1}
                    <img src="{$AUM_FILE}" style="width:50%;" />
                {/if}</td>
        </tr>
        <tr>
            <td>The month-end asset mix chart illustrates the change in the portfolios exposure to different asset classes over the course
                of the previous 12 months.  These changes may be caused by change in the values of the individual investments or trading
                in the portfolio.</td>
        </tr>
    </table>
</div>
*}
<div style="page-break-after: always" />