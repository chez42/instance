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
<div id="massEditContainer" class='modelContainer'>
    <div id="massEdit">
        <div class="modal-header contentsBackground">
            <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="massEditHeader">{if $RECORD}{vtranslate('LBL_EDIT')}{else}{vtranslate('LBL_ADD')}{/if} {vtranslate('GoogleAddress', 'GoogleAddress')}</h3>
        </div>
        <form class="form-horizontal" id="editAddress">
            <input type="hidden" name="record" value="{$RECORD}" />

            <div name='massEditContent' class="row-fluid" style="position: relative; overflow: scroll;  width: auto;
    height: 500px; overflow-x: hidden">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="muted control-label">
                            <span class="redColor">*</span>&nbsp;{vtranslate('LBL_ADDRESS_NAME', 'GoogleAddress')}
                        </label>
                        <div class="controls row-fluid">
                            <input type="text" class="span6" name="address_name" value="{$ADDRESS_DETAIL['address_name']}" data-validation-engine='validate[required]]' />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="muted control-label">
                            &nbsp;{vtranslate('LBL_MODULE', 'GoogleAddress')}
                        </label>
                        <div class="controls row-fluid">
                            <select class="select2 span6" name="select_module" data-validation-engine='validate[required]]'>
                                {*<option value="">Select a module</option>*}
                                {foreach item=MODULE from=$LIST_MODULE name=moduleIterator}
                                    <option value="{$MODULE}" {if $MODULE eq $ADDRESS_DETAIL['module_name']}selected{/if}>
                                        {vtranslate($MODULE, $MODULE)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div id="mapping_fields">
                        {include file='MappingFields.tpl'|@vtemplate_path:'GoogleAddress'}
                    </div>
                    <div class="control-group">
                        <label class="muted control-label">
                            &nbsp;{vtranslate('LBL_STATUS', 'GoogleAddress')}
                        </label>
                        <div class="controls row-fluid">
                            <select class="select2 span6" name="status">
                                <option value="Active" {if $ADDRESS_DETAIL['status'] eq 'Active'}selected{/if}>Active</option>
                                <option value="Inactive" {if $ADDRESS_DETAIL['status'] eq 'Inactive'}selected{/if}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
                    <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
                <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
            </div>
        </form>
    </div>
</div>
{/strip}