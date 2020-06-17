{assign var = "t3_performance_summed" value = $T3PERFORMANCE->GetPerformanceSummed()}
{assign var = "t6_performance_summed" value = $T6PERFORMANCE->GetPerformanceSummed()}
{assign var = "t12_performance_summed" value = $T12PERFORMANCE->GetPerformanceSummed()}

{assign var = "t3_performance" value = $T3PERFORMANCE->GetPerformance()}
{assign var = "t6_performance" value = $T6PERFORMANCE->GetPerformance()}
{assign var = "t12_performance" value = $T12PERFORMANCE->GetPerformance()}


{if $MARGIN_BALANCE neq 0}
    <p>Margin Balance: <span style="{if $MARGIN_BALANCE lt 0}color:red;{else}color:green;{/if}">${$MARGIN_BALANCE|number_format:2}</span></p>
{/if}
{if $NET_CREDIT_DEBIT neq 0}
    <p>Net Credit Debit: <span style="{if $NET_CREDIT_DEBIT lt 0}color:red;{else}color:green;{/if}">${$NET_CREDIT_DEBIT|number_format:2}</span></p>
{/if}
{if $UNSETTLED_CASH neq 0}
    <p>Unsettled Cash: <span style="{if $UNSETTLED_CASH lt 0}color:red;{else}color:green;{/if}">${$UNSETTLED_CASH|number_format:2}</span></p>
{/if}

<table id="overview_performance" class="collap_performance table table-bordered" style="width:100%; display:block;">
    <thead>
    <tr>
        <th style="font-weight:bold; vertical-align:top; text-align:left; font-size:16px; text-decoration:underline;"></th>
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
        <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t3_performance_summed.Income->amount|number_format:2:".":","}</td>
        <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t6_performance_summed.Income->amount|number_format:2:".":","}</td>
        <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t12_performance_summed.Income->amount|number_format:2:".":","}</td>
    </tr>
    {foreach from=$TABLECATEGORIES key=k item=v}
        {if $k eq 'Income'}
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
        <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t3_performance_summed.Expense->amount|number_format:2:".":","}</td>
        <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t6_performance_summed.Expense->amount|number_format:2:".":","}</td>
        <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;">${$t12_performance_summed.Expense->amount|number_format:2:".":","}</td>
    </tr>
    {foreach from=$TABLECATEGORIES key=k item=v}
        {if $k eq 'Expense'}
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
    <tr data-id="8" data-parent="">
        <td style="font-weight:bold;">Time Weighted Return (<span style="font-size:10px;">as of {$T3PERFORMANCE->GetIntervalEndDate()})</td>
        <td style="text-align:right; font-weight:bold;">{$T3PERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
        <td style="text-align:right; font-weight:bold;">{$T6PERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
        <td style="text-align:right; font-weight:bold;">{$T12PERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
    </tr>
    <tr data-id="9" data-parent="8">
        <td style="font-weight:bold;">&nbsp;&nbsp;S&P 500</td>
        <td style="text-align:right; font-weight:bold;">{$T3PERFORMANCE->GetIndex("S&P 500")|number_format:2:".":","}%</td>
        <td style="text-align:right; font-weight:bold;">{$T6PERFORMANCE->GetIndex("S&P 500")|number_format:2:".":","}%</td>
        <td style="text-align:right; font-weight:bold;">{$T12PERFORMANCE->GetIndex("S&P 500")|number_format:2:".":","}%</td>
    </tr>
    <tr data-id="10" data-parent="8">
        <td style="font-weight:bold;">&nbsp;&nbsp;AGG</td>
        <td style="text-align:right; font-weight:bold;">{$T3PERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
        <td style="text-align:right; font-weight:bold;">{$T6PERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
        <td style="text-align:right; font-weight:bold;">{$T12PERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
    </tr>
    </tbody>
</table>