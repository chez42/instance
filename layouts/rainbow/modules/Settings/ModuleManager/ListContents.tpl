{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Settings/ModuleManager/views/List.php *}

{strip}
	<div class="listViewPageDiv detailViewContainer" id="moduleManagerContents">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
			<div id="listview-actions" class="listview-actions-container">
				<div class="clearfix">
					<h4 class="pull-left">{vtranslate('LBL_MODULE_MANAGER', $QUALIFIED_MODULE)} 
					 </h4>




<div class="pull-right">
						<div class="btn-group">
							<button class="btn btn-secondary" type="button" onclick='window.location.href="{$IMPORT_USER_MODULE_FROM_FILE_URL}"'>
								{vtranslate('LBL_IMPORT_MODULE_FROM_ZIP', $QUALIFIED_MODULE)}
							</button>
						
							<button class="btn btn-secondary" type="button" onclick='window.location.href = "{$IMPORT_MODULE_URL}"'>
								{vtranslate('LBL_EXTENSION_STORE', 'Settings:ExtensionStore')}
							</button>
						</div>
					</div>
				</div>
				<br>
				<div class="contents">
					{assign var=COUNTER value=0}
					<table class="table table-bordered modulesTable">
						<tr>
							<style>
								.moduleManagerBlock span.module-icon :not(.material-icons) {
									filter:none !important;
								}
							</style>
							{foreach item=MODULE_MODEL key=MODULE_ID from=$ALL_MODULES}
								{if !$MODULE_MODEL->ishide}
								{assign var=MODULE_NAME value=$MODULE_MODEL->get('name')}
								{assign var=MODULE_ACTIVE value=$MODULE_MODEL->isActive()}
								{assign var=MODULE_LABEL value=vtranslate($MODULE_MODEL->get('label'), $MODULE_MODEL->get('name'))}
								{if $COUNTER eq 2}
								</tr><tr>
									{assign var=COUNTER value=0}
								{/if}
								<td class="ModulemanagerSettings">
									<div class="moduleManagerBlock">
										<span class="col-lg-1" style="line-height: 2.5;">
											<input type="checkbox" value="" name="moduleStatus" data-module="{$MODULE_NAME}" data-module-translation="{$MODULE_LABEL}" {if $MODULE_MODEL->isActive()}checked{/if} />
										</span>
										<span class="col-lg-1 moduleImage {if !$MODULE_ACTIVE}dull {/if}">
											{if !$MODULE}
											{assign var=MODULE value=$MODULE_NAME}
											{/if}
												
											{if $MODULE_NAME eq 'PortfolioInformation'}
												<span class="module-icon"><img src="layouts/rainbow/icons/PortfolioInformation.png" title="{$MODULE_LABEL}"></span>
												{*<i style="line-height: 28px;font-size: 20px;" class="fa fa-line-chart" aria-hidden="true"></i>*}
											{else if $MODULE_NAME eq 'Connection'}
												<span class="module-icon"><img src="layouts/rainbow/icons/Connection.png" title="{$MODULE_LABEL}"></span>
												{*<i style="line-height: 28px;font-size: 20px;" class="fa fa-users" aria-hidden="true"></i>*}
											{else if $MODULE_NAME eq 'ModComments'}
												<span class="module-icon"><img src="layouts/rainbow/icons/ModComments.png" title="{$MODULE_LABEL}"></span>
												{*<i style="line-height: 28px;font-size: 20px;" class="fa fa-comments-o" aria-hidden="true"></i>*}
											{else if $MODULE_NAME eq 'RingCentral'}
												<i style="line-height: 28px;font-size: 20px;" class="fa fa-phone-square" aria-hidden="true"></i>
											{else if $MODULE_NAME eq 'Task'}
												<span class="module-icon"><img src="layouts/rainbow/icons/Task.png" title="{$MODULE_LABEL}"></span>
												{*<i style="line-height: 28px;font-size: 20px;" class="fa fa-tasks" aria-hidden="true"></i>*}
											{else if $MODULE_NAME eq 'Timecontrol'}
												<i style="line-height: 28px;font-size: 20px;" class="fa fa-hourglass" aria-hidden="true"></i>
											{else if $MODULE_NAME eq 'CalendarTemplate'}
												<i style="line-height: 28px;font-size: 20px;" class="fa fa-fast-forward" aria-hidden="true"></i>
											{else if $MODULE_NAME eq 'EmailTemplates'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/EmailTemplates.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Documents'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Documents.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'HelpDesk'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/HelpDesk.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Instances'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Instances.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'ModSecurities'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/ModSecurities.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Notifications'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Notifications.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'PositionInformation'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/PositionInformation.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'QuotingTool'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/QuotingTool.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Transactions'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Transactions.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Contacts'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Contacts.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'PandaDoc'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/PandaDoc.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'VTEEmailMarketing'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/EmailMarketing.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Invoice'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Invoice.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'PurchaseOrder'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/PurchaseOrder.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'Quotes'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/Quotes.png" title="{$MODULE_LABEL}"></span>
											{else if $MODULE_NAME eq 'SalesOrder'} 
												<span class="module-icon"><img src="layouts/rainbow/icons/SalesOrder.png" title="{$MODULE_LABEL}"></span>
											{else if vimage_path($MODULE_NAME|cat:'.png') != false}
												<i class="ti-{strtolower($MODULE_LABEL)} alignMiddle" alt="{$MODULE_LABEL}" title="{$MODULE_LABEL}" ></i>
											{else}
												<i class="ti-folder alignMiddle" alt="{$MODULE_LABEL}" title="{$MODULE_LABEL}"></i>
											{/if}	
											
										</span>
										<span class="col-lg-7 moduleName {if !$MODULE_ACTIVE} dull {/if}"><h5>{$MODULE_LABEL}</h5></span>
											{assign var=SETTINGS_LINKS value=$MODULE_MODEL->getSettingLinks()}
											{if !in_array($MODULE_NAME, $RESTRICTED_MODULES_LIST) && (count($SETTINGS_LINKS) > 0)}
											<span class="col-lg-3 moduleblock">
												<span class="btn-group pull-right actions {if !$MODULE_ACTIVE}hide{/if}">
													<button class="btn btn-success btn dropdown-toggle unpin hiden " data-toggle="dropdown">
														{vtranslate('LBL_SETTINGS', $QUALIFIED_MODULE)}&nbsp;<i class="caret"></i>
													</button>
													<ul class="dropdown-menu pull-right dropdownfields animated flipInY">
														{foreach item=SETTINGS_LINK from=$SETTINGS_LINKS}
															{if $MODULE_NAME eq 'Calendar'}
																{if $SETTINGS_LINK['linklabel'] eq 'LBL_EDIT_FIELDS'}
																	<li><a href="{$SETTINGS_LINK['linkurl']}&sourceModule=Events">{vtranslate($SETTINGS_LINK['linklabel'], $MODULE_NAME, vtranslate('LBL_EVENTS',$MODULE_NAME))}</a></li>
																	{*<li><a href="{$SETTINGS_LINK['linkurl']}&sourceModule=Calendar">{vtranslate($SETTINGS_LINK['linklabel'], $MODULE_NAME, vtranslate('LBL_TASKS','Calendar'))}</a></li>*}
																{else if $SETTINGS_LINK['linklabel'] eq 'LBL_EDIT_WORKFLOWS'} 
																	<li><a href="{$SETTINGS_LINK['linkurl']}&sourceModule=Events">{vtranslate('LBL_EVENTS', $MODULE_NAME)} {vtranslate('LBL_WORKFLOWS',$MODULE_NAME)}</a></li>	
																	{*<li><a href="{$SETTINGS_LINK['linkurl']}&sourceModule=Calendar">{vtranslate('LBL_TASKS', 'Calendar')} {vtranslate('LBL_WORKFLOWS',$MODULE_NAME)}</a></li>*}
																{else}
																	<li><a href={$SETTINGS_LINK['linkurl']}>{vtranslate($SETTINGS_LINK['linklabel'], $MODULE_NAME, vtranslate($MODULE_NAME, $MODULE_NAME))}</a></li>
																{/if}
															{else}
																<li>
																	<a {if stripos($SETTINGS_LINK['linkurl'], 'javascript:')===0} onclick='{$SETTINGS_LINK['linkurl']|substr:strlen("javascript:")};'{else} onclick='window.location.href = "{$SETTINGS_LINK['linkurl']}"'{/if}>{vtranslate($SETTINGS_LINK['linklabel'], $MODULE_NAME, vtranslate("SINGLE_$MODULE_NAME", $MODULE_NAME))}</a>
																</li>
															{/if}
														{/foreach}
													</ul>
												</span>
											</span>
										{/if}
									</div>
									{assign var=COUNTER value=$COUNTER+1}
								</td>
								{/if}
							{/foreach}
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
{/strip}