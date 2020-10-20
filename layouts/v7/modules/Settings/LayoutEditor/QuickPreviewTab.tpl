{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="quickPreviewTabHandlingDiv padding20">
		<form class="QuickPreviewTabHandlingForm">
			<input type="hidden" name="_source" value="{$SOURCE}" />
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" id="sourceModule" />
			<input type="hidden" name="parent" value="Settings" />
			<input type="hidden" name="module" value="LayoutEditor" />
			<input type="hidden" name="action" value="Field" />
			<input type="hidden" name="mode" value="updateFieldForQuickPreview" />

			<br>
			<div class="row">
				<div class="quickPreviewTabHandlingContainer  col-lg-12">
					<div class="fieldsBlock">
						<div><b>{vtranslate('Select the fields for quick Preview', $QUALIFIED_MODULE)}</b></div><br>
						<select class="col-lg-10 select" id="fieldsList" multiple name="fieldIdsList[]" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $QUALIFIED_MODULE)}" data-rule-required="true" >
							{foreach key=FIELD_ID item=FIELD_DATA from=$FIELDS}
								<option {if $FIELD_DATA['field_seq']}selected=""{/if} value={$FIELD_ID} data-id="{$FIELD_ID}">
									{vtranslate($FIELD_DATA['label'], $SOURCE_MODULE)}
								</option>
							{/foreach}
						</select>
					</div>
					
					<div class="formFooter hide" style="margin-top:100px">
						<button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
						<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/strip}