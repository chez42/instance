{if !$HIDE_CHARTS}
{foreach key=index item=jsModel from=$SCRIPTS}
<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

{foreach key=index item=cssModel from=$CSS}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

<input type="hidden" id="asset_pie" value='{$ASSET_PIE}' />
<input type="hidden" id="trailing_12_revenue" value='{$TRAILING_12_REVENUE}' />
<input type="hidden" id="trailing_12_aum" value='{$TRAILING_12_AUM}' />

<div id="AssetAllocationWidget">
{*{if $SOURCE_MODULE eq "PortfolioInformation"}*}
{*    <select id="reportselect">
        <option value="0" selected="selected">Report View...</option>
        <option class="selectedClick" value="OmniOverview" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Overview</option>
        <option class="selectedClick" value="AssetClassReport" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Asset Class Report</option>
        <option class="selectedClick" value="GainLoss" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Gain/Loss</option>
        <option class="selectedClick" value="GHReport" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">GH Report (Estimated Income)</option>
        <option class="selectedClick" value="GHReportActual" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">GH Report (Actual Income)</option>
        <option class="selectedClick" value="GH2Report" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER-L">GH2 Report</option>
        <option class="selectedClick" value="GHXReport" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER-L">GHX Report</option>
        <option class="selectedClick" value="LastYearIncome" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Income - Last Year</option>
        <option class="selectedClick" value="OmniProjected" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Income Projected</option>
        <option class="selectedClick" value="OmniIncome" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}' data-orientation="LETTER">Income - Trailing 12</option>
        <option class="selectedClick" value="OmniIntervalsDaily" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Intervals Daily</option>
        <option class="selectedClick" value="MonthOverMonth" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>Month Over Month</option>
        {if $CURRENT_USER->isAdminUser()}
            <option class="selectedClick" value="PortfoliosReset" data-account='{$ACCOUNTS}' data-calling='{$CALLING_RECORD}'>--Portfolios Transaction Reset--</option>
        {/if}
    </select>
*}
    <div class="pie_holder">
        <div id="revenuediv" style="text-align:center;">
            <h3>Asset Allocation</h3>
        </div>
        <div id="filtered_pie"></div>
{*        <div style="clear:both;"></div>*}
        <div id="legenddiv"></div>
        {if $MARGIN_BALANCE neq 0}
            <p>Margin Balance: <span style="{if $MARGIN_BALANCE lt 0}color:red;{else}color:green;{/if}">${$MARGIN_BALANCE|number_format:2}</span></p>
        {/if}
        {if $NET_CREDIT_DEBIT neq 0}
            <p>Net Credit Debit: <span style="{if $NET_CREDIT_DEBIT lt 0}color:red;{else}color:green;{/if}">${$NET_CREDIT_DEBIT|number_format:2}</span></p>
        {/if}
        {if $UNSETTLED_CASH neq 0}
            <p>Unsettled Cash: <span style="{if $UNSETTLED_CASH lt 0}color:red;{else}color:green;{/if}">${$UNSETTLED_CASH|number_format:2}</span></p>
        {/if}
    </div>
</div>
{/if}