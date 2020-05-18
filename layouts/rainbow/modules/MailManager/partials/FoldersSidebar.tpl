<div id="modules-menu" class="modules-menu mmModulesMenu" style="width: 100%;">
        <div>
        	<button href="javascript:void(0);" data-toggle="dropdown" class="btn btn-default" style="margin-left: 4px;">
				<span><i class="material-icons">email</i>&nbsp;<span class="mailUserName">{$MAILBOX->username()}</span>&nbsp;<i class="caret"></i></span>
			</button>
			<ul class="dropdown-menu pull-left mailManagerDropDown" style="top: auto;left: auto;">
				<li>
					<a href="#" data-boxid="" class="mailbox_setting" style="text-transform:unset;color:black!important;font-weight:bold!important;"> 
						<i class="fa fa-plus"></i>&nbsp; Add Mail Box
					</a>
				</li>
				{foreach item=MAILMODEL from=$MAILMODELS}
					<li>
						<a href="#" class="openMailId" data-boxid="{$MAILMODEL['account_id']}" style="text-transform:unset;color:black!important;font-weight:bold!important;"> 
							{$MAILMODEL['account_name']}
							<i class="fa fa-trash pull-right deleteMailManager" id="deleteMailboxBtn" title="Delete MailBox" data-boxid="{$MAILMODEL['account_id']}"></i>&nbsp;
							<i class="fa fa-pencil pull-right mailbox_setting" title="Edit MailBox" data-boxid="{$MAILMODEL['account_id']}"></i>&nbsp;
						</a>
					</li>
				{/foreach}
			</ul>
            {*<span><i class="material-icons">email</i>&nbsp;{$MAILBOX->username()}</span>*}<br/><br/>
            <div class="btn-group">
                <span class="cursorPointer mailbox_refresh CountBadge btn btn-success btn-sm" title="{vtranslate('LBL_Refresh', $MODULE)}" tippytitle data-toggle="toolstip" data-original-title="{vtranslate('LBL_Refresh', $MODULE)}" data-tippy aria-describedby="tippy-1">
                    <i class="material-icons">refresh</i>
                </span>
                 
                {*<span class="cursorPointer mailbox_setting CountBadge btn btn-info btn-sm" title="{vtranslate('JSLBL_Settings', $MODULE)}" tippytitle data-toggle="toolstip" data-original-title="{vtranslate('JSLBL_Settings', $MODULE)}" data-tippy aria-describedby="tippy-2">
                    <i class="material-icons">settings</i> 
                </span>*}
                <span id="mail_compose" class="btn btn-danger cursorPointer btn-sm" title="{vtranslate('LBL_Compose', $MODULE)}" tippytitle data-toggle="toolstip" data-original-title="{vtranslate('LBL_Compose', $MODULE)}" data-tippy aria-describedby="tippy-3">
            <i class="material-icons">create</i> 
                </span>
                
            </div>
        <div id='folders_list'></div>
        </div>
</div>