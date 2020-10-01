{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{strip}
    <div class="modal-dialog modal-lg moduleContainer">
        <div class="modal-content">
            {assign var=MODAL_TITLE value=vtranslate('Select modules', $MODULE)}
            {include file="ModalHeader.tpl"|vtemplate_path:$SOURCE_MODULE TITLE=$MODAL_TITLE}
            <form id="moduleListEditView" method="POST">
            	<input type="hidden" name="activemodules" value='{json_encode($ACTIVEMODULES)}' />
            	<input type="hidden" name="allmodules" value='{json_encode($ALLMODULES)}' />
                <div class="modal-body" id="insselectmodules">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="fieldLabel width40per" style = "width:20%;">
                                    <label class="pull-right detailViewButtoncontainer">{vtranslate('Select Modules',$MODULE)}</label>
                                </td>
                                <td class="fieldValue selectModule">
                                    <select name="select_modules" class="select2 selectModules" multiple style="width:100%;">
                                        {foreach item=INMODULE from=$INSMODULES}
                                            <option value="{$INMODULE['id']}" {if !$INMODULE['ishide']}selected{/if}>{$INMODULE['name']}</option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" id="saveMailboxBtn" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE',$MODULE)}</strong></button>
                    <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
            </form>
        </div>
    </div>
{/strip}
