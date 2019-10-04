<div class="dashboardWidgetHeader">
    {include file="dashboards/Header.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div style="overflow: scroll;height: 86%;">
    {if $REPORT_TYPE == "tabular"}
        <table class="table table-container listViewEntriesTable">
            <thead>
            <tr class="listViewHeaders">
                {foreach item=HEADER from=$DATA_REPORTS['headers']}
                    <th class="medium">
                        {$HEADER}
                    </th>
                {/foreach}
            </tr>
            </thead>
            <tbody>
            {for $index = 0 to $LIMIT_ROW -1}
                <tr class="VTEReports_listView_Widget" style="cursor: pointer;" data-recordurl="index.php?module=VTEReports&view=Detail&record={$REPORTS['id']}" class="listViewEntries">
                    {if $DATA_REPORTS['data'][$index]|@count gt 1}
                        {foreach item=DATA from=$DATA_REPORTS['data'][$index]}
                            <td class="medium">{$DATA}</td>
                        {/foreach}
                    {/if}
                </tr>
            {/for}
            </tbody>
        </table>
    {else}
        {$DATA_REPORTS[0]}
    {/if}
</div>
<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:"Vtiger"}
    </div>
</div>
<script>
    jQuery('.no-print').remove();
    jQuery('.vteGrpHeadInfo').closest('table').remove();
    jQuery('a[name="widgetFullScreen"]').remove();
</script>

