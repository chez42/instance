
<input type="hidden" name='folderName' value="{$FOLDERNAME}"/>
<input type="hidden" name='folderId' value="{$FOLDERID}"/>
<input type="hidden" name='startIndex' value="{$INDEX}"/>
<input type="hidden" name='listLimit' value="{$INDEX}"/>
<div class='foldersData dragfile ' {if $FOLDERID} data-parent-folder="{$FOLDERID}" {/if}>
	{if !empty($FOLDERS)}
		<input type="hidden" name='scrollevent' value="{if count($FOLDERS) >= $INDEX}1{else}0{/if}" />
		{foreach item="FOLDER" from=$FOLDERS}
			{if $MODE neq 'openFolderFiles'}
				{assign var=FOLDERNAME value={vtranslate($FOLDER->get('folder_name'), $MODULE)}}
				{assign var=owner value=getSingleFieldValue('vtiger_crmentity', 'smcreatorid', 'crmid', $FOLDER->getId())}
				<div class="col-md-3  folderFiles {if $owner eq $CURRENT_USER_MODEL->id} folderActions {/if}" title="{$FOLDERNAME}" data-folderid="{$FOLDER->getId()}" style="padding:5px;cursor:pointer;" >
					<div class="pull-left" ><img style="border-radius:10px;" src="layouts/v7/skins/images/Folder.jpg" /> </div>
					<span style='font-size:12px;{if $owner eq $CURRENT_USER_MODEL->id}color:#15c !important;{/if}'>{substr($FOLDERNAME,0,30)}</span></br><span class="fieldLabel">File Folder</span>
				</div>
			{else if $MODE eq 'openFolderFiles'}
				{if $FOLDER['type'] eq 'folder'}
					{assign var=owner value=getSingleFieldValue('vtiger_crmentity', 'smcreatorid', 'crmid', $FOLDER['id'])}
	                 <div class="col-md-3 folderFiles {if $owner eq $CURRENT_USER_MODEL->id} folderActions {/if}" title="{$FOLDER['text']}" data-folderid="{$FOLDER['id']}" style="padding:5px;cursor:pointer;" >
						<div class="pull-left"><img style="border-radius:10px;" src="layouts/v7/skins/images/Folder.jpg" /> </div>
						<span style='font-size:11px;{if $owner eq $CURRENT_USER_MODEL->id}font-weight:bold;color:#596875;{/if}'>{substr($FOLDER['text'],0,30)}</span></br><span class="fieldLabel">File Folder</span>
					</div>
	            {else if $FOLDER['type'] eq 'file'}
	                 <div class="col-md-3 fileDrag" id="fileDrag" title="{$FOLDER['text']}" data-fileid="{$FOLDER['id']}" style="padding:5px;cursor:pointer;" >
						<div class="pull-left"><img style="border-radius:10px;" src="layouts/v7/skins/images/{$FOLDER['icon']}" /> </div>
						<span style='font-size:11px;padding:2px;'>
							<a href="javascript:void(0)" data-filelocationtype="{$FOLDER['fileLocation']}" data-filename="{$FOLDER['fileName']}" >
								{substr($FOLDER['text'],0,30)}
							</a>
						</span></br><span class="fieldLabel"style="padding:2px;">{$FOLDER['fileType']}</span>
					</div>
	           {/if}
			{/if}	
		{/foreach}
	{else}
		<div class="col-md-12 emptyRecordsDiv " style="padding:20% 0;">
			<div class="emptyRecordsContent">
				{vtranslate('LBL_NO')} {vtranslate('Data', $MODULE)} {vtranslate('LBL_FOUND')}.
				{if $IS_CREATE_PERMITTED}
					<a style="color:blue" class="createFolder"> {vtranslate('LBL_CREATE')}</a>
					{vtranslate('Folder', $MODULE)}
				{/if}
			</div>
		</div>
	{/if}	
</div>