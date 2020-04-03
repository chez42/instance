<form id="IntervalForm" method="post" action="index.php?module=PortfolioInformation&view=IntervalReport">
    <input type="hidden" name="module" value="PortfolioInformation" />
    <input type="hidden" name="view" value="IntervalReport" />
    <input type="hidden" name="source_module" id="source_module" value="{$SOURCE_MODULE}" />
    <input type="hidden" name="source_record" id="source_record" value="{$SOURCE_RECORD}" />
    <input type="hidden" name="account_numbers" id="account_numbers" value="{$ACCOUNT_NUMBERS}" />
    <input type="hidden" id="start_date" name="start_date" value="" />
    <input type="hidden" id="end_date" name="end_date" value="" />
    <input type="hidden" id="report_type" name="report_type" value="daily" />
    <input type="hidden" id="calculated_return" name="calculated_return" value="" />
</form>

<div id="interval_page_wrapper">
    <div id="controls" style="width: 100%; overflow: hidden;">
        <div class="controls_dates">
            From: <input type="text" id="fromfield" class="amcharts-input" />
            To: <input type="text" id="tofield" class="amcharts-input" />
        </div>
        <div class="control_buttons">
            <button id="lyr" class="amcharts-input">2019</button>
            <button id="b1m" class="amcharts-input">1m</button>
            <button id="b3m" class="amcharts-input">3m</button>
            <button id="b6m" class="amcharts-input">6m</button>
            <button id="b1y" class="amcharts-input">1y</button>
            <button id="bytd" class="amcharts-input">YTD</button>
            <button id="bmax" class="amcharts-input">MAX</button>
        </div>
    </div>
    <div id="linechartdiv"></div>

    {if $INTERVALS|@count > 0}
        <div id="IntervalWrapper">
            <div id="IntervalLeft" class="gradient-border">
                {*        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                *}
                {*        <p><strong>Disclaimer: </strong>This page is currently in alpha testing and values may not have an accurate representation of the account</p>*}
                <table id="IntervalTable">
                    <thead>
                    <tr>
                        {*                            <td style="text-align:center; padding:2px;">Account Number</td>
                                            <td style="text-align:center; padding:2px;">Begin Date</td>*}
                        <th class="left_text padding2">End Date</th>
                        <th class="right_text padding2">Begin Value</th>
                        <th class="right_text padding2">Net Flow Amount</th>
                        <th class="right_text padding2">Income Amount</th>
                        <th class="right_text padding2">Expense Amount</th>
                        <th class="right_text padding2">Investment Return</th>
                        <th class="right_text padding2 end_value">End Value</th>
                        <th class="right_text padding2">Day Return %</th>
                        <th class="right_text padding2">Calculated TWR %</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach item=v from=$INTERVALS}
                        <tr>
                            {*                                <td style="padding:2px;">{$v.account_number}</td>
                                                    <td style="padding:2px;">{$v.begin_date}</td>*}
                            <td class="left_text padding2 data_end_date" data-date="{$v.end_date}">{$v.end_date}</td>
                            <td class="right_text padding2 data_begin_value" data-begin_value="{$v.begin_value}">${$v.begin_value|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_net_flow {if $v.net_flow lt 0} red {/if} {if $v.net_flow gt 0} green {/if}" data-net_flow="{$v.net_flow}">${$v.net_flow|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_incomeamount {if $v.incomeamount lt 0} red {/if} {if $v.incomeamount gt 0} green {/if}" data-incomeamount="{$v.incomeamount}">${$v.incomeamount|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_expense_amount {if $v.expense_amount lt 0} red {/if}" data-expense_amount="{$v.expense_amount}">${$v.expense_amount|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_investmentreturn {if $v.investmentreturn lt 0} red {/if} {if $v.investmentreturn gt 0} green {/if}" data-investmentreturn="{$v.investmentreturn}">${$v.investmentreturn|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_end_value" data-end_value='{$v.end_value}'>${$v.end_value|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_net_return {if $v.net_return_percent lt 0} red {/if} {if $v.net_return_percent gt 0} green {/if}" data-net_return='{$v.net_return}'>{$v.net_return_percent|number_format:2:".":","}</td>
                            <td class="right_text padding2 data_twr {if $v.twr lt 0} red {/if} {if $v.twr gt 0} green {/if}" data-twr="{$v.twr}">{$v.twr|number_format:2:".":","}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div id="IntervalRight">
                <div class="table">
                    <div class="tr">
                        <div class="td">
                            <h2>&nbsp;</h2>
                        </div>
                        <div class="td">
                            <h2>Client</h2>
                        </div>
                        <div class="td">
                            <h2>S&P 500</h2>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>Begin Value</p>
                        </div>
                        <div class="td">
                            <p class="begin_value"></p>
                        </div>
                        <div class="td">
                            <p class="sp_return"></p>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>Flows</p>
                        </div>
                        <div class="td">
                            <p class="selected_flows"></p>
                        </div>
                        <div class="td">
                            <p class="sp_return"></p>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>Income</p>
                        </div>
                        <div class="td">
                            <p class="selected_income"></p>
                        </div>
                        <div class="td">
                            <p class="sp_return"></p>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>Expenses</p>
                        </div>
                        <div class="td">
                            <p class="selected_expenses"></p>
                        </div>
                        <div class="td">
                            <p class="sp_return"></p>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>Period Return</p>
                        </div>
                        <div class="td">
                            <p class="selected_twr"></p>
                        </div>
                        <div class="td">
                            <p class="sp_return"></p>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>Average Daily Return</p>
                        </div>
                        <div class="td">
                            <p class="average_return"></p>
                        </div>
                        <div class="td">
                            <p class="sp_average"></p>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            <p>End Value</p>
                        </div>
                        <div class="td">
                            <p class="end_value"></p>
                        </div>
                        <div class="td">
                            <p class="sp_return"></p>
                        </div>
                    </div>

                </div>
            </div>

            {*
                            <table>
                                <thead>
                                    <th>&nbsp;</th>
                                    <th>Client</th>
                                    <th>S&P 500</th>
                                </thead>
                                <tr>
                                    <td>
                                        <h2>Period Return</h2>
                                    </td>
                                    <td>
                                        <p class="calculated_return"></p>
                                    </td>
                                    <td>
                                        <p class="calculated_return"></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h2>Average Daily Return</h2>
                                    </td>
                                    <td>
                                        <p class="average_return"></p>
                                    </td>
                                    <td>
                                        <p class="average_return"></p>
                                    </td>
                                </tr>
            {*                    <tr>
                                    <td style="width:100%; vertical-align:top; text-align:center; display:block;">
                                        <h2 style="font-size:12px; color:white;">Annualized Return</h2>
                                        <p style="font-weight:bold; font-size:16px; color:white;" class="annual_return"></p>
                                    </td>
                                </tr>
                            </table>*}
        </div>
    {else}
        <h2>Sorry, there are no Intervals available currently</h2>
    {/if}
</div>