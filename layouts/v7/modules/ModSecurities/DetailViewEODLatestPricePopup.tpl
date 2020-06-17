{*{foreach key=index item=jsModel from=$EXTRA_SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}

{foreach key=index item=cssModel from=$EXTRA_STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{if $EOD->close eq "NA"}
    <div id="no_result">
        <p>No delayed pricing available</p>
    </div>
    {else}
    <div id="price_wrapper" style="padding:5px; font-size:16px;">
        <p><strong><span style="color:yellow; font-size:18px;">${$EOD->close}</span></strong>
            <strong>
                {if $CHANGE >= 0}
                    <span style="color:#35aa47; font-size:16px;"><span style="font-size:16px;">+</span>${$CHANGE} ({$PERCENTAGE|number_format:2:".":","}%)</span>
                {else}
                    <span style="color:red; font-size:16px;">${$CHANGE} ({$PERCENTAGE|number_format:2:".":","}%)</span>
                {/if}
            </strong>
            {if isset($LOGO)}
                <img src="{$LOGO}" width="25" />
            {/if}
        </p>
        <p style="font-size:10px;"><strong>{$EOD->last_update}</strong></p>
        <p style="font-size:8px;"><strong>{$NOTES}</strong></p>
    </div>
{/if}
