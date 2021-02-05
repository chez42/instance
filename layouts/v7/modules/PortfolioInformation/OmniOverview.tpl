{assign var = "t3_performance_summed" value = $T3PERFORMANCE->GetPerformanceSummed()}
{assign var = "t6_performance_summed" value = $T6PERFORMANCE->GetPerformanceSummed()}
{assign var = "t12_performance_summed" value = $T12PERFORMANCE->GetPerformanceSummed()}

{assign var = "t3_performance" value = $T3PERFORMANCE->GetPerformance()}
{assign var = "t6_performance" value = $T6PERFORMANCE->GetPerformance()}
{assign var = "t12_performance" value = $T12PERFORMANCE->GetPerformance()}

<input type="hidden" value='{$HOLDINGSPIEVALUES}' id="holdings_values" class="holdings_values" />
<input type="hidden" id="t12_balances" class="t12_balances" value='{$T12BALANCES}' />

<input type="hidden" value='{$DYNAMIC_PIE}' id="estimate_pie_values" />

<div class="row-fluid ReportTitle detailViewTitle">
    <div class=" span12 ">
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown"><span></span>&nbsp;</span>
                </div>
            </div>
            <div class="span6">
                <div class="pull-right">
                    <div class="btn-toolbar">
							<span class="btn-group">
								<button class="btn ExportReport"><strong>Generate PDF</strong></button>
							</span>
                    </div>
                    <form method="post" id="export">
                        <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
                        <input type="hidden" value="PortfolioInformation" name="module" />
                        <input type="hidden" value="OmniOverview" name="view" />
                        <input type="hidden" value="" name="pie_image" id="pie_image" />
                        <input type="hidden" value="" name="graph_image" id="graph_image" />
                        <input type="hidden" value="1" name="pdf" />
                        <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<h1 style="text-align:center;">Account Overview</h1>
<div id="overview_charts" style="display:block; width:100%;">
    <div id="dynamic_pie_holder" class="dynamic_pie_holder" style="display:block; height: 300px; width:35%; float:left;"></div>
    <div id="dynamic_chart_holder" class="dynamic_chart_holder" style="display:block; height: 300px; width:65%; float:right;"></div>
</div>

<div style="display:block; clear:both; width:100%">
<table>
    <body>
    {if $MARGIN_BALANCE neq 0}
        <tr>
            <td>
                <p>Margin Balance: <span style="{if $MARGIN_BALANCE lt 0}color:red;{else}color:green;{/if}">${$MARGIN_BALANCE|number_format:2}</span></p>
            </td>
        </tr>
    {/if}
    {if $NET_CREDIT_DEBIT neq 0}
        <tr>
            <td>
                <p>Net Credit Debit: <span style="{if $NET_CREDIT_DEBIT lt 0}color:red;{else}color:green;{/if}">${$NET_CREDIT_DEBIT|number_format:2}</span></p>
            </td>
        </tr>
    {/if}
    {if $UNSETTLED_CASH neq 0}
        <tr>
            <td>
                <p>Unsettled Cash: <span style="{if $UNSETTLED_CASH lt 0}color:red;{else}color:green;{/if}">${$UNSETTLED_CASH|number_format:2}</span></p>
            </td>
        </tr>
    {/if}
    </body>
</table>
</div>

<table id="overview_performance" class="collap_performance table table-bordered">
    <thead>
        <tr>
            <th style="font-weight:bold; vertical-align:top; text-align:left; font-size:16px; text-decoration:underline;">Performance</th>
            <th style="font-weight:bold;">Trailing 3<br />({$T3PERFORMANCE->GetStartDateWithParams("m/d/Y")} to {$T3PERFORMANCE->GetEndDateWithParams("m/d/Y")})</th>
            <th style="font-weight:bold;">Year to Date<br />({$T6PERFORMANCE->GetStartDateWithParams("m/d/Y")} to {$T6PERFORMANCE->GetEndDateWithParams("m/d/Y")})</th>
            <th style="font-weight:bold;">Trailing 12<br />({$T12PERFORMANCE->GetStartDateWithParams("m/d/Y")} to {$T12PERFORMANCE->GetEndDateWithParams("m/d/Y")})</th>
        </tr>
    </thead>
    <tbody>
        <tr data-id="1" data-parent="">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Beginning Value</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T3PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T6PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T12PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</td>
        </tr>
        <tr data-id="2" data-parent="">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Flow</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t3_performance_summed.Flow->amount|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t6_performance_summed.Flow->amount|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t12_performance_summed.Flow->amount|number_format:2:".":","}</td>
        </tr>
        {foreach from=$TABLECATEGORIES key=k item=v}
            {if $k eq 'Flow'}
                {foreach from=$v item=val}
                    <tr data-parent="2">
                        <td>&nbsp;&nbsp;{$val}</td>
                        <td style="text-align:right;">${$t3_performance.$k.$val->amount|number_format:2:".":","}</td>
                        <td style="text-align:right;">${$t6_performance.$k.$val->amount|number_format:2:".":","}</td>
                        <td style="text-align:right;">${$t12_performance.$k.$val->amount|number_format:2:".":","}</td>
                    </tr>
                {/foreach}
            {/if}
        {/foreach}
        <tr data-id="3" data-parent="">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Income</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${($t3_performance_summed.Income->amount+$t3_performance_summed.income_div_interest->amount)|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${($t6_performance_summed.Income->amount+$t6_performance_summed.income_div_interest->amount)|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${($t12_performance_summed.Income->amount+$t12_performance_summed.income_div_interest->amount)|number_format:2:".":","}</td>
        </tr>
        {foreach from=$TABLECATEGORIES key=k item=v}
            {if $k eq 'Income' OR $k eq 'income_div_interest'}
                {foreach from=$v item=val}
                    <tr data-parent="3">
                        <td>&nbsp;&nbsp;{$val}</td>
                        <td style="text-align:right;">${$t3_performance.$k.$val->amount|number_format:2:".":","}</td>
                        <td style="text-align:right;">${$t6_performance.$k.$val->amount|number_format:2:".":","}</td>
                        <td style="text-align:right;">${$t12_performance.$k.$val->amount|number_format:2:".":","}</td>
                    </tr>
                {/foreach}
            {/if}
        {/foreach}
        <tr data-id="4" data-parent="">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Expenses</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${($t3_performance_summed.Expense->amount+$t3_performance_summed.Reversal->amount)|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${($t6_performance_summed.Expense->amount+$t6_performance_summed.Reversal->amount)|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${($t12_performance_summed.Expense->amount+$t12_performance_summed.Reversal->amount)|number_format:2:".":","}</td>
        </tr>
        {foreach from=$TABLECATEGORIES key=k item=v}
            {if $k eq 'Expense' OR $k eq 'Reversal'}
                {foreach from=$v item=val}
                    <tr data-parent="4">
                        <td>&nbsp;&nbsp;{$val}</td>
                        <td style="text-align:right;">${$t3_performance.$k.$val->amount|number_format:2:".":","}</td>
                        <td style="text-align:right;">${$t6_performance.$k.$val->amount|number_format:2:".":","}</td>
                        <td style="text-align:right;">${$t12_performance.$k.$val->amount|number_format:2:".":","}</td>
                    </tr>
                {/foreach}
            {/if}
        {/foreach}
        <tr data-id="6" data-parent="">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Ending Value</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T3PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T6PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T12PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</td>
        </tr>
        <tr data-id="5" data-parent="">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Investment Return</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T3PERFORMANCE->GetCapitalAppreciation()|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T6PERFORMANCE->GetCapitalAppreciation()|number_format:2:".":","}</td>
            <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$T12PERFORMANCE->GetCapitalAppreciation()|number_format:2:".":","}</td>
        </tr>
{*        <tr data-id="7" data-parent="" style="border:none;">
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;">Time Weighted Return (<span style="font-size:10px;">as of {$T3PERFORMANCE->GetIntervalEndDate()})</span></td>
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;"></td>
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;"></td>
            <td style="background-color:RGB(245, 245, 245); font-weight:bold;"></td>
        </tr>*}
        <tr data-id="8" data-parent="">
            <td style="font-weight:bold;">Time Weighted Return (<span style="font-size:10px;">as of {$T3PERFORMANCE->GetIntervalEndDate()})</td>
            <td style="text-align:right; font-weight:bold;">{$T3PERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
            <td style="text-align:right; font-weight:bold;">{$T6PERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
            <td style="text-align:right; font-weight:bold;">{$T12PERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
        </tr>
        <tr data-id="9" data-parent="8">
            <td style="font-weight:bold;">&nbsp;&nbsp;S&P 500</td>
            <td style="text-align:right; font-weight:bold;">{$T3PERFORMANCE->GetIndex("GSPC")|number_format:2:".":","}%</td>
            <td style="text-align:right; font-weight:bold;">{$T6PERFORMANCE->GetIndex("GSPC")|number_format:2:".":","}%</td>
            <td style="text-align:right; font-weight:bold;">{$T12PERFORMANCE->GetIndex("GSPC")|number_format:2:".":","}%</td>
        </tr>
        <tr data-id="10" data-parent="8">
            <td style="font-weight:bold;">&nbsp;&nbsp;AGG</td>
            <td style="text-align:right; font-weight:bold;">{$T3PERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
            <td style="text-align:right; font-weight:bold;">{$T6PERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
            <td style="text-align:right; font-weight:bold;">{$T12PERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
        </tr>
    </tbody>
</table>