{assign var = "t3_individual_performance_summed" value = $T3PERFORMANCE->GetIndividualSummedBalance()}
{assign var = "t6_individual_performance_summed" value = $T6PERFORMANCE->GetIndividualSummedBalance()}
{assign var = "t12_individual_performance_summed" value = $T12PERFORMANCE->GetIndividualSummedBalance()}

{assign var = "t3_begin_values" value = $T3PERFORMANCE->GetIndividualBeginValues()}
{assign var = "t6_begin_values" value = $T6PERFORMANCE->GetIndividualBeginValues()}
{assign var = "t12_begin_values" value = $T12PERFORMANCE->GetIndividualBeginValues()}

{assign var = "t3_end_values" value = $T3PERFORMANCE->GetIndividualEndValues()}
{assign var = "t6_end_values" value = $T6PERFORMANCE->GetIndividualEndValues()}
{assign var = "t12_end_values" value = $T12PERFORMANCE->GetIndividualEndValues()}

{assign var = "t3_appreciation" value = $T3PERFORMANCE->GetIndividualCapitalAppreciation()}
{assign var = "t6_appreciation" value = $T6PERFORMANCE->GetIndividualCapitalAppreciation()}
{assign var = "t12_appreciation" value = $T12PERFORMANCE->GetIndividualCapitalAppreciation()}

{assign var = "t3_appreciation_percent" value = $T3PERFORMANCE->GetIndividualCapitalAppreciationPercent()}
{assign var = "t6_appreciation_percent" value = $T6PERFORMANCE->GetIndividualCapitalAppreciationPercent()}
{assign var = "t12_appreciation_percent" value = $T12PERFORMANCE->GetIndividualCapitalAppreciationPercent()}

{assign var = "t3_twr" value = $T3PERFORMANCE->GetIndividualTWR()}
{assign var = "t6_twr" value = $T6PERFORMANCE->GetIndividualTWR()}
{assign var = "t12_twr" value = $T12PERFORMANCE->GetIndividualTWR()}

{*{assign var = "t3_irr" value = $T3PERFORMANCE->GetIndividualIRR()}
{assign var = "t6_irr" value = $T6PERFORMANCE->GetIndividualIRR()}
{assign var = "t12_irr" value = $T12PERFORMANCE->GetIndividualIRR()}*}

<table cellpadding = "5" style="width:100%;font-family:Arial,Sans-Serif;font-size:16px;">
    <thead>
    <tr>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">&nbsp;</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center">Account Number</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center">Beginning Balance</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Flow</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Income</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Expenses</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Ending Value</th>
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Investment Return</th>
{*        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Investment Gain (%)</th>*}
        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">TWR</th>
{*        <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">IRR</th>*}
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="9" style="font-weight:bold; background-color:RGB(245, 245, 245);">Trailing 3 ({$T3PERFORMANCE->GetStartDateWithParams("M y")} to {$T3PERFORMANCE->GetEndDateWithParams("M y")})</td>
    </tr>
    {foreach from=$t3_individual_performance_summed key=account_number item=v}
        <tr>
            <td>&nbsp;</td>
            <td>{$account_number}</td>
            <td style="text-align:right;">${$t3_begin_values[$account_number]->value|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t3_individual_performance_summed[$account_number]['Flow']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t3_individual_performance_summed[$account_number]['Income']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t3_individual_performance_summed[$account_number]['Expense']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t3_end_values[$account_number]->value|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t3_appreciation[$account_number]|number_format:2:".":","}</td>
{*            <td style="text-align:right;">{$t3_appreciation_percent[$account_number]|number_format:2:".":","}%</td>*}
            <td style="text-align:right;">{$t3_twr[$account_number]|number_format:2:".":","}%</td>
{*            <td style="text-align:right;">{$t3_irr[$account_number]|number_format:2:".":","}%</td>*}
        </tr>
    {/foreach}
    <tr>
        <td colspan="9" style="font-weight:bold; background-color:RGB(245, 245, 245);">Year to Date ({$T6PERFORMANCE->GetStartDateWithParams("M y")} to {$T6PERFORMANCE->GetEndDateWithParams("M y")})</td>
    </tr>
    {foreach from=$t6_individual_performance_summed key=account_number item=v}
        <tr>
            <td>&nbsp;</td>
            <td>{$account_number}</td>
            <td style="text-align:right;">${$t6_begin_values[$account_number]->value|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t6_individual_performance_summed[$account_number]['Flow']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t6_individual_performance_summed[$account_number]['Income']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t6_individual_performance_summed[$account_number]['Expense']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t6_end_values[$account_number]->value|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t6_appreciation[$account_number]|number_format:2:".":","}</td>
{*            <td style="text-align:right;">{$t6_appreciation_percent[$account_number]|number_format:2:".":","}%</td>*}
            <td style="text-align:right;">{$t6_twr[$account_number]|number_format:2:".":","}%</td>
{*            <td style="text-align:right;">{$t6_irr[$account_number]|number_format:2:".":","}%</td>*}
        </tr>
    {/foreach}
    <tr>
        <td colspan="9" style="font-weight:bold; background-color:RGB(245, 245, 245);">Trailing 12 ({$T12PERFORMANCE->GetStartDateWithParams("M y")} to {$T12PERFORMANCE->GetEndDateWithParams("M y")})</td>
    </tr>
    {foreach from=$t12_individual_performance_summed key=account_number item=v}
        <tr>
            <td>&nbsp;</td>
            <td>{$account_number}</td>
            <td style="text-align:right;">${$t12_begin_values[$account_number]->value|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t12_individual_performance_summed[$account_number]['Flow']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t12_individual_performance_summed[$account_number]['Income']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t12_individual_performance_summed[$account_number]['Expense']->amount|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t12_end_values[$account_number]->value|number_format:2:".":","}</td>
            <td style="text-align:right;">${$t12_appreciation[$account_number]|number_format:2:".":","}</td>
{*            <td style="text-align:right;">{$t12_appreciation_percent[$account_number]|number_format:2:".":","}%</td>*}
            <td style="text-align:right;">{$t12_twr[$account_number]|number_format:2:".":","}%</td>
{*            <td style="text-align:right;">{$t12_irr[$account_number]|number_format:2:".":","}%</td>*}
        </tr>
    {/foreach}
    </tbody>
</table>