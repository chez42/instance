<div class="dashboardWidgetHeader">
    {include file="dashboards/Header.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div style="overflow: scroll;">
    <table class="table table-bordered listViewEntriesTable">
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
</div>

