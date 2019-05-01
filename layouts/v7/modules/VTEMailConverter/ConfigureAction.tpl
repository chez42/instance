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
	.select2-drop{z-index: 100001;}
	.vte-email-converter-configure-action{width: 90%;}
	.vte-email-converter-configure-action .addButton{margin-left: 10px;}
	.vte-email-converter-configure-action .chzn-drop{text-align: left;}
	.vte-email-converter-configure-action .vte-email-converter-mapping-fields input,
	.vte-email-converter-configure-action .vte-email-converter-mapping-fields select{margin: 0;}
	input[type="text"].inputElement, input[type="password"].inputElement,
	input[type="number"].inputElement{
		padding: 3px 8px;
	}
	.vte-email-converter-configure-action .select2-container{min-width: 200px;}
	.vte-email-converter-configure-action .match-field .select2-container{min-width: 20px; max-width: 100px;}
	.vte-email-converter-configure-action .vte-email-converter-action .select2-container{min-width: 300px;}
	.vte-email-converter-configure-action .create-if-not-existed .select2-container{min-width: 10px;}
	.vte-email-converter-configure-action .create-if-not-existed{display: inline-block;}
	.modal-open .tooltip-inner{max-width: 400px; text-align: left;}
	.modal-open .ui-state-focus{border: none; font-weight: inherit; margin: 0;}
</style>
{/literal}
{strip}
	<div class="modal-dialog modal-lg vte-email-converter-configure-action" style="padding: 10px;">
		<div class="modal-content">
			<form name="vte-email-converter-configure-action-form" action="" method="post" >
				<input type='hidden' id='current_module1_active' value="{$RULE.modulename1}">
				<input type='hidden' id='current_module2_active' value="{$RULE.modulename2}">
				<input type='hidden' id='current_action_active' value="{$RULE.action}">
				<input type='hidden' name="module" value="VTEMailConverter" />
				<input type='hidden' name="action" value="SaveRule" />
				<div class="modal-header">
					<div class="clearfix">
						<div class="pull-right ">
							<button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
						</div>
						<h4 class="pull-left">{vtranslate('LBL_CONFIGURE_ACTIONS', $MODULE)}</h4>
					</div>
				</div>
				<div class="modal-body mCustomScrollbar">
					<div class="vte-email-converter-action" style="display: block; margin-bottom: 15px; text-align: center;">
						<select class="select2" name="current_action_module">
							<option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                            {foreach item=ACTION from=$ACTIONS}
								<option {if $ACTION.action eq $RULE.action} selected {/if} value="{$ACTION.action}">{vtranslate($ACTION.action_name, $MODULE)}</option>
                            {/foreach}
						</select>
						<a style="background-color: #35aa47 ; color: #FFFFFF;" data-url="index.php?module=VTEMailConverter&view=CreateAction" class="btn btn-success pull-right create-new-action-form" href="javascript:void(0);">
							<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;{vtranslate('LBL_NEW_ACTION_BTN', $MODULE)}
						</a>
					</div>
					<div class="vte-email-converter-mapping-fields" style="height: 550px; overflow-y: auto;">
						<table class="table table-bordered ">
							<thead>
							<tr class="listViewHeaders">
								<th colspan="6">
									{vtranslate('VALUES_FROM_EMAIL', $MODULE)}
									<a href="javascript:void(0);" class="pull-right advanced-options-btn" style="text-decoration: underline !important;">{vtranslate('LBL_ADVANCED_OPTIONS_BTN', $MODULE)}</a>
								</th>
								<th style="width: 20px;">&nbsp;</th>
								<th colspan="4">{vtranslate('CRM_FIELDS', $MODULE)}</th>
							</tr>
							<tr class="listViewHeaders" style="background-color: #c9cccf;">
								<td>{vtranslate('LBL_IDENTIFIER', $MODULE)}</td>
								<td></td>
								<td>
									{vtranslate('LBL_END_WITH', $MODULE)}
									&nbsp;<i class="fa fa-info-circle alignMiddle ep-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate('LBL_END_WITH_DESC', $MODULE))}"></i>
								</td>
								<td colspan="3" class="advanced-options">
									{vtranslate('LBL_PARSE_EXTRA', $MODULE)}
									&nbsp;<i class="fa fa-info-circle alignMiddle ep-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate('LBL_PARSE_EXTRA_DESC', $MODULE))}"></i>
								</td>
								<td></td>
								<td>{vtranslate('CRM_FIELDS', $MODULE)}</td>
								<td class="match-field">
									{vtranslate('LBL_MATCH_FIELD', $MODULE)}
									&nbsp;<i class="fa fa-info-circle alignMiddle ep-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate('LBL_MATCH_FIELD_DESC', $MODULE))}"></i>
								</td>
								<td colspan="2">{vtranslate('LBL_CRM_FIELDS_VALUES', $MODULE)}</td>
							</tr>
							</thead>
							<tbody>
                            {include file="ConfigureActionAjax.tpl"|@vtemplate_path:$MODULE RULE=$RULE RULE_EXISTED=$RULE_EXISTED LIST_FIELDS=$LIST_FIELDS ACTION_TYPE=$ACTION_TYPE}
							</tbody>
						</table>
						<br />
						<div class="pull-left create-if-not-existed" style="display: {if $ACTION_TYPE eq 'UPDATE'}inline-block {else}none {/if};">
							<select class="select2 pull-left" name="rules[create_if_not_existed]">
								<option value="0" {if $ACTION.create_if_not_existed neq 1} selected {/if}>{vtranslate('LBL_CREATE_IF_RECORD_IS_NOT_FOUND_NO', $MODULE)}</option>
								<option value="1" {if $ACTION.create_if_not_existed eq 1} selected {/if}>{vtranslate('LBL_CREATE_IF_RECORD_IS_NOT_FOUND_YES', $MODULE)}</option>
							</select>
							<label style="margin-left: 5px; margin-top: 5px; color: #ff0000;">{vtranslate('LBL_CREATE_IF_RECORD_IS_NOT_FOUND_PLACE_HOLDER', $MODULE)}</label>
						</div>
						<button class="btn addButton pull-right"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;{vtranslate('LBL_ADD_FIELD_BTN', $MODULE)}</button>
					</div>
				</div>
				<div class="modal-footer">
					<div class="vte-email-converter-btn" style="display: block; text-align: center;">
						<button class="btn btn-success vte-email-converter-save-action" style="margin-right: 5px;">{vtranslate('LBL_SAVE', $MODULE)}</button>
						<button class="btn btn-default vte-email-converter-close-action" style="margin-right: 5px;">{vtranslate('LBL_CANCEL', $MODULE)}</button>
						<button class="btn btn-danger vte-email-converter-delete-action">{vtranslate('LBL_DELETE_RULE', $MODULE)}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}
