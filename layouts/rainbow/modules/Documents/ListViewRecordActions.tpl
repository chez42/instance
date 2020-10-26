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
    {if $LISTVIEW_ENTRY->get('starred') eq 'Yes'}
        {assign var=STARRED value=true}
    {else}
        {assign var=STARRED value=false}
    {/if}
	{if $MODULE_MODEL->isStarredEnabled()}
		<span class="markStar icon action " title="{if $STARRED} {vtranslate('LBL_STARRED', $MODULE)} {else} {vtranslate('LBL_NOT_STARRED', $MODULE)}{/if}"><i class="material-icons">{if $STARRED}star active {else}star_border{/if}</i></span> 
	{/if}
    <span class="more dropdown action">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="material-icons icon">info_outline</i></a>
        <ul class="dropdown-menu animated fadeIn" style="min-height:120px!important;height:120px!important;">
            <li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}">{vtranslate('LBL_DETAILS', $MODULE)}</a></li>
            {if $RECORD_ACTIONS}
				{if $RECORD_ACTIONS['edit']}
					<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0);" data-url="{$LISTVIEW_ENTRY->getEditViewUrl()}" name="editlink">{vtranslate('LBL_EDIT', $MODULE)}</a></li>
				{/if}
				{if $RECORD_ACTIONS['delete']}
					<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0);" class="deleteRecordButton">{vtranslate('LBL_DELETE', $MODULE)}</a></li>
				{/if}
			{/if}
            {assign var=RECORD_ID value=$LISTVIEW_ENTRY->getId()}
            {assign var="DOCUMENT_RECORD_MODEL" value=Vtiger_Record_Model::getInstanceById($RECORD_ID)}
			{if $DOCUMENT_RECORD_MODEL->get('filename') && $DOCUMENT_RECORD_MODEL->get('filestatus')}
				<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0)" data-filelocationtype="{$DOCUMENT_RECORD_MODEL->get('filelocationtype')}" data-filename="{$DOCUMENT_RECORD_MODEL->get('filename')}" onclick="Vtiger_Header_Js.previewFile(event)">File Preview</a></li>
			{/if}
			{assign var="IS_DOWNLOAD_PERMITTED" value=Users_Privileges_Model::isPermitted('Documents', 'Download')}
			{if $IS_DOWNLOAD_PERMITTED && $DOCUMENT_RECORD_MODEL->get('filename') && $DOCUMENT_RECORD_MODEL->get('filestatus') && $DOCUMENT_RECORD_MODEL->get('filelocationtype') eq 'I'}
				<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="{$DOCUMENT_RECORD_MODEL->getDownloadFileURL()}">Download</a></li>
			{/if}
	      
        </ul>
    </span>

    <div class="btn-group inline-save hide">
        <button class="button btn-success btn-small save" name="save"><i class="material-icons">check</i></button>
        <button class="button btn-danger btn-small cancel" name="Cancel"><i class="material-icons">close</i></button>
    </div>
</div>
{/strip}
