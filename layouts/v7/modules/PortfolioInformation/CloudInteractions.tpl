<h2>Self Diagnosis</h2>
<p>This section will perform self diagnosis each time the page is entered.  It will check to make sure the previous date's file has been added for each custodian and has been marked as completed</p>
<br /><br />
<div style="display:block; float:left; width:100%;">
{foreach from=$TRANSACTION_DIAGNOSIS item=v}
    <table style="display:block; float:left;">
        <thead>
            <tr style="font-size:20px;">
                <td style="padding-left:20px; padding-right:20px;">Custodian</td>
                <td>Last Transaction Date</td>
            </tr>
        </thead>
        <tbody>
            <tr style="font-size:18px; {if $v.date == $DATE}background-color:lightgreen; {else} background-color:lightcoral{/if}">
                <td style="padding-left:20px; padding-right:20px;">{$v.custodian}</td>
                <td>{$v.date}</td>
            </tr>
        </tbody>
    </table>
{/foreach}
<select multiple style="display:block; float:left; width:400px; height:400px;">
    {foreach from=$FILES_RUN item=v}
        <option>ID: {$v.id} - Filename: {$v.filename} - Custodian: {$v.custodian}</option>
    {/foreach}
</select>
    <div id="integrity_results" style="display:block; width:400px; float:left; margin-left:10px; border:1px solid black; overflow:auto; max-height:400px;">
        <table id='integrity_table' style='width:100%;'><thead><tr><td>Account Number</td><td>CRM Value</td><td>Custodian Value</td><td>Difference</td></tr></thead><tbody></tbody></table>
    </div>
    <div id="integrity_interaction" style="margin-left:10px; display:block; float:left;">
        <input type="button" value="Show Bad Only" id="show_bad" /><br />
        <input type="button" value="Remove Bad Dupes" id="remove_bad_dupes" /><br />
        <input type="button" value="Recalculate Bad" id="recalculate_bad" />
    </div>
</div>

<br /><br />
<h2>Cloud Interactions Overview</h2>
<div id="global_descriptions">
    <p>Each custodian has a different way of storing their information in the Cloud.  When we do a pull, it converts their data into our standard way of showing things.  This section will pull data for the individual custodian for the specified module from the cloud and place that information into our modules.<br />
        For the most part, we want date to be today minus one day.  The files we get from the Custodian are from the previous day, or if it is a Monday then the files are from Friday.  The date should auto fill in accordingly to the latest files we have from the custodian for you.</p>
    <ul>
        <li>Date must be entered in Y-M-D format.  This is how it is in the database and any other format will not work</li>
        <li>Custodian defaults to Fidelity.  This drop down will be customized in the future to auto change based on account number should one be entered, but for now you must match the account to the custodian</li>
        <li>Make sure all securities exist in the CRM for specified Custodian</li>
    </ul>
</div>
Custodian:&nbsp;
<select id="custodian">
    <option value="fidelity">Fidelity</option>
    <option value="pershing">Pershing</option>
    <option value="td">TD</option>
    <option value="schwab">Schwab</option>
</select>
<input type="button" value="Integrity Check" id="integrity_check" />&nbsp;
<input type="button" value="Update Balances" id="update_balances" />
<br />
Date: <strong>(AFTER 2016-04-20)</strong> <input type="text" id="date" value="{$DATE}" /> (Must be Y-M-D Format), Comparitor: <select id="comparitor">
                                                                                            <option value="=">=</option>
                                                                                            <option value=">=">>=</option>
                                                                                        </select><br />
Account Number: <input type="text" id="account_number" placeholder="Account Number, Empty For All" /><br />

<h2>Pricing Table Interactions</h2><br />
<ul>
    <li>Until we flip the switch on PC, prices is being fed from PC using security ID's.  We need to map these to their proper symbols so our new modules can identify with them.  The <strong>update symbols</strong> button does this for us</li>
</ul>
<input type="button" id="add_symbols" value="Update Symbols" />

<h2>Transactions</h2>
<ul>
    <li>Pulling transactions will pull and update all transactions as of a given date</li>
    <li>Assigning transactions matches the transactions account number against the portfolio account number and assigns the portfolios owner to the account number (currently only re-assigns if the transaction is owned by administrator)</li>
</ul>
<input type="button" value="Update Transactions" id="update_transactions" />&nbsp;&nbsp;New Transactions Only <input type="checkbox" id="new_only" checked /><br />
<input type="button" value="Assign Transactions" id="assign_transactions" /> Assign Transactions

<h2>Securities Module Interaction</h2><br />
<ul>
    <li>Get New Securities compares our security list against the cloud.  Any securities we don't have, it will pull and insert into our securities module then update the prices.</li>
    <li>Update Securities updates the securities against the latest from the custodian -- Uses the security box if wanting to just update a single security</li>
{*    <li>Update Prices updates all securities to the latest price, or just the specified security name if one is entered in the security box</li> *}
</ul>
<input type="button" id="get_new_securities" value="Get New Securities" /> (Uses Custodian above... If 0, there are no new Securities to be added)<br />
<input type="button" id="update_securities" value="Update Securities" /> (Uses Custodian above)<br />
<input type="button" id="update_security_type" value="Update Security Type/Asset Class" /> (Uses Custodian above)<br />
<input type="text" id="symbol" placeholder="Security Name, Empty For All" />&nbsp;&nbsp; {*<input type="button" id="update_prices" value="Update Prices" /> (Uses Custodian and Security text field)*}


<h2>Positions Module Interaction</h2><br />
<ul>
    <li>-New Positions Pull will get all non exists CRM Positions from the cloud for the given date and insert them</li>
    <li>-Update Positions will pull all positions as of the given date from the Cloud and fill in the positions module with that information.  If an account number is entered, it will only replace positions for that individual account.  This process will wipe all position data for the account(s) first, then update based on the specified date accordingly.  Using an earlier date WILL update it with that day's values!</li>
    <li>-Calculate Portfolio Values compares our portfolios in the system against the Portfolios in the cloud for the given Custodian.  Using those accounts only, it adds up the position values and inserts them into the PortfolioInformation module.  This does not 'create portfolios' nor does it pull from the Custodian Balances</li>
</ul>
<input type="button" value="New Positions Pull" id="new_positions" />&nbsp;&nbsp;
<input type="button" value="Update Positions" id="update_positions" /> (Takes approximately 3 minutes for Fidelity)<br />
<input type="button" value="Calculate Portfolio Values" id="calculate_portfolios" /> Calculate Portfolio Values based on Positions (uses Custodian above and optionally Account Number)

<h2>Portfolios Module Interaction</h2><br />
<ul>
    <li>-New Portfolio Pull will get all non existing CRM Portfolios from the cloud for the given date and insert them.  This has no effect on existing Portfolios</li>
    <li>-Update Portfolios Values pulls all portfolio balance information in the system for the given date and updates the values accordingly.  The key here again is given date!.. It will in fact replace values with older ones!</li>
    <li>-Link Portfolios to contacts/households matches its SSN against the contact and fills in its contact link based on that</li>
    <li><-Assign Portfolios Based on Contact_Link will compare the Portfolio Information Contact Link (The contact the Portfolio is attached to), and set its owner to same as the contact</li>
</ul>
<input type="button" value="New Portfolio Pull" id="new_portfolios" />&nbsp;&nbsp;
<input type="button" value="Update Portfolios" id="update_portfolios" />&nbsp;&nbsp;
<input type="button" value="Link Portfolios" id="link_portfolios" />
<input type="button" value="Assign Portfolios" id="assign_portfolios" /><br />
<h2>GLOBAL SUMMARY</h2><br />
<ul>
    <li>-This is not specifically a cloud interaction, but the daily global totals need to be run manually because they are done at the end of the cron job, but cloud data isn't done until afterwards.  Global Summary Update takes the PortfolioInformation values and inserts them into our daily totals tables exactly as they appear in the list view</li>
</ul>
<input type="button" value="Global Summary Update" id="global_summary" />

<h2>Index Pricing</h2>
<ul>
    <li>-Start Date, End Date must be in Y-m-d format.  The symbol is based on the Omniscient symbol.  S&P 500 for example is called ^GSPC in yahoo.  We would enter S&P 500 for symbol here, not ^GSPC</li>
</ul>
<input type="text" value="" id="index_symbol" placeholder="Symbol" />&nbsp;
<input type="text" value="" id="index_start" placeholder="Start Date" />&nbsp;
<input type="text" value="" id="index_end" placeholder="End Date" />&nbsp;
<input type="button" value="Update Prices" id="index_update" /><br />

<h2>RYAN ONLY - You break it, you buy it</h2><br />
<ul>
    <li>-Remove Duplicate Positions (Caused by dashed accounts)</li>
    <li>-Update Portfolio Center (Transfers our Custodian information to PC)</li>
</ul>
<input type="button" value="Remove Dupe Positions" id="remove_dupe_positions" />
<input type="button" value="Update PC Custodians" id="update_portfolio_center" />

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}