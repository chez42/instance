<div id="tools_wrapper">
    <div class="section">
        <div class="section_header">
            <h2>Intervals</h2>
        </div>
        <div class="block_left">
{*            <div class="date_selection">
                Start Date (M/D/Y) <input type="text" id="select_start_date" value="01/01/2019" />
                End Date (M/D/Y) <input type="text" id="select_end_date" />
            </div>*}

            Rep Codes:
            <select class="rep_code_select" multiple>
                {foreach item=i from=$REP_CODES}
                    <option value="{$i.rep_code}">{$i.rep_code} ({$i.custodian})</option>
                {/foreach}
            </select>
        </div>
        <div class="block_right">
            <div class="inception_intervals">Inception:
                <input id="inception" type="checkbox" name="inception" value="inception" />
                <input type="button" id="daily_intervals" value="Run Daily Intervals" />
                <img src="{$LOADER}" width="25" id="inception_loader" />
            </div>
            <div class="block_right clear_both">
                <input type="button" id="account_list" value="View Account List" />
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section_header">
            <h2>Custodian Parsing (Not Omnisol)</h2>
        </div>
        <div class="block_left">
            Custodians:
            <select class="custodian_select">
                <option value="td">TD</option>
                <option value="fidelity">Fidelity</option>
                <option value="pershing">Pershing</option>
                <option value="schwab">Schwab</option>
            </select>
            <select class="parse_files">
                <option value="parse_pricing">Pricing</option>
                <option value="parse_securities">Securities</option>
                <option value="parse_positions">Positions</option>
                <option value="parse_portfolios">Portfolios</option>
                <option value="parse_balances">Balances</option>
                <option value="parse_transactions">Transactions</option>
{*                <option value="parse_all">All</option>*}
            </select>
            Number of days to parse back <input type="text" id="num_days" value="7" />
        </div>
        <div class="block_right clear_both">
            <input type="button" id="parse" value="Parse" />
        </div>
    </div>

    <div class="section">
        <div class="section_header">
            <h2>Cloud To CRM</h2>
        </div>
        <div class="block_left">
            Custodians:
            <select class="custodian_select_push">
                <option value="td">TD</option>
                <option value="fidelity">Fidelity</option>
                <option value="pershing">Pershing</option>
                <option value="schwab">Schwab</option>
            </select>
            <select class="push_files">
                <option value="push_securities">Securities</option>
                <option value="push_positions">Positions</option>
                <option value="push_portfolios">Portfolios</option>
                <option value="push_transactions">Transactions</option>
{*                <option value="push_all">All</option>*}
            </select>
        </div>
        <div class="block_right clear_both">
            <input type="button" id="push" value="Push" />
        </div>
    </div>

    <div class="section">
        <div class="section_header">
            <h2>Missing Files</h2>
        </div>
        <div class="block_left">
            File Type:
            <select class="type_select">
                <option value="Balances">Balances</option>
                <option value="Portfolios">Portfolios</option>
                <option value="Securities">Securities</option>
                <option value="Positions">Positions</option>
                <option value="Prices">Prices</option>
                <option value="Transactions">Transactions</option>
            </select>
            <input type="text" id="type_sdate" />
            <input type="text" id="type_edate" />
        </div>
        <div class="block_right clear_both">
            <input type="button" id="find" value="Find" />
        </div>
        <div class="block_left clear_both missing_files">
        </div>
    </div>
</div>