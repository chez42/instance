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
<div class="editViewPageDiv editViewContainer globalPortalInfoBlockDiv" id="globalportal">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="clearfix">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				<h3 style="margin-top: 0px;">{vtranslate('Global Portal Permissions', $QUALIFIED_MODULE)}</h3>
			</div>
			{if $mode neq 'edit'}
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
					<div class="btn-group pull-right">
						<button class="btn btn-default editButton" data-url='{$URL}&mode=edit' type="button" title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}">{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}</button>
					</div>
				</div>
			{/if}
		</div>
		<form class="form-horizontal" id="defaultPortalPermission" data-url="{$URL}&mode=detail" method="post" action="index.php">
			
			<input type="hidden" name="module" value="Users" />
			<input type="hidden" name="action" value="SaveDefaultPortalPermission" />
			<input type="hidden" name="from" value="global" />
			
			<div class="modal-body">
				<div class="container-fluid">
					<style>
						.portalTable>tbody>tr>td{
							padding: 8px;
							/*border: 1px solid !important;*/
						}
						
					</style>
					<div class="table-responsive">
						{if $mode eq 'edit'}
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
										<b>{vtranslate('Enable?', $MODULE)}</b>
									</td>
									<td class="fieldLabel textOverflowEllipsis text-center">
										<b>{vtranslate('Can view across Organization', $MODULE)}</b>
									</td>
									<td class="fieldLabel textOverflowEllipsis text-center">
										<b>{vtranslate('Can Edit Records?', $MODULE)}</b>
									</td>
									
								</tr>
										
								{assign var=PortalModules value=[{getTabid('HelpDesk')} => 'Tickets', {getTabid('Documents')} => 'Documents', {getTabid('Reports')} => 'Reports']}
								{assign var=PortalReports value=['Portfolios'=>['Asset Class Report'],'Income'=>['Last 12 months','Last Year','Projected','Month Over Month'],'Performance'=>['Gain Loss','GH1 Report','GH2 Report','Overview']]}
								
								{foreach key=TAB_ID item=MODULE_NAME from=$PortalModules}
									
									{assign var=NAME_MODULE value=strtolower(str_replace(' ', '_', $MODULE_NAME))}
									
									{if $SELECTED_PORTAL_MODULES|@count gt 0}
										{assign var=VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_visible]}
										{assign var=RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_record_across_org]}
										{assign var=EDIT_RECORDS value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_edit_records]}
									{/if}
									{if $MODULE_NAME neq 'Reports'}		
									<tr>
										<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}</td>
										<td class="fieldLabel textOverflowEllipsis text-center"></td>
										<td class="fieldValue text-center">
											<input type="hidden" name="portalModulesInfo[{$NAME_MODULE}_visible]" value="0" />
											<input type="checkbox" {if $MODULE_NAME eq 'Reports'} id="portal_reports" {/if} name="portalModulesInfo[{$NAME_MODULE}_visible]" value="1" {if $VISIBLE == '1'} checked {/if}/>
										</td>
										<td class="fieldValue text-center">
											{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME eq 'Reports'}
												&nbsp;
											{else}
												<input type="hidden" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" value="0" />
												<input type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" value="1" {if $RECORD_VISIBLE == '1'} checked {/if}/>
											{/if}
										</td>
										<td class="fieldValue text-center">
											{if $MODULE_NAME neq 'Accounts'}
												<input type="hidden" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" value="0" />
												<input type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" value="1" {if $EDIT_RECORDS == '1'} checked {/if}/>
											{/if}
										</td>
									</tr>
									{/if}
									{if $MODULE_NAME eq 'Reports'}
										
										{if count($PortalReports) > 0}
											
											{assign var=ReportTab value=$TAB_ID}
											
											{foreach key=ReprtName item=PortalReport from=$PortalReports}
												
												<tr class="portal_reports_row"  {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
													<td class="fieldLabel textOverflowEllipsis text-left" ><a href="#" class="mainmodule" data-value="{$ReprtName}" style="cursor:pointer;">{vtranslate($ReprtName, $MODULE)}<span>(+) click to enable all</span></a></td>
													
													<td class="fieldValue text-center">
														
													</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													
												</tr>
												
												{foreach item=ReportModules from=$PortalReport}
													{assign var=NAME_REPORT value=strtolower(str_replace(' ', '_', $ReportModules))}
													{if $SELECTED_PORTAL_MODULES|@count gt 0}
														{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_REPORT|cat:_visible]}
														{assign var=REPORT_RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_REPORT|cat:_record_across_org]}
													{else}
														{assign var=REPORT_VISIBLE value="0"}
														{assign var=REPORT_RECORD_VISIBLE value="0"}
													{/if}
													<tr  {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
														<td>&nbsp;</td>
														<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($ReportModules, $MODULE)}</td>
														<td class="fieldValue text-center">
															<input type="hidden" name="portalModulesInfo[{$NAME_REPORT}_visible]" value="0" />
															<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_visible]" value="1" {if $REPORT_VISIBLE == '1'} checked {/if}/>
														</td>
														<td class="fieldValue text-center">
															<input type="hidden" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" value="0" />
															<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" value="1" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if}/>
														</td>
														<td>&nbsp;</td>
													</tr>
												{/foreach}
											{/foreach}
										{/if}
									{/if}
								{/foreach}
							</table>
						{else if $mode eq 'detail'}
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
										<b>{vtranslate('Enable?', $MODULE)}</b>
									</td>
									<td class="fieldLabel textOverflowEllipsis text-center">
										<b>{vtranslate('Can view across Organization', $MODULE)}</b>
									</td>
									<td class="fieldLabel textOverflowEllipsis text-center">
										<b>{vtranslate('Can Edit Records?', $MODULE)}</b>
									</td>
									
								</tr>
										
								{assign var=PortalModules value=[{getTabid('HelpDesk')} => 'Tickets', {getTabid('Documents')} => 'Documents', {getTabid('Reports')} => 'Reports']}
								{assign var=PortalReports value=['Portfolios'=>['Asset Class Report'],'Income'=>['Last 12 months','Last Year','Projected','Month Over Month'],'Performance'=>['Gain Loss','GH1 Report','GH2 Report','Overview']]}
								
								{foreach key=TAB_ID item=MODULE_NAME from=$PortalModules}
									
									{assign var=NAME_MODULE value=strtolower(str_replace(' ', '_', $MODULE_NAME))}
									
									{if $SELECTED_PORTAL_MODULES|@count gt 0}
										{assign var=VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_visible]}
										{assign var=RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_record_across_org]}
										{assign var=EDIT_RECORDS value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_edit_records]}
									{/if}
									{if $MODULE_NAME neq 'Reports'}		
									<tr>
										<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}</td>
										<td class="fieldLabel textOverflowEllipsis text-center"></td>
										<td class="fieldValue text-center">
											{if $VISIBLE == '1'}
												Yes
											{else}
												No
											{/if}
										</td>
										<td class="fieldValue text-center">
											{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME eq 'Reports'}
												&nbsp;
											{else}
												{if $RECORD_VISIBLE == '1'}
													Yes
												{else}
													No
												{/if}
											{/if}
										</td>
										<td class="fieldValue text-center">
											{if $MODULE_NAME neq 'Accounts'}
												{if $EDIT_RECORDS == '1'} 
													Yes
												{else}
													No
												{/if}
											{/if}
										</td>
									</tr>
									{/if}
									{if $MODULE_NAME eq 'Reports' && $REPORT_PERMISSION}
										
										{if count($PortalReports) > 0}
											
											{assign var=ReportTab value=$TAB_ID}
											
											{foreach key=ReprtName item=PortalReport from=$PortalReports}
												
												<tr class="portal_reports_row">
													<td class="fieldLabel textOverflowEllipsis text-left" ><a href="#" class="mainmodule" data-value="{$ReprtName}" style="cursor:pointer;">{vtranslate($ReprtName, $MODULE)}<span>(+) click to enable all</span></a></td>
													
													<td class="fieldValue text-center">
														
													</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													
												</tr>
												
												{foreach item=ReportModules from=$PortalReport}
													{assign var=NAME_REPORT value=strtolower(str_replace(' ', '_', $ReportModules))}
													{if $SELECTED_PORTAL_MODULES|@count gt 0}
														{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_REPORT|cat:_visible]}
														{assign var=REPORT_RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_REPORT|cat:_record_across_org]}
													{else}
														{assign var=REPORT_VISIBLE value="0"}
														{assign var=REPORT_RECORD_VISIBLE value="0"}
													{/if}
													<tr>
														<td>&nbsp;</td>
														<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($ReportModules, $MODULE)}</td>
														<td class="fieldValue text-center">
															{if $REPORT_VISIBLE == '1'}
																Yes
															{else}
																No
															{/if}
														</td>
														<td class="fieldValue text-center">
															{if $REPORT_RECORD_VISIBLE == '1'} 
																Yes
															{else}
																No
															{/if}
														</td>
														<td>&nbsp;</td>
													</tr>
												{/foreach}
											{/foreach}
										{/if}
									{/if}
								{/foreach}
							</table>
						{/if}
					</div>
				</div>
			</div>
			{if $mode neq 'detail'}
				<div class='modal-overlay-footer clearfix'>
					<div class="row clearfix">
						<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
							<button type='submit' class='btn btn-success saveButton' name="saveButton" >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
							<a class='cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
						</div>
					</div>
				</div>
			{/if}
		</form>
	</div>
</div>
{/strip}

