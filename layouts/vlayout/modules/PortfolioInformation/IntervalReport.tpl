{$IMAGE}

<table style="border:1px solid black; width:100%;">
    <tr>
        <td style="width:50%">
        <table style="width:100%; border:1px solid black;">
            <tr>
                <td>Beginning value as of {$SUMMARIZED.begin_date}</td>
                <td>${$SUMMARIZED.begin_value|number_format:2:".":","}</td>
            </tr>
            <tr>
                <td>Deposits / Withdrawals</td>
                <td>${$SUMMARIZED.flow_value|number_format:2:".":","}</td>
            </tr>
            <tr>
                <td>Investment Return Value</td>
                <td>${$SUMMARIZED.investment_return_value|number_format:2:".":","}</td>
            </tr>
            <tr>
                <td>Ending value as of {$SUMMARIZED.end_date}</td>
                <td>${$SUMMARIZED.end_value|number_format:2:".":","}</td>
            </tr>
        </table>
        </td>
        <td align="center">
            <div style="display:block; border-radius: 25px; padding:5px; border: 2px solid black; text-align:center; font-size: 22px; background-color:{if CALCULATED_RETURN ge 0}#72d15d{else}#d1655d{/if}">
                <p style="padding:5px;">CALCULATED RETURN: {$CALCULATED_RETURN}%</p>
            </div>
        </td>
    </tr>
</table>

{if $INTERVALS|@count > 0}
    <table id="IntervalTable" style="border:1px solid black; width:100%;">
        <thead>
        <tr style="background-color: #c2c2c2">
            <td style="text-align:center; padding:2px; width:10%">Account Number</td>
            <td style="text-align:center; padding:2px; width:10%">Begin Date</td>
            <td style="text-align:center; padding:2px; width:10%">End Date</td>
            <td style="text-align:right; padding:2px; width:10%">Begin Value</td>
            <td style="text-align:right; padding:2px; width:20%">Deposits / Withdrawals</td>
            <td style="text-align:right; padding:2px; width:20%">Investment Return</td>
            <td style="text-align:right; padding:2px; width:10%">End Value</td>
            <td style="text-align:right; padding:2px;">Period Return %</td>
        </tr>
        </thead>
        <tbody>
        {foreach item=v from=$INTERVALS}
            <tr>
                <td style="padding:2px;">{$v.account_number}</td>
                <td style="padding:2px;">{$v.begin_date}</td>
                <td style="padding:2px;">{$v.end_date}</td>
                <td style="text-align:right; padding:2px;">${$v.begin_value|number_format:2:".":","}</td>
                <td style="text-align:right; padding:2px;">${$v.net_flow|number_format:2:".":","}</td>
                <td style="text-align:right; padding:2px;">${$v.investment_return|number_format:2:".":","}</td>
                <td style="text-align:right; padding:2px;">${$v.end_value|number_format:2:".":","}</td>
                <td style="text-align:right; padding:2px;">{$v.period_return|number_format:2:".":","}%</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <h2>Sorry, there are no Intervals available currently</h2>
{/if}