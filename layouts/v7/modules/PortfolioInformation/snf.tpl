<h2>Global</h2>
<div id="global_descriptions">
    <ul>
        <li>Copy Security Codes Table -- Copies the securities table from PC updating our own table and creating new elements if needed</li>
        <li>Copy Positions uses either date or symbol name.  If symbol name is empty, it will pull all securities from the specified date from PC and insert into our table.
            If a position name is used, it will pull only that symbol and ignore date entirely.</li>
        <li>Calendar Sharing will reset all users calendar sharing to selectedusers and assign their group members as shared.</li>
        <li>Copy All Mod Securities will pull all securities from the securities table, match them against what we currently have in our ModSecurities Module, then insert all
            that do not exist already -- This may take some time</li>
        <li>Update All Securities Prices takes the latest price from the pricing table and updates the ModSecurities module value accordingly.  This takes awhile</li>
        <li>Asset Allocations calculates the assets for all portfolios and puts it in the vtiger_portfolioinformation_current table (this takes a long time -- couple hours)</li>
        <li>Remove Bad Portfolios -- Takes <strong>ALL</strong> portfolios from PC and compares them against ours.  If we have one they don't, we remove ours.</li>
        <li>Pull Individual Security -- Pulls an individual security from PC and puts it into our vtiger_securities table if it doesn't already exist</li>
        <li>Fix Portfolio Information Numbers -- If the Portfolio Information module is showing massive high numbers due to the cron still running, this will set them to the previous day's values.  When the cron is done, it will update itself</li>
        <li>Reset Transactions wipes all transactions from the CRM and re-pulls them from PC</li>
    </ul>
</div>
<p>
    <input id="securityCodes" type="button" value="Copy Security Codes Table" />
    <input id="badPortfolios" type="button" value="Remove Bad Portfolios" />
    <input id="copyCurrent" type="button" value="Fix Portfolio Information Numbers" />
</p>
<p>
    <input id="positionDate" type="text" placeholder="Y-m-d to start from" value="{$HISTORICAL_DATE}" />&nbsp;&nbsp;
    <!--<input id="positionName" type="text" placeholder="Security Symbol" value="" />&nbsp;&nbsp;-->
    <input id="copyPositions" type="button" value="Security Update" />
</p>

<p>
    <input id="calendarSharing" type="button" value="Calendar Sharing" />
    <input id="ModSecuritiesAll" type="button" value="Copy All Mod Securities" />
    <input id="ModSecuritiesPricingAll" type="button" value="Update All Securities Prices" />
</p>

<p>
    <input id="autoCloseAccounts" type="button" value="Auto Close Accounts" />
</p>

<p>
    <input id="assetAccountNumber" type="text" placeholder="Account Number" />&nbsp;
    <input id="accountAssetAllocation" type="button" value="Individual Asset Allocation" />
    <input id="assetAllocation" type="button" value="All Asset Allocations" />
</p>
<p>
    <input id="individualSecurity" type="text" placeholder="Pull Individual Security (enter symbol)" />&nbsp;
    <input id="pullIndividualSecurity" type="button" value="Pull Individual Security" />
</p>
<p>
    <input id="InsertSecurityPriceName" type="text" placeholder="Security Name" />&nbsp;
    <input id="InsertSecurityPricePrice" type="text" placeholder="Price" />&nbsp;
    <input id="InsertSecurityPriceDate" type="text" placeholder="yyyy-mm-dd" />
    <input id="InsertSecurityPriceSubmit" type="button" value="Insert New Price" />
</p>
<h2>Individual Cron Tasks</h2>
    <ul>
        <li>Pull Latest Prices pulls all prices from the PC pricing table.  Today - 1 week</li>
        <li>Pull all historical price for the specified security</li>
    </ul>
<p>
    <input id="pullPrices" type="button" value="Pull Latest Prices" /></p>
<p>
    <input id="securityPriceName" type="text" placeholder="Security Name" /><input id="pullSecurityPrice" type="button" value="Pull Security Price" />
</p>

<h2>Account Specific</h2>
<p>
    <input id="transactions_account_number" type="text" placeholder="Account Number" />&nbsp;
    <input id="transactions_account_reset" type="button" value="Reset Transactions" />
</p>
<p>
    <input id="portfolio_id" type="text" placeholder="Portfolio ID" />&nbsp;
    <input id="portfolio_id_reset" type="button" value="Reset Portfolio Transactions" />    
</p>

<p>
    Mod Securities:
    <input id="ModSecurity" type="text" placeholder="Security Symbol" />&nbsp;
    <input id="ModSecuritiesIndividual" type="button" value="Copy Individual Security" />
    <input id="ModSecuritiesIndividualPrice" type="button" value="Update Individual Security Price" />
</p>

<h2>Advisor Specific</h2>
<ul>
    <li>Reset transactions using advisor control number will reset all transactions for accounts associated with the given control number</li>
</ul>
<p>
    <input id="control_number" type="text" placeholder="Control Number" />&nbsp;
    <input id="control_number_transactions_reset" type="button" value="Reset Transactions" />
    <input id="portfolio_information_numbers_reset" type="button" value="Reset PortfolioInformation Numbers" />
</p>

<h2>Audit Filtering</h2>
<ul>
    <li>Reset Clients will reset the client check box to 'no' for everybody in the system first.  
        This is an issue if there is a contact with no portfolios attached to them that is a client, they won't auto fill with the rest.</li>
    <li>Update client "yes" contacts will set all contacts associated with PortfolioInformation to yes</li>
    <li>Update client "yes" households will set all households associated with PortfolioInformation to yes</li>
    <li>Attach SMAAccountDescription will attach appropriately to the PortfolioInformation module based on account number</li>
</ul>

<p>
    Reset Clients? <input type="checkbox" id="ResetClients" /><br />
    <input id="ClientContacts" type="button" value="ClientContacts" /><br />
    <input id="ClientHouseholds" type="button" value="ClientHouseholds" /><br />
    <input id="SMAAccountDescription" type="button" value="SMA Account Description" />
</p>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}