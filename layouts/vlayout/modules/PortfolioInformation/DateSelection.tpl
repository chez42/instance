<div id="dateselection">
    <select id="report_date_selection">
        {foreach key=index item=option from=$DATE_OPTIONS}
            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}">{$option.option_name}</option>
        {/foreach}
{*        <option value="lastyear">Last Year</option>
        <option value="trailing3">Last 3 Months</option>
        <option value="trailing6">Last 6 Months</option>
        <option value="trailing12">Last 12 Months</option>*}
    </select>
    {if $SHOW_START_DATE EQ 1}
        <input type="text" id="select_start_date" value="{$START_DATE}">
    {/if}
    {if $SHOW_END_DATE EQ 1}
        <input type="text" id="select_end_date" value="{$END_DATE}">
    {/if}
    <input type="button" id="calculate_report" value="Calculate" />
</div>