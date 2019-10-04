{*/* * *******************************************************************************
* The content of this file is subject to the VTE History Log ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="vteHistoryButtons btn-group" role="group" style="padding-bottom: 15px;">
	<button type="button" class="btn btn-default btn_updates" style="float: none; margin-right: 5px;" onclick='Vtiger_VTEAHistoryLog_Js.showUpdates(this);' disabled>
        {vtranslate("LBL_UPDATES",$MODULE_NAME)}
	</button>
	<button type="button" class="btn btn-default btn-success btn_history_updates" style="float: none;" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLog(this);" data-automatically-show="{$AUTOMATICALLY_SHOW}">
        {vtranslate('LBL_HISTORY_BTN', $MODULE)}
	</button>
</div>