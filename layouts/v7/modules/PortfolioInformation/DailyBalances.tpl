<div id="BalanceWrapper" style="display:block; width:1000px;">
    <div id="BalanceHeader">
        <h1>Daily Balances {$CUSTODIAN_NAME}</h1>
    </div>
    <div id="CustodianLinks">
        <a href="index.php?module=PortfolioInformation&view=DailyBalances&custodian_name=fidelity">Fidelity</a>,
        <a href="index.php?module=PortfolioInformation&view=DailyBalances&custodian_name=pershing">Pershing</a>,
        <a href="index.php?module=PortfolioInformation&view=DailyBalances&custodian_name=td">TD</a>,
        <a href="index.php?module=PortfolioInformation&view=DailyBalances&custodian_name=schwab">Schwab</a>
    </div>
    <div id="BalanceBody">
        <table id="FidelityBalances" border="1" style="font-size:10px;">
            <thead>
                <tr>
                    {foreach from=$CUSTODIAN_HEADERS key=k item=v}
                        <td>{$k}</td>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
                {foreach from=$CUSTODIAN key=k item=v}
                <tr>
                    {foreach from=$CUSTODIAN_HEADERS key=h item=hv}
                        <td>{$v[$h]}</td>
                    {/foreach}
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>