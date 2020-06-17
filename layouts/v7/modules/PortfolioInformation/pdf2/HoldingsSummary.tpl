<div id="rightside" style="float:right; width:48%; clear:both;">
    <h2>OMNIVue Asset Allocation</h2>
</div>
{assign var="ou" value=$ASSET_CLASS.other+$ASSET_CLASS.unclassified}
<div class="block_wrapper">
    <div id="holdings_description">
        <p>OMNIVue&trade; determines the <span style="text-decoration: underline;">actual</span> exposure to various
            asset classes utilizing data from respected 3rd parties to provide a more accurate view of a portfolio&rsquo;s true asset allocation.<br /><br />
            <span style="font-size: 8px; font-style: italic">OMNIVue&trade; relies on the estimates and approximations of these 3rd parties to perform its calculations.
                    As such, the totals and values represented in OMNIVue&trade; may not correspond exactly to custodial statement balances and other reports of actual account value.</span>
        </p>
    </div>
    <div id="holdings_summary_wrapper">
        <div id="holdings_summary_table_holder">
            <table id="holdings_summary_table">
                <thead>
                    <tr>
                        <td class="report_heading">Holdings Summary</td>
                        <td class="report_heading">Weight</td>
                        <td class="report_heading tright">Total</td>
                    </tr>
                    <tr>
                        <td class="align_left">Total Value</td>
                        <td class="align_center">-</td>
                        <td class="align_right">${$GLOBAL_TOTAL.global_total|number_format:2:".":","}</td>
                    </tr>
                </thead>
                <tbody>
                {if $ASSET_CLASS.equities neq 0}
                    <tr class="make_dotted">
                        <td class="left" style="">Equity</td>
                        <td class="right" style="">{$ASSET_CLASS_WEIGHT.equities|number_format:2}%</td>
                        <td class="right" style="">${$ASSET_CLASS.equities|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;US Stock</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.us_stock_value|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;Intl Stock</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.intl_stock_value|number_format:2:".":","}</td>
                    </tr>
                {/if}
                {if $ASSET_CLASS.fixed neq 0}
                    <tr class="make_dotted">
                        <td class="left" style="">Fixed Income</td>
                        <td class="right" style="">{$ASSET_CLASS_WEIGHT.fixed|number_format:2}%</td>
                        <td class="right" style="">${$ASSET_CLASS.fixed|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;US Bond</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.us_bond_value|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;Intl Bond</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.intl_bond_value|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;Preferred</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.preferred_net_value|number_format:2:".":","}</td>
                    </tr>
                {/if}
                {if $ASSET_CLASS.cash neq 0}
                    <tr class="make_dotted">
                        <td class="left" style="">Cash</td>
                        <td class="right" style="">{$ASSET_CLASS_WEIGHT.cash|number_format:2}%</td>
                        <td class="right" style="">${$INDIVIDUAL_AC.cash_net_value|number_format:2:".":","}</td>
                    </tr>
                {/if}
                {if $ou neq 0}{* $ou refers to the combination of Other + Unclassified.  Defined at the top of this file *}
                    <tr class="make_dotted">
                        <td class="left" style="">Other</td>
                        <td class="right" style="">{$ASSET_CLASS_WEIGHT.other|number_format:2+$ASSET_CLASS_WEIGHT.unclassified|number_format:2}%</td>
                        <td class="right" style="">${$ou|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;Convertible</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.convertible_net_value|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;Other</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.other_net_value|number_format:2:".":","}</td>
                    </tr>
                    <tr>
                        <td class="left" style="">&nbsp;&nbsp;Unclassified</td>
                        <td class="right" style=""> - </td>
                        <td class="right" style="">${$INDIVIDUAL_AC.unclassified_net_value|number_format:2:".":","}</td>
                    </tr>
                {/if}
        {*        {foreach from=$CATEGORIES key=ck item=cv}
                    <tr class="primary" style="background-color:{$COLORS.$ck}">
                        <td class="report_heading">{$ck}</td>
                        <td class="right">{$cv.weight|number_format:2:".":","}%</td>
                        <td class="right strong">${$cv.total|number_format:2:".":","}</td>
                    </tr>
                {/foreach}*}
                </tbody>
            </table>
        </div>
        <div id="holdings_summary_pie">
            {if !empty($PIE_FILE) AND $PIE_IMAGE eq 1}
                <img src="{$PIE_FILE}" style="width:70%; float:right;"/>
            {else}
                <div id="report_top_pie" class="report_top_pie" style="height: 320px; width: 450px; float:left; margin-top:-20px;"></div>
            {/if}
        </div>
    </div>
</div>