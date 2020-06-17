{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}
<div id="tableofcontents">
    <div id="rightside" style="float:right;width:48%;font-family:Arial,Sans-Serif;font-size:16px;">
        <h2>Table Of Contents</h2>
    </div>
    <div class="leftside" style="text-align:left;padding-top:100px; width:50%; clear:both;font-family:Arial,Sans-Serif;font-size:16px;">
        <table class="TableOfContentsTable" style="width:100%;">
            <tr>
                <td colspan="2" style="border-bottom: 1px dotted black;"><h2>Section</h2></td>
            </tr>
            {foreach key=index item=v from=$TOC}
                <tr>
                    <td>{$v.title}</td>
                    <td style="text-align:right;">{$v.name}</td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>
<div style="page-break-after: always">&nbsp;</div>	