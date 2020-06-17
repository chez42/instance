<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>Balances</h2>
</div>

<div id="balances_wrapper">
    <div id="balances_table_holder">
        <table id="balances_table">
            <thead>
            <tr>
                <td class="report_heading">Account Number</td>
                <td class="report_heading tright">Total Value</td>
                <td class="report_heading tright">Market Value</td>
                <td class="report_heading tright">Cash Value</td>
            </tr>
            </thead>
            <tbody>
            {foreach from=$ACCOUNTINFO key=k item=v}
                <tr class="primary">
                    <td class="report_heading">{$v.account_number}</td>
                    <td class="right">${$v.total|number_format:2:".":","}</td>
                    <td class="right">${$v.securities|number_format:2:".":","}</td>
                    <td class="right">${$v.cash|number_format:2:".":","}</td>
                </tr>
            {/foreach}
            <tr>
                <td class="report_heading upper_line">Total:</td>
                <td class="right strong upper_line">${$ACCOUNTINFOTOTAL.total|number_format:2:".":","}</td>
                <td class="right strong upper_line">${$ACCOUNTINFOTOTAL.securities_total|number_format:2:".":","}</td>
                <td class="right strong upper_line">${$ACCOUNTINFOTOTAL.cash_total|number_format:2:".":","}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>