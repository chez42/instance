{foreach key=index item=jsModel from=$EXTRA_SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

{foreach key=index item=cssModel from=$EXTRA_STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

<h2>Historical Pricing</h2>
<input id="record_id" type='hidden' value='{$RECORD_ID}' />
<table id="historical_prices">
    <thead>
        <tr>
            <td>Price</td>
            <td>Date</td>
        </tr>
    </thead>
    <tbody>
    {foreach from=$PRICING_HISTORY item=v}
        <tr {if $v@iteration > 10} class='hidden' {/if}>
            <td><input style='width:100px;' type="text" value="{$v.price}" class="historical_price" data-id="{$v.security_price_id}" data-original='{$v.price}' /></td>
            <td>{$v.american_format}</td>
        </tr>
    {/foreach}
    </tbody>
</table>

<a href='#' onclick='return false;' class='show_more'>Show More</a>
