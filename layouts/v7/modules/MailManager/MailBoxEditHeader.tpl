{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{strip}

{strip}
 <div class="modal-dialog modal-lg">
	 <div class="modal-content">
		 {foreach key=index item=jsModel from=$SCRIPTS}
			<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
		{/foreach}
	 	<div class="modal-header">
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
			<div class="editViewPageDiv mailBoxEditDiv viewContent row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<input type="hidden" id="create" value="{$CREATE}" />
					<input type="hidden" id="recordId" value="{$RECORD_ID}" />
					<input type="hidden" id="step" value="{$STEP}" />
					<h4>
						{if $CREATE eq 'new'}
							{vtranslate('LBL_ADDING_NEW_MAILBOX', $QUALIFIED_MODULE_NAME)}
						{else}
							{vtranslate('LBL_EDIT_MAILBOX', $QUALIFIED_MODULE_NAME)}
						{/if}
					</h4>
					<hr>
					<div class="editViewContainer" style="padding-left: 2%;padding-right: 2%">
						<div class="row">
							{assign var=BREADCRUMB_LABELS value = ["step1" => "MAILBOX_DETAILS", "step2" => "SELECT_FOLDERS"]}
							{if $CREATE eq 'new'}
								{append var=BREADCRUMB_LABELS index=step3 value=ADD_RULES}
							{/if}
							
							<div id="{$BREADCRUMB_ID}" class="breadcrumb">
								<ul class="crumbs">
									{assign var=ZINDEX value=9}
									{foreach key=CRUMBID item=STEPTEXT from=$BREADCRUMB_LABELS name=breadcrumbLabels}
										{assign var=INDEX value=$smarty.foreach.breadcrumbLabels.index}
										{assign var=INDEX value=$INDEX+1}
										<li class="step {if $smarty.foreach.breadcrumbLabels.first} first {$FIRSTBREADCRUMB} {else} {$ADDTIONALCLASS} {/if} {if $smarty.foreach.breadcrumbLabels.last} last {/if} {if $ACTIVESTEP eq $INDEX}active{/if}"
											id="{$CRUMBID}" data-value="{$INDEX}" style="z-index:{$ZINDEX}">
											<a href="#">
												<span class="stepNum">{$INDEX}</span>
												<span class="stepText" title="{vtranslate($STEPTEXT,$QUALIFIED_MODULE_NAME)}">{vtranslate($STEPTEXT, $QUALIFIED_MODULE_NAME)}</span>
											</a>
										</li>
										{assign var=ZINDEX value=$ZINDEX-1}
									{/foreach}
								</ul>
							</div>
						</div>
						<div class="clearfix"></div>
{/strip}