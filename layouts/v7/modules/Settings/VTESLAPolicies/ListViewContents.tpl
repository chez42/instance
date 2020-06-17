{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="col-sm-12 col-xs-12 ">
		<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
		<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
		<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
		<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
		<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
		<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
		<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
		<input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">
		<div class = "row">
			<div class='col-md-5'>
				<div class="foldersContainer hidden-xs pull-left">
					<button type="button" class="btn addButton btn-default module-buttons"
							onclick='window.location.href = "{$MODULE_MODEL->getCreateViewUrl()}"'>
						<div class="fa fa-plus" aria-hidden="true"></div>
						&nbsp;&nbsp;{vtranslate('New SLA Policy' , $MODULE)}
					</button>
					<button type="button" class="btn btn-default btn-business" data-url="module=VTESLAPolicies&parent=Settings&view=PopupAddBusiness" >
						&nbsp;&nbsp;{vtranslate('Business Hours' , $MODULE)}
					</button>
				</div>
			</div>
			<div class="col-md-4">
				<div class="search-link hidden-xs" style="margin-top: 0px;">
					<span aria-hidden="true" class="fa fa-search"></span>
					<input class="searchWorkflows" type="text" type="text" value="" placeholder="{vtranslate('LBL_SEARCH', $QUALIFIED_MODULE)}">
				</div>
			</div>
			<div class="col-md-3">
                {assign var=RECORD_COUNT value=$LISTVIEW_ENTRIES_COUNT}
                {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
			</div>
		</div>
		<div class="list-content row">
			<div class="col-sm-12 col-xs-12 ">
				<div id="table-content" class="table-container" style="padding-top:0px !important;">
					<table id="listview-table" class="sla-policy-listview-table table listview-table">
						<thead>
						<tr class="listViewContentHeader">
							<th></th>
							<th nowrap>{vtranslate('LBL_MODULE' , $MODULE)}</th>
							<th nowrap>{vtranslate('LBL_POLICY_NAME' , $MODULE)}</th>
							<th nowrap>{vtranslate('LBL_TARGET' , $MODULE)}</th>
							<th nowrap>{vtranslate('LBL_CONDITIONS' , $MODULE)}</th>
							<th nowrap>{vtranslate('LBL_ACTIONS' , $MODULE)}</th>
						</tr>
						</thead>
						<tbody>
                        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
							<tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY->get('slaid')}"
								data-recordurl="{$LISTVIEW_ENTRY->getEditViewUrl()}">
								<td>
                                    {include file="ListViewRecordActions.tpl"|vtemplate_path:$QUALIFIED_MODULE}
								</td>
								<td>
                                    {assign var="MODULE_ICON_NAME" value="{strtolower($LISTVIEW_ENTRY->get('module'))}"}
									<i class="vicon-{$MODULE_ICON_NAME}" title="{$LISTVIEW_ENTRY->get('module')}"></i>
								</td>
								<td><span>{$LISTVIEW_ENTRY->get('policy_name')}</span></td>
								<td>
									<span>{$LISTVIEW_ENTRY->get('fieldLabel')} : <strong>{$LISTVIEW_ENTRY->get('picklist_value')}</strong></span>
									<br/>
									<span>Resolved Within : <strong>{$LISTVIEW_ENTRY->get('time')} {$LISTVIEW_ENTRY->get('typetime')}</strong></span>
								</td>
								<td>
                                    {assign var=SLA_CONDITION value=$LISTVIEW_ENTRY->getConditonDisplayValue()}
                                    {assign var=ALL_CONDITIONS value=$SLA_CONDITION['All']}
                                    {assign var=ANY_CONDITIONS value=$SLA_CONDITION['Any']}
									<span><strong>{vtranslate('All')}&nbsp;: </strong></span>
                                    {if is_array($ALL_CONDITIONS) && !empty($ALL_CONDITIONS)}
                                        {foreach item=ALL_CONDITION from=$ALL_CONDITIONS name=allCounter}
											<span>{$ALL_CONDITION}</span>
											<br/>
                                        {/foreach}
                                    {else}
                                        {vtranslate('LBL_NA')}
										<br/>
                                    {/if}
									<span><strong>{vtranslate('Any')}&nbsp;:&nbsp;</strong></span>
                                    {if is_array($ANY_CONDITIONS) && !empty($ANY_CONDITIONS)}
                                        {foreach item=ANY_CONDITION from=$ANY_CONDITIONS name=anyCounter}
											<span>{$ANY_CONDITION}</span>
											<br/>
                                        {/foreach}
                                    {else}
                                        {vtranslate('LBL_NA')}
                                    {/if}
								</td>
								<td>
                                    {if $LISTVIEW_ENTRY->get('countEmail') != 0}
										<span>Email ({$LISTVIEW_ENTRY->get('countEmail')})</span>
                                    {/if}
									<br/>
                                    {if $LISTVIEW_ENTRY->get('countReassign') != 0}
										<span>Reassign ({$LISTVIEW_ENTRY->get('countReassign')})</span>
                                    {/if}
									<br/>
                                    {if $LISTVIEW_ENTRY->get('countWorkflow') != 0}
										<span>Workflows ({$LISTVIEW_ENTRY->get('countWorkflow')})</span>
                                    {/if}
								</td>
							</tr>
                        {/foreach}
						</tbody>
					</table>
				</div>
				<div id="scroller_wrapper" class="bottom-fixed-scroll">
					<div id="scroller" class="scroller-div"></div>
				</div>
			</div>
		</div>
	</div>
{/strip}
