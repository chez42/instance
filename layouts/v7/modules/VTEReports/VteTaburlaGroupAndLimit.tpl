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
        <table class="table table-bordered table-report">
            <tbody>
            <tr>
                <td style="text-align: right;vertical-align: middle;">
                    {vtranslate('LBL_SORT_FIELD',$MODULE)}&nbsp;
                </td>
                <td style="text-align: left;vertical-align: middle;">
                    <div class="select-style" style="float: left;margin-right: 20px;width: 260px;">
                        <select id="SortByColumn" name="SortByColumn" class="txtBox" style="width:261px!important;margin:auto;" >
                            <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                            {$BLOCK3}
                        </select>
                    </div>
                    <div style="margin-top: 11px;">
                        {$COLUMNASCDESC}
                    </div>
                </td>
                <td style="text-align: right;vertical-align: middle;">
                    {vtranslate('Limit',$MODULE)}
                </td>
                <td style="text-align: left;vertical-align: middle;">
                    <input type="text" id="columns_limit" name="columns_limit" value="{if $COLUMNS_LIMIT!="0"}{$COLUMNS_LIMIT}{/if}" class="txtBox" style="width:100px;margin:auto;">&nbsp;&nbsp;<small>({vtranslate('Leave blank for no limit',$MODULE)})</small>
                </td>
            </tr>
            <tbody>
        </table>
    </div>
</div>
