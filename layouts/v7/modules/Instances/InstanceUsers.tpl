<div id="instanceUserContainer" class='modal-lg modal-dialog'>

    <div class = "modal-content">

        {assign var=TITLE value="{vtranslate('Instance Users', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="massSaveInstance" method="post" action="index.php">
        
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="action" value="InstanceSave" />
            <input type="hidden" name="record" value="{$RECORD}" />
            
            {assign var=USERS value=array()}
            
            <div class="modal-body">
	            <div style="overflow:auto;max-height:250px;">
	            	<table id="listview-table" class="table table-striped table-hover " >
	            		<thead>
							<tr class="listViewContentHeader">
								<th>User Id</th>
								<th>User Name</th>
								<th>Full Name</th>
								<th>Cust#</th>
							</tr>
						</thead>
						<tbody class="overflow-y">
							{foreach item=LISTVIEW_ENTRY from=$INSTANCEUSERS name=listview}
								{$USERS[] = $LISTVIEW_ENTRY['id']}
								<tr class="listViewEntries">
									<td>{$LISTVIEW_ENTRY['id']}</td>
									<td>{$LISTVIEW_ENTRY['user_name']}</td>
									<td>{$LISTVIEW_ENTRY['first_name']} {$LISTVIEW_ENTRY['last_name']}</td>
									<td><input type="text" class="inputElement" name="{$LISTVIEW_ENTRY['id']}" value="{$LISTVIEW_ENTRY['advisor_control_number']}" /></td>
								</tr>
							{/foreach}
							<input type="hidden" name="users" value={ZEND_JSON::encode($USERS)} />
							
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
