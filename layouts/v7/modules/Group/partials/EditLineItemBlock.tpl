<h4 class="fieldBlockHeader">{vtranslate('Group Billing Items', $MODULE)}</h4>
<hr>
<table class="table table-bordered" id="LineItemTab" width="100%">
	<thead>
		<tr>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;">&nbsp;</td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Portfolio',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Billing Specification',$MODULE)}</b></td>
			<td style="background-color: #f5f5f5; border-top: 1px solid #ddd;"><b>{vtranslate('Active',$MODULE)}</b></td>
		</tr>
	</thead>
	<tbody>
		<tr id="row0" class="hide lineItemCloneCopy">
			{include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=0 data=[]}
		</tr>
		{foreach key=row_no item=data from=$RELATED_ITEMS}
			<tr id="row{$row_no}" class="lineItemRow" >
				{include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=$row_no data=$data}
			</tr>
		{/foreach}
		{if count($RELATED_ITEMS) eq 0}
			<tr id="row1" class="lineItemRow">
				{include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=1 data=[]}
			</tr>
		{/if}
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">
				<button class="btn btn-success" type="button" id="btnAddItem"><i class="icon-plus"></i> &nbsp;<strong>Add</strong></button>
			</td>
		</tr>
	</tfoot>
</table>
<input type="hidden" name="totalItemCount" id="totalItemCount" value="{if $RELATED_ITEMS} count($RELATED_ITEMS){/if}" />
