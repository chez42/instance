{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Google Address ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
<div id="massEditContainer" class='modal-dialog modal-lg' style="width: 600px;">
    <div id="massEdit" class="modal-content">
        <div class="modal-header">
            <div class="clearfix">
                <div class="pull-right "><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
                </div><h4 class="pull-left">{if $RECORD}{vtranslate('LBL_EDIT')}{else}{vtranslate('LBL_ADD')}{/if} {vtranslate('GoogleAddress', 'GoogleAddress')}</h4>
            </div>
        </div>
        <form class="form-horizontal" id="editAddress">
            <input type="hidden" name="record" value="{$RECORD}" />

            <div name='massEditContent' class="row-fluid" style="position: relative; overflow: scroll;  width: auto;height: 500px; overflow-x: hidden">
                <div class="modal-body">
                    <table class="massEditTable table no-border">
                        <tr>
                            <td class="fieldLabel col-lg-2">
                                <label class="muted pull-right">
                                    <span class="redColor">*</span>&nbsp;{vtranslate('LBL_ADDRESS_NAME', 'GoogleAddress')}
                                </label>
                            </td>
                            <td class="fieldValue col-lg-3">
                                <input type="text" class="inputElement" name="address_name" value="{$ADDRESS_DETAIL['address_name']}" data-validation-engine='validate[required]]' />
                            </td>
                            <td class="fieldValue col-lg-2"></td>
                        </tr>
                        <tr class="module_row">
                            <td class="fieldLabel col-lg-2">
                                <label class="muted pull-right">
                                    <span class="redColor">*</span>&nbsp;{vtranslate('LBL_MODULE', 'GoogleAddress')}
                                </label>
                            </td>
                            <td class="fieldValue col-lg-3">
                                <select class="inputElement select2" type="picklist" name="select_module" data-validation-engine='validate[required]]'>
                                    {foreach item=MODULE from=$LIST_MODULE name=moduleIterator}
                                        <option value="{$MODULE}" {if $MODULE eq $ADDRESS_DETAIL['module_name']}selected{/if}>
                                            {vtranslate($MODULE, $MODULE)}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td class="fieldValue col-lg-2"></td>
                        </tr>

                        {include file='MappingFields.tpl'|@vtemplate_path:'GoogleAddress'}

                        <tr>
                            <td class="fieldLabel col-lg-2">
                                <label class="muted pull-right">
                                    &nbsp;{vtranslate('LBL_STATUS', 'GoogleAddress')}
                                </label>
                            </td>
                            <td class="fieldValue col-lg-3">
                                <select class="inputElement select2 " name="status" type="picklist">
                                    <option value="Active" {if $ADDRESS_DETAIL['status'] eq 'Active'}selected{/if}>Active</option>
                                    <option value="Inactive" {if $ADDRESS_DETAIL['status'] eq 'Inactive'}selected{/if}>Inactive</option>
                                </select>
                            </td>
                            <td class="fieldValue col-lg-2"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <center>
                <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </center>
            </div>
        </form>
    </div>
</div>
{/strip}