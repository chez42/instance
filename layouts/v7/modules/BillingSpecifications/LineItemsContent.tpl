
{assign var="hdnScheduleId" value="scheduleid"|cat:$row_no}
{assign var="from" value="from"|cat:$row_no}
{assign var="to" value="to"|cat:$row_no}
{assign var="type" value="type"|cat:$row_no}
{assign var="value" value="value"|cat:$row_no}

{assign var=displayId value=$data.$hdnScheduleId} 
   
<td class="" >
	<i class="fa fa-trash deleteRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"style="display:none;"></i>
	<input type="hidden" class="rowNumber" value="{$row_no}" />
</td>
<td class="schedule">
	<input name="{$from}" type="text" value="{$data.$from}" class="inputElement" id="{$from}" />
</td>

<td>
	<input id="{$to}" type="text" class="inputElement" name="{$to}" value="{$data.$to}" />	
</td>

<td class="medium">
	<select id="{$type}" name="{$type}" class="{if $row_no}select2{/if} inputElement scheduletype" />
		<option value="">Select an option</option>
		<option value="Rate"{if $data.$type eq 'rate'} selected{/if}>Rate</option>
		<option value="Amount" {if $data.$type  eq 'amount'} selected{/if}>Amount</option>
	</select>
</td>

<td class="">
	<input id="{$value}" type="text" class="inputElement" name="{$value}" value="{$data.$value}" />	
</td>

