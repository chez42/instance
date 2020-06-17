{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Documents/views/ExportZip.php *}
{strip}
<div class="modal-dialog modelContainer">
	<div class = "modal-content">
	{assign var=HEADER_TITLE value={vtranslate('Export File Name', $MODULE)}}
	
	{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
	<form class="form-horizontal" id="exportFiles" method="post" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="ExportZip" />
		<input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)} />
		<input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)} />
		<input type="hidden" name="viewname" value="{$VIEWNAME}" />
        <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
        <input type="hidden" name="operator" value="{$OPERATOR}" />
        <input type="hidden" name="folder_id" value="{$FOLDER_ID}" />
        <input type="hidden" name="folder_value" value="{$FOLDER_VALUE}" />
        <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
		
		<div class="modal-body">
			<div class="container-fluid">
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						<span class="redColor">*</span>
						{vtranslate('File Name', $MODULE)}
					</label>
					<div class="controls col-sm-7">
						<div class="input-group inputElement" style="margin-bottom: 3px">
							<input class="form-control" id="fileName" data-rule-required="true" name="filename" type="text" value=""/>
							<span class="input-group-addon">.zip</span>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
	</form>
	</div>
</div>
{/strip}

