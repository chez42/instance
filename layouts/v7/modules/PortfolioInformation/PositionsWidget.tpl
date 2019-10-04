{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}?parameter=1" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{*<input type="button" id="HoldingsReport" value="Generate Holdings Report" data-account='{$ACCOUNT}' data-calling="{$CALLING_RECORD}"/>*}
{if $SOURCE_MODULE eq "PortfolioInformation"}
    <table style="width:100%">
        <tbody>
            <tr>
{*                <td>
                    <input type="button" class="btn btn-success" id="OmniviewReport" onclick="return false;" style="width:100%; float:left;" data-account='{$ACCOUNT}' data-calling='{$CALLING_RECORD}' value="OmniVue" />
                </td>*}
                <td>
                    <input type="button" class="btn btn-success" id="HoldingsReport" onclick="return false;" style="width:100%; float:left;" data-account='{$ACCOUNT}' data-calling='{$CALLING_RECORD}' value="Holdings" />
                </td>
{*                <td>
                    <input type="button" id="IncomeReport" class="btn btn-success report_detail" onclick="return false;" style="width:100%; float:left; margin-left:2px;" data-account='{$ACCOUNT}' value="Income" />
                </td>
                <td>
                    <input type="button" id="OverviewReport" class="btn btn-success report_detail" onclick="return false;" style="width:100%; float:left;  margin-left:2px;" data-account='{$ACCOUNT}' value="Overview" />
                </td>*}
            </tr>
        </tbody>
    </table>
{/if}
<input type="hidden" id="source_module" value="{$SOURCE_MODULE}" />
<input type="hidden" id="source_record" value="{$SOURCE_RECORD}" />

{if $POSITIONS|@count > 0}
    <table id="ReportWidgetTable">
        <thead>
            <tr>
                <td>Symbol</td>
                <td class="aright">Quantity</td>
                <td class="aright">Price</td>
                <td class="aright">Total</td>
                <td class="aright">Weight</td>
            </tr>
        </thead>
        <tbody>
            {foreach item=v from=$POSITIONS}
                <tr>
                    <td>
                        <div class="context_stuff">
                            <label class="hover_symbol" id="{$v.security_symbol}" data-position_record="{$v.positioninformationid}" data-security_record="{$v.modsecuritiesid}" data-account='{$ACCOUNT}'>{$v.security_symbol}</label>
                        </div>
                    </td>
                    <td class="aright">{$v.quantity|number_format:2:".":","}</td>
                    <td class="aright">${$v.last_price|number_format:4:".":","}</td>
                    <td class="aright">${$v.current_value|number_format:2:".":","}</td>
                    <td class="aright">{$v.weight|number_format:2:".":","}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}