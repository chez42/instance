{foreach key=index item=cssModel from=$CSS}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

<div id="consolidated_chart" data-vals='{$CONSOLIDATED}' style="width:100%; min-height: 200px;"></div>
