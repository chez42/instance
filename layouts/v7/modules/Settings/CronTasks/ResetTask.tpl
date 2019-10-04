{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
    <div class="modal-dialog modelContainer">
        {assign var=HEADER_TITLE value={vtranslate($RECORD_MODEL->get('name'), $QUALIFIED_MODULE)}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
        <div class="modal-content">
            <form class="form-horizontal" id="cronJobSaveAjax" method="post" action="index.php">
                <input type="hidden" name="module" value="{$MODULE}" />
                <input type="hidden" name="parent" value="Settings" />
                <input type="hidden" name="action" value="ResetTask" />
                <input type="hidden" name="record" value="{$RECORD}" />
                <input type="hidden" name="laststart" value="0" />
                <input type="hidden" name="lastend" value="0" />

                <div class="modal-body tabbable">
                    <div class="control-group">
                        <div class="control-label">Reset Task? </div>
                        <div class="controls">
                            <input type="checkbox" name="resettask" />
                        </div>
                    </div>
                    <div class="alert alert-info">Resetting will set the last start/end times to 0.  When the workflow CRON re-runs (every minute) again, this task will fire</div>
                </div>
                {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
            </form>
        </div>
    </div>

{/strip}