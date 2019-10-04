{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}?parameter=1" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{assign var = "ytd_individual_performance_summed" value = $YTDPERFORMANCE->GetIndividualSummedBalance()}
{assign var = "ytd_begin_values" value = $YTDPERFORMANCE->GetIndividualBeginValues()}
{assign var = "ytd_end_values" value = $YTDPERFORMANCE->GetIndividualEndValues()}
{assign var = "ytd_appreciation" value = $YTDPERFORMANCE->GetIndividualCapitalAppreciation()}
{assign var = "ytd_appreciation_percent" value = $YTDPERFORMANCE->GetIndividualCapitalAppreciationPercent()}
{assign var = "ytd_twr" value = $YTDPERFORMANCE->GetIndividualTWR()}
{assign var = "ytd_performance_summed" value = $YTDPERFORMANCE->GetPerformanceSummed()}

<input type="hidden" value='{$HOLDINGSPIEVALUES}' id="holdings_values" class="holdings_values" />
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
                        <input type="hidden" value="" name="pie_image" id="pie_image" />
                        <input type="hidden" value="GHReport" name="view" />
                        <input type="hidden" value="{$ORIENTATION}" name="orientation" />
                        <input type="hidden" value="1" name="pdf" />
                        <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
                        <input type="hidden" value="{$START_DATE}" name="report_start_date" />
                        <input type="hidden" value="{$END_DATE}" name="report_end_date" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="GHReport_wrapper">
    <div class="GHReport_section">
        <h2 class="blue_header">PLAN GOALS AND ASSUMPTIONS</h2>
        <p>{$POLICY|nl2br}</p>
        <p>Report Notes:</p>
        <textarea rows="5" name="personal_notes" form="export" style="font-size:14pt;"></textarea>
    </div>
    <div class="GHReport_section">
        <h2 class="blue_header">PORTFOLIO SUMMARY</h2>
        <table style="width:100%" border="0">
            <tr>
                <td style="width:50%;">
                    <table style="display:block; width:90%; font-size:16px;"  border="0">
                        <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th style="font-weight:bold; text-align:right;" class="borderBottom">VALUE</th>
                            <th style="font-weight:bold; text-align:right;" class="borderBottom">ALLOC</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$HOLDINGSPIEARRAY item=v}
                            <tr>
                                <td style="font-weight:bold; width:50%; padding-bottom:10px;">{$v.title}</td>
                                <td style="text-align:right; width:25%;">${$v.value|number_format:2:".":","}</td>
                                <td style="text-align:right; width:25%;">{$v.percentage|number_format:2:".":","}%</td>
                            </tr>
                        {/foreach}
                        <tr>
                            <td>&nbsp;</td>
                            <td style="text-align:right;" class="borderTop borderBottom">${$GLOBALTOTAL|number_format:2:".":","}</td>
                            <td>&nbsp;</td>
                        </tr>
                        {if $MARGIN_BALANCE neq 0}
                            <tr>
                                <td colspan="3">
                                    <p>Margin Balance: <span style="{if $MARGIN_BALANCE lt 0}color:red;{else}color:green;{/if}">${$MARGIN_BALANCE|number_format:2}</span></p>
                                </td>
                            </tr>
                        {/if}
                        {if $NET_CREDIT_DEBIT neq 0}
                            <tr>
                                <td colspan="3">
                                    <p>Net Credit Debit: <span style="{if $NET_CREDIT_DEBIT lt 0}color:red;{else}color:green;{/if}">${$NET_CREDIT_DEBIT|number_format:2}</span></p>
                                </td>
                            </tr>
                        {/if}
                        {if $UNSETTLED_CASH neq 0}
                            <tr>
                                <td colspan="3">
                                    <p>Unsettled Cash: <span style="{if $UNSETTLED_CASH lt 0}color:red;{else}color:green;{/if}">${$UNSETTLED_CASH|number_format:2}</span></p>
                                </td>
                            </tr>
                        {/if}
                        {if $YTDPERFORMANCE->GetDividendAccrualAmount() neq 0}
                            <tr>
                                <td style="padding-top:10px; font-weight:bold;">Dividend Accrual:</td>
                                <td style="text-align:right; padding-top:10px;">${$YTDPERFORMANCE->GetDividendAccrualAmount()|number_format:2:".":","}</td>
                                <td>&nbsp;</td>
                            </tr>
                        {/if}
                        </tbody>
                    </table>
                </td>
                <td>
                    <div id="dynamic_pie_holder" class="dynamic_pie_holder" style="height: 300px; width:450px;"></div>
                </td>
            </tr>
        </table>
    </div>
    <div class="GHReport_section">
        <h2 class="grey_header"><span style="font-size:14px;">{$HEADING} PERFORMANCE ({$START_DATE|date_format:'%B, %Y'} to {$YTDPERFORMANCE->GetEndDate()|date_format:'%B, %Y'})</span></h2>
        <table class="table" style="display:block; width:100%;"  border="0">
            <thead>
            <tr>
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center; text-decoration:underline;">ACCOUNT NAME</th>
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center; text-decoration:underline;">ACCT NUMBER</th>
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center; text-decoration:underline;">BEG. BALANCE</th>
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">ADDTNS/ WTHDRWLS</th>
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">CHANGE IN VALUE</th>
                {*                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Expenses</th>*}
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">END BALANCE</th>
                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">ESTIMATED INCOME</th>
                {*                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Investment Return</th>
                {*                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">Investment Gain (%)</th>
                                <th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center">TWR</th>*}
            </tr>
            </thead>
            <tbody>
            {foreach from=$ytd_individual_performance_summed key=account_number item=v}
                <tr {if $ytd_individual_performance_summed[$account_number]['Flow']->disable_performance eq 1} style="{*background-color:#FFFFE0;*}" {/if}>
                    <td>{$ytd_individual_performance_summed[$account_number]['account_name']}</td>
                    <td>**{$account_number|substr:5}</td>
                    <td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_begin_values[$account_number]->value|number_format:2:".":","}</span></td>
                    <td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_individual_performance_summed[$account_number]['Flow']->amount|number_format:2:".":","}</span></td>
                    <td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_individual_performance_summed[$account_number]['change_in_value']|number_format:2:".":","}</span></td>
                    <td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_end_values[$account_number]->value|number_format:2:".":","}</span></td>
                    <td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_individual_performance_summed[$account_number]['income_div_interest']->amount|number_format:2:".":","}</span></td>
                </tr>
            {/foreach}
            <tr>
                <td style="background-color:RGB(245, 245, 245); font-weight:bold;" colspan="2">&nbsp;{*Blended Portfolio Return*}</td>
                <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$YTDPERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</span></td>
                <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_performance_summed.Flow->amount|number_format:2:".":","}</span></td>
                <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_performance_summed.change_in_value|number_format:2:".":","}</span></td>
                <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$YTDPERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</span></td>
                <td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;">{$ytd_performance_summed.income_div_interest->amount|number_format:2:".":","}</span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="GHReport_section">
        <h2 class="blue_header"><span style="font-size:14px;"><span style="font-size:14px;">Benchmark and Index Performance</span></h2>
        <table class="table" border="0">
            <tbody>
            <tr>
                <td colspan="2" style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:left; text-decoration:underline;">PORTFOLIO PERFORMANCE</td>
                <td colspan="2" style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:left; text-decoration:underline;">BENCHMARK PERFORMANCE</td>
            </tr>
            <tr>
                <td>Combined Return</td>
                <td style="text-align:right; font-weight:bold;">{$YTDPERFORMANCE->GetTWR()|number_format:2:".":","}%</td>
                <td>Combined Benchmark</td>
                <td style="text-align:right; font-weight:bold;">{$YTDPERFORMANCE->GetBenchmark()|number_format:2:".":","}%</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:left; text-decoration:underline;">BENCHMARK PERFORMANCE</td>
            </tr>
            <tr>
                <td>S&amp;P 500</td>
                <td style="text-align:right; font-weight:bold;">{$YTDPERFORMANCE->GetIndex("S&P 500")|number_format:2:".":","}%</td>
                <td>AGG</td>
                <td style="text-align:right; font-weight:bold;">{$YTDPERFORMANCE->GetIndex("AGG")|number_format:2:".":","}%</td>
            </tr>
            <tr>
                <td>MSCI Emerging Market index</td>
                <td style="text-align:right; font-weight:bold;">{$YTDPERFORMANCE->GetIndex("EEM")|number_format:2:".":","}%</td>
                <td>MSCI EAFE index</td>
                <td style="text-align:right; font-weight:bold;">{$YTDPERFORMANCE->GetIndex("MSCI_EAFE")|number_format:2:".":","}%</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>