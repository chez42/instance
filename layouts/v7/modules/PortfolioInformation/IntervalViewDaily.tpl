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
                        <th class="left_text padding2">End<br />Date</th>
                        <th class="right_text padding2">Begin<br />Value</th>
                        <th class="right_text padding2">Net Flow Amount</th>
                        <th class="right_text padding2">Income Amount</th>
                        <th class="right_text padding2">Expense Amount</th>
                        <th class="right_text padding2">Investment Return</th>
                        <th class="right_text padding2">End<br />Value</th>
                        <th class="right_text padding2">Day(%)<br />G/L</th>
                        <th class="right_text padding2">TWR(%)</th>
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
                            <td class="right_text padding2 data_twr {if $v.twr lt 0} red {/if} {if $v.twr gt 0} green {/if}" data-twr="{$v.twr}" data-calculated_twr="0">{$v.twr|number_format:2:".":","}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div id="IntervalRight">
                <div class="table">
                    <div class="thead">
                        <div class="td">
                        </div>
                        <div class="td aright">
                            <h2>Portfolio</h2>
                        </div>
                        <div class="td aright">
                            <h2>S&P 500</h2>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            Begin Value
                        </div>
                        <div class="td aright">
                            <span class="begin_value"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_begin_value"></span>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            Flows
                        </div>
                        <div class="td aright">
                            <span class="selected_flows"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_selected_flows">N/A</span>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            Income
                        </div>
                        <div class="td aright">
                            <span class="selected_income"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_selected_income">N/A</span>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            Expenses
                        </div>
                        <div class="td aright">
                            <span class="selected_expenses"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_selected_expenses">N/A</span>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            Period Return
                        </div>
                        <div class="td aright">
                            <span class="selected_twr"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_twr"></span>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            Average Daily Return
                        </div>
                        <div class="td aright">
                            <span class="average_return"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_average_return"></span>
                        </div>
                    </div>
                    <div class="tr">
                        <div class="td">
                            End Value
                        </div>
                        <div class="td aright">
                            <span class="end_value"></span>
                        </div>
                        <div class="td aright">
                            <span class="sp_end_value"></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    {else}
        <h2>Sorry, there are no Intervals available currently</h2>
    {/if}
</div>