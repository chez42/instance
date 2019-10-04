{*<!--
/* ********************************************************************************
* The content of this file is subject to the VTEMailConverter("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{literal}
<style type="text/css">
	.vte-email-converter-create-action input[readonly],
	.vte-email-converter-create-action input[disabled],
	.vte-email-converter-create-action select[readonly],
	.vte-email-converter-create-action select[disabled]{
		background-color: #f5f5f5;
		border: #ddd;
		cursor: not-allowed;
	}
</style>
{/literal}
{strip}
	<div class="modal-dialog modal-lg vte-email-converter-create-action" style="padding: 10px;">
		<div class="modal-content">
			<form name="vte-email-converter-configure-action-form" action="" method="post" >
				<input type='hidden' name="module" value="VTEMailConverter" />
				<input type='hidden' name="action" value="SaveAction" />
				<div class="modal-header">
					<div class="clearfix">
						<div class="pull-right ">
							<button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
						</div>
						<h4 class="pull-left">{vtranslate('LBL_CREATE_NEW_ACTION', $MODULE)}</h4>
					</div>
				</div>
				<div class="modal-body mCustomScrollbar">
					<table class="table table-bordered blockContainer showInlineTable equalSplit">
						<tbody>
						<tr>
							<td class="fieldLabel medium">{vtranslate('ACTION_NAME', $MODULE)}</td>
							<td class="fieldValue medium">
								<input name="action_name" type="text" value="" class="input-xlarge inputElement" />
							</td>
						</tr>
						<tr>
							<td class="fieldLabel medium">{vtranslate('LBL_ACTION_TYPE', $MODULE)}</td>
							<td class="fieldValue medium">
								<select class="select2 inputElement" id="action_type">
									<option value="CREATE">{vtranslate('LBL_ACTION_TYPE_CREATE', $MODULE)}</option>
									<option value="UPDATE">{vtranslate('LBL_ACTION_TYPE_UPDATE', $MODULE)}</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="fieldLabel medium">{vtranslate('LBL_MODULE', $MODULE)}</td>
							<td class="fieldValue medium">
								<select class="select2 inputElement" name="modulename1">
									<option value="">{vtranslate('LBL_SELECT_MODULE', $MODULE)}</option>
                                    {foreach item=MODULE_DATA from=$ALL_MODULES}
										<option value="{$MODULE_DATA.name}">{vtranslate($MODULE_DATA.tablabel, $MODULE)}</option>
                                    {/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td class="fieldLabel medium">{vtranslate('ACTION_KEY', $MODULE)}</td>
							<td class="fieldValue medium">
								<input name="action_key" type="text" readonly class="input-xlarge inputElement" />
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<div class="vte-email-converter-btn" style="display: block; text-align: center;">
						<button class="btn btn-success vte-email-converter-create-action-btn" style="margin-right: 5px;">{vtranslate('LBL_SAVE', $MODULE)}</button>
						<button class="btn btn-default vte-email-converter-cancel-action" style="margin-right: 5px;">{vtranslate('LBL_CANCEL', $MODULE)}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}
