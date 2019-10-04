{*/* * *******************************************************************************
* The content of this file is subject to the VTE History Log ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <form action="index.php?module=VTEHistoryLog&parent=Settings&view=Settings" method="post">
        <div class="contentHeader row-fluid">
            <div class="span12 btn-toolbar">
                <div class="pull-right">
                    <button class="btn btn-success saveButton" type="submit" title="{vtranslate('LBL_SAVE',$QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_SAVE',$QUALIFIED_MODULE)}</strong></button>
                </div>
            </div>
        </div>
        <hr>
        <div class="clearfix"></div>

        <div class="listViewContentDiv row-fluid" id="listViewContents">
            <table class="table table-bordered table-condensed themeTableColor">
                <tbody>
                <tr>
                    <td class="medium" nowrap="">
                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_LIMIT_SHOW_FIELD',$QUALIFIED_MODULE)}</label>
                    </td>
                    <td class="medium" style="border-left: none;">
                        <input type="text" value="{$SETTINGS.limit_field}" class="inputElement" name="settings[limit_field]"/>
                    </td>
                    <td class="medium" style="border-left: none;">
                        {vtranslate('LBL_LIMIT_SHOW_FIELD_DESC',$QUALIFIED_MODULE)}
                    </td>
                </tr>
                <tr>
                    <td class="medium" nowrap="">
                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_LIMIT_CHARACTER',$QUALIFIED_MODULE)}</label>
                    </td>
                    <td class="medium" style="border-left: none;">
                        <input type="text" value="{$SETTINGS.limit_character}" class="inputElement" name="settings[limit_character]"/>
                    </td>
                    <td class="medium" style="border-left: none;">
                        {vtranslate('LBL_LIMIT_CHARACTER_DESC',$QUALIFIED_MODULE)}
                    </td>
                </tr>
                <tr>
                    <td class="medium" nowrap="">
                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_MODULE_ACTIVE',$QUALIFIED_MODULE)}</label>
                    </td>
                    <td class="medium" style="border-left: none;">
                        <select name="settings[modules][]" class="chzn-select inputElement" multiple>
                            {foreach item=MODULE_INFO from=$LIST_MODULE}
                            <option value="{$MODULE_INFO.name}" {if in_array($MODULE_INFO.name, $SETTINGS.modules)} selected{/if}>{vtranslate($MODULE_INFO.tablabel, $MODULE_INFO.name)}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td class="medium" style="border-left: none;">
                        {vtranslate('LBL_MODULE_ACTIVE_DESC',$QUALIFIED_MODULE)}
                    </td>
                </tr>
                <tr>
                    <td class="medium" nowrap="">
                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_MILTIPLE_RELATION_TYPE',$QUALIFIED_MODULE)}</label>
                    </td>
                    <td class="medium" style="border-left: none;">
                        <select name="settings[multiple_relation_type]" class="chzn-select inputElement">
                            <option value="0" {if $SETTINGS.multiple_relation_type neq 1} selected{/if}>{vtranslate('LBL_NO', $QUALIFIED_MODULE)}</option>
                            <option value="1" {if $SETTINGS.multiple_relation_type eq 1} selected{/if}>{vtranslate('LBL_YES', $QUALIFIED_MODULE)}</option>
                        </select>
                    </td>
                    <td class="medium" style="border-left: none;">
                        {vtranslate('LBL_MILTIPLE_RELATION_TYPE_DESC',$QUALIFIED_MODULE)}
                    </td>
                </tr>
                <tr>
                    <td class="medium" nowrap="">
                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_AUTOMATICALLY_SHOW_COMPLETE_TIMELINE',$QUALIFIED_MODULE)}</label>
                    </td>
                    <td class="medium" style="border-left: none;">
                        <select name="settings[automatically_show]" class="chzn-select inputElement">
                            <option value="1" {if $SETTINGS.automatically_show eq 1} selected{/if}>{vtranslate('LBL_YES', $QUALIFIED_MODULE)}</option>
                            <option value="0" {if $SETTINGS.automatically_show neq 1} selected{/if}>{vtranslate('LBL_NO', $QUALIFIED_MODULE)}</option>
                        </select>
                    </td>
                    <td class="medium" style="border-left: none;">
                        {vtranslate('LBL_AUTOMATICALLY_SHOW_COMPLETE_TIMELINE_DESC',$QUALIFIED_MODULE)}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="module" value="VTEHistoryLog" />
        <input type="hidden" name="parent" value="Settings" />
        <input type="hidden" name="action" value="Save" />
    </form>
</div>

