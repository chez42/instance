{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{*<input type="hidden" class="chartdata" value='{$CHARTDATA}' />*}

<div class="ReportTopOmni">
    <div class="AccountDetails">
        <div><strong>{$DATE}</strong></div>
        <p><strong>Account Details</strong></p>
        <p><span class="left">CLIENT:</span><span class="right">{$PORT_INFO.first_name} {$PORT_INFO.last_name}</span></p>
        <p><span class="left">ACCT:</span><span class="right"><strong>{$PORT_INFO.account_number}</strong></span></p>
        <p><span class="left">SECURITIES:</span><span class="right">${$PORT_INFO.market_value|number_format:2:".":","}</span></p>
        <p><span class="left">CASH:</span><span class="right">${$PORT_INFO.cash_value|number_format:2:".":","}</span></p>
        <p><span class="left">TOTAL:</span><span class="right">${$PORT_INFO.total_value|number_format:2:".":","}</span></p>
        <p><span class="left">ANNL REVS:</span><span class="right">{$PORT_INFO.annual_management_fee|number_format:2:".":","}</span></p>
    </div>
</div>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
