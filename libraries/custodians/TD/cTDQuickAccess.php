<?php
class cTDQuickAccess{
    public function PullPositions(array $account_number){
        //Pull all specified position data.  Auto setup will pull all info and set it up for us.  If there are memory issues due to too much data
        //then account numbers will need to be set manually and auto setup turned off.  We can then use the GetPositionsData function (follow the
        //constructor for an example on how to load.  This could be done in a loop setting <x> number of account numbers at a time
        StatusUpdate::UpdateMessage("CRONUPDATER", "Pulling Custodian Positions (TD)");
        $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
            "custodian_portfolios_td", "custodian_positions_td", array(), array());
        $positions->SetAccountNumbers($account_number);

        $missing_positions = $positions->GetMissingCRMPositions();
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        StatusUpdate::UpdateMessage("CRONUPDATER", "Starting Securities (TD)");
        $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
            "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
            "f.issue_date", "f.share_per_contact", "pr.factor");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols, array(), $fields);
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
    }
}