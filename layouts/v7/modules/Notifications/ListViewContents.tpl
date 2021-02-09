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
		<div class="col-sm-12 col-xs-12" style="padding-right: 0px;padding-left: 0px;">
			<h4 class="display-inline-block">{vtranslate($MODULE, $MODULE)}</h4>
		</div>
		<div id= "customtabs" class="tabs col-sm-12 col-xs-12" style="padding-right:0px;padding-left:0px;margin-bottom:1rem;">
			<div class="related-tabs">
				<ul class="nav nav-tabs tab-links" role="tablist">
					<li class="tab-item active" data-id="all">
						<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#all_notifications" role="tab">
				    		<span class="tab-label">
					    		<strong>
									{vtranslate('All',{$MODULE_NAME})}
								</strong>
							</span>
				    	</a>
					</li>
					<li class="tab-item" data-id="comments">
						<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#comments" role="tab">
				    		<span class="tab-label">
					    		<strong>
									{vtranslate('Comments',{$MODULE_NAME})}
								</strong>
							</span>
				    	</a>
					</li>
					<li class="tab-item" data-id="events">
						<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#events" role="tab">
				    		<span class="tab-label">
					    		<strong>
									{vtranslate('Events',{$MODULE_NAME})}
								</strong>
							</span>
				    	</a>
					</li>
					<li class="tab-item" data-id="followup">
						<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#followup" role="tab">
				    		<span class="tab-label">
					    		<strong>
									{vtranslate('Follow Up',{$MODULE_NAME})}
								</strong>
							</span>
				    	</a>
					</li>
				</ul>
			</div>
		</div>
		<div id="table-content" class="table-container" style="padding: 10px 0px 10px 0px !important;">
			<div id="notificationsBody" class="mainList">
				
				
				<div class = "tab-content col-sm-12 col-xs-12" style="margin-top:10px;">
					<div class="active tab-pane all" id="all_notifications" >
						{if $LISTVIEW_ENTRIES_COUNT}
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
										<span class="notification_description" data-fullComment="{$LIST_DATA['description']|escape:"html"}" data-shortComment="{$LIST_DATA['description']|mb_substr:0:150|escape:"html"}..." data-more='{vtranslate('LBL_SHOW_MORE',$MODULE)}' data-less='{vtranslate('LBL_SHOW',$MODULE)} {vtranslate('LBL_LESS',$MODULE)}'>
											{if $LIST_DATA['description']|count_characters:true gt 150}
												{mb_substr(trim($LIST_DATA['description']),0,150)}...
												<a class="pull-right toggleNotification showMore" style="color: blue;"><small>{vtranslate('LBL_SHOW_MORE',$MODULE)}</small></a>
											{else}
												{$LIST_DATA['description']}
											{/if}
										</span>
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
						{else}
							<div class="emptyRecordsDiv">
								<div class="text-center" style="padding-top: 12%;" >
									You're all caught up!
								</div>
							</div>
						
						{/if}
					</div>
					<div class="tab-pane comments" id="comments" >
						
					</div>
					<div class="tab-pane events" id="events" >
						
					</div>
					<div class="tab-pane followup" id="followup" >
						
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-xs-12 text-center" style="margin-top: 10px;">
                {assign var=RECORD_COUNT value=$LISTVIEW_ENTRIES_COUNT}
				<button class="btn loadMoreNotifications" style="border-radius:25px !important;border:1px solid #0a66c2 !important;background-color:transparent !important;color:#0a66c2 !important;" {if !$PAGING_MODEL->isNextPageExists()}disabled{/if}> Load More...</button>  
            </div>
		</div>	
	</div>
	<div class="col-sm-2 col-xs-2"> </div>
</div>
