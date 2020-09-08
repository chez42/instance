{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
	
	{assign var=RELATED_MODULE_NAME value=$RELATED_MODULE->get('name')}

	<div class="relatedContainer">
	
	    <div class="relatedHeader">
	        <div class="btn-toolbar row">
	            <div class="col-lg-6 col-md-6 col-sm-6 btn-toolbar">
	                <div class="row">
                     	{foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}

							{if $RELATED_LINK->get('linkmodule') eq 'Documents'}
	                            <div class="col-sm-3" style="width:22%;">
	                                {assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
	                                {* setting button module attribute to Events or Calendar based on link label *}
	                                {assign var=LINK_LABEL value={$RELATED_LINK->get('linklabel')}}
	                                {if $RELATED_LINK->get('_linklabel') === '_add_event'}
	                                    {assign var=RELATED_MODULE_NAME value='Events'}
	                                {elseif $RELATED_LINK->get('_linklabel') === '_add_task'}
	                                    {assign var=RELATED_MODULE_NAME value='Calendar'}
	                                {/if}
	                                <button type="button" module="{$RELATED_MODULE_NAME}"  class="btn addButton btn-default
	                                    {if $IS_SELECT_BUTTON eq true} selectRelation {/if} "
	                                    {if $IS_SELECT_BUTTON eq true} data-moduleName={$RELATED_LINK->get('_module')->get('name')} {/if}
	                                    {if ($RELATED_LINK->isPageLoadLink())}
	                                    {if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
	                                    data-url="{$RELATED_LINK->getUrl()}"
	                                    {/if}
	                                {if $IS_SELECT_BUTTON neq true}name="addButton"{/if}>{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;{$RELATED_LINK->getLabel()}</button>
	                            </div>
                            {/if}
                            
                            {if $RELATED_LINK->getLabel() eq 'Vtiger'}
								{if $IS_CREATE_PERMITTED}        
									<div class="col-sm-3">
										<div class="dropdown">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
												<span class="fa fa-plus" title="{vtranslate('LBL_NEW_DOCUMENT', $MODULE)}"></span>&nbsp;&nbsp;{vtranslate('LBL_NEW_DOCUMENT', $RELATED_MODULE_NAME)}&nbsp; <span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												<li class="dropdown-header"><i class="fa fa-upload"></i> {vtranslate('LBL_FILE_UPLOAD', $RELATED_MODULE_NAME)}</li>
												<li id="VtigerAction">
													<a href="javascript:Documents_Index_Js.uploadTo('Vtiger',{$PARENT_ID},'{$MODULE}')">
														{*<img style="  margin-top: -3px;margin-right: 4%;" title="Vtiger" alt="Vtiger" src="layouts/v7/skins//images/Vtiger.png">*}
														<i class="fa fa-desktop"> </i>  {vtranslate('LBL_FROM_COMPUTER', 'Documents' )}
													</a>
												</li>
												<li role="separator" class="divider"></li>
												<li class="dropdown-header"><i class="fa fa-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', $RELATED_MODULE_NAME)}</li>
												<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E',{$PARENT_ID},'{$MODULE}')">&nbsp;<i class="fa fa-external-link"></i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', $RELATED_MODULE_NAME, {vtranslate('LBL_FILE_URL', $RELATED_MODULE_NAME)})}</a></li>
												<li role="separator" class="divider"></li>
												<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W',{$PARENT_ID},'{$MODULE}')"><i class="fa fa-file-text"></i> {vtranslate('LBL_CREATE_NEW', $RELATED_MODULE_NAME, {vtranslate('SINGLE_Documents', $RELATED_MODULE_NAME)})}</a></li>
											</ul>
										</div>
									</div>
								{/if}
                            {/if}
                            
                    	{/foreach}	
	                </div>&nbsp;
	            </div>
	             
	             <div class="col-lg-2 col-md-2 col-sm-2 btn-toolbar pull-right">
	                <div class="row pull-right">
	            	  	<div class="col-sm-6" style= "width: 42%;" >
	                        <button type="button" class="btn changeMode btn-default" name="list"><i class="icon-plus icon-white"></i>&nbsp;List View</button>
	                    </div>
	                    <div class="col-sm-6" >
	                         <button type="button" class="btn changeMode btn-default" name="folder"><i class="icon-plus icon-white"></i>&nbsp;Folder View</button>
	                    </div>
	                </div>
	            </div>
             </div>
	       
	    </div>
	    
	    <input type="hidden" name="link_id" value ='{$LINKID}'>
		<input type="hidden" name="module_entity_id" value ='{$DOC_ENTITY_ID}'>
		<input type="hidden" class = 'relatedModuleName' name="relatedModuleName" value ='Documents'>
		<input type="hidden" name="parent_id" value ='{$PARENT_ID}'>
		
		<div class="relatedContents col-lg-12 col-md-12 col-sm-12 ">
			
			<div class="col-md-2 sidebarFolder pull-right">
				{include file="partials/FolderSidebarEssentials.tpl"|vtemplate_path:$MODULE}
			</div>
			
			<div class="col-md-10 relatedFolderView" id="listViewContent" {*style="width:calc(98vw - 230px);"*}>
				<div class="col-sm-12 col-xs-12" >
					<input type="hidden" name="view" id="view" value="{$VIEW}" />
					<input type="hidden" name="app" id="appName" value="{$SELECTED_MENU_CATEGORY}">
					<div class="pull-right"	style="margin:10px;">
						<span class="fieldLabel textOverflowEllipsis {$WIDTHTYPE}" >
							<span class='muted'>{vtranslate('Show Empty Folders', $MODULE)}</span>
						</span>
						<span class="fieldValue {$WIDTHTYPE} pull-right" style="margin-left:10px;">
							<input type="checkbox" class="inputElement showHiddenFolders" style="width:15px;height:15px;" name="showHiddenFolder" value='1' {if $SHOWFOLDER} checked {/if	}>
						</span>	
					</div>
					<div style = "margin-top:10px;margin-bottom:10px;font-weight:800;font-size:14px;">
						<label> Notes :</label>
						<ul>
							<li>Right click on any Folder name to edit/delete</li>
							<li>Right click on Document name to delete/edit/preview/download</li>
						</ul> 
						
					</div>
					
					<div class="preFolder module-breadcrumb" title="Back to Previous Folder">
						<style>
							.preFolder .current-filter-name, .preFolder .leftIcon{
						    	margin: 0px !important;
						    	line-height: 20px !important;
						    }
						</style>
						{if !empty($FOLDERS)}
							<p class="current-filter-name filter-name pull-left cursorPointer">
								<a class="folderBreadcrumb" data-folder-id="" > <i class="fa fa-home" style="font-size:20px"></i> &nbsp </a>
							</p>
						{/if}
					</div>
					<div class="clearfix"></div>
					<div id="table-content" class="folder-table-container" style="border:0px;margin-top:5px;">
						<table id="folder-table">
							<thead>
							</thead>
							<div class="folderContent">
								{include file="FolderContent.tpl"|vtemplate_path:$MODULE}
							</div>
						</table>	
					</div>
					<div id="scroller_wrapper" class="bottom-fixed-scroll">
						<div id="scroller" class="scroller-div"></div>
					</div>
				</div>
			</div>
		
		</div>
		
	</div>
	
	<script type="text/javascript">
        var related_uimeta = (function() {
            var fieldInfo  = {$FIELDS_INFO};
            return {
                field: {
                    get: function(name, property) {
                        if(name && property === undefined) {
                            return fieldInfo[name];
                        }
                        if(name && property) {
                            return fieldInfo[name][property]
                        }
                    },
                    isMandatory : function(name){
                        if(fieldInfo[name]) {
                            return fieldInfo[name].mandatory;
                        }
                        return false;
                    },
                    getType : function(name){
                        if(fieldInfo[name]) {
                            return fieldInfo[name].type
                        }
                        return false;
                    }
                },
            };
        })();
        
        jQuery(document).ready(function () {
			var instance = Documents_FolderViewRelated_Js.getInstance();
			instance.loadSideBar();
		});
    </script>
    
    
    
{/strip}
