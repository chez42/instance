{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
{if $display_widget_header == 'true'}
    <script type="text/javascript" src="layouts/v7/modules/VTEReports/resources/ChartWidgetDisplay.js"></script>
    <script type="text/javascript">
            Vtiger_ChartWidgetDisplay_Widget_Js('Vtiger_ChartWidgetDisplay_Widget_Js',{},{});
    </script>
    
    <div class="dashboardWidgetHeader">
        {include file="dashboards/Header.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
{/if}
<div style="height:5px;"></div>
{strip}
    <input class="widgetData" type='hidden' value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
    <input id="widgetvtereportsId" type='hidden' value='{$recordid}' />

    <div id="vtereports_widget_{$recordid}" style="height:83%;width:100%;margin:auto;" ></div>
    {$DATA}
    <div class="widgeticons dashBoardWidgetFooter">
        <div class="footerIcons pull-right">
            {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:"Vtiger"}
        </div>
    </div>
{/strip}
<script>
    jQuery('.no-print').remove();
    jQuery('.vteGrpHeadInfo').closest('table').remove();
    jQuery('a[name="widgetFullScreen"]').remove();
    var container = jQuery(".dashBoardTabContents");
    container.on(Vtiger_Widget_Js.widgetPostResizeEvent, function(e) {
        jQuery(Highcharts.charts).each(function(i,chart){
            if(typeof chart !== "undefined"){
                var height = chart.renderTo.clientHeight;
                var width = chart.renderTo.clientWidth;
                chart.setSize(width, height);
            }
        });
    });
</script>
