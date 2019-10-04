<h2>Global</h2>
<div id="global_descriptions">
    <ul>
        <li>Copy Security Codes Table -- Copies the securities table from PC updating our own table and creating new elements if needed</li>
        <li>Historical Update -- Updates the Historical charts starting from the provided month.  This takes an extremely long time so set it and forget it.
            It will also <strong>never</strong> return a result due to taking so long so the best way to check the status for now is by checking the chart on live.
            Another note, the provided month is the first day of last month, this should almost always be the case and normally not have to be touched.</li>
        <li>Copy Positions uses either date or symbol name.  If symbol name is empty, it will pull all securities from the specified date from PC and insert into our table.
            If a position name is used, it will pull only that symbol and ignore date entirely.</li>
        <li>Calendar Sharing will reset all users calendar sharing to selectedusers and assign their group members as shared.</li>
        <li>Copy All Mod Securities will pull all securities from the securities table, match them against what we currently have in our ModSecurities Module, then insert all
            that do not exist already -- This may take some time</li>
        <li>Update All Securities Prices takes the latest price from the pricing table and updates the ModSecurities module value accordingly.  This takes awhile</li>
        <li>Pull All Securities pulls all securities from PC, compares them to the vtiger_securities table and anything we don't have it will insert</li>
        <li>Asset Allocations calculates the assets for all portfolios and puts it in the vtiger_portfolioinformation_current table (this takes a long time -- couple hours)</li>
        <li>Remove Bad Portfolios -- Takes <strong>ALL</strong> portfolios from PC and compares them against ours.  If we have one they don't, we remove ours.</li>
        <li>Pull Individual Security -- Pulls an individual security from PC and puts it into our vtiger_securities table if it doesn't already exist</li>
        <li>Fix Portfolio Information Numbers -- If the Portfolio Information module is showing massive high numbers due to the cron still running, this will set them to the previous day's values.  When the cron is done, it will update itself</li>
        <li>Fix Null Inceptions -- Loops through all inception dates that are Null and updates them to their first trade date</li>
    </ul>
</div>
<p>
    <input id="securityCodes" type="button" value="Copy Security Codes Table" />
    <input id="badPortfolios" type="button" value="Remove Bad Portfolios" />
    <input id="copyCurrent" type="button" value="Fix Portfolio Information Numbers" />
    <input id="FixNullInceptions" type="button" value="Fix Null Inceptions" />
</p>
<p>
    <input id="historicalDate" type="text" placeholder="Y-m-d to start from" value="{$HISTORICAL_DATE}" />&nbsp;
    <input id="historicalUpdate" type="button" value="Historical Update" />
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
    <input id="pullAllSecurities" type="button" value="Pull All Securities" />
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
<ul>
    <li>Total Annihilation is as destructive as it sounds.  It destroys an account... all transactions, all history, all portfolio information.  Cannot be recovered. GONE</li>
</ul>
<p>
    <input id="transactions_account_number" type="text" placeholder="Account Number" />&nbsp;
    <input id="ssn_reset" type="button" value="Get SSN From PC" />
    <input id="transactions_account_reset" type="button" value="Reset Transactions" />
    <input id="account_annihilation" type="button" value="Total Annihilation" /><br />
    <input id="UpdateAccountInceptionDate" type="button" value="Update Inception Date" /> 
    <input id="UpdateAllInceptionDates" type="button" value="Update All Inception Dates" /> 
    <input id="UpdateAdvisorControlNumber" type="button" value="Update Advisor Control Number" /><br />
</p>
<p>As of: (use the first of the month when entering.. IE: 2015-05-01 will calculate from May 1st until now.  If it is August it will calculate 3 months worth)</p>
<p>(requires account number filled in above)
    <input id="account_number_historical_date" type="text" placeholder="yyyy-mm-dd" />
    <input id="account_number_historical_update" type="button" value="Recalculate Historical Numbers" />
    <input id="account_number_trailing_update" type="button" value="Recalculate Trailing 12 Numbers" />
</p>

<p>
    <input id="portfolio_id" type="text" placeholder="Portfolio ID" />&nbsp;
    <input id="portfolio_id_reset" type="button" value="Reset Portfolio Transactions" />    
</p>
<p>
    <input id="calendarUsername" type="text" placeholder="Username" />&nbsp;
    <input id="calendarIndividualSharing" type="button" value="Calendar Reset" />
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
<p>As of: (use the first of the month when entering.. IE: 2015-05-01 will calculate from May 1st until now.  If it is August it will calculate 3 months worth)</p>
<p>
    <input id="control_number_historical_date" type="text" placeholder="yyyy-mm-dd" />
    <input id="control_number_historical_update" type="button" value="Recalculate Historical Numbers" />
</p>

<h2>Audit Filtering</h2>
<ul>
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

<h2>Securities/Positions Modules</h2>
<ul>
    <li>Undefined Security Type - Determines the security types that are null, takes their code and updates the ModSecurities entities, which in turn updates the PositionInformation module</li>
</ul>
<p>
    <input id="undefined_security_type" type="button" value="Undefined Security Type Fix" />
</p>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}