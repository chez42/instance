{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/MailManager/views/LinkTo.php *}
{strip}
<div class="modal-dialog modelContainer">
	<div class = "modal-content">
	
	{assign var=HEADER_TITLE value={vtranslate('Mails Link To', $MODULE)}}
	
	{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
	<form class="form-horizontal" id="addLinkTo" method="post" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="SaveLinkTo" />
		<input type="hidden" name="idList" value="{$MSGNOS}" />
		<input type="hidden" name="folder" value="{$FOLDER}" />
		
		<div class="modal-body">
			<div class="container-fluid">
				<div class="form-group">
					<table style="width:100%">
						<tr>
							<td class="fieldLabel col-lg-3">
								<span class="pull-right input-group">
									<select style="width:140px;" class="select2 referenceModulesList select2-offscreen" tabindex="-1" title="">
										<option value="Contacts">{vtranslate('Contacts', $MODULE)}</option>
										<option value="Accounts">{vtranslate('Accounts', $MODULE)}</option>
										<option value="Leads">{vtranslate('Leads', $MODULE)}</option>
									</select>
								</label>
							</td>
							<td class="fieldValue col-lg-9">
								<div class="referencefield-wrapper" style="width: 210px;">
									<input name="popupReferenceModule" type="hidden" value="Contacts">
									<div class="input-group">
										<input name="parent_id" type="hidden" value="" class="sourceField" data-displayvalue="">
										<input id="parent_id_display" name="parent_id_display" data-fieldname="parent_id" 
										data-fieldtype="reference" type="text" 
										class="marginLeftZero autoComplete inputElement ui-autocomplete-input" 
										value="" placeholder="Type to search" autocomplete="off" data-rule-required="true">
										<a href="#" class="clearReferenceSelection hide"> x </a>
										<span class="input-group-addon relatedPopup cursorPointer" title="Select">
											<i id="MailManager_linkTo_fieldName_parent_id_select" class="fa fa-search"></i>
										</span>
									</div>
								</div>
							</td>
						</tr>
					</table>	
				</div>
			</div>
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
	</form>
	</div>
</div>
{/strip}

