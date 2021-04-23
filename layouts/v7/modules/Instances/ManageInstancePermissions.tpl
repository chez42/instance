<div id="instanceUserContainer" class='modal-md modal-dialog'>

    <div class = "modal-content">

        {assign var=TITLE value="{vtranslate('Manage Instance Permissions', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="SavePermissions" method="post" action="index.php">
        
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="action" value="SaveInstancePermissions" />
            <input type="hidden" name="record" value="{$RECORD}" />
            
            <div class="modal-body">
	            <div style="overflow:auto;max-height:250px;">
	            	<table id="listview-table" class="table table-striped table-hover " >
	            		<tbody class="overflow-y">
							<tr>
								<td style = "width:25%">Portfolio Reports</td>
								<td><input type = "checkbox" name = "portfolio_reports" {if $PORTFOLIO_REPORTS eq 1} checked {/if}/></td>
							</tr>
						</tbody>
	            	</table>
	        	</div>
            </div>
            
            <div class="modal-footer">
                <center>
                    <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                    <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </center>
            </div>
            	
        </form>

    </div>

</div>
