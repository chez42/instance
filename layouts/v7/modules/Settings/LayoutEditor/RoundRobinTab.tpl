{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="roundRobinHandlingDiv padding20">
		<form class="RoundRobinHandlingForm">
			<input type="hidden" name="_source" value="{$SOURCE}" />
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" id="sourceModule" />
			<input type="hidden" name="parent" value="Settings" />
			<input type="hidden" name="module" value="LayoutEditor" />
			<input type="hidden" name="action" value="Field" />
			<input type="hidden" name="mode" value="updateForRoundRobinField" />

			<br>
			<div class="row">
				<div class="roundRobinHandlingContainer show col-lg-12">
					<div class="fieldsBlock">
						<div><b>{vtranslate('Select Roles', $QUALIFIED_MODULE)}</b></div><br>
						<select class="col-lg-10 select" id="rolesList" multiple name="roleIdsList[]" data-placeholder="{vtranslate('Select Roles', $QUALIFIED_MODULE)}" data-rule-required="true" >
							{foreach key=NAME item=ROLE_ID from=$ROLES_FIELD}
								<option value="{$ROLE_ID}" {if in_array($ROLE_ID, $SELECTED_ROLES)} selected {/if}>
									{vtranslate($NAME, $SOURCE_MODULE)}
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