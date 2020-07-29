<div id="interval_page_wrapper">
    {$LINE_IMAGE}
    {if $INTERVALS|@count > 0}
        <div id="IntervalWrapper">
            <div id="IntervalLeft" class="gradient-border">
                <table id="IntervalTable">
                    <thead>
                    <tr>
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
                            <td class="left_text padding2 data_end_date" data-date="{$v.end_date}">{$v.end_date}</td>
                            <td class="right_text padding2 data_begin_value" data-begin_value='{$v.begin_value}'>${$v.begin_value|number_format:2:".":","}</td>
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
                            <div id="date_ranges">
                                (<span class="start_date_range"></span>
                                &nbsp;-&nbsp;
                                <span class="end_date_range"></span>)
                            </div>
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