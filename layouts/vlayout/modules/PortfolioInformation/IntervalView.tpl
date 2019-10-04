{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}?parameter=1" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

<form id="IntervalForm" method="post" action="index.php?module=PortfolioInformation&view=IntervalReport">
    <input type="hidden" name="module" value="PortfolioInformation" />
    <input type="hidden" name="view" value="IntervalReport" />
    <input type="hidden" name="source_module" id="source_module" value="{$SOURCE_MODULE}" />
    <input type="hidden" name="source_record" id="source_record" value="{$SOURCE_RECORD}" />
    <input type="hidden" name="account_numbers" id="account_numbers" value="{$ACCOUNT_NUMBERS}" />
    <input type="hidden" id="start_date" name="start_date" value="" />
    <input type="hidden" id="end_date"  name="end_date" value="" />
    <input type="hidden" id="calculated_return" name="calculated_return" value="" />
</form>

<div id="chartdiv" style="width:100%; height: 500px; font-size: 11px;"></div>
{*
<form method="post" id="export">
    <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
    <input type="hidden" value="PortfolioInformation" name="module" />
    <input type="hidden" value="HoldingsReport" name="view" />
    <input type="hidden" value="" name="pie_image" id="pie_image" />
    <input type="hidden" value="" name="aum_image" id="aum_image" />
    <input type="hidden" value="" name="revenue_image" id="revenue_image" />
    <input type="hidden" value="1" name="pdf" />
    <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
</form>*}
{if $INTERVALS|@count > 0}
    <p><strong>Disclaimer: </strong>This page is currently in alpha testing and values may not have an accurate representation of the account</p>
    <table style="width:100%;">
        <tr style="width:100%">
        <td style="width:80%">
            <table id="IntervalTable" border="1px solid black;" style="width:100%;">
                <thead>
                <tr>
                    <td style="text-align:center; padding:2px;">Account Number</td>
                    <td style="text-align:center; padding:2px;">Begin Date</td>
                    <td style="text-align:center; padding:2px;">End Date</td>
                    <td style="text-align:center; padding:2px;">Begin Value</td>
                    <td style="text-align:center; padding:2px;">Deposits / Withdrawals</td>
                    <td style="text-align:center; padding:2px;">Investment Return</td>
                    <td style="text-align:center; padding:2px;" class="end_value">End Value</td>
                    <td style="text-align:center; padding:2px;">Period Return %</td>
                </tr>
                </thead>
                <tbody>
                {foreach item=v from=$INTERVALS}
                    <tr>
                        <td style="padding:2px;">{$v.account_number}</td>
                        <td style="padding:2px;">{$v.begin_date}</td>
                        <td style="padding:2px;" class="end_date">{$v.end_date}</td>
                        <td style="text-align:right; padding:2px;">${$v.begin_value|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;">${$v.net_flow|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;">${$v.investment_return|number_format:2:".":","}</td>
                        <td style="text-align:right; padding:2px;">${$v.end_value|number_format:2:".":","}</td>

                        <td style="text-align:right; padding:2px;" class="period_return" data-period_return='{$v.period_return|number_format:2:".":","}'>{$v.period_return|number_format:2:".":","}%</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </td>
        <td style="width:100%; vertical-align:top; text-align:center;">
            <h2 style="font-size:12px;">Calculated Return</h2>
            <p style="font-weight:bold; font-size:16px;" class="calculated_return"></p>
        </td>
        </tr>
    </table>
{else}
    <h2>Sorry, there are no Intervals available currently</h2>
{/if}

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}