{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{strip}
	<div id="taskManagementContainer" class='fc-overlay-modal modal-content' style="height:100%;">
		<input type="hidden" name="colors" value='{json_encode($COLORS)}'>
		<div class="overlayHeader">
			{assign var=HEADER_TITLE value="TASK MANAGEMENT"}
			{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
		</div>
		<hr style="margin:0px;">
		<div class='modal-body overflowYAuto'>
			<div class='datacontent'>
				<div class="data-header clearfix">
					<div id="taskManagementOtherFilters" class="otherFilters pull-right" style="width:550px;">
						<div class='field pull-right' >
							<button type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-filter icon"></i>
								<span id="selected" style="margin-left:5px;"> {vtranslate('LBL_MORE','Vtiger')}</span>&nbsp;<span class="caret"></span>
							</button>
							<ul class="dropdown-menu filter-menu" style="min-width:250px;top:52px!important;right:15px!important;">
								<div class="taskManagement-filters" id="taskManagement-filters">
									<div class="sidebar-container lists-menu-container" style="padding: 0 12px!important;">
										<div class="sidebar-header clearfix">
											<h5 class="pull-left">{vtranslate('LBL_LISTS',$MODULE)}</h5>
										</div>
										<hr>
										<div>
											<input class="search-list"  type="hidden" placeholder="{vtranslate('LBL_SEARCH_FOR_LIST',$MODULE)}">
										</div>
										<div class="menu-scroller scrollContainer" style="position:relative; top:0; left:0;height: 450px;">
											<div class="taskManagement-menu-content">
												<input type="hidden" name="taskManagementallCvId" value="{$VIEWID}" />
												{assign var="CUSTOM_VIEW_NAMES" value=array()}
												{if $CUSTOM_VIEWS && count($CUSTOM_VIEWS) > 0}
													{foreach key=GROUP_LABEL item=GROUP_CUSTOM_VIEWS from=$CUSTOM_VIEWS}
													{if $GROUP_LABEL neq 'Mine' && $GROUP_LABEL neq 'Shared'}
														{continue}
													 {/if}
													<div class="list-group" id="{if $GROUP_LABEL eq 'Mine'}myList{else}sharedList{/if}">   
														<h6 class="lists-header {if count($GROUP_CUSTOM_VIEWS) <=0} hide {/if}" >
															{if $GROUP_LABEL eq 'Mine'}
																{vtranslate('LBL_MY_LIST',$MODULE)}
															{else}
																{vtranslate('LBL_SHARED_LIST',$MODULE)}
															{/if}
														</h6>
														<ul class="lists-menu" style="list-style: none;">
															{assign var=count value=0}
															{assign var=MODULE_MODEL value=Vtiger_Module_Model::getInstance($MODULE)}
															{assign var=LISTVIEW_URL value=$MODULE_MODEL->getListViewUrl()}
															{foreach item="CUSTOM_VIEW" from=$GROUP_CUSTOM_VIEWS name="customView"}
																{assign var=IS_DEFAULT value=$CUSTOM_VIEW->isDefault()}
																{assign var="CUSTOME_VIEW_RECORD_MODEL" value=CustomView_Record_Model::getInstanceById($CUSTOM_VIEW->getId())}
																{assign var="MEMBERS" value=$CUSTOME_VIEW_RECORD_MODEL->getMembers()}
																{assign var="LIST_STATUS" value=$CUSTOME_VIEW_RECORD_MODEL->get('status')}
																{foreach key=GROUP_LABEL item="MEMBER_LIST" from=$MEMBERS}
																	{if $MEMBER_LIST|@count gt 0}
																	{assign var="SHARED_MEMBER_COUNT" value=1}
																	{/if}
																{/foreach}
																<li style="font-size:12px;" class='taskManagementFilter {if $VIEWID eq $CUSTOM_VIEW->getId()} active {if $smarty.foreach.customView.iteration gt 10} {assign var=count value=1} {/if} {else if $smarty.foreach.customView.iteration gt 10} filterHidden hide{/if} '> 
																	{assign var=VIEWNAME value={vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}}
																	{append var="CUSTOM_VIEW_NAMES" value=$VIEWNAME}
																	 <a class="filterName listViewFilterElipsis" data-filter-id="{$CUSTOM_VIEW->getId()}" title="{$VIEWNAME|@escape:'html'}">{$VIEWNAME|@escape:'html'}</a> 
																</li>
															{/foreach}
														</ul>
													 </div>
													{/foreach}
												{/if}
												<div class="list-group hide noLists">
													<h6 class="lists-header"><center> {vtranslate('LBL_NO')} {vtranslate('LBL_LISTS')} {vtranslate('LBL_FOUND')} ... </center></h6>
												</div>
											</div>
									   </div> 
									</div>
								</div>    
							</ul>
						</div>
					</div>
				</div>

				<hr>

				<div class="data-body row">
					{assign var=MODULE_MODEL value= Vtiger_Module_Model::getInstance($MODULE)}
                    {assign var=USER_PRIVILEGES_MODEL value= Users_Privileges_Model::getCurrentUserPrivilegesModel()}
					{foreach item=PRIORITY from=$PRIORITIES}
						<div class="col-lg-4 contentsBlock {strtolower($PRIORITY)} ui-droppable" data-priority='{$PRIORITY}' data-page="{$PAGE}">
							<div class="{strtolower($PRIORITY)}-header" style="border-bottom: 2px solid {$COLORS[$PRIORITY]}">
								<div class="title" style="background:{$COLORS[$PRIORITY]}"><span>{$PRIORITY}</span></div>
							</div>
							<br>
							<div class="{strtolower($PRIORITY)}-content content" data-priority='{$PRIORITY}' style="border-bottom: 1px solid {$COLORS[$PRIORITY]};padding-bottom: 10px">
								{if $USER_PRIVILEGES_MODEL->hasModuleActionPermission($MODULE_MODEL->getId(), 'CreateView')}
									<div class="input-group">
										<input type="text" class="form-control taskSubject {$PRIORITY}" placeholder="{vtranslate('LBL_ADD_TASK_AND_PRESS_ENTER', $MODULE)}" aria-describedby="basic-addon1" style="width: 99%">
										<span class="quickTask input-group-addon js-task-popover-container more cursorPointer" id="basic-addon1" style="border: 1px solid #ddd; padding: 0 13px;"> 
											<a href="#" id="taskPopover" priority='{$PRIORITY}'><i class="fa fa-plus icon"></i></a>
										</span>
									</div>
								{/if}
								<br>
								<div class='{strtolower($PRIORITY)}-entries container-fluid scrollable dataEntries padding20' style="height:400px;overflow:auto;width:400px;padding-left: 0px;padding-right: 0px;">

								</div>
							</div>
						</div>
					{/foreach}
				</div>
				<div class="editTaskContent hide"> 
					{include file="TaskManagementEdit.tpl"|vtemplate_path:$MODULE} 
				</div> 
			</div>
		</div>
	</div>
{/strip}