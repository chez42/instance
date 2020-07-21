{*{foreach key=index item=jsModel from=$EXTRA_SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}

{foreach key=index item=cssModel from=$EXTRA_STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

<div id="price_wrapper" style="padding:5px; font-size:16px;">
    <p>{$STOCK->symbol}
        {if isset($STOCK->logo)}
            <img src="{$STOCK->logo}" width="25" />
        {/if}
    </p>
    <p><strong><span style="color:yellow; font-size:18px;">${$STOCK->price}</span></strong>
        <strong>
            {if $STOCK->change >= 0}
                <span style="color:#35aa47; font-size:16px;"><span style="font-size:16px;">+</span>${$STOCK->change} ({$STOCK->change_percent|number_format:2:".":","}%)</span>
            {else}
                <span style="color:red; font-size:16px;">${$STOCK->change} ({$STOCK->change_percent|number_format:2:".":","}%)</span>
            {/if}
        </strong>
    </p>
    <p style="font-size:10px;">Fiscal Year End: <strong>{$STOCK->fiscal}</strong></p>
    <p style="font-size:10px;">{$STOCK->description}</p>
    <p style="font-size:10px;"><strong>As of: {$STOCK->as_of}</strong></p>
</div>
