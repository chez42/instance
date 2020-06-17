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
		<input type ="hidden" class="documentTree" name="docTree" value = 1/>
		
		<style>
			
			.details.row ul.jstree-children > li > ul > li:first-child {
			    
			    margin-left: 18px;
			    
			}
			
			.tree-body { max-width:100%;min-height:600px; min-width:100%; margin:0 auto; padding:20px 10px; font-size:14px; font-size:1em; }
			
			.demo { overflow:auto; border:1px solid silver; min-height:600px; }
			
		</style>

		<div class="relatedContents col-lg-12 col-md-12 col-sm-12 ">
			<div class="pull-right"	style="margin:10px;">
				<span class="fieldLabel textOverflowEllipsis {$WIDTHTYPE}" >
					<span class='muted'>{vtranslate('Show Empty Folders', $MODULE)}</span>
				</span>
				<span class="fieldValue {$WIDTHTYPE} pull-right" style="margin-left:10px;">
					<input type="checkbox" class="inputElement showHiddenFolders" style="width:15px;height:15px;" name="showHiddenFolder" value='1' {if $USER_MODEL->show_hidden_folders} checked {/if	}>
				</span>	
			</div>
			<div class="bottomscroll-div ">
				<div class="tree-body" >
					<div class="demo" id="tree_folder">
						
					</div>
				</div>
			</div>
		</div>
		
	</div>
{/strip}
<script>
	
	function editFunction() {
		
		event.stopImmediatePropagation();
		event.preventDefault();
		
		var relation = Vtiger_Detail_Js.getInstance();
		
		var element = jQuery(event.currentTarget);
		var editUrl = element.data('url');
		
		relation.showOverlayEditView(editUrl);
		
	}	
	
	function unlinkFunction() {
		
		event.stopImmediatePropagation();
		event.preventDefault();
		
		var relation = Vtiger_Detail_Js.getInstance();
		
		var element = jQuery(event.currentTarget);
		var key = relation.getDeleteMessageKey();
		var message = app.vtranslate(key);
		var relatedModuleName = relation.getRelatedModuleName();
		var relatedRecordid = element.data('id');
		
		var relatedController = relation.getRelatedController();
		
		if(relatedController){
			if(relatedModuleName == 'Documents'){
				var params = {
					'message' : message
				};
				app.helper.showPromptBox(params).then(
					function(e) {
						relatedController.deleteRelation([relatedRecordid]).then(function(response){
							relatedController.loadRelatedList().then(function() {
								relatedController.triggerRelationAdditionalActions();
							});
						});
					},
					function(error, err){
					}
				);
				
				
			} 
		}
	}	
</script>	