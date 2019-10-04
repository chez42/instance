{if !$HIDE_CHARTS}
{foreach key=index item=jsModel from=$SCRIPTS}
<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

<input type="hidden" id="asset_pie" value='{$ASSET_PIE}' />
<input type="hidden" id="trailing_12_revenue" value='{$TRAILING_12_REVENUE}' />
<input type="hidden" id="trailing_12_aum" value='{$TRAILING_12_AUM}' />

<div id="AssetAllocationWidget">
{*{if $SOURCE_MODULE eq "PortfolioInformation"}*}
    <select id="reportselect">
        <option value="0" selected="selected">Report View...</option>
        <option value="OmniOverview" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Overview</option>
        <option value="AssetClassReport" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Asset Class Report</option>
        <option value="GainLoss" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Gain/Loss</option>
        <option value="GHReport" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">GH Report</option>
        <option value="GH2Report" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER-L">GH2 Report</option>
        <option value="LastYearIncome" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Income - Last Year</option>
        <option value="OmniProjected" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Income Projected</option>
        <option value="OmniIncome" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Income - Trailing 12</option>
{*        <option value="GHReport" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER-L">Annual Summary</option>*}
        {*<option value="GH2Report" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">GH2 Report-P</option>*}
        <option value="OmniIntervalsDaily" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Intervals Daily</option>
        <option value="OmniIntervals" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Intervals Monthly</option>
        <option value="MonthOverMonth" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Month Over Month</option>
        {if $CURRENT_USER->isAdminUser()}
            <option value="PortfoliosReset" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>--Portfolios Transaction Reset--</option>
        {/if}
    </select>
{*    <table style="width:100%">
        <tbody>
        <tr>
{*            <td>
                <input type="button" class="btn btn-success" id="OmniviewReport" onclick="return false;" style="width:100%; float:left;" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' value="OmniVue" />
            </td>*}
{*            <td>
                <input type="button" class="btn btn-success" id="HoldingsReport" onclick="return false;" style="width:100%; float:left;" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' value="Holdings" />
            </td>
            <td>
                <input type="button" id="IncomeReport" class="btn btn-success report_detail" onclick="return false;" style="width:100%; float:left; margin-left:2px;" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' value="Income" />
            </td>
            <td>
                <input type="button" id="OverviewReport" class="btn btn-success report_detail" onclick="return false;" style="width:100%; float:left;  margin-left:2px;" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' value="Overview" />
            </td>
        </tr>
        </tbody>
    </table>
{*{/if}*}

<table width="100%">
    <tr>
        <td>
            <div class="pie_holder" style="width:300px; display:block; float:left;">
                <div id="revenuediv" style="text-align:center; width:300px;"><h3>Asset Allocation</h3></div>
                <div id="filtered_pie" style="height:175px; width:300px; float:left; padding-left:5px;"></div>
                <div style="clear:both;"></div>
                <div id="legenddiv" style="margin: 5px 0 20px 0; width:300px; height:175px; float:left;"></div>
            </div>
{*            <div class="omnitext" style="display:block; float:left; width:200px; padding-left:5px; border-left:1px dotted black; margin-left:10px;">
                <p>OMNIVue&trade; determines the <span style="text-decoration: underline;">actual</span> exposure to various
                    asset classes utilizing data from respected 3rd parties to provide a more accurate view of a portfolio&rsquo;s true asset allocation.<br /><br />
                    <span style="font-size: 8px; font-style: italic">OMNIVue&trade; relies on the estimates and approximations of these 3rd parties to perform its calculations.
                    As such, the totals and values represented in OMNIVue&trade; may not correspond exactly to custodial statement balances and other reports of actual account value.</span>
                </p>
            </div>*}
        </td>
    </tr>
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

{*    <tr>
        <td>
            <div class="revenue_holder" style="display:block; float:left;">
                <div id="revenuediv "style="text-align:center;"><h3>Trailing 12 Revenue</h3></div>
                <div id="filtered_revenue_graph" style="height:275px; width:430px; float:left; padding-left:2px;"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="assets_holder" style="display:block; float:left;">
            <div id="revenuediv"style="text-align:center;"><h3>Asset Mix Trailing 12</h3></div>
                <div id="filtered_assets_graph" style="height:275px; width:430px; float:left; padding-left:2px;"></div>
            </div>
        </td>
    </tr>*}
</table>
</div>
{/if}