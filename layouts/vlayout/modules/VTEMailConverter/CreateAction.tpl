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

</style>
{/literal}
{strip}
	<div class="vte-email-converter-create-action" style="padding: 10px; width: 375px;">
		<form name="vte-email-converter-create-action-form" action="" method="post" >
			<input type='hidden' name="module" value="VTEMailConverter" />
			<input type='hidden' name="action" value="SaveAction" />
			<div class="vte-email-converter-action" style="display: block; margin-bottom: 15px; text-align: center;">
				<table class="table table-bordered blockContainer showInlineTable equalSplit">
					<thead>
						<tr>
							<th class="blockHeader" colspan="2">{vtranslate('LBL_CREATE_NEW_ACTION', $MODULE)}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="fieldLabel medium">{vtranslate('ACTION_NAME', $MODULE)}</td>
							<td class="fieldValue medium">
								<input name="action_name" type="text" value="" class="input-xlarge" />
							</td>
						</tr>
						<tr>
							<td class="fieldLabel medium">{vtranslate('LBL_ACTION_TYPE', $MODULE)}</td>
							<td class="fieldValue medium">
								<select class="chzn-select" id="action_type">
									<option value="CREATE">{vtranslate('LBL_ACTION_TYPE_CREATE', $MODULE)}</option>
									<option value="UPDATE">{vtranslate('LBL_ACTION_TYPE_UPDATE', $MODULE)}</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="fieldLabel medium">{vtranslate('LBL_MODULE', $MODULE)}</td>
							<td class="fieldValue medium">
								<select class="chzn-select" name="modulename1">
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
								<input name="action_key" type="text" readonly class="input-xlarge" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="vte-email-converter-btn" style="display: block; margin-top: 15px; text-align: center;">
				<button class="btn btn-success vte-email-converter-create-action-btn" style="margin-right: 5px;">{vtranslate('LBL_SAVE', $MODULE)}</button>
				<button class="btn btn-default vte-email-converter-cancel-action" style="margin-right: 5px;">{vtranslate('LBL_CANCEL', $MODULE)}</button>
			</div>
		</form>
	</div>
{/strip}
