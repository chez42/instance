{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

<div class="row-fluid">       
    <div class="span12">
        <div class="row-fluid">  
            <table class="table table-bordered table-report">
                <thead>
                    <tr class="blockHeader">
                       <th colspan="2">
                            {vtranslate('LBL_GRAPHS',$MODULE)}
                            <input type="hidden" name="none_chart" id="none_chart" value="{'LBL_SELECT_CHARTTYPE'|@getTranslatedString:'VTEReports'}">
                       </th>
                   </tr>
                </thead>
                <tbody> 

                    <tr style="height:25px">
                        <td class="medium"><label class="pull-right marginRight10px">{vtranslate('Chart title',$MODULE)}</label></td>
                        <td>
                            <input class="span5" id="charttitle" name="charttitle" value="{$charttitle}" type="text" placeholder="{'LBL_CHART_Title'|@getTranslatedString:'VTEReports'}" onblur="setChartTitle(this)">
                        </td>
                    </tr>
                    <tr style="height:25px">
                        <td class="medium"><label class="pull-right marginRight10px">{vtranslate('LBL_CHART_DataSeries',$MODULE)}</label></td>
                        <td>
                            <div class="select-style">
                                <select id="x_group" name="x_group" onchange="ChartDataSeries(this);">
                                    {foreach key=x_column_str item=x_column_arr from=$X_GROUP}
                                        <option value="{$x_column_str}" {$x_column_arr.selected}>{$x_column_arr.value}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr style="height:25px">
                        <td class="medium"><label class="pull-right marginRight10px">{vtranslate('Graph1',$MODULE)}</label></td>
                        <td>
                            {assign var="chtype1" value=$CHARTS_ARRAY.1.charttype}
                            <div class="select-style" style="float: left;margin-right: 10px;">
                                <select id="chartType1" name="chartType1">
                                    <option value="none">{'LBL_NONE'|@getTranslatedString:'VTEReports'}</option>
                                    {foreach key=chart_type_key item=charttype_arr from=$CHART_TYPE}
                                        <option value="{$chart_type_key}" {if $chart_type_key==$chtype1}selected='selected'{/if}>{$charttype_arr.value}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="select-style">
                                <select id="data_series1" name="data_series1">
                                    <option value="none">{'LBL_NONE'|@getTranslatedString:'VTEReports'}</option>
                                    {foreach key=column_i item=column_arr from=$selected_summaries}
                                        <option value="{$column_arr.value}" {if $column_arr.value==$CHARTS_ARRAY.1.dataseries}selected='selected'{/if}>{$column_arr.label}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr style="height:25px">
                        <td class="medium"><label class="pull-right marginRight10px">{vtranslate('Graph2',$MODULE)}</label></td>
                        <td>
                            <div class="select-style" style="float: left;margin-right: 10px;">
                                {assign var="chtype2" value=$CHARTS_ARRAY.2.charttype }
                                <select id="chartType2" name="chartType2">
                                    <option value="none">{'LBL_NONE'|@getTranslatedString:'VTEReports'}</option>
                                    {foreach key=chart_type_key item=charttype_arr from=$CHART_TYPE}
                                        <option value="{$chart_type_key}" {if $chart_type_key==$chtype2}selected='selected'{/if}>{$charttype_arr.value}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="select-style">
                                <select id="data_series2" name="data_series2">
                                    <option value="none">{'LBL_NONE'|@getTranslatedString:'VTEReports'}</option>
                                    {foreach key=column_i item=column_arr from=$selected_summaries}
                                        <option value="{$column_arr.value}" {if $column_arr.value==$CHARTS_ARRAY.2.dataseries}selected='selected'{/if}>{$column_arr.label}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr style="height:25px">
                        <td class="medium"><label class="pull-right marginRight10px">{vtranslate('Graph3',$MODULE)}</label></td>
                        <td>
                            <div class="select-style" style="float: left;margin-right: 10px;">
                                {assign var="chtype3" value=$CHARTS_ARRAY.3.charttype }
                                <select id="chartType3" name="chartType3">
                                    <option value="none">{'LBL_NONE'|@getTranslatedString:'VTEReports'}</option>
                                    {foreach key=chart_type_key item=charttype_arr from=$CHART_TYPE}
                                        <option value="{$chart_type_key}" {if $chart_type_key==$chtype3}selected='selected'{/if}>{$charttype_arr.value}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="select-style">
                                <select id="data_series3" name="data_series3">
                                    <option value="none">{'LBL_NONE'|@getTranslatedString:'VTEReports'}</option>
                                    {foreach key=column_i item=column_arr from=$selected_summaries}
                                        <option value="{$column_arr.value}" {if $column_arr.value==$CHARTS_ARRAY.3.dataseries}selected='selected'{/if}>{$column_arr.label}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>