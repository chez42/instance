{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	{foreach item=LIST_MASSACTION from=$LISTVIEW_MASSACTIONS name=massActions}
        {if $LIST_MASSACTION->getLabel() eq 'LBL_EDIT'}
            {assign var=editAction value=$LIST_MASSACTION}
        {else if $LIST_MASSACTION->getLabel() eq 'LBL_DELETE'}
            {assign var=deleteAction value=$LIST_MASSACTION}
        {/if}
    {/foreach}
	<div class="listViewPageDiv" id="listViewContent" style="padding-left: 0px; width: 100%">
		<!--id="listViewContent"-->
		<div class="col-sm-12 col-xs-12 full-height">
			<div id="listview-actions" class="listview-actions-container">
				<div class = "row">
					<div class="btn-group  col-md-2 listViewActionsContainer" style="padding-top: 5px;text-align: center;">
						 {if $editAction}
		                    <button type="button" class="btn btn-warning" id={$MODULE}_listView_massAction_{$editAction->getLabel()} 
		                            {if stripos($editAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$editAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$editAction->getUrl()}' {/if} title="{vtranslate('LBL_EDIT', $MODULE)}" disabled="disabled">
		                        <i class="material-icons">create</i>
		                    </button>
		                {/if}
		                {if $deleteAction}
		                    <button type="button" class="btn btn-danger" id={$MODULE}_listView_massAction_{$deleteAction->getLabel()} 
		                            {if stripos($deleteAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$deleteAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$deleteAction->getUrl()}' {/if} title="{vtranslate('LBL_DELETE', $MODULE)}" disabled="disabled">
		                        <i class="material-icons">delete</i>
		                    </button>
		                {/if}
		                <div class="btn-group listViewMassActions">
			                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
	                            {vtranslate('LBL_MORE','Vtiger')}&nbsp;
	                           <i class="material-icons">arrow_drop_down</i>
	                        </button>
	                        <ul class="dropdown-menu" role="menu">
	                            {foreach item=LISTVIEW_ADVANCEDACTIONS from=$LISTVIEW_LINKS['LISTVIEW']}
	                                {if $LISTVIEW_ADVANCEDACTIONS->getLabel() == 'LBL_IMPORT'}
	                                {*Remove Import Action*}  
	                                {else}
                                        <li class="selectFreeRecords"><a id="{$MODULE}_listView_advancedAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_ADVANCEDACTIONS->getLabel())}" {if stripos($LISTVIEW_ADVANCEDACTIONS->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$LISTVIEW_ADVANCEDACTIONS->getUrl()|substr:strlen("javascript:")};'{else} href='{$LISTVIEW_ADVANCEDACTIONS->getUrl()}' {/if}>{vtranslate($LISTVIEW_ADVANCEDACTIONS->getLabel(), $MODULE)}</a></li>
	                                {/if}
	                            {/foreach}
	                        </ul>
		               </div> 
					</div>
					<div class='col-md-7 listViewTopMenuDiv'  style="padding-top: 5px;text-align: center;">
					
						<div class="hide alert alert-success messageContainer">
		                    <center><a href="#" id="selectAllMsgDiv">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}&nbsp;(<span id="totalRecordsCount" value=""></span>)</a></center>
		                </div>
		                <div class="hide alert alert-warning messageContainer">
		                    <center><a href="#" id="deSelectAllMsgDiv">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></center>
		                </div> 
		                
					</div>	
					<div class="col-lg-3 col-md-3 col-xs-12">
						{assign var=RECORD_COUNT value=$LISTVIEW_ENTIRES_COUNT}
						{include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
					</div>
				</div>
				<div class="list-content">
{/strip}
