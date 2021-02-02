<h4 class="fieldBlockHeader">{vtranslate('Captial Flows', $MODULE)}</h4>
<hr>
<table class="table table-bordered" id="LineItemTab" width="100%">
	<thead>
		<tr>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;">&nbsp;</td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Trade Date',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Day Diff',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Total Days',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Transaction Fee',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Transaction Type',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Total Amount',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Transaction Amount',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Total Adjustment',$MODULE)}</b></td>
		</tr>
	</thead>
	<tbody>
		<tr id="row0" class="hide lineItemCloneCopy">
			{include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=0 data=[]}
		</tr>
		{foreach key=row_no item=data from=$RELATED_FLOWS}
			<tr id="row{$row_no}" class="lineItemRow" >
				{include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=$row_no data=$data}
			</tr>
		{/foreach}
		{if count($RELATED_FLOWS) eq 0}
			<tr id="row1" class="lineItemRow">
				{include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=1 data=[]}
			</tr>
		{/if}
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">
				<button class="btn btn-success" type="button" id="btnCapitalFlows"><i class="icon-plus"></i> &nbsp;<strong>Add</strong></button>
			</td>
		</tr>
	</tfoot>
</table>
<input type="hidden" name="totalCaptialFlowCount" id="totalCaptialFlowCount" value="{if $RELATED_FLOWS} count($RELATED_FLOWS){/if}" />
