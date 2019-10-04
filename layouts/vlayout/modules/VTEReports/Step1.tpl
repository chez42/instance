{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
{assign var="custom_style" value=" style='' " }
{strip}
    <div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <table class="table table-bordered table-report">
                <tbody>
                    <tr style="height:25px">
                        <td class="fieldLabel medium" {$custom_style} >
                            <label class="pull-right marginRight10px">
                                {vtranslate('LBL_REPORT_NAME',$MODULE)}<span class="redColor">*</span>
                            </label>
                        </td>
                        <td {$custom_style} colspan="3" >
                            <input type="text" name="reportname" id="reportname" class="span12" maxlength="30"
                                   style="margin:auto;" value="{$REPORTNAME}">
                        </td>
                    </tr>
                    <tr style="height:25px">
                        <td class="fieldLabel medium" {$custom_style} ><label class="pull-right marginRight10px">{vtranslate('LBL_DESCRIPTION',$MODULE)}</label></td>
                        <td align="left" {$custom_style} colspan="3" ><textarea name="reportdesc" id="reportdesc" class="span12" rows="5">{$REPORTDESC}</textarea></td>
                    </tr>
                    <tr style="height:25px">
                        <td class="fieldLabel medium" {$custom_style} ><label class="pull-right marginRight10px">{vtranslate('LBL_PRIMARY_MODULE',$MODULE)}</label></td>
                        <td {$custom_style} >
                            <input name="report_primarymodule" id="report_primarymodule" type="hidden" >
                            <div class="select-style">
                                <select name="primarymodule" id="primarymodule" class="span3"  style="margin:auto;">
                                    {foreach item=primarymodulearr from=$PRIMARYMODULES}
                                        {if $RECORD_MODE == "ChangeSteps" || $MODE == "edit"}
                                            {if $primarymodulearr.selected!=''}
                                                <option value="{$primarymodulearr.id}" selected >{$primarymodulearr.module}</option>
                                            {/if}
                                        {else}
                                            <option value="{$primarymodulearr.id}" {if $primarymodulearr.selected!=''}selected{/if} >{$primarymodulearr.module}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>
                        </td>
                        <td class="fieldLabel medium" {$custom_style} ><label class="pull-right marginRight10px">{vtranslate('LBL_REP_FOLDER',$MODULE)}</label></td>
                        <td style="padding-right: 26px !important;" >
                            <input name="is_over_step1" id="is_over_step1" type="hidden" value = "0" >
                            <div class="select-style">
                            <select name="reportfolder" id="reportfolder" class="span3"  style="margin:auto;">
                                {foreach item=folder from=$REP_FOLDERS}
                                    <option value="{$folder.folderid}" {if $folder.selected!=''}selected{/if} >{$folder.foldername}</option>
                                {/foreach}
                            </select>
                            </div>
                            <div style="float: right;margin-top: -33px; margin-right: 95px;">
                                <a id = "addFolder" class="btn addButton" data-url="index.php?module=VTEReports&view=EditFolder">
                                    <i class="icon-plus"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
    <br />
{/strip}
