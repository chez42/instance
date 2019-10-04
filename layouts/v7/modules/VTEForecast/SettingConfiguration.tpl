{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
 <div class='container-fluid editViewContainer'>
         <form class="form-horizontal" action="index.php" id="configuration_form">                
				 <div name='massEditContent' class="row">
					<div class="modal-body">
                        <div class="row form-group">
                            <label class="col-lg-3 control-label">
                                &nbsp;<strong>{vtranslate('Amount field', 'VTEForecast')}</strong>
                            </label>
                            <div class="col-lg-3">
                                <select class="form-control" name="amount_field" id="amount_field" >
                                    {foreach item=ITEM from=$FIELDS}
                                        <option value="{$ITEM->value}" {if $CONFIG_PARAMS['amount_field'] eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 control-label">
                                &nbsp;<strong>{vtranslate('Datetime field', 'VTEForecast')}</strong>
                            </label>
                            <div class="col-lg-3">
                                <select class="form-control" name="date_field" id="date_field" >
                                    {foreach item=ITEM from=$FIELDS}
                                        <option value="{$ITEM->value}" {if $CONFIG_PARAMS['date_field'] eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 control-label">
                                &nbsp;<strong>{vtranslate('Period', 'VTEForecast')}</strong>
                            </label>
                            <div class="col-lg-3">
                                <label class="control-label">
                                    <input type="radio" name="forecast_period" value="0" {if $CONFIG_PARAMS['forecast_period'] eq '0'}checked{/if}>
                                    &nbsp;{vtranslate('Monthly', 'VTEForecast')}
                                </label>&nbsp;&nbsp;
                                <label class="control-label">
                                    <input type="radio" name="forecast_period" value="1" {if $CONFIG_PARAMS['forecast_period'] eq '1'}checked{/if}>
                                    &nbsp;{vtranslate('Quarterly', 'VTEForecast')}
                                </label>&nbsp;&nbsp;
                                <label class="control-label">
                                    <input type="radio" name="forecast_period" value="2" {if $CONFIG_PARAMS['forecast_period'] eq '2'}checked{/if}>
                                    &nbsp;{vtranslate('Yearly', 'VTEForecast')}
                                </label>
                            </div>
                        </div>

                        <div class="row form-group">
                            <label class="col-lg-3 control-label">
                                &nbsp;<strong>{vtranslate('Number of Periods', 'VTEForecast')}</strong>
                            </label>
                            <div class="col-lg-3">
                                <select class="form-control" name="number_of_periods_yearly" id="number_of_periods_yearly" {if $CONFIG_PARAMS['forecast_period'] neq '2'}style="display:none"{/if} >
                                    <option value="4" {if $CONFIG_PARAMS['number_of_periods'] eq '4'}selected{/if}>4</option>
                                    <option value="6" {if $CONFIG_PARAMS['number_of_periods'] eq '6'}selected{/if}>6</option>
                                    <option value="8" {if $CONFIG_PARAMS['number_of_periods'] eq '8'}selected{/if}>8</option>
                                </select>
                                <select class="form-control" name="number_of_periods_quarterly" id="number_of_periods_quarterly" {if $CONFIG_PARAMS['forecast_period'] neq '1'}style="display:none"{/if} >
                                    <option value="4" {if $CONFIG_PARAMS['number_of_periods'] eq '4'}selected{/if}>4</option>
                                    <option value="6" {if $CONFIG_PARAMS['number_of_periods'] eq '6'}selected{/if}>6</option>
                                    <option value="8" {if $CONFIG_PARAMS['number_of_periods'] eq '8'}selected{/if}>8</option>
                                    <option value="12" {if $CONFIG_PARAMS['number_of_periods'] eq '12'}selected{/if}>12</option>
                                    <option value="18" {if $CONFIG_PARAMS['number_of_periods'] eq '18'}selected{/if}>18</option>
                                </select>
                                <select class="form-control" name="number_of_periods_month" id="number_of_periods_month" {if $CONFIG_PARAMS['forecast_period'] neq '0'}style="display:none"{/if} >
                                    <option value="12" {if $CONFIG_PARAMS['number_of_periods'] eq '12'}selected{/if}>12</option>
                                    <option value="18" {if $CONFIG_PARAMS['number_of_periods'] eq '18'}selected{/if}>18</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <label class="col-lg-3 control-label">
                                &nbsp;<strong>{vtranslate('FY starts in', 'VTEForecast')}</strong>
                            </label>
                            <div class="col-lg-2">
                                <select class="form-control" name="financial_year_starts_in_month">
                                    <option value="1" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '1'}selected{/if}>January</option>
                                    <option value="2" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '2'}selected{/if}>February</option>
                                    <option value="3" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '3'}selected{/if}>March</option>
                                    <option value="4" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '4'}selected{/if}>April</option>
                                    <option value="5" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '5'}selected{/if}>May</option>
                                    <option value="6" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '6'}selected{/if}>June</option>
                                    <option value="7" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '7'}selected{/if}>July</option>
                                    <option value="8" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '8'}selected{/if}>August</option>
                                    <option value="9" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '9'}selected{/if}>September</option>
                                    <option value="10" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '10'}selected{/if}>October</option>
                                    <option value="11" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '11'}selected{/if}>November</option>
                                    <option value="12" {if $CONFIG_PARAMS['financial_year_starts_in_month'] eq '12'}selected{/if}>December</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select class="form-control" name="financial_year_starts_in_year">
                                    {foreach item=ITEM_YEAR from=$CONFIG_YEARS}
                                        <option value="{$ITEM_YEAR}" {if $ITEM_YEAR eq $CONFIG_PARAMS['financial_year_starts_in_year']}selected{/if}>
                                            {$ITEM_YEAR}</option>
                                    {/foreach}
                                </select>
                            </div>
						</div>
                        
                        <div class="row form-group">
                            <label class="col-lg-3 control-label">
                                &nbsp;<strong>{vtranslate('Show Other Users', 'VTEForecast')}</strong>
                            </label>
                            <div class="col-lg-2">
                                <input type="checkbox" name="show_other_users" id="show_other_users" value="1" {if $CONFIG_PARAMS['show_other_users'] eq '1'}selected{/if}/>
                            </div>
                        </div>

					</div>	
				</div>
				<div class="text-center">
					<button class="btn btn-success" id="btnSave" type="button">{vtranslate('LBL_SAVE','VTEForecast')}</button>
					<a class="btn btn-default" href="index.php?module=VTEForecast&view=List" >View Forecast</a>
				</div>
			</div>
            </form>	
</div>				
{/strip}