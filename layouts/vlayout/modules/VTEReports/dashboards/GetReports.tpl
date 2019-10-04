{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
<script type="text/javascript" src="layouts/vlayout/modules/VTEReports/resources/ChartWidgetDisplay.js"></script>
<script type="text/javascript">
	Vtiger_ChartWidgetDisplay_Widget_Js('Vtiger_ChartWidgetDisplay_Widget_Js',{},{});
</script>

<div class="dashboardWidgetHeader">
	{include file="dashboards/Header.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
</div>
<div class="dashboardWidgetContent">
	{include file="dashboards/Contents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>