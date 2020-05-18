{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div id="modules-menu" class="modules-menu mmModulesMenu" style="width: 100%;">
        <div>
        	<button href="javascript:void(0);" data-toggle="dropdown" class="btn btn-default" style="margin-left: 4px;">
				<span class="mailUserName">{$MAILBOX->username()}</span><i class="caret"></i>
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
							<i class="fa fa-trash pull-right deleteMailManager" id="deleteMailboxBtn" title="Delete MailBox" data-boxid="{$MAILMODEL['account_id']}" style="font-size: 14px;"></i>
							<i class="fa fa-pencil pull-right mailbox_setting" title="Edit MailBox" data-boxid="{$MAILMODEL['account_id']}" style="font-size: 14px;"></i>
						</a>
					</li>
				{/foreach}
			</ul>
        	{*<span>{$MAILBOX->username()}</span>*}
            <span class="pull-right">
                <span class="cursorPointer mailbox_refresh" title="{vtranslate('LBL_Refresh', $MODULE)}">
                    <i class="fa fa-refresh"></i>
                </span>
                &nbsp;
                {*<span class="cursorPointer mailbox_setting" title="{vtranslate('JSLBL_Settings', $MODULE)}">
                    <i class="fa fa-cog"></i>
                </span>*}
            </span>
        </div>
        <div id="mail_compose" class="cursorPointer">
            <i class="fa fa-pencil-square-o"></i>&nbsp;{vtranslate('LBL_Compose', $MODULE)}
        </div>
        <div id='folders_list'></div>
    </div>
{/strip}