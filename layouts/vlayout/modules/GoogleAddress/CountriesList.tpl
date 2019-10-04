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
                <h3 id="massEditHeader">{vtranslate('LBL_SELECTED_COUNTRIES', 'GoogleAddress')}</h3>
            </div>
        </div>
        <form class="form-horizontal" id="editCountries">
            <input type="hidden" name="mode" value="saveCountries">
            <div name='massEditContent' class="row-fluid">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="muted control-label">
                            <span class="redColor">*</span>&nbsp;{vtranslate('LBL_COUNTRIES', 'GoogleAddress')}
                        </label>
                        <div class="controls row-fluid">
                            <select class="select2" multiple="true" id="countriesList" name="countries[]" data-placeholder="Select Countries" style="width: 400px">
                                <option value="All" {if in_array('All', $SELECTED_COUNTRIES)}selected{/if}>{vtranslate('LBL_ALL', 'GoogleAddress')}</option>
                                {foreach key=CODE item=NAME from=$ARR_COUNTRIES}
                                    <option value="{$CODE}" {if in_array($CODE, $SELECTED_COUNTRIES)}selected{/if}>
                                        {$NAME}
                                    </option>
                                {/foreach}
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
{/strip}