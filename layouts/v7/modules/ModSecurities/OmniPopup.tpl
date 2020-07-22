<link type='text/css' rel='stylesheet' href='layouts/v7/modules/ModSecurities/css/EODPopup.css' />

<div id="price_wrapper" style="padding:5px; font-size:16px;">
    <p>{$OMNI->symbol}
        {if isset($OMNI->logo)}
            <img src="{$OMNI->logo}" width="25" />
        {/if}
    </p>
    <p>{$OMNI->name}</p>
    <p><strong><span style="color:yellow; font-size:18px;">${$OMNI->price}</span></strong>
        <strong>
            {if $OMNI->change >= 0}
                <span style="color:#35aa47; font-size:16px;"><span style="font-size:16px;">+</span>${$OMNI->change} ({$OMNI->change_percent|number_format:2:".":","}%)</span>
            {else}
                <span style="color:red; font-size:16px;">${$OMNI->change} ({$OMNI->change_percent|number_format:2:".":","}%)</span>
            {/if}
        </strong>
    </p>
    <p style="font-size:10px;">{$OMNI->description}</p>
    <p style="font-size:10px;"><strong>As of: {$OMNI->as_of}</strong></p>
</div>
