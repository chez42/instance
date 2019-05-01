<div class="symbol_header" style="width:100%; display:block; float:left;">
        <h6 style="text-align:left;">{$SYMBOL_INFO->description} ({$SYMBOL_INFO->symbol})</h6>
        <h2>${$SYMBOL_INFO->last} {$DIFFERENCE}</h2>
</div>
<div style="clear:both;"></div>
<div class="symbol_body">
        <div class="col1" style="width:100%; display:block; float:left;">
            <table width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #666; border-radius:5px;">
                <tr>
                  <td>Asset Type</td>
                  <td>{$SYMBOL_INFO->assetType}</td>
                  <td>Real Time?</td>
                  <td>{$SYMBOL_INFO->isRealTime}</td>
                </tr>
                <tr>
                  <td>Exchange</td>
                  <td>{$SYMBOL_INFO->exchange}</td>
                  <td>Bid </td>
                  <td>${$SYMBOL_INFO->bid}</td>
                </tr>
                <tr>
                  <td>Ask </td>
                  <td>${$SYMBOL_INFO->ask}</td>
                  <td>Bid Size</td>
                  <td>{$SYMBOL_INFO->bidSize}</td>
                </tr>
                <tr>
                  <td>Ask Size</td>
                  <td>{$SYMBOL_INFO->askSize}</td>
                  <td>Last Price</td>
                  <td>${$SYMBOL_INFO->last}</td>
                </tr>
                <tr>
                  <td>High</td>
                  <td>${$SYMBOL_INFO->high}</td>
                  <td>Low</td>
                  <td>${$SYMBOL_INFO->low}</td>
                </tr>
                <tr>
                  <td>Volume</td>
                  <td>{$SYMBOL_INFO->volume}</td>
                  <td>Last Trade Date</td>
                  <td>{$SYMBOL_INFO->lastTradeDate}</td>
                </tr>
                <tr>
                  <td>Change</td>
                  <td>{$SYMBOL_INFO->change}</td>
                  <td>Year High</td>
                  <td>${$SYMBOL_INFO->yearHigh}</td>
                </tr>
                <tr>
                  <td>Year Low</td>
                  <td>${$SYMBOL_INFO->yearLow}</td>
                  <td>Dividend Amount</td>
                  <td>${$SYMBOL_INFO->dividendAmount}</td>
                </tr>
                <tr>
                  <td>Ratio</td>
                  <td>{$SYMBOL_INFO->peRatio}</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
            </table>
        </div>
            <input type="hidden" id="security_symbol" value="{$SYMBOL_INFO->symbol}" />
            <div class="thechart" style="min-width:100%; min-height:400px; display:block; float:left;"></div>
    </div>

    <!--
<table width="100%" cellspacing="0" cellpadding="5" style="border:1px solid #666; border-radius:5px;">
  <tr>
    <td>Description</td>
    <td>{$SYMBOL_INFO->description}</td>
  </tr>
  <tr>
    <td>Asset Type</td>
    <td>{$SYMBOL_INFO->assetType}</td>
  </tr>
  <tr>
    <td>Exchange</td>
    <td>{$SYMBOL_INFO->exchange}</td>
  </tr>
  <tr>
    <td>Bid </td>
    <td>$ {$SYMBOL_INFO->bid}</td>
  </tr>
  <tr>
    <td>Ask </td>
    <td>$ {$SYMBOL_INFO->ask}</td>
  </tr>
  <tr>
    <td>Bid Size</td>
    <td>{$SYMBOL_INFO->bidSize}</td>
  </tr>
  <tr>
    <td>Ask Size</td>
    <td>{$SYMBOL_INFO->askSize}</td>
  </tr>
  <tr>
    <td>Last Price</td>
    <td>${$SYMBOL_INFO->last}</td>
  </tr>
  <tr>
    <td>High</td>
    <td>$ {$SYMBOL_INFO->high}</td>
  </tr>
  <tr>
    <td>Low</td>
    <td>$ {$SYMBOL_INFO->low}</td>
  </tr>
  <tr>
    <td>Volume</td>
    <td>{$SYMBOL_INFO->volume}</td>
  </tr>
  <tr>
    <td>Last Trade Date</td>
    <td>{$SYMBOL_INFO->lastTradeDate}</td>
  </tr>
  <tr>
    <td>Change</td>
    <td>{$SYMBOL_INFO->change}</td>
  </tr>
  <tr>
    <td>Year High</td>
    <td>$ {$SYMBOL_INFO->yearHigh}</td>
  </tr>
  <tr>
    <td>Year Low</td>
    <td>$ {$SYMBOL_INFO->yearLow}</td>
  </tr>
  <tr>
    <td>Dividend Amount</td>
    <td>$ {$SYMBOL_INFO->dividendAmount}</td>
  </tr>
  <tr>
    <td>Ratio</td>
    <td>{$SYMBOL_INFO->peRatio}</td>
  </tr>
  <tr>
    <td>Real Time?</td>
    <td>{$SYMBOL_INFO->isRealTime}</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
  </tr>
</table>
    -->
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}