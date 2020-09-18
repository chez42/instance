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
<div class="modal-dialog modelContainer modal-lg">
	<div class = "modal-content">
	{assign var=HEADER_TITLE value={vtranslate('OwnCloud Credentials', $MODULE)}}
	
	{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
	<form class="form-horizontal" id="updatedetails" method="post" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="SaveAuthSettings" />
		<input type="hidden" name="record" value="{$RECORD}" />
		<input type="hidden" name="mode" value="OwncloudCredentials" />

		<div class="modal-body">
			<div class="container-fluid">
				<table class="table editview-table no-border">
					<tbody>
						
						<tr>
							<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('UserName', $QUALIFIED_MODULE)}</label>&nbsp;<span class="redColor">*</span></td>
							<td class="{$WIDTHTYPE} fieldValue"><div class=" col-lg-6 col-md-6 col-sm-12">
							<input type="text" class="inputElement" name="username" data-rule-required="true" value="{$USERNAME}" ></div></td>
						</tr>
						
						<tr>
							<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Password', $QUALIFIED_MODULE)}</label>&nbsp;<span class="redColor">*</span></td>
							<td>
								<div class=" col-lg-6 col-md-6 col-sm-12">
									<input type="text" class="inputElement" name="password" data-rule-required="true" value="{$PASSWORD}" >
								</div>
							</td>
						</tr>
						
					</tbody>
				</table>
			</div>
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
	</form>
	</div>
</div>
{/strip}

