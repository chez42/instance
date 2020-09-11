{foreach key=index item=cssModel from=$CSS}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
    
<div class="modal-dialog modal-lg">
    <div class="modal-content">
    	{assign var=HEADER_TITLE value={vtranslate('Upload Documents', $MODULE)}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
    	<div class="modal-body">
    		<div class="row" style="margin:10px;">
    			<div class="col-md-3 muted">
    				<label> Select Folder </label>
    			</div>
    			<div class="col-md-9">
    				<select name="folder_id" class="select2 inputElement folder_id">
    					 <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
    					 {foreach item=FOLDER_NAME key=FOLDER_VALUE from=$FOLDER_VALUES}
							<option value="{$FOLDER_VALUE}">{$FOLDER_NAME}</option>
						 {/foreach}
    				</select>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-md-12">
					<div class="add_doc_modal" style="margin-left:7%;"></div>
				</div>
			</div>
    	</div>
    	<div class="modal-footer">
    	</div>
	</div>
</div>