{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}

{literal}
<style>
	
	#mails_container, .sidebar-essentials{
		margin-top: 0px !important;
	}	
	@media(min-width: 768px){
		.main-container-MailManager .listViewPageDiv{
			padding-left: 230px !important;
		}
	}
</style>
{/literal}
    <div id="modules-menu" class="modules-menu mmModulesMenu" style="width: 100%;">
        <div><span>{$MAILBOX->username()}</span>
            <span class="pull-right">
                <span class="cursorPointer mailbox_refresh CountBadge label label-rouded label-success" title="{vtranslate('LBL_Refresh', $MODULE)}">
                    <i class="fa fa-refresh"></i>
                </span>
                &nbsp;
                <span class="cursorPointer mailbox_setting CountBadge label label-rouded label-info" title="{vtranslate('JSLBL_Settings', $MODULE)}">
                    <i class="fa fa-cog"></i>
                </span>
            </span>
        </div>
        <div id="mail_compose" class="btn btn-danger btn-block cursorPointer">
            <i class="ti-pencil-alt"></i>&nbsp;{vtranslate('LBL_Compose', $MODULE)}
        </div>
        <div id='folders_list'></div>
    </div>
{/strip}