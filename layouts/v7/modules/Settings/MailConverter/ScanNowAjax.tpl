{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="modelContainer modal-dialog modal-xs" style="width: 600px;">
		<div class="modal-content">
			 {foreach key=index item=jsModel from=$SCRIPTS}
				<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
			{/foreach}
			<form class="form-horizontal" id="scanRules" method="post" action="index.php">
				
				{assign var=TITLE value={vtranslate('Scan Settings', $QUALIFIED_MODULE)}}
				{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
				<input type="hidden" name="module" value="{$MODULE_NAME}" />
				<input type="hidden" name="parent" value="Settings" />
				<input type="hidden" name="action" value="ScanNow" />
				<input type="hidden" name="scannerId" value="{$SCANNER_ID}" />
				<input type="hidden" name="record" value="{$SCANNER_ID}" />
				<div class="addMailBoxStep modal-body">
					<table class="table editview-table no-border">
						<tbody>
							<tr class="row">
								<td class="col-lg-2 control-label"><label class="fieldLabel">Date</label>
								<td class="col-lg-10">
									<div class="input-group" style="min-width:100%;">
										<input type="text" class="fieldValue inputElement dateField"
										data-date-format="dd-mm-yyyy" data-calendar-type="range" name="date" value="" /> 
										<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
									</div>
									<!--<div class="input-daterange input-group dateRange widgetFilter dateField" id="datepicker" name="trade_date" data-date-format="dd-mm-yyyy">
										<input type="text" class="input-sm form-control" name="start" value="" style="height:30px;">
										<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
										<span class="input-group-addon">to</span>
										<input type="text" class="input-sm form-control" name="end" value="" style="height:30px;">
										<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
									</div>-->
									
								</td>
							</tr>
							<tr class="row">
								<td class="col-lg-2 control-label"><label class="fieldLabel">Subject</label>
								<td class="col-lg-10">
									<input type="text" class="fieldValue inputElement" name="subject" value="" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer ">
			        <center>
			            <button class="btn btn-success scanmails" onclick="Settings_MailConverter_Index_Js.scanMails()" type="button" name="scanButton"><strong>Scan Now</strong></button>
			            <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			        </center>
				</div>
			</form>
		</div>
	</div>
{/strip}
