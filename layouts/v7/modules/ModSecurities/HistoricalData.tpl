<input type="hidden" class="price_data" value='{$PRICE_DATA}' />

<div id="chartdiv" style="height: {$HEIGHT};"></div>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}