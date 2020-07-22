{*{foreach key=index item=jsModel from=$EXTRA_SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}

<link type='text/css' rel='stylesheet' href='layouts/v7/modules/ModSecurities/css/EODPopup.css' />

<div id="price_wrapper" style="padding:5px; font-size:16px;">
    <p><strong><span style="color:yellow; font-size:18px;">${$OPTION->price|number_format:2:".":","}</span></strong>
        <strong>
            {if $OPTION->change >= 0}
                <span style="color:#35aa47; font-size:16px;"><span style="font-size:16px;">+</span>${$OPTION->change|number_format:2:".":","} ({$OPTION->change_percent|number_format:2:".":","}%)</span>
            {else}
                <span style="color:red; font-size:16px;">${$OPTION->change|number_format:2:".":","} ({$OPTION->change_percent|number_format:2:".":","}%)</span>
            {/if}
        </strong>
        {if isset($OPTION->logo)}
            <img src="{$OPTION->logo}" width="25" />
        {/if}
    </p>
    {if isset($OPTION->call)}
        <p>Option Info</p>
        <div class="eod_table">
            <div class="eod_tr">
                <div class="eod_td">{$OPTION->call->contractName}</div>
                <div class="eod_td">Call</div>
            </div>
            <div class="eod_tr">
                <div class="eod_td">Volatility - {$OPTION->call->impliedVolatility}</div>
                <div class="eod_td">Strike - ${$OPTION->call->strike}</div>
            </div>
            <div class="eod_tr">
                <div class="eod_td">In The Money</div>
                <div class="eod_td">{$OPTION->call->inTheMoney}</div>
            </div>
            <div class="eod_tr">
                <div class="eod_td">Expiration</div>
                <div class="eod_td">{$OPTION->call->expirationDate}</div>
            </div>
        </div>
    {elseif isset($OPTION->put)}
        <p>Option Info</p>
        <div class="eod_table">
            <div class="eod_tr">
                <div class="eod_td">{$OPTION->put->contractName}</div>
                <div class="eod_td">Put</div>
            </div>
            <div class="eod_tr">
                <div class="eod_td">Volatility - {$OPTION->put->impliedVolatility}</div>
                <div class="eod_td">Strike - ${$OPTION->put->strike}</div>
            </div>
            <div class="eod_tr">
                <div class="eod_td">In The Money</div>
                <div class="eod_td">{$OPTION->put->inTheMoney}</div>
            </div>
            <div class="eod_tr">
                <div class="eod_td">Expiration</div>
                <div class="eod_td">{$OPTION->put->expirationDate}</div>
            </div>
        </div>
    {else}
        <p>Extra option data unavailable</p>
    {/if}

        <p style="font-size:10px;"><strong>As of: {$OPTION->as_of}</strong></p>
</div>
{*
stdClass Object (
[contractName] => MU200717C00050000
[contractSize] => REGULAR
[currency] => USD
[type] => CALL
[inTheMoney] => TRUE
[lastTradeDateTime] => 2020-07-15 15:59:59
[expirationDate] => 2020-07-17
[strike] => 50
[lastPrice] => 1.06
[bid] => 1.05
[ask] => 1.09
[change] => 0.27
[changePercent] => 0.3418
[volume] => 4377
[openInterest] => 36203
[impliedVolatility] => 52.3133
[delta] => 0.6115
[gamma] => 0.1959
[theta] => -0.1885
[vega] => 0.0143
[rho] => 0.0016
[theoretical] => 1.06
[intrinsicValue] => -3.73
[timeValue] => 6.58
[updatedAt] => 2020-07-15 23:03:55
[daysBeforeExpiration] => 1 )
*}