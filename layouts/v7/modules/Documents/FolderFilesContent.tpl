<input type='hidden' name='scrollevent' value='{$COUNT}'>
{if !empty($FOLDERS)}
	{foreach item="FOLDER" from=$FOLDERS}
		<div class="col-md-3  fileDrag" id="fileDrag" title="{$FOLDER['text']}" data-fileid="{$FOLDER['id']}" style="padding:5px;cursor:pointer;" >
			<div class="pull-left"><img style="border-radius:10px;" src="{vimage_path($FOLDER['icon'])}" /> </div>
			<span style='font-size:11px;padding:2px;'>
				<a href="javascript:void(0)" data-filelocationtype="{$FOLDER['fileLocation']}" data-filename="{$FOLDER['fileName']}"  >
					{substr($FOLDER['text'],0,30)}
				</a>
			</span></br><span class="fieldLabel"style="padding:2px;">{$FOLDER['fileType']}</span>
		</div>
	{/foreach}
{/if}		