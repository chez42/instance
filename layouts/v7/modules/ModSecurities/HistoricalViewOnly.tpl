<div style="display:block; float:left; clear:both; width:{$WIDTH}}; height:{$HEIGHT};">
    <input type="hidden" class="price_data" value='{$PRICE_DATA}' />
    <div id="chartdiv" style="width: {$WIDTH}; height: {$HEIGHT};"></div>
</div>
    {foreach key=index item=jsModel from=$SCRIPTS}
        <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
    {/foreach}