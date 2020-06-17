
<div class="printed_transactions_wrapper">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <thead style="font-weight:bold">
            <tr style="font-weight:bold">
                <th align="left" valign="bottom">Trade Date</th>
                <th align="left" valign="bottom">Account Number</th>
                <th align="left" valign="bottom">Activity</th>
                <th align="left" valign="bottom">Security Symbol</th>
                <th align="left" valign="bottom">Description</th>
                <th align="left" valign="bottom">Security Type</th>
                <th align="left" valign="bottom">Detail</th>
                <th align="right" valign="bottom">Quantity</th>
                <th align="right" valign="bottom">Price</th>
                <th align="right" valign="bottom">Amount</th>            
            </tr>
        </thead>

    {foreach from=$TRANSACTIONS key=k item=v}
        <tr>
            <td width="10%" align="left">{$v.trade_date}</td>
            <td align="left">{$v.account_number}</td>
            <td align="left">{$v.activity}</td>
            <td align="left">{$v.security_symbol}</td>
            <td align="left">{$v.description}</td>
            <td align="left">{$v.security_type}</td>
            <td align="left">{$v.detail}</td>
            <td align="right">{$v.quantity}</td>
            <td align="right">${$v.price|number_format:2}</td>
            <td align="right">${$v.amount|number_format:2}</td>
        </tr>
    {/foreach}
</table>
</div>