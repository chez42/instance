<table width="90%" border="0" cellpadding="5" cellspacing="0" id="transactions_table">
        <thead style="font-weight:bold">
            <tr id="transactions_table_header">
                <th align="left" nowrap="nowrap">Trade Date</th>
                <th align="left" nowrap="nowrap">Acct Number</th>
                <th align="left">Activity</th>
                <th align="left" nowrap="nowrap">Activity Type</th>
                <th align="left" nowrap="nowrap"> Symbol</th>
                <th align="left">Description</th>
                <th align="left" nowrap="nowrap">Security Type</th>
                <th align="left">Detail</th>
                <th align="right">Quantity</th>
                <th align="right">Price</th>
                <th align="right">Amount</th>            
            </tr>
        </thead>

    {foreach from=$TRANSACTIONS key=k item=v}
        <tr>
            <td width="10%" align="left">{$v.trade_date}</td>
            <td align="left">{$v.account_number}</td>
            <td align="left">{$v.activity_name}</td>
            <td align="left">{$v.report_as_type_name}</td>
            <td align="left">{$v.security_symbol}</td>
            <td align="left">{$v.description}</td>
            <td align="left">{$v.code_description}</td>
            <td align="left">{$v.transaction_description}</td>
            <td align="right">{$v.quantity}</td>
            <td align="right">${$v.current_price|number_format:2}</td>
            <td align="right">${$v.value|number_format:2}</td>
        </tr>
    {/foreach}
</table>