{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

{if $HASFINWORX}
{*    <p id="finworx_link" data-link="{$LINK}">Contact Report</p>
    <p id="finworx_report">{$HTML}</p>*}
    <iframe id='finworx_window' width="100%" height="480" data-link="{$LINK}"></iframe>
{else}
    <p>A risk assessment has not been filled in for this contact yet</p>
{/if}
