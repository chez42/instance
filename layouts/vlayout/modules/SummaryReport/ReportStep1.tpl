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
    <div class="reportContents">
        <form class="form-horizontal recordEditView" id="report_step1" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="view" value="Edit" />
            <input type="hidden" name="mode" value="step2" />
            <input type="hidden" class="step" value="1" />
            <input type="hidden" name="record" value="{$RECORD}" />
            <div class="well contentsBackground">
                <div class="row-fluid padding1per">
                    <span class="span3">{vtranslate('LBL_REPORTNAME',$MODULE)}<span class="redColor">*</span></span>
                    <span class="span7 row-fluid"><input class="span6" data-validation-engine='validate[required]' type="text" name="reportname" value="{$REPORT_MODEL->get('reportname')}"/></span>
                </div>
                <div class="row-fluid padding1per">
                    <span class="span3">{vtranslate('LBL_SELECT_MODULE',$MODULE)}<span class="redColor">*</span></span>
					<span class="span7 row-fluid">
						<select class="chzn-select span6" name="selected_module" id="primary_module">
                            {foreach item=SELECT_MODULE from=$SUPPORTED_MODULES name=moduleIterator}
                                <option value="{$SELECT_MODULE}" {if $SELECT_MODULE eq $REPORT_MODEL->get('modulename')}selected{/if}>
                                    {vtranslate($SELECT_MODULE, $SELECT_MODULE)}</option>
                            {/foreach}
                        </select>
					</span>
                </div>
                <div class="row-fluid padding1per">
                    <span class="span3">{vtranslate('LBL_REPORT_TYPE',$MODULE)}</span>
					<span class="span7 row-fluid">
						<select class="chzn-select span6" name="reporttype" id="reporttype">
                            <option value="LBL_STANDARD" {if $REPORT_MODEL->get('reporttype') eq 'LBL_STANDARD'}selected{/if}>{vtranslate('LBL_STANDARD', $MODULE)}</option>
                            <option value="LBL_TABULAR" {if $REPORT_MODEL->get('reporttype') eq 'LBL_TABULAR'}selected{/if}>{vtranslate('LBL_TABULAR', $MODULE)}</option>
                        </select>
					</span>
                </div>
                <div class="row-fluid padding1per" id="fields">
                    {include file='Fields.tpl'|@vtemplate_path:$MODULE}
                </div>
                <div class="row-fluid padding1per">
                    <span class="span3">{vtranslate('LBL_DESCRIPTION',$MODULE)}</span>
                    <span class="span7"><textarea class="span6" type="text" name="description" >{$REPORT_MODEL->get('description')}</textarea></span>
                </div>
            </div>
            <div class="pull-right">
                <button type="submit" class="btn btn-success"><strong>{vtranslate('LBL_SAVE',$MODULE)}</strong></button>&nbsp;&nbsp;
                <a onclick='window.history.back()' class="cancelLink cursorPointer">{vtranslate('LBL_CANCEL',$MODULE)}</a>
            </div>
        </form>
    </div>
{/strip}