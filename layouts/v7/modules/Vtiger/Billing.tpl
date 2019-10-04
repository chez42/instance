{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/MergeRecord.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
<script>
{*//    window.recordmodels_stored = {$RECORDMODELS_STORED};*}
//    console.log(recordmodels_stored);
</script>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

{literal}
    <style>
        #BillingInfo tbody tr:nth-child(even) {background: #FFF}
        #BillingInfo tbody tr:nth-child(odd) {background: #CCC}
        #BillingInfo thead {border-bottom:1px solid black}
        #BillingInfo td {padding:5px}
    </style>
{/literal}

<div class="fc-overlay-modal">
{*        <div class="overlayHeader">
            {assign var=TITLE value="Billing > {vtranslate($MODULE,$MODULE)}"}
            {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
        </div>*}
        <div class="overlayBody">
            <div class="container-fluid modal-body">
                <p>Note: Default calculations show as of the latest balance date calculated arrear quarterly</p>
                <p>As Of: <input type="text" id="as_of" value="{$AS_OF}" /></p>
                <form id="billingForm" method="post" target="_blank" action="index.php">
                    <input type="hidden" name="module" value="PortfolioInformation" />
                    <input type="hidden" name="action" value="CreateCSV" />
                    <input type="hidden" id="recordmodels" name="recordmodels" value="{$RECORDMODELS_STORED}" />
                    <input type="hidden" id="billreport" name="bill_report" value="" />
                    <input type="hidden" id="custodianselect" name="custodian_select" value="" />
                    <input type="hidden" id="filename" name="filename" value="" />
                </form>
                <table id="BillingInfo" style="width:100%;">
                    <thead>
                    <tr>
                        <td>Account #</td>
                        <td>Description</td>
                        <td>Fee %</td>
                        <td>Manual Fee Amt</td>
                        <td>Cycle</td>
                        <td>Term</td>
                        <td>In Arrears</td>
                        <td>In Advance</td>
                        <td>Total Value</td>
                        <td>Cash Available</td>
                        <td>As Of Date</td>
                        <td>Bill Amount</td>
                        <td>Rep Code</td>
                        <td>Omni Code</td>
                        <td>Custodian</td>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach item=RECORD from=$RECORDMODELS name=recordList}
                        <tr id="rowid_{$RECORD->getDisplayValue('account_number')}">
                            <td class="account_number">{$RECORD->getDisplayValue('account_number')}</td>
                            <td class="last_name">{$RECORD->getDisplayValue('last_name')}</td>
                            <td class="annual_fee_percentage">{$RECORD->getDisplayValue('annual_fee_percentage')}%</td>
                            <td class="cf_2656">{$RECORD->getDisplayValue('cf_2656')}</td>
                            <td class="periodicity">{$RECORD->getDisplayValue('periodicity')}</td>
                            <td class="cf_3476">{$RECORD->getDisplayValue('cf_3476')}</td>
                            <td class="in_arrears">${$RECORD->getDisplayValue('in_arrears')}</td>
                            <td class="in_advance"></td>
                            <td class="total_value" data-total_value="{$RECORD->getDisplayValue('total_value')}">${$RECORD->getDisplayValue('total_value')}</td>
                            <td class="cash" data-cash_value="{$RECORD->getDisplayValue('cash')}">${$RECORD->getDisplayValue('cash')}</td>
                            <td class="as_of" data-as_of="{$AS_OF}">{$AS_OF}</td>
                            <td class="bill_amount" data-bill_amount="{$RECORD->getDisplayValue('bill_amount')}">${$RECORD->getDisplayValue('bill_amount')}</td>
                            <td class="production_number" data-rep_code="{$RECORD->getDisplayValue('production_number')}">{$RECORD->getDisplayValue('production_number')}</td>
                            <td class="omniscient_control_number" data-omni_code="{$RECORD->getDisplayValue('omniscient_control_number')}">{$RECORD->getDisplayValue('omniscient_control_number')}</td>
                            <td class="origination" data-origination="{$RECORD->getDisplayValue('origination')}">{$RECORD->getDisplayValue('origination')}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                <div id="BillingExports">
                    <table id="BillingSelection" style="width:50%;">
 {*                       <thead>
                        <tr>
                            <td style="font-weight:bold; width:25%;">Filtering</td>
                            <td style="text-align:center; border-left:1px solid black; font-weight:bold;">Reporting</td>
                        </tr>
                        </thead>*}
{*                        <tbody>
                        <tr>
                            <td style="font-weight:bold;"><select id="custodian_select">
                                                            <option value="All">All</option>
                                                    {foreach item=v from=$CUSTODIANS}
                                                            <option value="{$v}">{$v}</option>
                                                    {/foreach}
                                                          </select>
                            </td>
                            <td style="border-left:1px solid black;">*}
                                <select id="bill_report">
                                    <option value="fees">Fees</option>
{*                                    <option value="prebilling">Pre-Billing</option>*}
                                </select>
{*                            </td>
                            <td>
                                <input type="text" value="{$DEFAULT_FILE}" class="filename" />.csv
                                <input type="button" value="Submit" class="billing_csv" />
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold" colspan="2"><select id="filter2">
                                                                        <option value="All">All</option>
                                                                        <option value="rep">Rep Code</option>
                                                                        <option value="omni">Omni Code</option>
                                                                     </select>
                            </td>
                        </tr>
                        </tbody>*}
                </div>
            </div>
        </div>
{*        <div class="overlayFooter">
            {include file="ModalFooter.tpl"|vtemplate_path:$MODULE}
        </div>*}
</div>
