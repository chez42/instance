{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
<div class="row-fluid" style="margin-top:20px;">
    <div class="span12">
        <table class="table table-bordered table-report table-filter">
            <tr>
                <td>
                    <div class="row-fluid span5" style="float: left;">
                        <input type="hidden" name='all_related_modules' id='all_related_modules' value="{$REL_MODULES_STR}"/>
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="select-style cb-small">
                                                    <select id="timeline_type1" name="timeline_type1" class="txtBox" readonly style="float:left;width:100px;margin:auto;">
                                                        <option value="rows" selected="selected" >{vtranslate('LBL_ROWS',$MODULE)}</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-medium">
                                                    <select id="Group1" name="Group1" class="span5"  style="margin:auto;float:left;" onChange="checkTimeLineColumn(this,1)">
                                                        <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                                                        {$FT_BLOCK1}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-small2">
                                                    {$ASCDESC1}
                                                    <div id="radio_group1" style="margin:auto;float:left;">{$timelinecolumn1_html}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr id="group2_table_row">
                                <td>
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="select-style cb-small">
                                                    <select id="timeline_type2" name="timeline_type2" class="txtBox" {$reporttype_readonly} style="float:left;width:7em;margin:auto;">
                                                        {if $REPORTTYPE != "summaries_matrix"}
                                                            <option value="rows" {if $timeline_type2=="rows"}selected="selected"{/if} >{vtranslate('LBL_ROWS',$MODULE)}</option>
                                                        {/if}
                                                        {if $REPORTTYPE != "summaries"}
                                                            <option value="cols" {if $timeline_type2=="cols"}selected="selected"{/if} >{vtranslate('LBL_COLS',$MODULE)}</option>
                                                        {/if}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-medium">
                                                    <select id="Group2" name="Group2" class="span5" style="margin:auto;float:left;" onChange="checkTimeLineColumn(this,2)">
                                                        <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                                                        {$FT_BLOCK2}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-small2">
                                                    {$ASCDESC2}
                                                    <div id="radio_group2" style="margin:auto;float:left;">{$timelinecolumn2_html}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr id="group3_table_row">
                                <td>
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="select-style cb-small">
                                                    <select id="timeline_type3" name="timeline_type3" class="txtBox" {$reporttype_readonly} style="float:left;width:7em;margin:auto;">
                                                        <option value="rows" {if $timeline_type3=="rows"}selected="selected"{/if} >{vtranslate('LBL_ROWS',$MODULE)}</option>
                                                        {if $REPORTTYPE != "summaries" && $REPORTTYPE != "summaries_matrix"}
                                                            <option value="cols" {if $timeline_type3=="cols"}selected="selected"{/if} >{vtranslate('LBL_COLS',$MODULE)}</option>
                                                        {/if}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-medium">
                                                    <select id="Group3" name="Group3" class="span5" style="margin:auto;float:left;"  onChange="checkTimeLineColumn(this,3)">
                                                        <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                                                        {$FT_BLOCK3}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-small2">
                                                    {$ASCDESC3}
                                                    <div id="radio_group3" style="margin:auto;float:left;">{$timelinecolumn3_html}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr id="group4_table_row">
                                <td>
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="select-style cb-small">
                                                    <select id="timeline_type4" name="timeline_type4" class="txtBox" {$reporttype_readonly} style="float:left;width:7em;margin:auto;">
                                                        <option value="rows" {if $timeline_type4=="rows"}selected="selected"{/if} >{vtranslate('LBL_ROWS',$MODULE)}</option>
                                                        {if $REPORTTYPE != "summaries" && $REPORTTYPE != "summaries_matrix"}
                                                            <option value="cols" {if $timeline_type4=="cols"}selected="selected"{/if} >{vtranslate('LBL_COLS',$MODULE)}</option>
                                                        {/if}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-medium">
                                                    <select id="Group4" name="Group4" class="span5" style="margin:auto;float:left;"  onChange="checkTimeLineColumn(this,4)">
                                                        <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                                                        {$FT_BLOCK4}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="select-style cb-small2">
                                                    {$ASCDESC3}
                                                    <div id="radio_group4" style="margin:auto;float:left;">{$timelinecolumn4_html}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row-fluid span10" style="float: right;width: 600px;">
                        <table class="table table-report">
                            <tbody>
                            <tr>
                                <td style="text-align: right;vertical-align: middle;">
                                    {vtranslate('SUMMARIES_ORDER_BY',$MODULE)}&nbsp;
                                </td>
                                <td style="text-align: left;vertical-align: middle;">
                                    <table>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="summaries_orderby_columnString" id="summaries_orderby_columnString" value="{$summaries_orderby}">
                                            </td>
                                            <td>
                                                <div class="select-style" style="height: 37px;width: 188px;">
                                                    <select id="summaries_orderby_column" class="txtBox" style="width:auto;margin:auto;" >
                                                        <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                                                        {$RG_BLOCK6}
                                                    </select>&nbsp;
                                                </div>
                                            </td>
                                            <td>
                                                <input type="hidden" name="summaries_orderby_typeString" id="summaries_orderby_typeString" value="{$summaries_orderby_type}">
                                                <input type='radio' name='summaries_orderby_type' id='summaries_orderby_asc' {if $summaries_orderby_type=="ASC"}checked="checked"{/if} $TimeLineColumnD value='ASC' style="margin:auto;">&nbsp;{vtranslate('Ascending',$MODULE)}&nbsp;
                                                <input type='radio' name='summaries_orderby_type' id='summaries_orderby_desc' {if $summaries_orderby_type=="DESC"}checked="checked"{/if} $TimeLineColumnW value='DESC' style="margin:auto;">&nbsp;{vtranslate('Descending',$MODULE)}&nbsp;
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;vertical-align: middle;">
                                    {vtranslate('Limit',$MODULE)}
                                </td>
                                <td style="text-align: left;vertical-align: middle;">
                                    <input type="text" id="summaries_limit" name="summaries_limit" value="{if $SUMMARIES_LIMIT!="0"}{$SUMMARIES_LIMIT}{/if}" class="txtBox" style="width:100px;vertical-align: middle;margin-left:25px;;">&nbsp;&nbsp;<small>({vtranslate('Leave blank for no limit',$MODULE)})</small>
                                </td>
                            </tr>
                            <tbody>
                        </table>
                        <br />
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
