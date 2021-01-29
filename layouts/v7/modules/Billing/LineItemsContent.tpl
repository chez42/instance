
{assign var="hdnCapitalFlowsId" value="capitalflowsid"|cat:$row_no}
{assign var="trade_date" value="trade_date"|cat:$row_no}
{assign var="diff_days" value="diff_days"|cat:$row_no}
{assign var="totalamount" value="totalamount"|cat:$row_no}
{assign var="totaldays" value="totaldays"|cat:$row_no}
{assign var="transactionamount" value="transactionamount"|cat:$row_no}
{assign var="transactiontype" value="transactiontype"|cat:$row_no}
{assign var="trans_fee" value="trans_fee"|cat:$row_no}
{assign var="totaladjustment" value="totaladjustment"|cat:$row_no}

{assign var=displayId value=$data.$hdnCapitalFlowsId} 
   
<td class="" >
	<i class="fa fa-trash deleteRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"style="display:none;"></i>
	<input type="hidden" class="rowNumber" value="{$row_no}" />
</td>
<td class="capitalflow">
	<input name="{$trade_date}" type="text" value="{$data.$trade_date}" class="inputElement" id="{$trade_date}" />
</td>

<td>
	<input id="{$diff_days}" type="text" class="inputElement" name="{$diff_days}" value="{$data.$diff_days}" />	
</td>

<td class="">
	<input id="{$totaldays}" type="text" class="inputElement" name="{$totaldays}" value="{$data.$totaldays}" />	
</td>

<td class="medium">
	<input id="{$trans_fee}" type="text" class="inputElement" name="{$trans_fee}" value="{$data.$trans_fee}" />	
</td>

<td>
	<input id="{$transactiontype}" type="text" class="inputElement" name="{$transactiontype}" value="{$data.$transactiontype}" />	
</td>

<td class="medium">
	<input id="{$totalamount}" type="text" class="inputElement" name="{$totalamount}" value="{$data.$totalamount}" />	
</td>

<td class="">
	<input name="{$transactionamount}" type="text" value="{$data.$transactionamount}" class="inputElement" id="{$transactionamount}" />
</td>

<td class="">
	<input id="{$totaladjustment}" type="text" class="inputElement" name="{$totaladjustment}" value="{$data.$totaladjustment}" />	
</td>

