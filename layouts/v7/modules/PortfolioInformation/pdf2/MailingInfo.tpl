{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}
<div id="mailinginfo">
    <div class="leftside" style="float:left; width:48%;">
        <img src="{if $LOGO neq ''}{$LOGO}{else}test/logo/Omni-CRM-LOGO-SMALL.png{/if}" width="100%"/>
    </div>
    <div class="rightside" style="float:right; width:48%;font-family:Arial,Sans-Serif;font-size:16px;">
        <p style="width:60%; display:block; border-bottom:1px solid black;font-family:Arial,Sans-Serif;font-size:16px;">Prepared for</p>
        {if $MAILING_INFO}
            {if $MAILING_INFO['name']}
                <p style="margin:0;padding:0">{$MAILING_INFO['name']}</p>
            {/if}
            {if $MAILING_INFO['street']}
                <p style="margin:0;padding:0">{$MAILING_INFO['street']}</p>
            {/if}
            {if $MAILING_INFO['city']}
                <p style="margin:0;padding:0">{$MAILING_INFO['city']}</p>
            {/if}
            {if $MAILING_INFO['state']}
                <p style="margin:0;padding:0">{$MAILING_INFO['state']}</p>
            {/if}
            {if $MAILING_INFO['zip']}
                <p style="margin:0;padding:0">{$MAILING_INFO['zip']}</p>
            {/if}
        {/if}
    </div>
</div>

<div style="page-break-after: always">&nbsp;</div>