<div id="account_status">
    <h2>Account Compare</h2>
    <input id="start_date" placeholder="Start Date" />
    <input id="end_date" placeholder="End Date" />
    <input id="submit_status" type="button" value="Compare Dates" />
    <table id="account_status">
        <thead>
            <tr>
                <td colspan="2">TD</td>
                <td colspan="2">Schwab</td>
                <td colspan="2">Pershing</td>
                <td colspan="2">Fidelity</td>
            </tr>
        </thead>
        <body>
        <tr>
            <td>Closed: <span id="td_closed">0</span></td>
            <td>New: <span id="td_new">0</span></td>
            <td>Closed: <span id="schwab_closed">0</span></td>
            <td>New: <span id="schwab_new">0</span></td>
            <td>Closed: <span id="pershing_closed">0</span></td>
            <td>New: <span id="pershing_new">0</span></td>
            <td>Closed: <span id="fidelity_closed">0</span></td>
            <td>New: <span id="fidelity_new">0</span></td>
        </tr>
        </body>
    </table>
    <p>Note:  <strong>Closed</strong> refers to accounts that existed for the first date but no longer exist in the second.  <strong>New</strong> refers to accounts that exist
       for the second date, but did not exist in the first</p>
</div>

<div id="td_push">
    <h2>TD</h2>
    <table class="omnisol_interaction">
        <tr><td><input type="text" id="td_rep_code" placeholder="REP CODES, COMMA SEPARATED" /></td></tr>
        <tr><td><input type="button" value="Personal Info" />
                <input type="button" value="Balances" />
            </td>
        </tr>
    </table>
</div>
