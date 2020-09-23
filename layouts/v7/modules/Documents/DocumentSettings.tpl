{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Documents/views/ListAjax.php *}
{strip}
	{foreach key=index item=jsModel from=$SCRIPTS}
		<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
	{/foreach}
	<div class="modal-dialog modal-lg documentsSettingsContainer">
		<div class="modal-content">
			{assign var=HEADER_TITLE value={vtranslate('Folder Settings', $MODULE)}}
			{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
			{assign var=TRANSLATION_MODULE value="Users"}
			<div class="modal-body">
				<form class="form-horizontal" id="DocumentSettings" name="DocumentSettings" method="post" action="index.php">
					<input type="hidden" name="module" value="Documents" />
					<input type="hidden" name="action" value="SaveDocumentSettings" />
					<input type="hidden" name="record" value="{$RECORD}" />
					<input type="hidden" name="sourcemodule" value="Users" />
					<input type=hidden name="sourceView" />
					<div class="row">
						<div class="col-md-7" style="margin-left: 20px;">
							<div class="form-group">
								<label class=" col-lg-4 col-sm-4 col-xs-4">{vtranslate('Default Folder',$MODULE)}</label>
								<div class=" col-lg-8 col-sm-8 col-xs-8">
									<select class="select2" name="documents_folder" style="width: 250px;">
										{foreach key=ID item=FOLDER from=$FOLDER_ENTRIES}
											<option value="{$FOLDER->getId()}" {if $FOLDERID eq $ID} selected="" {/if}>{vtranslate($FOLDER->getName(),$MODULE)}</option>
										{/foreach}
									</select>
									
								</div>
							</div>
							
						</div>
						<div class="col-md-4">
							<button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-success pull-left" type="submit" name="saveButton"><strong>Save</strong></button>
						</div>
					</div>
					
				</form>
				
				<hr>
				
				<input type='hidden' id='cvid' value="{$VIEWID}">
				<input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
		        <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
		        <input type="hidden" id="noOfEntries" value="{count($FOLDER_ENTRIES)}">
		        <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
		        <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
		        <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
		        <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
				 <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
				<div class = "row">
					<div class="col-md-7">
						<h4>Folders Permissions</h4>
					</div>
					{if count($FOLDER_ENTRIES) neq '0'}
						 <div class="col-md-5">
			                {assign var=RECORD_COUNT value=$FOLDERS_ENTRIES_COUNT}
			                {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
			            </div>
		            {/if}
	            </div>
	            <div class="folder-table-container" style="min-height: 250px;">
					<table id="folder-table" class="{if count($FOLDER_ENTRIES) eq '0'}listview-table-norecords {/if} listview-table table-bordered" 
					style="table-layout: fixed; width: 100%;">
						<thead>
							<tr class="listViewContentHeader">
								<th style="width:12% !important">
									
								</th>
								<th style="width:15% !important">
									Folder Name
								</th>
								<th style="width:50% !important">
									Share With
								</th>
								<th style="width:5% !important">
									Hide From Portal
								</th>
								{if $USER_MODEL->isAdminUser()}
									<th style="width:7% !important">
										Global Folder
									</th>
								{/if}
							</tr>
						</thead>
						<tbody class="overflow-y">
							{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
							
							{assign var=ACCESSIBLE_USER_LIST value=$USER_MODEL->getAccessibleUsersForModule($MODULE)}
						
							{foreach item=DOCFOLDER from=$FOLDER_ENTRIES}
								
								<tr class="folderListViewEntries" data-id='{$DOCFOLDER->getId()}' ondblclick="Documents_FolderSettings_Js.registerSettingsRowDoubleClickEvent({$DOCFOLDER->getId()})">
									<td style="width:17% !important" class="folderListViewEntryValue">
										<div class="btn-group inline-save hide">
									        <button class="button btn-success btn-small saveFolder" type="button" onclick="Documents_FolderSettings_Js.registerFolderInlineSave({$DOCFOLDER->getId()})" name="save"><i class="fa fa-check"></i></button>
									        <button class="button btn-danger btn-small cancelFolder" type="button" name="Cancel" onclick="Documents_FolderSettings_Js.registerFolderInlineCancel({$DOCFOLDER->getId()})"><i class="fa fa-close"></i></button>
									    </div>
									    <div class="btn-group pull-right deleteBtn">
									        <i class="fa fa-trash deleteFolder" type="button" onclick="Documents_FolderSettings_Js.registerDeleteFolderEvent({$DOCFOLDER->getId()})" name="delete"> </i>
									    </div>
									</td>
									<td style="width:15% !important" class="folderListViewEntryValue" data-name="folder_name" title="Folder Name" data-rawvalue="{$DOCFOLDER->getName()}" >
										<span class=" title">
											<span class="value">
												{$DOCFOLDER->getName()}
											</span>
										</span>	
										<span class="hide edit">
											<input class="inputElement" type="text" name="folderName" value="{$DOCFOLDER->getName()}">
										</span>
									</td>
									<td style="width:50% !important" class="folderListViewEntryValue" data-name="view_permissions" title="View Permissions" >
										<span class=" permissions">
											<span class="value">
												{assign var=permissions value=DocumentFolder_Record_Model::folderViewPermissions($DOCFOLDER->getId(),'name')}
												{$permissions}
											</span>
										</span>
										<span class="hide edit">
											{assign var=user_ids value=DocumentFolder_Record_Model::folderViewPermissions($DOCFOLDER->getId(),FALSE)}
											<select class="select2 inputElement" data-name="view_permissions" name="view_permissions[]" multiple>
												<optgroup label="{vtranslate('LBL_USERS')}">
													{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
									                    <option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}'{foreach item=USER from=$user_ids}{if $USER eq $OWNER_ID && $USER_MODEL->id neq $USER} selected {/if}{/foreach}
															{if array_key_exists($OWNER_ID, $ACCESSIBLE_USER_LIST)} data-recordaccess=true {else} data-recordaccess=false {/if}
															data-userId="{$CURRENT_USER_ID}">
									                    {$OWNER_NAME}
									                    </option>
													{/foreach}
												</optgroup>
											</select>	
										</span>
									</td>
									<td style="width:5% !important" class=" folderListViewEntryValue" data-name="hide_from_portal" title="Hide From Portal" >
										{assign var=portal value=DocumentFolder_Record_Model::folderHidePortal($DOCFOLDER->getId())}
										<span class="fieldValue hide_from_portal">
											<span class="value">
												{if $portal}
													Yes
												{else}
													No
												{/if}		
											</span>
										</span>	
										<span class="hide edit">
											<input type="checkbox" name='hide_from_portal' value="1" {if $portal} checked {/if}/>
										</span>
									</td>
									{if $USER_MODEL->isAdminUser()}
										<td class="folderListViewEntryValue" data-name="default_for_all_users" title="Default For All Users" style="width:7% !important" >
											{assign var=forAllUsers value=DocumentFolder_Record_Model::folderForAllUsers($DOCFOLDER->getId())}
											<span class="fieldValue default_for_all_users">
												<span class="value">
													{if $forAllUsers}
														Yes
													{else}
														No
													{/if}		
												</span>
											</span>	
											<span class="hide edit">
												<input type="checkbox" name='default_for_all_users' value="1" {if $forAllUsers} checked {/if}/>
											</span>
										</td>
									{/if}
								</tr>
							{/foreach}
							
							{if count($FOLDER_ENTRIES) eq '0'}
								<tr class="emptyRecordsDiv">
									{assign var=COLSPAN_WIDTH value=6}
									<td colspan="{$COLSPAN_WIDTH}">
										<div class="emptyRecordsContent">
											{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
											{vtranslate('LBL_NO')} {vtranslate('Document Folders', $MODULE)} {vtranslate('LBL_FOUND')}.
											
										</div>
									</td>
								</tr>	
							{/if}
						</tbody>
					</table>
				</div>	
			</div>
			<div class="modal-footer ">
		        <center>
		        </center>
			</div>
		</div>
	</div>
{/strip}