{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

{strip}
{assign var="ROWS_COUNT1" value=$ROWS_COUNT }
{assign var="ROWS_COUNT2" value=$ROWS_COUNT+1 }
{assign var="COL_SPAN" value=2 }
{if $ROWS_COUNT>20}
    {math assign="ROWS_COUNT1" equation="($ROWS_COUNT1/2)+2"}
    {math assign="ROWS_COUNT2" equation="($ROWS_COUNT2/2)+2"}
    {assign var="COL_SPAN" value=$COL_SPAN+2 }
{/if}
<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <input type="hidden" name="labels_to_go" id="labels_to_go" value="_XYZ_">
            <table class="table table-bordered table-report" id="vte_set_column_name">
                <thead>
                    <tr class="blockHeader">
                        <th> {vtranslate("Field",$MODULE)}</th>
                        {if $REPORT_TYPE == "tabular"}
                            <th> {vtranslate("Label",$MODULE)}</th>
                            <th> {vtranslate("Highlight",$MODULE)}</th>
                            <th> {vtranslate("Sum",$MODULE)}</th>
                            <th> {vtranslate("Min",$MODULE)}</th>
                            <th> {vtranslate("Avg",$MODULE)}</th>
                            <th colspan="2"> {vtranslate("Max",$MODULE)}</th>
                        {else}
                            <th colspan="7"> {vtranslate("Label",$MODULE)}</th>
                        {/if}
                    </tr>
                </thead>
                <tbody> 
                {assign var="ROWS_I" value=0 }
                {foreach key=lbl_type item=type_arr from = $labels_html}
                    {if $lbl_type == "SC"}
                        {assign var="type_row_lbl" value=vtranslate("LBL_SC_LABELS",$MODULE)}
                    {elseif $lbl_type == "SM"}
                        {assign var="type_row_lbl" value=vtranslate("LBL_SM_LABELS",$MODULE)}
                    {/if}
                    {assign var="ROWS_I" value=$ROWS_I+1 }
                    {assign var="make_row" value=1 }
                    {foreach key=fieldkey item=fieldinput from=$type_arr}
                        {if $ROWS_COUNT>20}
                            {if $make_row == 1}
                                <tr style="height:25px">
                                {/if}
                                <td class="dvtCellLabel" align="left" colspan="1"><b>{$fieldkey}</b></td>
                                <td class="dvtCellInfo" align="left" colspan="1">{$fieldinput['input_html']}</td>
                                {if $REPORT_TYPE == "tabular"}
                                    <td class="dvtCellInfo" align="center" colspan="1"><input type="checkbox" name="{$input_name}_highlight" {$fieldinput['is_highlight']} ></td>
                                    {assign var="input_name" value = $fieldinput['input_name'] }
                                    {foreach key=rowname item=calculations from=$CAL_BLOCK}
                                        {foreach item=checkbox from=$calculations}
                                            {if $checkbox.name|strstr:$input_name}
                                                <td class="dvtCellInfo" align="center" ><input name="{$checkbox.name}" type="checkbox" {$checkbox.checked} value=""></td>
                                            {/if}
                                        {/foreach}
                                    {/foreach}
                                {/if}
                                {if $make_row == 2}
                                    {assign var="make_row" value=1 }
                                </tr>
                            {else}
                                {assign var="make_row" value=$make_row+1 }
                            {/if}
                        {else}
                            <tr style="height:25px" class="drag_to_sort_order" data-id="{$fieldinput['input_name']}">
                                <td class="dvtCellLabel" align="left" colspan="1"><b>{$fieldkey}</b></td>
                                <td class="dvtCellInfo td_field_input_control" align="left" colspan="1">{$fieldinput['input_html']}</td>
                                {if $REPORT_TYPE == "tabular"}
                                    {assign var="input_name" value = $fieldinput['input_name'] }
                                    <td class="dvtCellInfo" align="center" colspan="1"><input type="checkbox" name="{$input_name}_highlight" {$fieldinput['is_highlight']} ></td>
                                    {foreach key=rowname item=calculations from=$CAL_BLOCK}
                                        {foreach item=checkbox from=$calculations}
                                            {if $checkbox.name|strstr:$input_name}
                                                <td class="dvtCellInfo" align="center" ><input name="{$checkbox.name}" type="checkbox" {$checkbox.checked} value=""></td>
                                            {/if}
                                        {/foreach}
                                    {/foreach}
                                {/if}
                                <td class="dvtCellInfo" style="text-align: right;" colspan="5"><a href="javascript:void(0);" class="removeSelectedField"><i title="Delete" class="icon-trash alignMiddle"></i></a></td>
                            </tr>
                        {/if}
                        {assign var="ROWS_I" value=$ROWS_I+1 }
                    {/foreach}
                {/foreach}
                </tbody> 
            </table>
        </div>
    </div>
</div>
{/strip} 
<script>
   jQuery(document).ready(function() {
        var lineItemTable = jQuery("#vte_set_column_name");
        lineItemTable.sortable({
            containment : lineItemTable,
            items: 'tr.drag_to_sort_order',
            cursor:'move',
            axis: 'y',
            start: function(event, ui){
                ui.item.addClass('myDragClass');
            },
            stop: function(event, ui){
                ui.item.removeClass('myDragClass');
            }
        });
    });
</script>