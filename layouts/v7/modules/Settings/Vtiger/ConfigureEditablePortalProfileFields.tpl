{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Documents/views/AddFolder.php *}
{strip}
<div class="editViewPageDiv editViewContainer configurePortalFieldBlockDiv" id="globalportal">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="clearfix">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				<h3 style="margin-top: 0px;">{vtranslate('Configure Portal Fields', $QUALIFIED_MODULE)}</h3>
			</div>
			
		</div>
		
		<div class="modal-body">
			<div class="container-fluid">
				
				<form class="configurePortalFields">
					<input type="hidden" name="_source" value="{$SOURCE}" />
					<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" id="sourceModule" />
					<input type="hidden" name="parent" value="Settings" />
					<input type="hidden" name="module" value="Vtiger" />
					<input type="hidden" name="action" value="SaveEditablePortalProfileFields" />
		
					<br>
					<div class="row">
						<div class="configurePortalFieldContainer  col-lg-12">
							<div class="fieldsBlock">
								<div><b>{vtranslate('Select Fields For Portal', $QUALIFIED_MODULE)}</b></div><br>
								<select class="col-lg-10 select" id="fieldsList" multiple name="fieldIdsList[]" data-placeholder="{vtranslate('Select Fields', $QUALIFIED_MODULE)}" data-rule-required="true" style="width:100%;" >
									{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$FIELDS name=blockIterator}
										<optgroup label="{vtranslate($BLOCK_LABEL,$SOURCE_MODULE)}">
											{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
												{if $FIELD_MODEL->getName() neq 'portal' && $FIELD_MODEL->getName() neq 'portal_password' && $FIELD_MODEL->getName() neq 'assigned_user_id' &&
												$FIELD_MODEL->getName() neq 'support_end_date' && $FIELD_MODEL->getName() neq 'support_start_date' && $FIELD_MODEL->getFieldDataType() neq 'reference'}	
													<option {if in_array($FIELD_MODEL->getName(), $PORTAL_FIELDS)}selected=""{/if} value="{$FIELD_MODEL->getName()}" data-id="{$FIELD_MODEL->getId()}">
														{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
													</option>
												{/if}
											{/foreach}
										</optgroup>
									{/foreach}
								</select>
							</div>
							
							<div class="modal-overlay-footer clearfix formFooter hide" style="text-align: center;">
								<button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
								<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{/strip}

