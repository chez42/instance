{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="quickCreateMenuDiv padding20">
		<form class="QuickCreateMenuForm">
		
			<input type="hidden" name="parent" value="Settings" />
			<input type="hidden" name="module" value="{$MODULE}" />
			<input type="hidden" name="action" value="QuickCreateMenu" />

			<br>
			<div class="row">
				<div class="quickCreateMenuContainer  col-lg-12">
					<div class="fieldsBlock">
						<div><b>{vtranslate('Quick Create Menu Sequence', $QUALIFIED_MODULE)}</b></div><br>
						<select class="col-lg-10 select" id="moduleList" multiple name="moduleIdsList[]" data-placeholder="{vtranslate('Select Modules', $QUALIFIED_MODULE)}" data-rule-required="true" >
							{foreach key=MODULE_ID item=MODULE_DATA from=$MODULELIST}
								<option {if $MODULE_DATA['seq']}selected=""{/if} value={$MODULE_ID} data-id="{$MODULE_ID}">
									{vtranslate($MODULE_DATA['name'], $SOURCE_MODULE)}
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