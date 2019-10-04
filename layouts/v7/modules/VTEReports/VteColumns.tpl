{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

<script>
var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
</script>
<div class="row-fluid" id="div_tabular_step2">
    <div class="span12">
        <div class="row-fluid">  
            <table class="table table-bordered table-report" style="width:98%;">
                <tbody>
                    <tr>
                        <td style="text-align: center;padding: 20px;">
                            <div class="row-fluid padding1per" style="margin-left: 20%">
                                <div class="span8">
                                    <div style="height:40px;">
                                        <div style="float: left;padding-top: 10px;padding-right: 15px;">{vtranslate('LBL_SELECT_MODULE',$MODULE)}</div>
                                        <div class="select-style" style="float: left;width: 280px!important;">
                                            <select id="availModules" name="availModules" onchange='defineModuleFields(this)' class="txtBox" style="width:auto;margin:auto;">
                                                {foreach item=modulearr from=$availModules}
                                                    <option value={$modulearr.id} {if $modulearr.checked == "checked"}selected="selected"{/if} >{$modulearr.name}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <input type="text" id="search_input" onkeyup="getFieldsOptionsSearch(this)" placeholder="{vtranslate('LBL_Search_column',$MODULE)}" style="height: 38px;margin-left: -225px;">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div id="availModValues" style="display:none;">{$ALL_FIELDS_STRING}</div>
                            <input type="hidden" name="selectedColumnsString" id="selectedColumnsString">
                            <input type="hidden" name="selectedColumnsList" id="selectedColumnsList" value="{$BLOCK2}">
                            <div id="div_list_fields_column"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div id="div_tabular_limit" class="hide">
                {include file='modules/VTEReports/VteTaburlaGroupAndLimit.tpl'}
            </div>
            </div>
        </div>
</div> 
