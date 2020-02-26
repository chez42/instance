{*<!--
/*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
-->*}

{strip}
    {assign var=LISTVIEW_MASSACTIONS_1 value=array()}
    <div id="listview-actions" class="listview-actions-container">
        {foreach item=LIST_MASSACTION from=$LISTVIEW_MASSACTIONS name=massActions}
            {if $LIST_MASSACTION->getLabel() eq 'LBL_EDIT'}
                {assign var=editAction value=$LIST_MASSACTION}
            {else if $LIST_MASSACTION->getLabel() eq 'LBL_DELETE'}
                {assign var=deleteAction value=$LIST_MASSACTION}
            {else if $LIST_MASSACTION->getLabel() eq 'LBL_ADD_COMMENT'}
                {assign var=commentAction value=$LIST_MASSACTION}
            {else}
                {$a = array_push($LISTVIEW_MASSACTIONS_1, $LIST_MASSACTION)}
                {* $a is added as its print the index of the array, need to find a way around it *}
            {/if}
        {/foreach}
        <div class = "row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
	            
	           	            
	            
            <div class="btn-group listViewActionsContainer" role="group" aria-label="...">
                {if $editAction}
                    <button type="button" class="btn btn-warning" id={$MODULE}_listView_massAction_{$editAction->getLabel()} 
                            {if stripos($editAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$editAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$editAction->getUrl()}' {/if} title="{vtranslate('LBL_EDIT', $MODULE)}" disabled="disabled" tippytitle >
                        <i class="material-icons">create</i>
                    </button>
                {/if}
                {if $deleteAction}
                    <button type="button" class="btn btn-danger" id={$MODULE}_listView_massAction_{$deleteAction->getLabel()} 
                            {if stripos($deleteAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$deleteAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$deleteAction->getUrl()}' {/if} title="{vtranslate('LBL_DELETE', $MODULE)}" disabled="disabled" tippytitle >
                        <i class="material-icons">delete</i>
                    </button>
                {/if}
                {if $commentAction}
                    <button type="button" class="btn btn-info" id="{$MODULE}_listView_massAction_{$commentAction->getLabel()}" 
                            onclick="Vtiger_List_Js.triggerMassAction('{$commentAction->getUrl()}')" title="{vtranslate('LBL_COMMENT', $MODULE)}" disabled="disabled" tippytitle >
                        <i class="material-icons">comment</i>
                    </button>
                {/if}

                {if count($LISTVIEW_MASSACTIONS_1) gt 0 or $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
                    <div class="btn-group listViewMassActions" role="group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            {vtranslate('LBL_MORE','Vtiger')}&nbsp;
                            <i class="material-icons">arrow_drop_down</i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            {foreach item=LISTVIEW_MASSACTION from=$LISTVIEW_MASSACTIONS_1 name=advancedMassActions}
                                <li class="hide"><a id="{$MODULE}_listView_massAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel())}" {if stripos($LISTVIEW_MASSACTION->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{else} href='{$LISTVIEW_MASSACTION->getUrl()}' {/if}>{vtranslate($LISTVIEW_MASSACTION->getLabel(), $MODULE)}</a></li>
                            {/foreach}
                            {if count($LISTVIEW_MASSACTIONS_1) gt 0 and $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
                                <li class="divider hide"></li>
                            {/if}
							{if $MODULE_MODEL->isStarredEnabled()}
								<li class="hide">
									<a id="{$MODULE}_listView_massAction_LBL_ADD_STAR" onclick="Vtiger_List_Js.triggerAddStar()">
										{vtranslate('LBL_FOLLOW',$MODULE)}
									</a>
								</li>
								<li class="hide">
									<a id="{$MODULE}_listView_massAction_LBL_REMOVE_STAR" onclick="Vtiger_List_Js.triggerRemoveStar()">
										{vtranslate('LBL_UNFOLLOW',$MODULE)}
									</a>
								</li>
							{/if}
                            <li class="hide">
                                <a id="{$MODULE}_listView_massAction_LBL_ADD_TAG" onclick="Vtiger_List_Js.triggerAddTag()">
                                    {vtranslate('LBL_ADD_TAG',$MODULE)}
                                </a>
                            </li>
                            {if $CURRENT_TAG neq ''}
                            <li class="hide">
                                <a id="{$MODULE}_listview_massAction_LBL_REMOVE_TAG" onclick="Vtiger_List_Js.triggerRemoveTag({$CURRENT_TAG})">
                                    {vtranslate('LBL_REMOVE_TAG', $MODULE)}
                                </a>
                            </li>
                            {/if}
                            <li class="divider hide" style="margin:9px 0px;"></li>
                            {assign var=FIND_DUPLICATES_EXITS value=false}
                            {foreach item=LISTVIEW_ADVANCEDACTIONS from=$LISTVIEW_LINKS['LISTVIEW']}
                                {if $LISTVIEW_ADVANCEDACTIONS->getLabel() == 'Print'}
                                    {assign var=PRINT_TEMPLATE value=$LISTVIEW_ADVANCEDACTIONS}
                                {else}
                                    {if $LISTVIEW_ADVANCEDACTIONS->getLabel() == 'LBL_FIND_DUPLICATES'}
                                        {assign var=FIND_DUPLICATES_EXISTS value=true}
                                    {/if}
                                {/if}
                            {/foreach}
                            
                            {if $PRINT_TEMPLATE}
                                <li class="hide"><a id="{$MODULE}_listView_advancedAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($PRINT_TEMPLATE->getLabel())}" {if stripos($PRINT_TEMPLATE->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$PRINT_TEMPLATE->getUrl()|substr:strlen("javascript:")};'{else} href='{$PRINT_TEMPLATE->getUrl()}' {/if}>{vtranslate($PRINT_TEMPLATE->getLabel(), $MODULE)}</a></li>
                            {/if}
                            {if $FIND_DUPLICATES_EXISTS}
                                <li class="hide"><a id="{$MODULE}_listView_advancedAction_MERGE_RECORD"  href="javascript:void(0);" onclick='Vtiger_List_Js.triggerMergeRecord()'>{vtranslate('LBL_MERGE_SELECTED_RECORDS', $MODULE)}</a></li>
                            {/if}
                            {foreach item=LISTVIEW_ADVANCEDACTIONS from=$LISTVIEW_LINKS['LISTVIEW']}
                                {if $LISTVIEW_ADVANCEDACTIONS->getLabel() == 'LBL_IMPORT'}
                                {*Remove Import Action*}  
                                {elseif $LISTVIEW_ADVANCEDACTIONS->getLabel() == 'Print'}
                                    {assign var=PRINT_TEMPLATE value=$LISTVIEW_ADVANCEDACTIONS}
                                {else}
                                    {if $LISTVIEW_ADVANCEDACTIONS->getLabel() == 'LBL_FIND_DUPLICATES'}
                                        {assign var=FIND_DUPLICATES_EXISTS value=true}
                                    {/if}
                                    {if $LISTVIEW_ADVANCEDACTIONS->getLabel() != 'Print'}
                                        <li class="selectFreeRecords"><a id="{$MODULE}_listView_advancedAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_ADVANCEDACTIONS->getLabel())}" {if stripos($LISTVIEW_ADVANCEDACTIONS->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$LISTVIEW_ADVANCEDACTIONS->getUrl()|substr:strlen("javascript:")};'{else} href='{$LISTVIEW_ADVANCEDACTIONS->getUrl()}' {/if}>{vtranslate($LISTVIEW_ADVANCEDACTIONS->getLabel(), $MODULE)}</a></li>
                                    {/if}  
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                
                
                
                
                
               </div> 
            
            
            </div>
            <div class='col-lg-4 col-md-4 col-sm-12 col-xs-12 text-center myc-filters'>
                                 
                <div class="btn-group " role="group" style="width: 100%;">
	                	

	                	{assign var="CURRENT_VIEW_RECORD_MODEL" value=CustomView_Record_Model::getInstanceById($VIEWID)}
	                	<!--
                        <button type="button" class="btn btn-info dropdown-toggle " data-toggle="dropdown" >
                            {vtranslate($CURRENT_VIEW_RECORD_MODEL->get('viewname'))}&nbsp;
                            -->
                        <button type="button" class="btn module-buttons btn-sm btn-rounded dropdown-toggle" data-toggle="dropdown" style="width: 100%">
                            {vtranslate('LBL_LISTS',$MODULE)} | Current: <b>{vtranslate($CURRENT_VIEW_RECORD_MODEL->get('viewname'))}</b >&nbsp;
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="width: 100%;">
                         
                            
                            
                            
                             {assign var="CUSTOM_VIEW_NAMES" value=array()}
                        {if $CUSTOM_VIEWS && count($CUSTOM_VIEWS) > 0}
                            {foreach key=GROUP_LABEL item=GROUP_CUSTOM_VIEWS from=$CUSTOM_VIEWS}
                            {if $GROUP_LABEL neq 'Mine' && $GROUP_LABEL neq 'Shared'}
                                {continue}
                             {/if}
                            
                                
                                <li class="disabled"  style="display: block !important">
                                	<a id=""><b>
	                                    {if $GROUP_LABEL eq 'Mine'}
                                        {vtranslate('LBL_MY_LIST',$MODULE)}
	                                    {else}
	                                        {vtranslate('LBL_SHARED_LIST',$MODULE)}
	                                    {/if}</b>
	                                </a>
	                            </li>
	                                
                                {assign var=count value=0}
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
                                    {assign var=VIEWNAME value={vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}}
                                    {append var="CUSTOM_VIEW_NAMES" value=$VIEWNAME}
                                        
                                    <li class="{if $VIEWID eq $CUSTOM_VIEW->getId() && ($CURRENT_TAG eq '')} active {/if}" style="display: block !important">
                                    	<div style="float: left;">
		                                <a href="{$LISTVIEW_URL|cat:'&viewname='|cat:$CUSTOM_VIEW->getId()}">
		                                    {$VIEWNAME|@escape:'html'}
		                                </a>
                                    	</div>
		                                <div style="float: right; text-align: right">
		                                <a class="editFilter" title="{vtranslate('LBL_EDIT', $MODULE)}" data-url="{$CUSTOME_VIEW_RECORD_MODEL->getEditUrl()}" tippytitle >
				                        	<i class="material-icons text-warning">create</i>
										</a>
				                        <a class="deleteFilter" title="{vtranslate('LBL_DELETE', $MODULE)}" data-url="{$CUSTOME_VIEW_RECORD_MODEL->getDeleteUrl()}" tippytitle >
				                        	<i class="material-icons text-danger">delete</i>
										</a>
										<a class="duplicateFilter" title="{vtranslate('LBL_DUPLICATE', $MODULE)}" data-url="{$CUSTOME_VIEW_RECORD_MODEL->getDuplicateUrl()}" tippytitle >
				                        	<i class="material-icons text-info">content_copy</i>
										</a>
										<a class="toggleDefault" title="{vtranslate('LBL_DEFAULT', $MODULE)}"   data-url="{$CUSTOME_VIEW_RECORD_MODEL->getToggleDefaultUrl()}" tippytitle >
				                        	<i class="material-icons text-success">check</i>
										</a>
		                                </div>
		                                <div class="clearfix"></div>                
		                            </li>
                                    
                                   
                                        {/foreach}
                                  
                    {/foreach}
                          {/if}      
                            
                        </ul>

                                                    
                       
                    </div>
                
                
                
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                
                {assign var=RECORD_COUNT value=$LISTVIEW_ENTRIES_COUNT}
                {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
            </div>
            
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
	            {if $LISTVIEW_ENTRIES_COUNT eq '0' and $REQUEST_INSTANCE and $REQUEST_INSTANCE->isAjax()}
                    {if $smarty.session.lvs.$MODULE.viewname}
                        {assign var=VIEWID value=$smarty.session.lvs.$MODULE.viewname}
                    {/if}
                    {if $VIEWID}
                        {foreach item=FILTER_TYPES from=$CUSTOM_VIEWS}
                            {foreach item=FILTERS from=$FILTER_TYPES}
                                {if $FILTERS->get('cvid') eq $VIEWID}
                                    {assign var=CVNAME value=$FILTERS->get('viewname')}
                                    {break}
                                {/if}
                            {/foreach}
                        {/foreach}
                        {assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getDefaultUrl()}
                        {assign var=DEFAULT_FILTER_ID value=$MODULE_MODEL->getDefaultCustomFilter()}
                        {if $DEFAULT_FILTER_ID}
                            {assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getListViewUrl()|cat:"&viewname="|cat:$DEFAULT_FILTER_ID}
                        {/if}
                        {if $CVNAME neq 'All'}
                            <div>{vtranslate('LBL_DISPLAYING_RESULTS',$MODULE)} {vtranslate('LBL_FROM',$MODULE)} <b>{$CVNAME}</b>. <a style="color:blue" href='{$DEFAULT_FILTER_URL}'>{vtranslate('LBL_SEARCH_IN',$MODULE)} {vtranslate('All',$MODULE)} {vtranslate($MODULE, $MODULE)}</a> </div>
                        {/if}
                    {/if}
                {/if}
                <div class="hide alert alert-success messageContainer" style = "margin-top:20px;">
                    <center><a href="#" id="selectAllMsgDiv">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}&nbsp;(<span id="totalRecordsCount" value=""></span>)</a></center>
                </div>
                <div class="hide alert alert-warning messageContainer"  style = "margin-top:20px;">
                    <center><a href="#" id="deSelectAllMsgDiv">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></center>
                </div>    
            </div>
            
        </div>	
     </div>
{/strip}