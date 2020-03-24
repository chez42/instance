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
        <div id="IntervalLeft">
    {*        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
            </div>
    *}
            {*        <p><strong>Disclaimer: </strong>This page is currently in alpha testing and values may not have an accurate representation of the account</p>*}
            <table id="IntervalTable" border="1px solid black;">
                <thead>
                <tr>
{*                            <td style="text-align:center; padding:2px;">Account Number</td>
                    <td style="text-align:center; padding:2px;">Begin Date</td>*}
                    <td style="text-align:center; padding:2px;">End Date</td>
                    <td style="text-align:center; padding:2px;">Begin Value</td>
                    <td style="text-align:center; padding:2px;">Net Flow Amount</td>
                    <td style="text-align:center; padding:2px;">Investment Return</td>
                    <td style="text-align:center; padding:2px;" class="end_value">End Value</td>
                    <td style="text-align:center; padding:2px;">Day Return %</td>
                    <td style="text-align:center; padding:2px;">TWR (TO BE REMOVED)</td>
                </tr>
                </thead>
                <tbody>
                {foreach item=v from=$INTERVALS}
                    <tr>
{*                                <td style="padding:2px;">{$v.account_number}</td>
                        <td style="padding:2px;">{$v.begin_date}</td>*}
                        <td style="padding:2px;" class="end_date" data-date="{$v.end_date}">{$v.end_date}</td>
                        <td style="text-align:right; padding:2px;">${$v.begin_value|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;">${$v.net_flow|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;">${$v.investmentreturn|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;" class="end_value" data-end_value='{$v.end_value}'>${$v.end_value|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;" class="net_return" data-net_return='{$v.net_return|number_format:6:".":","}'>{$v.net_return_percent|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;" class="twr" data-twr="{$v.twr}">{$v.twr|number_format:2:".":","}%</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div id="IntervalRight" style="width:19%; display:block; float:left;">
            <table style="position:relative; width:100%;">
                <tr style="width:100%; display:block;">
                    <td style="width:100%; vertical-align:top; text-align:center; display:block;">
                        <h2 style="font-size:12px;">Period Return</h2>
                        <p style="font-weight:bold; font-size:16px;" class="calculated_return"></p>
                    </td>
                </tr>
                <tr>
                    <td style="width:100%; vertical-align:top; text-align:center; display:block;">
                        <h2 style="font-size:12px;">Average Return</h2>
                        <p style="font-weight:bold; font-size:16px;" class="average_return"></p>
                    </td>
                </tr>
                <tr>
                    <td style="width:100%; vertical-align:top; text-align:center; display:block;">
                        <h2 style="font-size:12px;">Annualized Return</h2>
                        <p style="font-weight:bold; font-size:16px;" class="annual_return"></p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
{else}
    <h2>Sorry, there are no Intervals available currently</h2>
{/if}