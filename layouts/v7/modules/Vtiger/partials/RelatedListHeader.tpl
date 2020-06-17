{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	{foreach item=RELATED_LIST_MASSACTION from=$RELATED_LIST_MASSACTIONS name=massActions}
        
        {if $RELATED_LIST_MASSACTION->getLabel() eq 'LBL_EDIT'}
            {assign var=editAction value=$RELATED_LIST_MASSACTION}
        {else if $RELATED_LIST_MASSACTION->getLabel() eq 'LBL_DELETE'}
            {assign var=deleteAction value=$RELATED_LIST_MASSACTION}
        {else if $RELATED_LIST_MASSACTION->getLabel() eq 'LBL_ADD_COMMENT'}
            {assign var=commentAction value=$RELATED_LIST_MASSACTION}
        {else if $RELATED_LIST_MASSACTION->getLabel() eq 'LBL_EXPORT'}
            {assign var=exportAction value=$RELATED_LIST_MASSACTION}
            {* $a is added as its print the index of the array, need to find a way around it *}
        {/if}
        
    {/foreach}
    
	<div class="relatedHeader">
		<div class="btn-toolbar row">
			<div class="col-lg-12 col-md-12 col-sm-12 btn-toolbar">
				
				 {foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}
					<div class="btn-group">
						{assign var=DROPDOWNS value=$RELATED_LINK->get('linkdropdowns')}
						{if count($DROPDOWNS) gt 0}
							<a class="btn dropdown-toggle" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="200" data-close-others="false" style="width:20px;height:18px;">
								<img title="{$RELATED_LINK->getLabel()}" alt="{$RELATED_LINK->getLabel()}" src="{vimage_path("{$RELATED_LINK->getIcon()}")}">
							</a>
							<ul class="dropdown-menu">
								{foreach item=DROPDOWN from=$DROPDOWNS}
									<li><a id="{$RELATED_MODULE_NAME}_relatedlistView_add_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DROPDOWN['label'])}" class="{$RELATED_LINK->get('linkclass')}" href='javascript:void(0)' data-documentType="{$DROPDOWN['type']}" data-url="{$DROPDOWN['url']}" data-name="{$RELATED_MODULE_NAME}" data-firsttime="{$DROPDOWN['firsttime']}"><i class="icon-plus"></i>&nbsp;{vtranslate($DROPDOWN['label'], $RELATED_MODULE_NAME)}</a></li>
								{/foreach}
							</ul>
						{else}
							{assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
							{* setting button module attribute to Events or Calendar based on link label *}
							{assign var=LINK_LABEL value={$RELATED_LINK->get('linklabel')}}
							{if $RELATED_LINK->get('_linklabel') === '_add_event'}
								{assign var=RELATED_MODULE_NAME value='Events'}
							{elseif $RELATED_LINK->get('_linklabel') === '_add_task'}
								{assign var=RELATED_MODULE_NAME value='Calendar'}
							{/if}
							{if $IS_SELECT_BUTTON || $IS_CREATE_PERMITTED}
								<button type="button" module="{$RELATED_MODULE_NAME}" class="btn btn-default
									{if $IS_SELECT_BUTTON eq true} selectRelation{else} addButton" name="addButton{/if}"
									{if $IS_SELECT_BUTTON eq true} data-moduleName="{$RELATED_LINK->get('_module')->get('name')}" {/if}
									{if ($RELATED_LINK->isPageLoadLink())}
										{if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
										data-url="{$RELATED_LINK->getUrl()}{if $SELECTED_MENU_CATEGORY}&app={$SELECTED_MENU_CATEGORY}{/if}"
									{/if}
									>{if $IS_SELECT_BUTTON eq false}<i class="fa fa-plus"></i>&nbsp;{/if}&nbsp;{$RELATED_LINK->getLabel()}</button>
							{/if}
						{/if}
					</div>
				{/foreach}
				&nbsp;
			</div>
			{assign var=CLASS_VIEW_ACTION value='relatedViewActions'}
			{assign var=CLASS_VIEW_PAGING_INPUT value='relatedViewPagingInput'}
			{assign var=CLASS_VIEW_PAGING_INPUT_SUBMIT value='relatedViewPagingInputSubmit'}
			{assign var=CLASS_VIEW_BASIC_ACTION value='relatedViewBasicAction'}
			{assign var=PAGING_MODEL value=$PAGING}
			{assign var=RECORD_COUNT value=$RELATED_RECORDS|@count}
			{assign var=PAGE_NUMBER value=$PAGING->get('page')}
			
		</div>
		<div class="clearfix" style="margin: 5px;"></div>
		<div class = "row">
			<div class="col-md-2">
				<div class="btn-group relatedlistViewActionsContainer" role="group" aria-label="...">
					{if $editAction}
	                    <button type="button" class="btn btn-default relatededit" id={$MODULE}_reletedlistView_massAction_{$editAction->getLabel()} 
	                            {if stripos($editAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" url='{$editAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$editAction->getUrl()}' {/if} title="{vtranslate('LBL_EDIT', $MODULE)}" disabled="disabled">
	                        <i class="fa fa-pencil"></i>
	                    </button>
	                {/if}
	                {if $deleteAction}
	                    <button type="button" class="btn btn-default relateddelete" id={$MODULE}_reletedlistView_massAction_{$deleteAction->getLabel()} 
	                            {if stripos($deleteAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" url='{$deleteAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$deleteAction->getUrl()}' {/if} title="{vtranslate('LBL_DELETE', $MODULE)}" disabled="disabled">
	                        <i class="fa fa-trash"></i>
	                    </button>
	                {/if}
	                {if $commentAction}
	                    <button type="button" class="btn btn-default relatedcomment" id="{$MODULE}_reletedlistView_massAction_{$commentAction->getLabel()}" 
	                            href="{$commentAction->getUrl()}" title="{vtranslate('LBL_COMMENT', $MODULE)}" disabled="disabled">
	                        <i class="fa fa-comment"></i>
	                    </button>
	                {/if}
	                {if $MODULE eq 'Accounts' && $RELATED_MODULE_NAME eq 'Contacts'}
	                	 <button type="button" class="btn btn-default relatedEmail" id="{$MODULE}_reletedlistView_massAction_LBL_SEND_EMAIL" 
	                            href="index.php?module={$RELATED_MODULE_NAME}&view=MassActionAjax&mode=showComposeEmailForm&step=step1" title="{vtranslate('Send Email', $MODULE)}" disabled="disabled">
	                        <i class="fa fa-envelope"></i>
	                    </button>
	                {/if}
				</div>
				{if $exportAction}
	                <div class="btn-group relatedlistViewMassActions" role="group">
	                	<button type="button" class="btn btn-default relatedexport export" id={$MODULE}_reletedlistView_massAction_{$exportAction->getLabel()} 
	                            {if stripos($exportAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" url='{$exportAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$exportAction->getUrl()}' {/if} title="{vtranslate('LBL_EXPORT', $MODULE)}" >
	                        Export
	                    </button>
	                </div>    
                {/if}
			</div>	
		 	<div class="col-md-6">
			 	<div class="hide messageContainer" style = "height:30px;">
	                <center><a href="#" id="selectAllMsgDiv">{vtranslate('LBL_SELECT_ALL',$RELATED_MODULE_NAME)}&nbsp;Related&nbsp;{vtranslate($RELATED_MODULE_NAME ,$RELATED_MODULE_NAME)}&nbsp;<span id="totalRecordsCount" class="hide" value=""></span></a></center>
	            </div>
	            <div class="hide messageContainer" style = "height:30px;">
	                <center><a href="#" id="deSelectAllMsgDiv">{vtranslate('LBL_DESELECT_ALL_RECORDS',$RELATED_MODULE_NAME)}</a></center>
	            </div> 
            </div>
            {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
      	</div>          
	</div>
{/strip}