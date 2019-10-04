{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

<div id="phantom_pie" data-value='{$PIE_VALUES}' style="display:block; width:400px; height:100%;"></div>