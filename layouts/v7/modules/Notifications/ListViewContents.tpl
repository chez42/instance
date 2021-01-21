{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/List.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE}
<div class="row">
	<div class="col-sm-2 col-xs-2"> </div>
	<div class="col-sm-8 col-xs-8" id="notificationListContainer">
	
		<input type="hidden" name="cvid" id="cvid"  value="{$VIEWID}" />
		<input type="hidden" name="pageStartRange" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
		<input type="hidden" name="pageEndRange" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
		<input type="hidden" name="previousPageExist" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
		<input type="hidden" name="nextPageExist" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
		<input type='hidden' name="pageNumber" value="{$PAGE_NUMBER}" id='pageNumber'>
		<input type='hidden' name="pageLimit" value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
		<input type='hidden' name="totalCount" value={$LISTVIEW_ENTRIES_COUNT}>
		<input type="hidden" name="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">
		<input type="hidden" name="list_headers" value='{$LIST_HEADER_FIELDS}'/>
		
		<div id="table-content" class="table-container" style="padding: 10px 0px 10px 0px !important;">
			<div id="notificationsBody" class="mainList">
				<div class="col-sm-12 col-xs-12">
					<h4 class="display-inline-block">{vtranslate($MODULE, $MODULE)}</h4>
				</div>
				{foreach item=LIST_DATA from=$LISTVIEW_ENTRIES name=listview key=type}
					{assign var=relatedRecord value=$LIST_DATA['relatedRecord']}
					{assign var=rel_id value=$LIST_DATA['rel_id']}
                    {if $LIST_DATA['relatedModule'] eq "ModComments"}
                    	{assign var=moduleIcon value='<i class="vicon-chat" title="comment" style="font-size: 2rem !important;"></i>'}
                    	{assign var=reply value="<button title='reply' data-commentid={$relatedRecord} class='btn replyComment' style='padding:5px 12px !important;border-radius:25px !important;border:1px solid #06d79c !important;background-color:transparent !important;color:#06d79c !important;'>Reply now</button>"}
                    	{assign var=toolTipClass value=''}
                    {else if $LIST_DATA['relatedModule'] eq "Documents"}
                    	{assign var=moduleIcon value='<i class="vicon-documents" title="document" style="font-size: 2rem !important;"></i>'}
                    	{assign var=toolTipClass value=''}
                    	{assign var=reply value=''}
                    {else if $LIST_DATA['relatedModule'] eq "Events"}
                    	{assign var=moduleIcon value='<i class="vicon-calendar" title="Events" style="font-size: 2rem !important;"></i>'}
                    	{if !$LIST_DATA['accepted']}
                    		{assign var=reply value="<button title='Accept' data-event='accept' data-eventid={$rel_id} class='btn eventAction' style='padding:5px 12px !important;border-radius:25px !important;border:1px solid #06d79c !important;background-color:transparent !important;color:#06d79c !important;'>Accept</button>&nbsp;<button title='Reject' data-event='reject' data-eventid={$rel_id} class='btn eventAction' style='padding:5px 12px !important;border-radius:25px !important;border:1px solid #ef5350 !important;background-color:transparent !important;color:#ef5350 !important;'>Reject</button>"}
                    	{else}
                    		{assign var=reply value=''}
                    	{/if}
                    	{assign var=toolTipClass value=''}
                    	
                    {else if $LIST_DATA['type'] eq "Follow Record"}
                    	{assign var=moduleIcon value='<i title="Follow" class="fa fa-star-o" style="font-size: 2rem !important;"></i>'}
                		{assign var=toolTipClass value='followUpClass'}
                		{assign var=reply value=''}
                    {/if}
                    
					<div class="col-sm-12 col-xs-12 maindiv notification_link {$toolTipClass}" data-rel_id={$rel_id} data-id="$LIST_DATA['id']" data-module="{$LIST_DATA['relatedModule']}">
						<div class="col-sm-10 col-xs-10">
							<div class="pull-left" style="margin:4px!important;">
								{$moduleIcon}
	                        </div>
							<span class="notification_full_name">{$LIST_DATA['title']}{if !$LIST_DATA['title']}{$LIST_DATA['description']}{/if}</span>
							<span class="notification_description">{$LIST_DATA['description']} </span>
							<br><br><span>{$reply}</span>
						</div>
						<div class="col-sm-2 col-xs-2" style="font-size:small !important;">
							<small class="pull-right notification_createdtime" title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($LIST_DATA['createdtime'])}">
								{Vtiger_Util_Helper::formatDateDiffInStrings($LIST_DATA['createdtime'])}
							</small>
							<br>
							<span aria-hidden="true" style="cursor: pointer;" class="fa fa-times-circle pull-right delete" onclick="NotificationsJS.deleteNotification({$LIST_DATA['id']},this)" data-id="{$LIST_DATA['id']}"></span>
						</div>
						<div class="col-sm-12 col-xs-12">
							<div class="divider" style="height: 1px;margin: 8px 0;overflow: hidden;background-color: #ebebeb;">&nbsp;</div>
						</div>
					</div>
				{/foreach}
			</div>
			<div class="col-sm-12 col-xs-12 text-center">
                {assign var=RECORD_COUNT value=$LISTVIEW_ENTRIES_COUNT}
                {if $PAGING_MODEL->isNextPageExists()}
					<button class="btn loadMoreNotifications" style="border-radius:25px !important;border:1px solid #0a66c2 !important;background-color:transparent !important;color:#0a66c2 !important;"> Load More...</button>  
				{/if}
            </div>
		</div>	
	</div>
	<div class="col-sm-2 col-xs-2"> </div>
</div>
