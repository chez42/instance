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
	{assign var=HEADER_TITLE value={vtranslate('Update Portal Permission', $MODULE)}}
	
	{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
	<form class="form-horizontal" id="updatePortalPermission" method="post" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="UpdatePortalPermission" />
		<input type="hidden" name="viewname" value="{$CVID}" />
		<input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
        <input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
        <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
            
		<div class="modal-body">
			<div class="container-fluid">
				<style>
					.portalTable>tbody>tr>td{
						padding: 8px;
						/*border: 1px solid !important;*/
					}
					
				</style>
				<div class="table-responsive">
					<table class="portalTable" border="1" style="width:100%;">
						<col width="20%">
						<col width="20%">
						<col width="20%">
						<col width="20%">
						<col width="20%">
						<tr>
							<td class="fieldLabel textOverflowEllipsis text-left">
								<b>{vtranslate('LBL_MODULE', $MODULE)}</b>
							</td>
							<td class="fieldLabel textOverflowEllipsis text-center">
								&nbsp;
							</td>
							<td class="fieldLabel textOverflowEllipsis text-center">
								<b>{vtranslate('LBL_ENABLE_MODULE', $MODULE)}</b>
							</td>
							<td class="fieldLabel textOverflowEllipsis text-center">
								<b>{vtranslate('LBL_VIEW_ALL_RECORDS', $MODULE)}</b>
							</td>
							<td class="fieldLabel textOverflowEllipsis text-center">
								<b>{vtranslate('LBL_EDIT_RECORDS', $MODULE)}</b>
							</td>
							
						</tr>
								
						{assign var=PortalModules value=[{getTabid('HelpDesk')} => 'Tickets', {getTabid('Documents')} => 'Documents', {getTabid('Reports')} => 'Reports']}
						{assign var=PortalReports value=['Portfolios'=>['Asset Class Report'],'Income'=>['Last 12 months','Last Year','Projected','Month Over Month'],'Performance'=>['Gain Loss','GH1 Report','GH2 Report','Overview']]}
						
						{foreach key=TAB_ID item=MODULE_NAME from=$PortalModules}
							
							{assign var=NAME_MODULE value=strtolower(str_replace(' ', '_', $MODULE_NAME))}
							
							{if $MODULE_NAME neq 'Reports'}		
							<tr>
								<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}</td>
								<td class="fieldLabel textOverflowEllipsis text-center"></td>
								<td class="fieldValue text-center">
									<select data-fieldtype="picklist" class="inputElement select2" type="picklist" name="portalModulesInfo[{$NAME_MODULE}_visible]"  >
										<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										<option value="1"> Yes </option>
										<option value="0"> No </option>
									</select>
									{*<input type="checkbox" {if $MODULE_NAME eq 'Reports'} id="portal_reports" {/if} name="portalModulesInfo[{$NAME_MODULE}_visible]" value="1" />*}
								</td>
								<td class="fieldValue text-center">
									{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME eq 'Reports'}
										&nbsp;
									{else}
										<select data-fieldtype="picklist" class="inputElement select2" type="picklist" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]"  >
											<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
											<option value="1"> Yes </option>
											<option value="0"> No </option>
										</select>
										{*<input type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" value="1"/>*}
									{/if}
								</td>
								<td class="fieldValue text-center">
									{if $MODULE_NAME neq 'Accounts' && $MODULE_NAME neq 'Tickets'}
										<select data-fieldtype="picklist" class="inputElement select2" type="picklist" name="portalModulesInfo[{$NAME_MODULE}_edit_records]"  >
											<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
											<option value="1"> Yes </option>
											<option value="0"> No </option>
										</select>
										{*<input type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" value="1" />*}
									{/if}
								</td>
							</tr>
							{/if}
							{if $MODULE_NAME eq 'Reports'}
								
								{if count($PortalReports) > 0}
									
									{assign var=ReportTab value=$TAB_ID}
									
									{foreach key=ReprtName item=PortalReport from=$PortalReports}
										
										<tr class="portal_reports_row">
											<td class="fieldLabel textOverflowEllipsis text-left" ><a href="#" class="mainmodule" data-value="{$ReprtName}" style="cursor:pointer;">{vtranslate($ReprtName, $MODULE)}{*<span>(+) click to enable all</span>*}</a></td>
											
											<td class="fieldValue text-center">
												
											</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											
										</tr>
										
										{foreach item=ReportModules from=$PortalReport}
											{assign var=NAME_REPORT value=strtolower(str_replace(' ', '_', $ReportModules))}
											
											<tr>
												<td>&nbsp;</td>
												<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($ReportModules, $MODULE)}</td>
												<td class="fieldValue text-center">
													<select data-fieldtype="picklist" class="inputElement select2 {$ReprtName}" type="picklist" name="portalModulesInfo[{$NAME_REPORT}_visible]"  >
														<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
														<option value="1"> Yes </option>
														<option value="0"> No </option>
													</select>
													{*<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_visible]" value="1" />*}
												</td>
												<td class="fieldValue text-center">
													<select data-fieldtype="picklist" class="inputElement select2 {$ReprtName}" type="picklist" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]"  >
														<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
														<option value="1"> Yes </option>
														<option value="0"> No </option>
													</select>
													{*<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" value="1"/>*}
												</td>
												<td>&nbsp;</td>
											</tr>
										{/foreach}
									{/foreach}
								{/if}
							{/if}
						{/foreach}
					</table>
				</div>
			</div>
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
	</form>
	</div>
</div>
{/strip}

