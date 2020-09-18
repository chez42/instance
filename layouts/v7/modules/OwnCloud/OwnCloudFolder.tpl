    
<div class="modal-dialog modal-lg">
    <div class="modal-content">
    	{assign var=HEADER_TITLE value={vtranslate('Sync Documents', $MODULE)}}
    	
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
    	
    	<div class="modal-body">
    		
			<style>
				.row ul.jstree-children > li > ul > li:first-child {
				    margin-left: 18px;
				}
				.demo { border:1px solid silver; min-height:400px; max-height:400px;overflow:scroll;overflow-x: hidden; }
				.vakata-context, .vakata-context ul{ z-index : 10001;}
			</style>
    		
    		<input type = "hidden" name = "own_cloud_folder" id = "own_cloud_folder"/>
    		
    		<div class="row" style="margin:10px;">
    			<div class="col-md-12">
					<div class="demo" id="tree_folder">
						
					</div>
				</div>
			</div>
    	
    	</div>
    	
    	<div class="modal-footer">
			 <center>
                <button class="btn btn-default" id="syncDocuments" type="button"><strong>Sync Documents</strong></button>
                <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
            </center>
    	</div>
	</div>
</div>