<div id="ranged_billing_wrapper">
    <div id="ranged_billing_header">
        <div id="title">{$BILLING->GetTitle()}</div>
        <div class="separator_green"></div>
        <table id="ranged_billing_table">
            <tbody>
                <tr>
                    <td class="width70">{$BILLING->GetClientName()}</td>
                    <td class="width20 align_right">Period Ending:&nbsp;</td>
                    <td class="width10 align_right">{$BILLING->GetEndPeriod()}</td>
                </tr>
                <tr>
                    <td>{$MODULE_NAME}</td>
                    <td class="align_right">Portfolio Inception Date:&nbsp;</td>
                    <td class="align_right">{$INCEPTION_DATE}</td>
                </tr>
            </tbody>
        </table>
        <div class="separator_gray"></div>
    </div>
    <div id="ranged_billing_body">
        <p>Statement for Services Rendered: {$BILLING->GetStartPeriod()} through {$BILLING->GetEndPeriod()}</p>
        <p>Billable Account Value as of {$BILLING->GetStartPeriod()}: ${$BILLING->GetStartDateValue()|number_format:2:".":","}</p>
        <div class="ranges">
            {foreach item=record from=$BILLING->GetPortfolios()}
                <p>Individualized Billable Account Value as of {$BILLING->GetStartPeriod()}: ${$BILLING->GetAccountInfoByNumber($record->get("account_number"))->start_amount|number_format:2:".":","} for account number {$record->get("account_number")}</p>
                <div class="ranged_portfolio">
                    <table class="ranged_table width50">
                        <thead>
                            <tr>
                                <th class="align_right">Asset Value Range</th>
                                <th class="align_right">Amount</th>
                                <th class="align_right">Rate</th>
                                <th class="align_right">Fee Amount</th>
                            </tr>
                        </thead>
                    {foreach key=k item=v from=$record->get("ranges") name=range_values}
                        <tbody>
                            {if $v.amount neq 0}
                            <tr>
                                <td class="align_right">${$v.range_start|number_format:2:".":","} to {if $v.range_end eq -1}{$BILLING->GetStartDateValue()|number_format:2:".":","}{else}${$v.range_end|number_format:2:".":","}{/if}</td>
                                <td class="align_right">${$v.bill_amount|number_format:2:".":","}</td>
                                <td class="align_right">{$v.rate|number_format:2:".":","}%</td>
                                <td class="align_right">${$v.amount|number_format:2:".":","}</td>
                            </tr>
                            {/if}
                            {if $smarty.foreach.range_values.last}
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                    <td class="align_right upper_line padded_top5 strong">${$record->get("bill_amount")|number_format:2:".":","}</td>
                                </tr>
                            {/if}
                        </tbody>
                    {/foreach}
                    </table>
                    <table class="flow_table ranged_table width50">
                        <thead>
                        <tr>
                            <th class="right">Date of Flow</th>
                            <th class="right">Fraction of Period</th>
                            <th class="right">Flow Amount</th>
                            <th class="right">Rate</th>
                            <th class="right">Adjustment Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach key=k item=v from=$record->get("capital_flows")}
                            <tr>
                                <td class="right">{$v->date}</td>
                                <td class="right">{$v->fraction} / {$v->period}</td>
                                <td class="right">${$v->flow|number_format:2:".":","}</td>
                                <td class="right">{$v->rate|number_format:2:".":","}%</td>
                                <td class="right">${$v->adjustment_amount|number_format:2:".":","}</td>
                            </tr>
                        {/foreach}
                            <tr>
                                <td class="adjustment_total right strong" colspan="4">Adjustment Total:</td>
                                <td class="right strong">${$record->get("capital_flow_total")|number_format:2:".":","}</td>
                            </tr>
                            <tr>
                                <td class="total_amount_due right strong" colspan="4">Total Amount Due:</td>
                                <td class="right strong">${$record->get("total_amount_due")|number_format:2:".":","}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            {/foreach}
        </div>
    </div>

    <p>COMBINED</p>
    <div class="ranges">
        <p>Combined Billable Account Value as of {$BILLING->GetStartPeriod()}: ${$BILLING->GetStartDateValue()|number_format:2:".":","}</p>
        <div class="ranged_portfolio">
            <table class="ranged_table width50">
                <thead>
                <tr>
                    <th class="align_right">Asset Value Range</th>
                    <th class="align_right">Amount</th>
                    <th class="align_right">Rate</th>
                    <th class="align_right">Fee Amount</th>
                </tr>
                </thead>
                {foreach key=k item=v from=$COMBINED.ranges name=range_values}
                    <tbody>
                    {if $v.amount neq 0}
                        <tr>
                            <td class="align_right">${$v.range_start|number_format:2:".":","} to {if $v.range_end eq -1}{$BILLING->GetStartDateValue()|number_format:2:".":","}{else}${$v.range_end|number_format:2:".":","}{/if}</td>
                            <td class="align_right">${$v.bill_amount|number_format:2:".":","}</td>
                            <td class="align_right">{$v.rate|number_format:2:".":","}%</td>
                            <td class="align_right">${$v.amount|number_format:2:".":","}</td>
                        </tr>
                    {/if}
                    {if $smarty.foreach.range_values.last}
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td class="align_right upper_line padded_top5 strong">${$COMBINED.bill_amount|number_format:2:".":","}</td>
                        </tr>
                    {/if}
                    </tbody>
                {/foreach}
            </table>
            <table class="flow_table ranged_table width50">
                <thead>
                <tr>
                    <th class="align_right">Date of Flow</th>
                    <th class="align_right">Fraction of Period</th>
                    <th class="align_right">Flow Amount</th>
                    <th class="align_right">Rate</th>
                    <th class="align_right">Adjustment Amount</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$COMBINED.capital_flows key=k item=v}
                    <tr>
                        <td class="align_right">{$v->date}</td>
                        <td class="align_right">{$v->fraction} / {$v->period}</td>
                        <td class="align_right">${$v->flow|number_format:2:".":","}</td>
                        <td class="align_right">{$v->rate|number_format:2:".":","}%</td>
                        <td class="align_right">${$v->adjustment_amount|number_format:2:".":","}</td>
                    </tr>
                {/foreach}
                    <tr>
                        <td class="adjustment_total right strong" colspan="4">Adjustment Total:</td>
                        <td class="right strong">${$COMBINED.capital_flow_total|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="total_amount_due right strong" colspan="4">Total Amount Due:</td>
                        <td class="right strong">${$COMBINED.total_amount_due|number_format:2:".":","}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>