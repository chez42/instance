{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

{foreach key=index item=cssModel from=$CSS}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

<div id="holdings_widget_switch_container">
    <select id="pie_type">
        <option value="0" selected="selected">Asset Class</option>
        <option value="1">Security Type</option>
        <option value="2">Security Symbol</option>
    </select>

    <div id="holdings_widget_switches">
        <label>Depth:</label><input id="depthRange" type="range" min="0" max="25" value="14" style="display:inline" />
        <label>Angle:</label><input id="angleRange" type="range" min="0" max="60" value="25" style="display:inline"/>
    </div>
</div>

<div id="widget_container" style="width:100%;" data-pie='{$ASSET_PIE}'>
    <div id="HoldingsWidgetPie" style="width: 100%; height:400px; position:relative; "></div>
    <div id="HoldingsWidgetLegend" style="width:100%; height:400px; position:relative; "></div>
</div>