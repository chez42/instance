<div style="padding:10px;">
    {if $timer eq false}
        <p>{vtranslate('no existing timer', 'Timecontrol')}</p>
        <input type="button" class="btn" onclick="Timecontrol.openNewTimer();" value="{vtranslate('create timer', 'Timecontrol')}" />

    {else}
        {foreach from=$timers item=record}
            <div style="overflow: hidden;">
                <img src="modules/Timecontrol/images/clock-green-small.png" class="pull-left" />
                <p  style="margin:16px 0 0px 0;"class="pull-right"><a class="btn btn-primary" href="index.php?module=Timecontrol&action=Finish&record={$record.timecontrolid}">{vtranslate('Stop')}</a></p>
                <p style="margin:5px 0 5px 0;"><a href="index.php?module=Timecontrol&view=Detail&record={$record.timecontrolid}">{$record.title}</a></p>
                <p id="sidebarTimer" data-staredts="{$record.timestamp}" style="font-size: 17px; font-weight: bold;">&nbsp;</p>
            </div>
        {/foreach}
    {/if}
</div>
<script type="text/javascript">jQuery(function() { Timecontrol.initTimer('#sidebarTimer'); });</script>