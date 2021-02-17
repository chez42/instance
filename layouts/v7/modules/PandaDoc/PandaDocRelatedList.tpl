{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
	{assign var=RELATED_MODULE_NAME value=$RELATED_MODULE->get('name')}
	{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE LISTVIEW_HEADERS=$RELATED_HEADERS}
	<div class="relatedContainer">
		{assign var=IS_RELATION_FIELD_ACTIVE value="{if $RELATION_FIELD}{$RELATION_FIELD->isActiveField()}{else}false{/if}"}
		
		<input type="hidden" name="relatedModuleName" class="relatedModuleName" value="{$RELATED_MODULE_NAME}" />
		
		<input type='hidden' value="{$TAB_LABEL}" id='tab_label' name='tab_label'>
		<input type='hidden' value="{$PARENT_ID}" id='parent_id' name='parent_id'>
		<input type='hidden' value="{$CVID}" name='cvid'>

		{include file="partials/RelatedListHeader.tpl"|vtemplate_path:$RELATED_MODULE_NAME}
		{if $MODULE eq 'Products' && $RELATED_MODULE_NAME eq 'Products' && $TAB_LABEL === 'Product Bundles' && $RELATED_LIST_LINKS}
			<div data-module="{$MODULE}" style = "margin-left:20px">
				{assign var=IS_VIEWABLE value=$PARENT_RECORD->isBundleViewable()}
				<input type="hidden" class="isShowBundles" value="{$IS_VIEWABLE}">
				<label class="showBundlesInInventory checkbox"><input type="checkbox" {if $IS_VIEWABLE}checked{/if} value="{$IS_VIEWABLE}">&nbsp;&nbsp;{vtranslate('LBL_SHOW_BUNDLE_IN_INVENTORY', $MODULE)}</label>
			</div>
		{/if}

		<div class="relatedContents col-lg-12 col-md-12 col-sm-12 table-container">
			<div class="bottomscroll-div">
				<table id="listview-table" class="table listview-table">
					<thead>
						<tr class="listViewHeaders text-center">
							<th style="min-width:100px">
							</th>
							<th class="nowrap">
								Name
							</th>
							<th class="nowrap">
								Sender Name
							</th>
							<th class="nowrap">
								Recipient
							</th>
							<th class="nowrap">
								Status
							</th>
							<th class="nowrap">
								Created Date
							</th>
							<th class="nowrap">
								Modified Date
							</th>
						</tr>
					</thead>
					{foreach item=RELATED_RECORD from=$RELATED_RECORDS}
						<tr class="listViewEntries" data-id='{$RELATED_RECORD['doc_id']}' >
							<td class="related-list-actions">
								<a href="index.php?module=PandaDoc&action=DownloadPandadocFile&record={$RELATED_RECORD['doc_id']}&name={$RELATED_RECORD['name']}" 
								 title="Save on desktop"><i class="fa fa-download"> </i></a>
							</td>
							<td class="relatedListEntryValues" title="{$RELATED_RECORD['name']}"  nowrap>
								<span class="value textOverflowEllipsis">
									{$RELATED_RECORD['name']}
								</span>
							</td>
							<td class="relatedListEntryValues" title="{$RELATED_RECORD['recipient']}"  nowrap>
								<span class="value textOverflowEllipsis">
									{$RELATED_RECORD['recipient']}
								</span>
							</td>
							<td class="relatedListEntryValues" title="{str_replace('document.','', $RELATED_RECORD['status'])}"  nowrap>
								<span class="value textOverflowEllipsis">
									{strtoupper(str_replace('document.','', $RELATED_RECORD['status']))}
								</span>
							</td><td class="relatedListEntryValues" title="{$RELATED_RECORD['date_created']}"  nowrap>
								<span class="value textOverflowEllipsis">
									{$RELATED_RECORD['date_created']}
								</span>
							</td>
							
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<script type="text/javascript">
			var related_uimeta = (function () {
				var fieldInfo = {$RELATED_FIELDS_INFO};
				return {
					field: {
						get: function (name, property) {
							if (name && property === undefined) {
								return fieldInfo[name];
							}
							if (name && property) {
								return fieldInfo[name][property]
							}
						},
						isMandatory: function (name) {
							if (fieldInfo[name]) {
								return fieldInfo[name].mandatory;
							}
							return false;
						},
						getType: function (name) {
							if (fieldInfo[name]) {
								return fieldInfo[name].type
							}
							return false;
						}
					}
				};
			})();
		</script>
	</div>
{/strip}