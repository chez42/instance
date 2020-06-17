{strip}
	<div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" id="portal_reset_password" name="portal_reset_password" method="post" action="index.php">
                <input type="hidden" name="module" value="{$MODULE}" />
            	<input type="hidden" name="record" value="{$RECORD}" />
            
                {assign var=HEADER_TITLE value={vtranslate('LBL_CHANGE_PASSWORD', $MODULE)}}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
                
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">
                            {vtranslate('LBL_NEW_PASSWORD',$MODULE)}
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-lg-6">
                        	<input type="password" class="form-control" name="new_password" data-rule-required="true"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">
                        	{vtranslate('LBL_CONFIRM_PASSWORD', $MODULE)}
                    		<span class="redColor">*</span>
                    	</label>
                        <div class="col-lg-6">
                            <input type="password" class="form-control" name="confirm_password" data-rule-required="true"/>
                        </div>
                    </div>
                </div>
                {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
            </form>
        </div>
    </div>
{/strip}
