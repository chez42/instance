<h2>Custodian File Pulling</h2>

<p style="background-color:lightyellow">In Progress</p>
<input type="button" id="audit_td_positions" value="Copy TD to table" />

<p style="background-color:lightgreen">Working</p>
<input type="button" id="audit_schwab_positions" value="Copy Schwab to table" />
<input type="button" id="audit_pershing_positions" value="Copy Pershing to table" />
<input type="button" id="audit_fidelity_positions" value="Copy Fidelity to table" />
<br /><br />

<h2>Custodian Interactions (Portfolios Section)</h2>
<p>The portfolios section is a direct comparison to the Portfolio values given by the custodian.</p>
<ul>
    <li>Empty Portfolios will reset the table.  This is most useful for comparing specific custodians and getting old accounts out of the mix</li>
    <li>Repair Bad List -- Takes the list shown in the bad accounts and initiates the reset transactions script on them</li>
</ul>
<input type="button" id="empty_portfolios_table" value="Empty Portfolios Table" />
<input type="button" id="repair_all_accounts" value="Repair Bad List" /><br />
Estimated Problem Accounts: <select class="select2 problem_account_select">
    {foreach item=account_number from=$BAD_ACCOUNTS}
        <option value="{$account_number}">{$account_number}</option>
    {/foreach}
</select><br />

<div id="compare_portfolio_result">
    <table id="compare_portfolio_table">
        <thead>
        <tr>
            <td style="padding-left:10px; padding-right:10px;">Account Number</td>
            <td style="padding-left:10px; padding-right:10px;">CSV Total</td>
            <td style="padding-left:10px; padding-right:10px;">CSV Market Value</td>
            <td style="padding-left:10px; padding-right:10px;">CSV Cash Value</td>
            <td style="padding-left:10px; padding-right:10px;">Omni Total</td>
            <td style="padding-left:10px; padding-right:10px;">Omni Market Value</td>
            <td style="padding-left:10px; padding-right:10px;">Omni Cash Value</td>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br /><br />
<h2>Custodian Interactions (Positions Section)</h2>
<div id="global_descriptions">
    <ul>
        <li>Copy TD .POS table -- Pulls all position files in the TD directory and converts them into a table to be queried against</li>
        <li>Compare To Fidelity -- Compare the given account to the table created from the custodian position file</li>
    </ul>
</div>
<p><strong>Note: </strong>TD doesn't consider cash as quantity, so any cash accounts are set to 0 for quantity but have a cash value.  Fidelity however considers
    cash quantity and value</p>

All Accounts: <select class="select2 account_select">
    {foreach item=account_number from=$ACCOUNTS}
        <option value="{$account_number}">{$account_number}</option>
    {/foreach}
</select>
<input type="text" id="audit_account_number" placeholder="Account Number" />
<input type="button" id="audit_compare_to_csv" value="Compare To Custodian" />
<input type="button" id="audit_reset" value="Reset From PC" /><br />

<div id="compare_result">
    <table id="compare_table">
        <thead>
        <tr>
            <td>Account Number</td>
            <td>Security Symbol</td>
            <td>CSV Quantity</td>
            <td>CSV Value(cash)</td>
            <td>Omni Quantity</td>
            <td>Omni Value</td>
            <td>Security Type</td>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}