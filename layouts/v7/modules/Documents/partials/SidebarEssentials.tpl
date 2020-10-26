{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
<div class="sidebar-menu">
    <div class="module-filters" id="module-filters">
        <div class="sidebar-container lists-menu-container">
            <div class="sidebar-header clearfix">
                <h5 class="pull-left">{vtranslate('LBL_LISTS',$MODULE)}</h5>
                <button id="createFilter" data-url="{CustomView_Record_Model::getCreateViewUrl($MODULE)}" class="btn btn-sm btn-default pull-right sidebar-btn" title="{vtranslate('LBL_CREATE_LIST',$MODULE)}">
                    <div class="fa fa-plus" aria-hidden="true"></div>
                </button> 
            </div>
            <hr>
            <div>
                <input class="search-list" type="text" placeholder="{vtranslate('LBL_SEARCH_FOR_LIST',$MODULE)}">
            </div>
            <div>
				<div class="list-menu-content">
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
										<i class="fa fa-plus-circle col pull-right" data-toggle="collapse" data-target="#shared_List"></i>
                                    {/if}
                                </h6>
								
                                <input type="hidden" name="allCvId" value="{CustomView_Record_Model::getAllFilterByModule($MODULE)->get('cvid')}" />
								
                                {if $GROUP_LABEL neq 'Mine'}
									<div class="collapse" id="shared_List"> 
								{/if}
								
                                <div class=" menu-scroller scrollContainer" style="position:relative; top:0; left:0;" {if $GROUP_LABEL neq 'Mine'}id="sharedList"{/if} > 
	                               <div class="list-menu-content"> 
	                                <ul class="lists-menu">
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
										<li style="font-size:12px;" class='listViewFilter {if $VIEWID eq $CUSTOM_VIEW->getId() && ($CURRENT_TAG eq '')} active {if $smarty.foreach.customView.iteration gt 10} {assign var=count value=1} {/if} {else if $smarty.foreach.customView.iteration gt 10} filterHidden hide{/if} '> 
	                                        {assign var=VIEWNAME value={vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}}
											{append var="CUSTOM_VIEW_NAMES" value=$VIEWNAME}
	                                         <a class="filterName listViewFilterElipsis" href="{$LISTVIEW_URL|cat:'&viewname='|cat:$CUSTOM_VIEW->getId()|cat:'&app='|cat:$SELECTED_MENU_CATEGORY}" oncontextmenu="return false;" data-filter-id="{$CUSTOM_VIEW->getId()}" title="{$VIEWNAME|@escape:'html'}">{$VIEWNAME|@escape:'html'}{if $CUSTOM_VIEW->get('status') eq '0'}  {vtranslate($MODULE, $MODULE)} {/if}</a> 
	                                            <div class="pull-right">
	                                                <span class="js-popover-container" style="cursor:pointer;">
	                                                   <span  class="fa fa-angle-down" rel="popover" data-toggle="popover" aria-expanded="true" 
															{if $CUSTOM_VIEW->isMine() and $CUSTOM_VIEW->get('viewname') neq 'All' || $CURRENT_USER_MODEL->isAdminUser()}
																{if $CUSTOM_VIEW->get('viewname') neq 'All'}	
																	data-deletable="{if $CUSTOM_VIEW->isDeletable()}true{else}false{/if}"
																{/if}
																data-editable="{if $CUSTOM_VIEW->isEditable() || $CURRENT_USER_MODEL->isAdminUser()}true{else}false{/if}" 
	                                                            {if $CUSTOM_VIEW->isEditable() || $CURRENT_USER_MODEL->isAdminUser()} data-editurl="{$CUSTOM_VIEW->getEditUrl()}{/if}" 
																{if $CUSTOM_VIEW->isDeletable()} 
																	{if $SHARED_MEMBER_COUNT eq 1 or $LIST_STATUS eq 3} data-shared="1"{/if} 
																	data-deleteurl="{$CUSTOM_VIEW->getDeleteUrl()}"
																{/if}
															{/if}
															toggleClass="fa {if $IS_DEFAULT}fa-check-square-o{else}fa-square-o{/if}" 
															data-filter-id="{$CUSTOM_VIEW->getId()}" 
															data-is-default="{$IS_DEFAULT}" 
															data-defaulttoggle="{$CUSTOM_VIEW->getToggleDefaultUrl()}" 
															data-default="{$CUSTOM_VIEW->getDuplicateUrl()}" 
															data-isMine="{if $CUSTOM_VIEW->isMine()||$CURRENT_USER_MODEL->isAdminUser()}true{else}false{/if}">
	                                                    </span>
	                                                     </span>
	                                                </div>
	                                            </li>
	                                        {/foreach}
	                                    </ul>
		                             </div>
	                             </div>
	                             <div class='clearfix'> 
									{if $smarty.foreach.customView.iteration - 5 - $count} 
									<a class="toggleFilterSize" data-more-text=" {$smarty.foreach.customView.iteration - 5 - $count} {vtranslate('LBL_MORE',Vtiger)|@strtolower}" data-less-text="Show less">
										{if $smarty.foreach.customView.iteration gt 5} 
											{$smarty.foreach.customView.iteration - 5 - $count} {vtranslate('LBL_MORE',Vtiger)|@strtolower} 
										{/if} 
									</a>{/if} 
								</div>
							</div>
						{/foreach}
								
							<input type="hidden" id='allFilterNames'  value='{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($CUSTOM_VIEWS_NAMES))}'/>
                            <div id="filterActionPopoverHtml">
                                <ul class="listmenu hide" role="menu">
                                    <li role="presentation" class="editFilter">
                                            <a role="menuitem"><i class="fa fa-pencil"></i>&nbsp;{vtranslate('LBL_EDIT',$MODULE)}</a>
                                        </li>
                                    <li role="presentation" class="deleteFilter">
                                            <a role="menuitem"><i class="fa fa-trash"></i>&nbsp;{vtranslate('LBL_DELETE',$MODULE)}</a>
                                    </li>
                                    <li role="presentation" class="duplicateFilter">
                                                <a role="menuitem" ><i class="fa fa-files-o"></i>&nbsp;{vtranslate('LBL_DUPLICATE',$MODULE)}</a>
                                            </li>
                                    <li role="presentation" class="toggleDefault">
                                                <a role="menuitem" >
                                            <i data-check-icon="fa-check-square-o" data-uncheck-icon="fa-square-o"></i>&nbsp;{vtranslate('LBL_DEFAULT',$MODULE)}
                                                </a>
                                            </li>
                                        </ul>
                            </div>

                        {/if}
                        <div class="list-group hide noLists">
                            <h6 class="lists-header"><center> {vtranslate('LBL_NO')} {vtranslate('LBL_LISTS')} {vtranslate('LBL_FOUND')} ... </center></h6>
                        </div>
                    {if $GROUP_LABEL neq 'Mine'}</div>{/if}	
                </div>
            </div>
        </div>
    
        <div class="sidebar-container lists-menu-container">
           
            <div class="sidebar-header clearfix">
                <h5 class="pull-left">{vtranslate('LBL_FOLDERS',$MODULE)}</h5>
                <button id="createFolder" class="btn btn-default pull-right sidebar-btn">
                    <span class="fa fa-plus" aria-hidden="true"></span>
                </button>
                <i class="fa fa-cog pull-right" title="Folders Settings"  onclick="Documents_Index_Js.showDocumentsSettings()" style = "font-size: 18px;color: #298337;margin:3%;"></i>
            </div>
            <hr>
            
            <div>
                <input class="search-folders" type="text" placeholder="Search for Folders">
            </div>
			
           <div class="menu-scroller scrollContainer scrollContainerFolder" style="position:relative; top:0; left:0;">
				<div class="list-menu-content">
					<div class="bottomscroll-div ">
						 <div class=" menu-scroller scrollContainer" data-mcs-theme="dark" style="position:relative; top:0; left:0;">
			                <div class="list-menu-content"> 
		               	 		<div class="list-group">
				               	 	<ul id="folders-list" class="lists-menu">
					               	 	{if !empty($FOLDERS)}
			                                {foreach item="FOLDER" from=$FOLDERS}
			                                     {assign var=FOLDERNAME value={vtranslate($FOLDER->get('folder_name'), $MODULE)}} 
			                                    <li style="font-size:12px;" data-filter-id="{$FOLDER->getId()}" class='documentFolder {if $FOLDER_VALUE eq $FOLDER->getId()} active{/if}'>
			                                        <a class="filterName" href="javascript:void(0);" data-filter-id="{$FOLDER->getId()}" data-folder-name="{$FOLDER->get('folder_name')}" title="{$FOLDERNAME}">
			                                            <i class="fa {if $FOLDER_VALUE eq $FOLDER->getId()}fa-folder-open{else}fa-folder{/if}"></i> 
			                                            <span class="foldername">{if {$FOLDERNAME|strlen > 30} } {$FOLDERNAME|substr:0:30|@escape:'html'}..{else}{$FOLDERNAME|@escape:'html'}{/if}</span>
			                                        </a>
			                                        {assign var=owner value=getSingleFieldValue('vtiger_crmentity', 'smcreatorid', 'crmid', $FOLDER->getId())}
		                                            {if $owner eq $CURRENT_USER_MODEL->id}
			                                            <div class="dropdown pull-right">
			                                                <span class="fa fa-caret-down dropdown-toggle" data-toggle="dropdown" aria-expanded="true"></span>
			                                                <ul class="dropdown-menu dropdown-menu-right vtDropDown" role="menu">
			                                                    <li class="editFolder " data-folder-id="{$FOLDER->getId()}">
			                                                        <a role="menuitem" ><i class="fa fa-pencil-square-o"></i>&nbsp;Edit</a>
			                                                    </li>
			                                                    <li class="deleteFolder " data-deletable="{!$FOLDER->hasDocuments()}" data-folder-id="{$FOLDER->getId()}">
			                                                        <a role="menuitem" ><i class="fa fa-trash"></i>&nbsp;Delete</a>
			                                                    </li>
			                                                </ul>
			                                            </div>
		                                            {/if}
			                                    </li>
			                                {/foreach}
		                                {else}
			                                <li class="noFolderText" style="display: none;">
			                                    <h6 class="lists-header"><center> 
			                                        {vtranslate('LBL_NO')} {vtranslate('LBL_FOLDERS', $MODULE)} {vtranslate('LBL_FOUND')} ... 
			                                    </center></h6>    
			                                </li>
		                                {/if}
	                                </ul>
			                	</div>
			            	</div>
			        	</div>
					</div>
                </div>
            </div>
        </div>
    </div>
  
    <div class="module-filters">
        <div class="sidebar-container lists-menu-container">
            <h5 class="lists-header">
                {vtranslate('LBL_TAGS', $MODULE)}
            </h5>
            <hr>
            <div class="menu-scroller scrollContainer tags" style="position:relative; top:0; left:0;">
                <div class="list-menu-content">
                    <div id="listViewTagContainer" class="multiLevelTagList" 
                    {if $ALL_CUSTOMVIEW_MODEL} data-view-id="{$ALL_CUSTOMVIEW_MODEL->getId()}" {/if}
                    data-list-tag-count="{Vtiger_Tag_Model::NUM_OF_TAGS_LIST}">
                        {foreach item=TAG_MODEL from=$TAGS name=tagCounter}
                            {assign var=TAG_LABEL value=$TAG_MODEL->getName()}
                            {assign var=TAG_ID value=$TAG_MODEL->getId()}
                            {if $smarty.foreach.tagCounter.iteration gt Vtiger_Tag_Model::NUM_OF_TAGS_LIST}
                                {break}
                            {/if}
                            {include file="Tag.tpl"|vtemplate_path:$MODULE NO_DELETE=true ACTIVE= $CURRENT_TAG eq $TAG_ID}
                        {/foreach}
                        <div> 
                           
                            <div class="moreListTags hide">
                        {foreach item=TAG_MODEL from=$TAGS name=tagCounter}
                            {if $smarty.foreach.tagCounter.iteration le Vtiger_Tag_Model::NUM_OF_TAGS_LIST}
                                {continue}
                            {/if}
                            {include file="Tag.tpl"|vtemplate_path:$MODULE NO_DELETE=true ACTIVE= $CURRENT_TAG eq $TAG_ID}
                        {/foreach}
                             </div>
                        </div>
                    </div>
                    {include file="AddTagUI.tpl"|vtemplate_path:$MODULE RECORD_NAME="" TAGS_LIST=array()}
                </div>
                <div id="dummyTagElement" class="hide">
                    {assign var=TAG_MODEL value=Vtiger_Tag_Model::getCleanInstance()}
                    {include file="Tag.tpl"|vtemplate_path:$MODULE NO_DELETE=true}
                </div>
                <div>
                    <div class="editTagContainer hide">
                        <input type="hidden" name="id" value="" />
                        <div class="editTagContents">
                            <div>
                                <input type="text" name="tagName" value="" style="width:100%" maxlength="25"/>
                            </div>
                            <div>
                                <div class="checkbox">
                                    <label>
                                        <input type="hidden" name="visibility" value="{Vtiger_Tag_Model::PRIVATE_TYPE}"/>
                                        <input type="checkbox" name="visibility" value="{Vtiger_Tag_Model::PUBLIC_TYPE}" />
                                        &nbsp; {vtranslate('LBL_SHARE_TAG',$MODULE)}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-mini btn-success saveTag" type="button" style="width:50%;float:left">
                                <center> <i class="fa fa-check"></i> </center>
                            </button>
                            <button class="btn btn-mini btn-danger cancelSaveTag" type="button" style="width:50%">
                                <center> <i class="fa fa-close"></i> </center>
                            </button>
                        </div>
                    </div>
                </div>
           </div>
           <div id="listViewTagContainerMore"> 
            	<a class="moreTags {if (count($TAGS) - Vtiger_Tag_Model::NUM_OF_TAGS_LIST) le 0} hide {/if}">
                    <span class="moreTagCount">{count($TAGS) - Vtiger_Tag_Model::NUM_OF_TAGS_LIST}</span>
                    &nbsp;{vtranslate('LBL_MORE',$MODULE)|strtolower}
                </a>
           </div>     
        </div>
     </div>
</div>
