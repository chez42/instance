{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{strip}
 <script type="text/javascript">
        var related_uimeta = (function() {
            var fieldInfo = {$FIELDS_INFO};
            return {
                field: {
                    get: function(name, property) {
                        if (name && property === undefined) {
                            return fieldInfo[name];
                        }
                        if (name && property) {
                            return fieldInfo[name][property]
                        }
                    },
                    isMandatory: function(name) {
                        if (fieldInfo[name]) {
                            return fieldInfo[name].mandatory;
                        }
                        return false;
                    },
                    getType: function(name) {
                        if (fieldInfo[name]) {
                            return fieldInfo[name].type
                        }
                        return false;
                    }
                },
            };
        })();
    </script>
 <div class='fc-overlay-modal overlayDetail'>
	 <div class="modal-content" style="width:100%;">
		  <div class="overlayDetailHeader col-lg-12 col-md-12 col-sm-12" style="z-index:1;">
	        <div class="clearfix">
	            <div class="pull-right " >
	                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
	                    <span aria-hidden="true" class='fa fa-close'></span>
	                </button>
	            </div>
	            <h4>{vtranslate('MailConverter', $QUALIFIED_MODULE_NAME)}</h4>
	        </div>
	    </div>
		 <div class="modal-body">
			{foreach key=index item=jsModel from=$SCRIPTS}
				<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
			{/foreach}
			<input type="hidden" id="scannerId" value="{$SCANNER_ID}"/>
			<div class="row">
				<div class="col-lg-12">
					<div class="col-lg-4 mailBoxDropdownWrapper" style="padding-left: 0px;">
						
					</div>
					<div class="col-lg-4" id="mailConverterStats">
						{if $CRON_RECORD_MODEL->isEnabled()}
							{if $CRON_RECORD_MODEL->hadTimedout()}
								{vtranslate('LBL_LAST_SCAN_TIMED_OUT', $QUALIFIED_MODULE_NAME)}.
							{elseif $CRON_RECORD_MODEL->getLastEndDateTime() neq ''}
								{vtranslate('LBL_LAST_SCAN_AT', $QUALIFIED_MODULE_NAME)}
								{$CRON_RECORD_MODEL->getLastEndDateTime()}
								<br />
								{vtranslate('LBL_FOLDERS_SCANNED', $QUALIFIED_MODULE_NAME)}&nbsp;:&nbsp;
								{foreach from=$FOLDERS_SCANNED item=FOLDER}<strong>{$FOLDER}&nbsp;&nbsp;</strong>{/foreach}
							{/if}
						{/if}
					</div>
					<div class="col-lg-4" style="padding-right: 0px;">
						<div class="btn-group pull-right">
							<button class="btn btn-default addButton" id="addRuleButton" title="{vtranslate('LBL_DRAG_AND_DROP_BLOCK_TO_PRIORITISE_THE_RULE', $QUALIFIED_MODULE_NAME)}"
								{if stripos($SCANNER_MODEL->getCreateRuleRecordUrl(), 'javascript:')===0}
									onclick='{$SCANNER_MODEL->getCreateRuleRecordUrl()|substr:strlen("javascript:")}' 
								{else}
									onclick='window.location.href="{$SCANNER_MODEL->getCreateRuleRecordUrl()}"'
								{/if}>
								<i class="fa fa-plus"></i>&nbsp;&nbsp;{vtranslate('LBL_ADD_RULE', $QUALIFIED_MODULE_NAME)}
							</button>
						</div>
					</div>
				</div>
				<br>
				<div id="mailConverterBody" class="col-lg-12">
					<br>
					<div id="rulesList">
						{if count($RULE_MODELS_LIST)}
							{assign var=RULE_COUNT value=1}
							{assign var=FIELDS value=$MODULE_MODEL->getSetupRuleFields()}
							{foreach from=$RULE_MODELS_LIST item=RULE_MODEL}
								<div class="row-fluid padding-bottom1per rule" data-id="{$RULE_MODEL->get('ruleid')}" data-blockid="block_{$RULE_MODEL->get('ruleid')}">
									{include file="Rule.tpl"|vtemplate_path:$MODULE_NAME RULE_COUNT=$RULE_COUNT}
								</div>
								{assign var=RULE_COUNT value=$RULE_COUNT+1}
							{/foreach}
						{else}
							<div class="details border1px" style="text-align: center; min-height: 200px; padding-top: 100px;">
								{vtranslate('LBL_NO_RULES', $QUALIFIED_MODULE_NAME)}
							</div>
						{/if}
					</div>
				</div>
			</div>	
		</div>
	</div>
{/strip}
