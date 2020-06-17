{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}
<div id="titlepage">
{*    <div id="leftside" style="float:left; width:48%;">
        <img src='test/logo/logo.png' />
    </div>*}
    <div id="rightside" style="float:right; width:48%;">
        <h2>{$REPORT_TYPE}<br />{$DATE}</h2>
        <br /><br />
        <p style="width:60%; display:block; border-bottom:1px solid black"><span style="font-size:8px;">Prepared for</span></p>
        {if $HOUSEHOLD}
            <p style="margin:0;padding:0">{$HOUSEHOLD->get('accountname')}</p>
            <p style="margin:0;padding:0">{$HOUSEHOLD->get('phone')}</p>
            <p style="margin:0;padding:0">{$HOUSEHOLD->get('email1')}</p>
        {/if}
        {if $HAS_ADVISOR eq 1}
            <br /><br />
            <p style="width:60%; display:block; border-bottom:1px solid black"><span style="font-size:8px;">Assigned to</span></p>
            <p style="margin:0;padding:0">{$ASSIGNED_TO}</p>
            <br /><br />
            <p style="width:60%; display:block; border-bottom:1px solid black"><span style="font-size:8px;">Advisor</span></p>
            <p style="margin:0;padding:0">{$ADVISOR->get('first_name')} {$ADVISOR->get('last_name')}</p>
            <p style="margin:0;padding:0">{$ADVISOR->get('address_street')}</p>
            <p style="margin:0;padding:0">{$ADVISOR->get('address_city')}</p>
            <p style="margin:0;padding:0">{$ADVISOR->get('address_state')}</p>
            <p style="margin:0;padding:0">{$ADVISOR->get('address_postalcode')}</p>
            <p style="margin:0;padding:0">{$ADVISOR->get('phone_work')}</p>
        {else}
            <br /><br />
            <p style="width:60%; display:block; border-bottom:1px solid black"><span style="font-size:8px;">Advisor</span></p>
            <p style="margin:0;padding:0">{$ASSIGNED_TO}</p>
        {/if}
    </div>
</div>
<div style="page-break-after: always" />