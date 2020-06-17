{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{strip}
	<div class="addMailBoxBlock">
		{if $IMAP_ERROR || $CONNECTION_ERROR}
			<div class="block">
				<strong>
					{if $IMAP_ERROR}
						{$IMAP_ERROR}
					{else if $CONNECTION_ERROR}
						{vtranslate('LBL_CONNECTION_ERROR', $QUALIFIED_MODULE_NAME)}
					{/if}
				</strong>
			</div>
			<br>
		{/if}
	
		<div class="row padding-bottom1per">
			<div class="col-md-12">
				<div id="mailConverterDragIcon"><i class="icon-info-sign"></i>&nbsp;&nbsp;{vtranslate('TO_CHANGE_THE_FOLDER_SELECTION_DESELECT_ANY_OF_THE_SELECTED_FOLDERS', $QUALIFIED_MODULE_NAME)}</div>
			</div>
		</div>
		<br>
		<br>
		<div class="block row">
			<div class="col-md-12">
				<div class="addMailBoxStep row" style="margin: 10px;">
					<select name= "folders[]" data-rule-required="true" class="inputElement select2" multiple style="width: 80%;">
						<option value="">Select an option</option>
						{foreach key=FOLDER item=SELECTED from=$FOLDERS}
							<option value="{$FOLDER}" {if $SELECTED eq 'checked'} selected {/if}>{$FOLDER}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
{/strip}