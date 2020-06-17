{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Summary Report ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div class="reportContents" style="padding: 2%;">
        <form class="form-horizontal recordEditView" id="report_step1" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="view" value="Edit" />
            <input type="hidden" name="mode" value="step2" />
            <input type="hidden" class="step" value="1" />
            <input type="hidden" name="record" value="{$RECORD}" />
            <hr>
            <div class=" contentsBackground" style="text-align: center">
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        {vtranslate('LBL_REPORTNAME',$MODULE)}
                        <span class="redColor">*</span>
                    </label>
                    <div class="col-lg-4">
                        <input class="inputElement" data-rule-required="true" type="text" name="reportname" value="{$REPORT_MODEL->get('reportname')}"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">
                        {vtranslate('LBL_SELECT_MODULE',$MODULE)}<span class="redColor">*</span>
                    </label>
                    <div class="col-sm-5 controls">
                            <select class="select2 col-sm-6 pull-left" name="selected_module" id="primary_module" data-rule-required="true" data-placeholder="Select Module..." style="text-align: left">
                                {foreach item=SELECT_MODULE from=$SUPPORTED_MODULES name=moduleIterator}
                                    <option value="{$SELECT_MODULE}" {if $SELECT_MODULE eq $REPORT_MODEL->get('modulename')}selected{/if}>
                                        {vtranslate($SELECT_MODULE, $SELECT_MODULE)}</option>
                                {/foreach}
                            </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status" class="col-sm-3 control-label">
                        {vtranslate('LBL_REPORT_TYPE',$MODULE)}
                    </label>
                    <div class="col-sm-5 controls">
                        <select class="chzn-select select2 col-sm-6 pull-left" name="reporttype" id="reporttype" style="text-align: left">
                            <option value="LBL_STANDARD" {if $REPORT_MODEL->get('reporttype') eq 'LBL_STANDARD'}selected{/if}>{vtranslate('LBL_STANDARD', $MODULE)}</option>
                            <option value="LBL_TABULAR" {if $REPORT_MODEL->get('reporttype') eq 'LBL_TABULAR'}selected{/if}>{vtranslate('LBL_TABULAR', $MODULE)}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="fields">
                    {include file='Fields.tpl'|@vtemplate_path:$MODULE}
                </div>
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">
                        {vtranslate('LBL_DESCRIPTION',$MODULE)}
                    </label>
                    <div class="col-sm-5 controls">
                        <textarea class="form-control" name="description" id="description">{$REPORT_MODEL->get('description')}</textarea>
                    </div>
                </div>
            </div>
            <div class='modal-overlay-footer clearfix modal-footer-overwrite-style'>
                <div class="row clearfix">
                    <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                        <button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                        <a class='cancelLink' href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/strip}