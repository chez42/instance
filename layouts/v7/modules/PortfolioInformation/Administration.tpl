<div id="administration_section">
    <h1>Administration</h1>
    <p>Generate initial transactions.  This will take the earliest positions date and create transactions as receipt of securities.  When
       we get new accounts that already have positions in them and we don't have transactions to show how those positions got there, it shows
       as a gain in TWR which causes major calculation issues (usually shows as a giant percentage).  This should resolve that</p>
    <button id="generate_transactions">Generate TD</button>
        <select id="account_number_selection" style="border:2px solid black; margin-right:5px;">
            {foreach key=index item=option from=$ACCOUNTS}
                <option value="{$option}">{$option}</option>
            {/foreach}
        </select>
    <button id="monthly_intervals">Rerun Monthly Intervals (inactive for now)</button>
    <button id="daily_intervals">Rerun Daily Intervals (inactive for now)</button>
</div>