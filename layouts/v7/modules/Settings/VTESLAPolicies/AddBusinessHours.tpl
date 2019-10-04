{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/QuickCreateAjax.php *}

{strip}

	<div class="modal-dialog modal-lg" style="width: 550px;">
		<div class="modal-header">
			<div class="clearfix">
				<div class="pull-right ">
					<button type="button" class="close" aria-label="Close" data-dismiss="modal">
						<span aria-hidden="true" class="fa fa-close"></span>
					</button>
				</div>
				<h4 class="pull-left">Business Hours</h4>
				<input id='curentTimeHeader' readonly="readonly" value="" type='text' style="background-color: #596875;border: none;text-align: center;font-size: 14px;margin-top: 8px;    margin-left: 60px;"  data-fieldtype='date'  data-date-format='{$dateFormat}'>
			</div>
		</div>
		<div class="modal-content">
			<form class="form-horizontal recordAddBusinessHosurs" id="AddBusinessHours" name="AddBusinessHours" method="post" action="index.php">
				<div class="modal-body">
					<input type="hidden" name="module" id="module" value="{$MODULE}">
					<input type="hidden" name="action" value="SaveBusinessHour" />
					<input type="hidden" name="parent" value="Settings" />
					<div class="quickCreateContent">
						<table style="border-bottom: 1px solid #DDDDDD;text-align: center; width: 100%;" id="table-weekday">
                            {foreach item=BUSINESS_RECORDS_VALUE from=$BUSINESS_RECORDS['businessHour']}
								<tr name="business-hour">
									<td style="width: 67%;text-align: left;">
										<label class="lbl-check" style="font-family: 'OpenSans-Regular', sans-serif; margin-left: 50px;color: #6f6f6f; font-size: 15px;font-weight: normal">
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==1}Monday{/if}
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==2}Tuesday{/if}
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==3}Wednesday{/if}
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==4}Thursday{/if}
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==5}Friday{/if}
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==6}Saturday{/if}
                                            {if $BUSINESS_RECORDS_VALUE['busihourid']==7}Sunday{/if}
											<input type="checkbox" style="display: none"  class="check-dayofweek" {if $BUSINESS_RECORDS_VALUE['status']==1}checked="checked"{/if} name="dayofweek[]" value="{$BUSINESS_RECORDS_VALUE['busihourid']}" />
											<span class="checkmark" ></span>
											<input class="status_check" id="status" type="hidden" name="status[]" value="{if $BUSINESS_RECORDS_VALUE['status']==1}1{elseif $BUSINESS_RECORDS_VALUE['status']==0}0{/if}" />
										</label>
									</td>
									<td style="width: 15%;">
										<div class="input-group inputElement time" style="width: 105px;min-width: 0px;margin-bottom: 10px;" >
											<input type="text" data-format="12" class="timepicker-default form-control" data-rule-time="true" id="start" name="start[]" value="{$BUSINESS_RECORDS_VALUE['start']}" {if $BUSINESS_RECORDS_VALUE['status']==0}disabled{/if}/>
											<span class="input-group-addon" style="width: 30px;">
													<i class="fa fa-clock-o"></i>
												</span>
										</div>
									</td>
									<td style="width: 3%">
										-
									</td>
									<td style="width: 15%">
										<div class="input-group inputElement time" style="width: 105px;min-width: 0px;margin-bottom: 10px;">
											<input type="text" data-format="12" class="timepicker-default form-control" data-rule-time="true" name="end[]" value="{$BUSINESS_RECORDS_VALUE['end']}" id="end" {if $BUSINESS_RECORDS_VALUE['status']==0}disabled{/if}/>
											<span class="input-group-addon" style="width: 30px;">
													<i class="fa fa-clock-o"></i>
												</span>
										</div>
									</td>
								</tr>
                            {/foreach}
						</table>
						{assign var="dateFormat" value=$USER_MODEL->get('date_format')}

						<table style="margin-top: 15px;" class="table-holiday" id="holidays_actions">
							<thead>
							</thead>
							<tbody>
							{if $BUSINESS_RECORDS['businessHoliday']}
								{foreach item=BUSINESS_RECORDS_VALUE from=$BUSINESS_RECORDS['businessHoliday']}
									<tr sla-holiday-record-id='{$BUSINESS_RECORDS_VALUE["holidayid"]}'>
										<td style='width: 10%; text-align: center; color: red' >
											{if $BUSINESS_RECORDS_VALUE['default'] neq '1'}
												<button type='button' class='deleterow' style='border: none; background: #ffffff' >x</button>
											{/if}
											<input type="hidden" name="sla_holiday_id[]" value="{$BUSINESS_RECORDS_VALUE['holidayid']}">
										</td>
										<td style='width: 60%; '>
											<input type='text' name='sla_holiday_name[]'  value='{$BUSINESS_RECORDS_VALUE['holidayName']}' style="border-left: none;border-right: none; height: 30px; border-bottom: 1px solid #d9d9d9; border-top: 1px solid #d9d9d9;font-family: 'OpenSans-Regular', sans-serif; font-size: 15px"
													{if $BUSINESS_RECORDS_VALUE['default'] eq '1'} readonly="readonly" {/if}>
										</td>
										<td style='width: 30%  '>
											<div class='input-group inputElement {if $BUSINESS_RECORDS_VALUE['default'] eq '1'} force-disabled-region {/if}' style='margin-bottom: 3px' >
												<input id='dateValue' value="{$BUSINESS_RECORDS_VALUE['holidayDate']}" type='text' class='dateField form-control' data-fieldname='closingdate' data-fieldtype='date' name='sla_holiday_date[]' data-date-format='{$dateFormat}' data-rule-required='true' data-rule-date='true' aria-required='true'>
												<span class='input-group-addon'><i class='fa fa-calendar'></i></span>
											</div>
										</td>
									</tr>
								{/foreach}
							{/if}
							{if empty($BUSINESS_RECORDS['businessHoliday'])}
								<tr sla-holiday-record-id='{$BUSINESS_RECORDS_VALUE["holidayid"]}'>
									<td style='width: 10%; text-align: center; color: red' >
										{if $BUSINESS_RECORDS_VALUE['default'] neq '1'}
											<button type='button' class='deleterow' style='border: none; background: #ffffff' >x</button>
										{/if}
										<input type="hidden" name="sla_holiday_id[]" value="{$BUSINESS_RECORDS_VALUE['holidayid']}">
									</td>
									<td style='width: 60%; '>
										<input type='text' name='sla_holiday_name[]' value='New Years' style="border-left: none;border-right: none; height: 30px; border-bottom: 1px solid #d9d9d9; border-top: 1px solid #d9d9d9;font-family: 'OpenSans-Regular', sans-serif; font-size: 15px"
												{if $BUSINESS_RECORDS_VALUE['default'] eq '1'} readonly="readonly" {/if}>
									</td>
									<td style='width: 30%  '>
										<div class='input-group inputElement {if $BUSINESS_RECORDS_VALUE['default'] eq '1'} force-disabled-region {/if}' style='margin-bottom: 3px' >
											<input id='dateValue1' value="" type='text' class='dateField form-control' data-fieldtype='date' name='sla_holiday_date[]' data-date-format='{$dateFormat}' data-rule-required='true' data-rule-date='true' aria-required='true'>
											<span class='input-group-addon'><i class='fa fa-calendar'></i></span>
										</div>
									</td>
								</tr>
								<tr sla-holiday-record-id='{$BUSINESS_RECORDS_VALUE["holidayid"]}'>
									<td style='width: 10%; text-align: center; color: red' >
										{if $BUSINESS_RECORDS_VALUE['default'] neq '1'}
											<button type='button' class='deleterow' style='border: none; background: #ffffff' >x</button>
										{/if}
										<input type="hidden" name="sla_holiday_id[]" value="{$BUSINESS_RECORDS_VALUE['holidayid']}">
									</td>
									<td style='width: 60%; '>
										<input type='text' name='sla_holiday_name[]' value='Christmas' style="border-left: none;border-right: none; height: 30px; border-bottom: 1px solid #d9d9d9; border-top: 1px solid #d9d9d9;font-family: 'OpenSans-Regular', sans-serif; font-size: 15px"
												{if $BUSINESS_RECORDS_VALUE['default'] eq '1'} readonly="readonly" {/if}>
									</td>
									<td style='width: 30%  '>
										<div class='input-group inputElement {if $BUSINESS_RECORDS_VALUE['default'] eq '1'} force-disabled-region {/if}' style='margin-bottom: 3px' >
											<input id='dateValue2' value="" type='text' class='dateField form-control'  data-fieldtype='date' name='sla_holiday_date[]' data-date-format='{$dateFormat}' data-rule-required='true' data-rule-date='true' aria-required='true'>
											<span class='input-group-addon'><i class='fa fa-calendar'></i></span>
										</div>
									</td>
								</tr>
							{/if}
							</tbody>
						</table>
						<center style="margin-top: 25px;">
							<button type="button" id="addholiday" class="btn btn-default"><i class="fa fa-plus"></i> {vtranslate('Add New',$MODULE)}</button>
							<button type="submit" class="btn btn-success buttonSaveBusinessHour" style="margin-left: 5px">{vtranslate('Save',$MODULE)}</button>
							<a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('Cancel',$MODULE)}</a>
						</center>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}