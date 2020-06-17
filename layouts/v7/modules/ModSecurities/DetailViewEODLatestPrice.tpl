{*{foreach key=index item=jsModel from=$EXTRA_SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}

{foreach key=index item=cssModel from=$EXTRA_STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{*<div id="price_wrapper">
    <p>Delayed Price: <strong>${$EOD->close}</strong></p>
    <p>Last Update: <strong>{$EOD->last_update}</strong></p>
</div>*}

{if $EOD->close eq "NA"}
    <div id="no_result">
        <p>No delayed pricing available</p>
    </div>
{else}
    <div id="price_wrapper" style="padding:5px; font-size:16px;">
        {if isset($LOGO)}
            <div class="logo_left">
                <img src="{$LOGO}" class="logo" />
            </div>
        {/if}
        <table id="delayed_pricing">
            <tr>
                <td>
                    <strong><span style="color:#0d3d78; font-size:18px;">${$EOD->close}</span></strong>
                </td>
                <td>
                    <strong>
                        {if $CHANGE >= 0}
                            <span style="color:#35aa47; font-size:16px;">+${$CHANGE} <span style="font-size:12px;">({$PERCENTAGE|number_format:2:".":","}%)</span></span>
                        {else}
                            <span style="color:red; font-size:16px;">${$CHANGE} <span style="font-size:12px;">({$PERCENTAGE|number_format:2:".":","}%)</span></span>
                        {/if}
                    </strong>
                </td>
            </tr>
            <tr>
                <td class="small_text rborder">
                    Open: <span class="right">{$EOD->open|number_format:2:".":","}</span>
                </td>
                <td class="small_text lborder">
                    High: <span class="right">{$EOD->high|number_format:2:".":","}</span>
                </td>
            </tr>
            <tr>
                <td class="small_text rborder">
                    Low: <span class="right">{$EOD->low|number_format:2:".":","}</span>
                </td>
                <td class="small_text lborder">
                    Close: <span class="right">{$EOD->close|number_format:2:".":","}</span>
                </td>
            </tr>
            <tr>
                <td class="small_text rborder">
                    Vol: <span class="right">{$EOD->volume}</span>
                </td>
                <td class="small_text lborder">
                    Prev Close: <span class="right">{$EOD->previousClose|number_format:2:".":","}</span>
                </td>
            </tr>
{*        <p style="font-size:12px;"></p>
        <p style="font-size:8px;"></p>*}
        </table>
        <table id="delayed_pricing_lower">
            <tr>
                <td class="center-align f10" {if !isset($LOGO)} style="padding-top:10px; padding-left:50px;" {/if}>
                    <strong>{$EOD->last_update}</strong>
                </td>
                <td class="center-align">
                    <strong>{$NOTES}</strong>
                </td>
            </tr>
        </table>
    </div>
{/if}
