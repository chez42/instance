<table class="lvt small" cellspacing="1" border="0" width="100%">
    <thead>
        <tr>
            <td>Account Number</td>
            <td align="right" style="text-align:right;">Total Value</td>
            <td align="right" style="text-align:right;">Market Value</td>
            <td align="right" style="text-align:right;">Cash Value</td>
        </tr>
    </thead>
    <tbody>
        {foreach from=$VALUES item=v}
        <tr>
            <td>{$v.account_number}</td>
            <td align='right'>${$v.total_value|number_format:2}</td>
            <td align='right'>${$v.market_value|number_format:2}</td>
            <td align='right'>${$v.cash_value|number_format:2}</td>
        </tr>
        {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Totals:</strong></td>
            <td align='right'>${$TOTALS.total_value|number_format:2}</td>
            <td align='right'>${$TOTALS.market_value|number_format:2}</td>
            <td align='right'>${$TOTALS.cash_value|number_format:2}</td>            
        </tr>
    </tfoot>
</table>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}