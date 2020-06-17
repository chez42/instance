<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Asset Mix Prior 12 Months</h2>
</div>
<div id="HoldingsChartsWrapper">
    <table id="HoldingsCharts">
        <tr>
            {*            <td>Combined Account Values Separated by Asset Class</td> *}
            <td>End of Month Values</td>
        </tr>
        <tr>
            {*            <td style="border-right:1px dotted black; padding-right:2px;">{if !empty($PIE_FILE)}
                                <img src="{$PIE_FILE}" style="width:75%;"/>
                        {/if}</td>*}
            <td>{if $AUM_IMAGE eq 1}
                    <img src="{$AUM_FILE}" style="width:75%;" />
                {/if}</td>
        </tr>
        <tr>
            <td>The month-end asset mix chart illustrates the change in the portfolios exposure to different asset classes over the course
                of the previous 12 months.  These changes may be caused by change in the values of the individual investments or trading
                in the portfolio.</td>
        </tr>
    </table>
</div>
<div style="page-break-after: always" />