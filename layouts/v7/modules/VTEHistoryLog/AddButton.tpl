{*/* * *******************************************************************************
* The content of this file is subject to the VTE History Log ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{if ($SOURCE_MODULE neq 'Leads' && $SOURCE_MODULE neq 'Accounts' && $SOURCE_MODULE neq 'Contacts') || $VTIGER_CURRENT_VERSION eq '7.1.0'}
	<div class="historyButtons btn-group" role="group" aria-label="...">
		<button type="button" class="btn btn-default btn-success" onclick='Vtiger_VTEAHistoryLog_Js.showUpdates(this);' disabled>
            {vtranslate("LBL_UPDATES",$MODULE_NAME)}
		</button>
	</div>
{/if}
<div class="vteHistoryButtons btn-group" role="group">
	<button type="button" class="btn btn-default btn-success" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLog(this);" data-automatically-show="{$AUTOMATICALLY_SHOW}">
		{vtranslate('LBL_HISTORY_BTN', $MODULE)}
	</button>
</div>
<div class="btn-group" role="group">
    <ul class="updates_timeline active_modules">
    {foreach item=ACTIVE_MODULE from=$ACTIVE_MODULES}
        <li {if $FILTER_MODULE eq $ACTIVE_MODULE} class="active" {/if} data-module="{$ACTIVE_MODULE}" title="{vtranslate($ACTIVE_MODULE, $ACTIVE_MODULE)}">
            <div class="update_icon bg-info bg-info-{$ACTIVE_MODULE|lower}">
				{if $ACTIVE_MODULE=='ModComments'}
					<i class='module-qtip update_image fa fa-comment'></i>
				{elseif $ACTIVE_MODULE=='Events'}
					<i class='module-qtip update_image vicon-calendar'></i>
				{else}
					<i class='module-qtip update_image vicon-{$ACTIVE_MODULE|lower}'></i>
				{/if}
            </div>
        </li>
    {/foreach}
    
        <a class="btnResetFilter" href="javascript: void(0);">{vtranslate('LBL_RESET_FILTER', $MODULE)}</a>
    </ul>
</div>