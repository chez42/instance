{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
<!--LIST VIEW RECORD ACTIONS-->

<div class="table-actions">
    {if !$SEARCH_MODE_RESULTS}
    <span class="input" >
        <input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox"/>
    </span>
    {/if}
    {if $RECORD_ACTIONS}
		{if $RECORD_ACTIONS['edit']}
		<span class="edit icon action" title="{vtranslate('LBL_EDIT', $MODULE)}">
		<a data-id="{$LISTVIEW_ENTRY->getId()}" href="{$LISTVIEW_ENTRY->getEditViewUrl()}&app={$SELECTED_MENU_CATEGORY}" data-url="{$LISTVIEW_ENTRY->getEditViewUrl()}&app={$SELECTED_MENU_CATEGORY}" name="editlink"> <i class="material-icons">create</i></a></span>
		
		{/if}
	{/if}
    {if $LISTVIEW_ENTRY->get('starred') eq 'Yes'}
        {assign var=STARRED value=true}
    {else}
        {assign var=STARRED value=false}
    {/if}
    {if $QUICK_PREVIEW_ENABLED eq 'true'}
        <span class="quickView icon action" data-app="{$SELECTED_MENU_CATEGORY}" title="{vtranslate('LBL_QUICK_VIEW', $MODULE)}">
		<i class="material-icons">zoom_in</i></span>
    {/if}
	{if $MODULE_MODEL->isStarredEnabled()}
		<span class="markStar icon action  " title="{if $STARRED} {vtranslate('LBL_STARRED', $MODULE)} {else} {vtranslate('LBL_NOT_STARRED', $MODULE)}{/if}">
		<i class="material-icons">{if $STARRED}star{else}star_border{/if}</i></span> 
	{/if}
    <span class="more dropdown action ">
        <span href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
            <i class="material-icons icon">info_outline</i></span>
        <ul class="dropdown-menu animated fadeIn">
            <li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}&app={$SELECTED_MENU_CATEGORY}">{vtranslate('LBL_DETAILS', $MODULE)}</a></li>
			{if $RECORD_ACTIONS}
				{if $RECORD_ACTIONS['edit']}
					<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0);" data-url="{$LISTVIEW_ENTRY->getEditViewUrl()}&app={$SELECTED_MENU_CATEGORY}" name="editlink">{vtranslate('LBL_EDIT', $MODULE)}</a></li>
				{/if}
				{if $RECORD_ACTIONS['delete']}
					<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0);" class="deleteRecordButton">{vtranslate('LBL_DELETE', $MODULE)}</a></li>
				{/if}
			{/if}
        </ul>
    </span>

    <div class="btn-group inline-save hide">
        <button class="button btn-success save" type="button" name="save"><i class="ti-check"></i></button>
        <button class="button btn-danger cancel" type="button" name="Cancel"><i class="ti-close"></i></button>
    </div>
</div>
{/strip}
