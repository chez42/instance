    
<div class="modal-dialog modal-lg">
    <div class="modal-content">
    	{assign var=HEADER_TITLE value={vtranslate('Own Cloud Documents', $MODULE)}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
    	<div class="modal-body">
    		<input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
			<style>
				.row ul.jstree-children > li > ul > li:first-child {
				    margin-left: 18px;
				}
				.tree-body { max-width:100%;min-height:600px; min-width:100%; margin:0 auto; padding:20px 10px; font-size:14px; font-size:1em; }
				.demo { overflow:auto; border:1px solid silver; min-height:600px; }
				.vakata-context, .vakata-context ul{ z-index : 10001;}
			</style>
    		<div class="row" style="margin:10px;">
    			<div class="col-md-12">
					<div class="tree-body" >
					<div class="demo" id="tree_folder">
						
					</div>
				</div>
				</div>
			</div>
    	</div>
    	<div class="modal-footer">
    	</div>
	</div>
</div>