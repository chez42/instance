{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/Export.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
	<div class="fc-overlay-modal modal-content">
		<form id="exportForm" class="form-horizontal" method="post" action="index.php">
			<input type="hidden" name="module" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="action" value="JournalExportData" />
			<input type="hidden" name="record" value="{$RECORD_ID}" />
			<input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
			<input type="hidden" name="page" value='{$PAGE}' />
			
			<div class="overlayHeader">
				{assign var=TITLE value="{vtranslate('LBL_EXPORT_RECORDS',$MODULE)}"}
				{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
			</div>

			<div class="modal-body" style="margin-bottom:250px">
				<div class="datacontent row">
					<div class="col-lg-3"></div>
					<div class="col-lg-6">
						<div class="well exportContents">
							
							<br><div><b>{vtranslate('LBL_EXPORT_DATA',$MODULE)}</b></div><br>
							<div style="margin-left: 50px;">
								
								<div>
									<input type="radio" name="mode" value="ExportCurrentPage" id="group2" style="margin:2px 0 -4px" />
									<label style="font-weight:normal" for="group2">&nbsp;&nbsp;{vtranslate('LBL_EXPORT_DATA_IN_CURRENT_PAGE',$MODULE)}</label>
								</div>
								<br>
								<div>
									<input type="radio" name="mode" value="ExportAllData" id="group3" {if empty($SELECTED_IDS)} checked="checked" {/if} style="margin:2px 0 -4px" />
									<label style="font-weight:normal" for="group3">&nbsp;&nbsp;{vtranslate('LBL_EXPORT_ALL_DATA',$MODULE)}</label>
								</div>
								
							</div>
							<br>
						</div>
					</div>
					<div class="col-lg-3"></div>
				</div>
			</div>
			<div class="modal-overlay-footer clearfix">
				<div class="row clearfix">
					<div class=" textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
						<div><button type="submit" class="btn btn-success btn-lg">{vtranslate('LBL_EXPORT', 'Vtiger')}</button>
							&nbsp;&nbsp;&nbsp;<a class="cancelLink" data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
{/strip}