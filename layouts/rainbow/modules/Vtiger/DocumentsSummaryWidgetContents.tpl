{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
    <div>
        <div class="row" style="margin-bottom:10px;">
            <div class="col-xs-5">
                <strong>{vtranslate('Title','Documents')}</strong>
        </div>
        <div class="col-xs-7">
            <strong>{vtranslate('File Name', 'Documents')}</strong>
        </div>
        </div>
	{foreach item=RELATED_RECORD from=$RELATED_RECORDS}
		{assign var=DOWNLOAD_FILE_URL value=$RELATED_RECORD->getDownloadFileURL()}
		{assign var=DOWNLOAD_STATUS value=$RELATED_RECORD->get('filestatus')}
		{assign var=DOWNLOAD_LOCATION_TYPE value=$RELATED_RECORD->get('filelocationtype')}
		<div class="recentActivitiesContainer row" style="height:22px">
			
					<div  class="" id="documentRelatedRecord pull-left">
						<span class="col-xs-4 textOverflowEllipsis">
							<a href="{$RELATED_RECORD->getDetailViewUrl()}" id="{$MODULE}_{$RELATED_MODULE}_Related_Record_{$RELATED_RECORD->get('id')}" title="{$RELATED_RECORD->getDisplayValue('notes_title')}">
								{$RELATED_RECORD->getDisplayValue('notes_title')}
							</a>
						</span>
                                                <span class="col-xs-5 textOverflowEllipsis" id="DownloadableLink">
                                                    {if $DOWNLOAD_STATUS eq 1}
                                                            {$RELATED_RECORD->getDisplayValue('filename', $RELATED_RECORD->getId(), $RELATED_RECORD)}
                                                    {else}
                                                            {$RELATED_RECORD->get('filename')} 
                                                    {/if}
						</span>
                                                <span class="col-xs-3">
                                                    {* Documents list view special actions "view file" and "download file" *}
                                                    {assign var=RECORD_ID value=$RELATED_RECORD->getId()}
                                                    {if isPermitted('Documents', 'DetailView', $RECORD_ID) eq 'yes'}
                                                        {assign var="DOCUMENT_RECORD_MODEL" value=Vtiger_Record_Model::getInstanceById($RECORD_ID)}
                                                        {assign var="IS_DOWNLOAD_PERMITTED" value=Users_Privileges_Model::isPermitted('Documents', 'Download')}
                                                        {if $DOCUMENT_RECORD_MODEL->get('filename') && $DOCUMENT_RECORD_MODEL->get('filestatus')}
                                                            <a name="viewfile" href="javascript:void(0)" data-filelocationtype="{$DOCUMENT_RECORD_MODEL->get('filelocationtype')}" data-filename="{$DOCUMENT_RECORD_MODEL->get('filename')}" onclick="Vtiger_Header_Js.previewFile(event,{$RECORD_ID})"><i title="{vtranslate('LBL_VIEW_FILE', 'Documents')}" class="ti-image alignMiddle"></i></a>&nbsp;
                                                        {/if}
                                                        {if $IS_DOWNLOAD_PERMITTED}
	                                                        {if $DOCUMENT_RECORD_MODEL->get('filename') && $DOCUMENT_RECORD_MODEL->get('filestatus') && $DOCUMENT_RECORD_MODEL->get('filelocationtype') eq 'I'}
	                                                            <a name="downloadfile" href="{$DOCUMENT_RECORD_MODEL->getDownloadFileURL()}"><i title="{vtranslate('LBL_DOWNLOAD_FILE', 'Documents')}" class="ti-download alignMiddle"></i></a>
	                                                        {/if}
                                                        {/if} 
                                                    {/if}
                                                </span>
					</div>
				
		</div>
	{/foreach}
    </div>
    {assign var=NUMBER_OF_RECORDS value=count($RELATED_RECORDS)}
    {if $NUMBER_OF_RECORDS eq 5}
            <div class="row">
                    <div class="pull-right">
                            <a class="moreRecentDocuments cursorPointer">{vtranslate('LBL_MORE',$MODULE_NAME)}</a>
                    </div>
            </div>
    {/if}
{/strip}