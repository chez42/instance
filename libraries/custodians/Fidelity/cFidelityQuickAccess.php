<?php
class cFidelityQuickAccess{
    public function PullPositions(array $account_number){
        /***STEP 2 - CREATE AND UPDATE POSITIONS/SECURITIES WORKING***/
        $position_fields = array( "account_number", "account_type", "cusip", "symbol", "SUM(trade_date_quantity) AS trade_date_quantity", "SUM(settle_date_quantity) AS settle_date_quantity",
            "close_price", "description", "as_of_date", "current_factor", "original_face_amount", "factored_clean_price",
            "factored_indicator", "security_type_code", "option_symbol", "registered_rep_1", "registered_rep_2", "filename",
            "zero_percent_shares", "one_percent_shares", "two_percent_shares", "three_percent_shares", "account_source",
            "account_type_description", "accrual_amount", "asset_class_type_code", "capital_gain_instruction_long_term",
            "capital_gain_instruction_short_term", "clean_price", "SUM(closing_market_value) AS closing_market_value", "core_fund_indicator", "cost",
            "cost_basis_indicator", "st_basis_per_share", "cost_method", "current_face", "custom_short_name",
            "dividend_instruction", "exchange", "fbsi_short_name", "floor_symbol", "fund_number", "host_type_code",
            "lt_shares", "maturity_date", "money_source_id", "money_source", "operation_code", "plan_name", "plan_number",
            "pool_id", "position_type", "pricing_factor", "primary_account_owner", "product_name", "product_type",
            "registration", "security_asset_class", "security_group", "security_id", "security_type_description", "st_shares",
            "SUM(unrealized_gain_loss_amount) AS unrealized_gain_loss_amount", "unsettled_cash", "file_date", "insert_date");

        $positions = new cFidelityPositions("FIDELITY", "custodian_omniscient", "positions",
            "custodian_portfolios_fidelity", "custodian_positions_fidelity", array(), array());
        $positions->SetAccountNumbers($account_number);
        $missing_positions = $positions->GetMissingCRMPositions();

        StatusUpdate::UpdateMessage("CRONUPDATER", "Getting Symbols List Old and New (FIDELITY)");
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        $security_fields = array("f.symbol", "f.type", "f.description", "f.cusip", "f.dividend_yield", "f.option_expiration_date", "f.strike_price",
            "f.option_symbol", "f.interest_rate", "f.maturity_date", "f.issue_date", "f.first_coupon_date",
            "f.zero_coupon_indicator", "f.abbreviated_fund_name", "f.accrual_method", "f.as_of_date", "f.asset_class_code",
            "f.asset_class_type_code", "f.bond_class", "f.close_price", "f.close_price_unfactored",
            "f.current_factor_inflation_factor", "f.current_factor_date", "f.dividend_rate", "f.exchange", "f.expiration_date",
            "f.fixed_income_call_put_date", "f.fixed_income_call_put_price", "f.floor_symbol", "f.foreign_security", "f.fund_family",
            "f.fund_family_id", "f.fund_number", "f.host_type_code", "f.interest_frequency", "f.issue_state", "f.margin",
            "f.mmkt_fund_designation", "f.operation_code", "f.options_symbol_underlying_security", "f.pricing_factor",
            "f.security_group", "f.security_id", "f.security_type_description", "f.sic_code", "f.tradable", "f.yield_to_maturity",
            "f.file_date", "f.filename", "f.insert_date", "pr.price AS latest_price", "map.multiplier", "map.omni_base_asset_class",
            "map.security_type");

        #$symbols = array("FDRXX");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cFidelitySecurities("FIDELITY", "custodian_omniscient", "securities",
            "custodian_securities_fidelity", $symbols, array(), $security_fields);
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->ManualSetupPositionComparisons();//Needed because there may be new positions
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        /*********END OF STEP 2********/
    }
}