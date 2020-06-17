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
                <tbody>
                    <tr>
                        <td class="fieldLabel medium"><label class="pull-right marginRight10px">{vtranslate("LBL_TEMPLATE_OWNER",$MODULE)}</label></td>
                        <td>
                            <div class="select-style">
                                <select name="template_owner" id="template_owner" class="classname" style="margin:auto;">
                                    {html_options  options=$TEMPLATE_OWNERS selected=$TEMPLATE_OWNER}
                                </select>
                            </div>

                        </td>
                        <td class="fieldLabel span8" colspan="2">
                            <label style="float: left;margin-right: 10px; margin-top: 4px;">{vtranslate("LBL_SHARING_TAB",$MODULE)}</label>
                            <div class="select-style" style="margin-left: -10px;">
                                <select name="sharing" id="sharing" class="classname" onchange="sharing_changed(this);" style="margin:auto;">
                                    {html_options options=$SHARINGTYPES selected=$SHARINGTYPE}
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr id="sharing_share_div" class="hide">
                        <td class="fieldLabel medium"><label class="pull-right marginRight10px">{vtranslate("Group Members",$MODULE)}</label></td>
                        <td>
                            <select id="sharingSelectedColumns" name="sharingSelectedColumns" class="select2 span8" multiple>
                                {$TEMPLATE_GRMEMBERS}
                            </select>
                            <input id="sharingSelectedColumnsString" name="sharingSelectedColumnsString" value="" type="hidden">
                        </td>
                        <td colspan="2">
                            <table class="vte-table-colors">
                                <tr class="op-Users"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Users",$MODULE)}</p></td></tr>
                                <tr class="op-Groups"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Groups",$MODULE)}</p></td></tr>
                                <tr class="op-Roles"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Roles",$MODULE)}</p></td></tr>
                                <tr class="op-RoleAndSubordinates"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Role And Subordinates",$MODULE)}</p></td></tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>                 
