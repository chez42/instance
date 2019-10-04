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
<div class="row-fluid">       
    <div class="span9">
        <div class="row-fluid">
            <table class="table table-bordered table-report">
                <thead>
                   <tr class="blockHeader">
                       <th colspan="5">
                            {vtranslate('LBL_CALCULATIONS',$MODULE)}
                            <input type="hidden" name="curl_to_go" id="curl_to_go" value="{$CURL}">
                       </th>
                   </tr>
                </thead>
                <tbody> 
        
                    <tr style="height:25px">
                            <td class="dvtCellLabel" nowrap width="26%" align="right" ><b>{vtranslate("LBL_COLUMNS", $MODULE)}</b></td>
                            <td class="dvtCellLabel" nowrap width="11%" align="center" ><b>{vtranslate("LBL_COLUMNS_SUM", $MODULE)}</b></td>
                            <td class="dvtCellLabel" nowrap width="11%" align="center" ><b>{vtranslate("LBL_COLUMNS_AVERAGE", $MODULE)}</b></td>
                            <td class="dvtCellLabel" nowrap width="11%" align="center" ><b>{vtranslate("LBL_COLUMNS_LOW_VALUE", $MODULE)}</b></td>
                            <td class="dvtCellLabel" nowrap width="11%" align="center" ><b>{vtranslate("LBL_COLUMNS_LARGE_VALUE", $MODULE)}</b></td>
                            {*<td class="dvtCellLabel" nowrap width="9%" align="center" ><b>{vtranslate("LBL_COLUMNS_COUNT", $MODULE)}</b></td>*}
                    </tr>
                    {foreach key=rowname item=calculations from=$BLOCK1}
                            <tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
                                <td class="dvtCellLabel" align="right" >{$rowname}</td>
                                {foreach item=checkbox from=$calculations}
                                        <td class="dvtCellInfo" align="center" ><input name="{$checkbox.name}" type="checkbox" {$checkbox.checked} value=""></td>
                                {/foreach}
                            </tr>
                    {foreachelse}
                            <tr class="lvtColData" bgcolor="white"><td colspan="5" align="center" style="text-align:center;font-size: 1.5em;width:100%;color:red;" ><b>{vtranslate("NO_CALCULATION_COLUMN", $MODULE)}</b></td></tr>
                    {/foreach}
                </tbody> 
            </table>
        </div>
    </div>
</div> 
{/strip}