<?php

class PortfolioInformation_ManualInteractions_Model extends PortfolioInformation_PCQuery_Model{
    public $reset = 500;
    /**
     * Updates the vtiger_pc_security_codes table to match the one from PC
     */
    public function UpdateSecurityCodes(){
        global $adb;
        
        $query = "SELECT SecurityID, CodeTypeID, CodeID FROM SecurityCodes";
        $result = $this->CustomQuery($query);
        $query = "INSERT INTO vtiger_pc_security_codes (security_id, code_type_id, code_id) VALUES ";
        $update = " ON DUPLICATE KEY UPDATE code_type_id = VALUES(code_type_id), code_id = VALUES(code_id)";
        $extension = "";
        
        $count = 0;
        $reset = 0;
        foreach($result AS $k => $v){
            $reset++;
            if($reset >= $this->reset){
                $reset = 0;//Reset the query insert
                $extension = rtrim($extension,', ');
                $tmp = $query . $extension . $update;
                $adb->pquery($tmp);
                $extension = '';
                $count++;
            } else{
                $extension .= " ({$v['SecurityID']}, {$v['CodeTypeID']}, {$v['CodeID']}), ";
            }
        }
        $extension = rtrim($extension,', ');
        $query .= $extension . $update;
        $result = $adb->pquery($query);
    }

    static public function UpdateAllHouseholdHistoricalValue(){
        global $adb;

        $query = "DROP TABLE IF EXISTS household_history";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE household_history
                  SELECT t1.account_number, SUM(t1.expense_amount) AS expense_amount FROM (SELECT p.account_number, month, year, SUM(expense_amount) AS expense_amount,
                      DATE(CONCAT(year, '-', month, '-', '01')) AS combined_date
                      FROM vtiger_portfolioinformation p
                      JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                      JOIN vtiger_portfolioinformation_fees vpif ON vpif.account_number = p.account_number
                      GROUP BY month, year, account_number) AS t1
                      WHERE t1.combined_date between CAST(DATE_FORMAT(NOW()-interval 1 year,'%Y-%m-01') as DATE) AND NOW()-interval 1 month
                  GROUP BY account_number";
        $adb->pquery($query, array());
        $query = "UPDATE vtiger_accountscf cf
                  JOIN (select expense_amount, household_account
                        FROM household_history h
                        JOIN vtiger_portfolioinformation p ON h.account_number = p.account_number
                        JOIN vtiger_portfolioinformationcf cf using (portfolioinformationid)
                        WHERE household_account is not null
                        AND household_account != 0
                        GROUP BY household_account) a1 ON a1.household_account = cf.accountid
                  SET trailing_fees = ABS(a1.expense_amount)";
        $adb->pquery($query, array());
    }

    static public function UpdateAnnualManagementFees(){
        global $adb;

        $query = "DROP TABLE IF EXISTS historical_transactions_tmp";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS historical_transactions";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE historical_transactions_tmp
                  SELECT p.portfolio_account_number, t.* FROM vtiger_pc_transactions t
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE t.activity_id = 160
                  AND t.report_as_type_id = 60
                  AND t.status_type_id = 100
                  AND t.trade_date >= date(NOW() - INTERVAL 1 YEAR)";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE historical_transactions
                  SELECT portfolio_account_number, SUM(cost_basis_adjustment) AS annual_fee
                  FROM historical_transactions_tmp
                  GROUP BY portfolio_account_number";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN historical_transactions ht ON ht.portfolio_account_number = p.account_number
                  SET p.annual_management_fee = ht.annual_fee";
        $adb->pquery($query, array());
    }

    static public function UpdateAllHouseholdTotalValue(){
        global $adb;

        $query = "UPDATE vtiger_accountscf SET household_total = 0";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_accountscf cf
                  JOIN (select SUM(total_value) AS total_value, household_account FROM vtiger_portfolioinformation p
                        JOIN vtiger_portfolioinformationcf cf using (portfolioinformationid)
                        WHERE household_account is not null
                        AND household_account != 0
                        AND p.accountclosed = 0
                        GROUP BY household_account) a1 ON a1.household_account = cf.accountid
                  SET household_total = a1.total_value";
        $adb->pquery($query, array());
    }

    static public function UpdateAllContactTotalValue(){
        global $adb;

        $query = "UPDATE vtiger_contactscf SET contact_total = 0";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_contactscf cf
                  JOIN (select SUM(total_value) AS total_value, contact_link FROM vtiger_portfolioinformation p
                        JOIN vtiger_portfolioinformationcf cf using (portfolioinformationid)
                        WHERE contact_link is not null
                        AND contact_link != 0
                        AND p.accountclosed = 0
                        GROUP BY contact_link) a1 ON a1.contact_link = cf.contactid
                  SET contact_total = a1.total_value";
        $adb->pquery($query, array());
    }

    static public function RemoveUndefinedSecurityType(){
        $list = ModSecurities_SecurityBridge_Model::PullAllSecurities();
        ModSecurities_SecurityBridge_Model::WriteListToModSecurities($list);
        ModSecurities_SecurityBridge_Model::UpdateModSecuritiesSecurityIDWhenEmpty();
        ModSecurities_SecurityBridge_Model::UpdateEmptySecurityTypes();
    }

    public function UpdateAdvisorControlNumber($account_number){
        global $adb;
        
        $query = "SELECT AdvisorID FROM PortfolioCenter.dbo.Portfolios WHERE AccountNumber = '{$account_number}'";
        $result = $this->CustomQuery($query);
        foreach($result AS $k => $v){
            $query = "UPDATE vtiger_portfolioinformation p
                      JOIN vtiger_portfolioinformationcf pcf ON p.portfolioinformationid = pcf.portfolioinformationid
                      SET p.advisor_id = ?, pcf.production_number = (SELECT pc_name FROM vtiger_pc_advisors WHERE pc_id = ?)
                      WHERE p.account_number = ?;";
            $adb->pquery($query, array($v['AdvisorID'], $v['AdvisorID'], $account_number));
            $query = "UPDATE vtiger_portfolios SET advisor_id = ? WHERE portfolio_account_number = ?";
            $adb->pquery($query, array($v['AdvisorID'], $account_number));
            return 1;
        }
        return 0;
    }
    
    public function UpdateCodeDescriptions(){
        global $adb;
        
        $query = "SELECT CodeID, DataSetID, CodeName, CodeDescription, CodeTypeID FROM Codes";
        $result = $this->CustomQuery($query);
        $query = "INSERT INTO vtiger_pc_codes (code_id, data_set_id, code_name, code_description, code_type_id) VALUES ";
        $update = " ON DUPLICATE KEY UPDATE code_description = VALUES(code_description)";
        $extension = "";

        $count = 0;
        $reset = 0;
        foreach($result AS $k => $v){
            $reset++;
            if($reset >= $this->reset){
                $reset = 0;//Reset the query insert
                $extension = rtrim($extension,', ');
                $tmp = $query . $extension . $update;
                $adb->pquery($tmp);
                $extension = '';
                $count++;
            } else{
                $extension .= " ({$v['CodeID']}, {$v['DataSetID']}, '{$v['CodeName']}', '{$v['CodeDescription']}', {$v['CodeTypeID']}), ";
            }
        }
        $extension = rtrim($extension,', ');
        $query .= $extension . $update;
        $result = $adb->pquery($query);        
    }

    public function GetAccountNumbersFromOmniControlNumber($control_number){
        global $adb;
        $query = "SELECT account_number FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
                  WHERE cf.omniscient_control_number = ?";
        $result = $adb->pquery($query, array($control_number));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $accounts[] = $v['account_number'];
            }
            return $accounts;
        }
        return 0;
    }
    
    public function GetAccountNumbersFromControlNumber($control_number){
        global $adb;
        $query = "SELECT account_number FROM vtiger_portfolioinformation WHERE advisor_id IN (
                    SELECT pc_id FROM vtiger_pc_advisor_linking WHERE user_id IN (
                            SELECT id FROM vtiger_users 
                            WHERE advisor_control_number REGEXP (?)
                    )
                  )";
        $result = $adb->pquery($query, array($control_number));
        $accounts = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $accounts[] = $v['account_number'];
            }
            return $accounts;
        }
        return 0;
    }
    
    public function GetPortfolioInformationIDsFromControlNumber($control_number){
        global $adb;
        $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE advisor_id IN (
                    SELECT pc_id FROM vtiger_pc_advisor_linking WHERE user_id IN (
                            SELECT id FROM vtiger_users 
                            WHERE advisor_control_number REGEXP (?)
                    )
                  )";
        $result = $adb->pquery($query, array($control_number));
        $accounts = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $accounts[] = $v['portfolioinformationid'];
            }
            return $accounts;
        }
        return 0;
    }
    
    public function GetPortfolioInformationIDFromAccountNumber($account_number){
        global $adb;
        $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'portfolioinformationid');
        }
        return 0;
    }
    
    public function ResetAccountTransactionsOverride($account_number){
        global $adb;
$account_numbers = $this->GetIDS();
        $query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number IN ({$account_numbers})";
        $pid_result = $adb->pquery($query, array());
        if($adb->num_rows($pid_result) > 0){
            foreach($pid_result AS $k => $v){
                $ids[] = $v['portfolio_id'];
            }
        }
        if($adb->num_rows($pid_result) == 0){
            return "Account number does not exist";
        }
        
        $ids = SeparateArrayWithCommas($ids);
//        $pid = $adb->query_result($pid_result, 0, "portfolio_id");

        $query = "SELECT * FROM Transactions WHERE PortfolioID IN ({$ids})";

        $result = $this->CustomQuery($query);

        if($result != "Error Connecting to PC"){
            $query = "DELETE FROM vtiger_pc_transactions WHERE portfolio_id IN ({$ids})";
            $adb->pquery($query, array());
            $transactions = new cTransactionsAccess();
            return $transactions->WriteTransactionsDirectly($result);
        }
    }
    
    public function GetIDS(){
return "'178-780774',
'224-198234',
'57639142',
'60323325801',
'60577712701',
'60577712702',
'60727752001',
'60858824501',
'60858864501',
'612-125968',
'638-086716',
'647-820113',
'648-509981',
'656-188714',
'656-190039',
'656-193792',
'656-197835',
'656-198296',
'670-282472'
'672-142530',
'672-860042',
'675-524263',
'676-078935',
'676-119316',
'676-119319',
'676-119335',
'676-119740',
'676-126851',
'676-132386',
'676-133033',
'676-151789',
'676-158353',
'676-179380',
'676-179402',
'676-179681',
'676-182666',
'676-209048',
'676-209903',
'676-211856',
'676-213770',
'676-217463',
'676-217503',
'676-217519',
'676-235216',
'676-267437',
'676-286223',
'676-286967',
'676-288392',
'676-312880',
'676-330149',
'676-334973',
'676-345107',
'676-353718',
'676-361914',
'676-367190',
'676-367199',
'676-369520',
'676-369533',
'676-374902',
'676-377634',
'676-377638',
'676-377645',
'676-377647',
'676-384721',
'676-389634',
'676-396655',
'676-410140',
'676-417843',
'676-417844',
'676-417888',
'676-422353',
'676-426760',
'676-426851',
'676-434967',
'676-438644',
'676-440686',
'676-454855',
'676-454865',
'676-458203',
'676-478017',
'676-480236',
'676-486659',
'676-488019',
'676-489608',
'676-492317',
'676-494964',
'676-497516',
'676-497518',
'676-499593',
'678-106755',
'678-106887',
'678-107352',
'86316735',
'913054790',
'913056672',
'913056678',
'918032806',
'918032807',
'918032809',
'918032892',
'918955045',
'918955082',
'918955238',
'918972194',
'918995218',
'920035097',
'920035144',
'920035156',
'920035158',
'920035169',
'920035182',
'920035299',
'920035315',
'920949131',
'920949135',
'920949153',
'920949255',
'920949263',
'920949295',
'920949303',
'920949345',
'920949374',
'920949382',
'920949388',
'920949402',
'922017323',
'922907784',
'922911672',
'922911685',
'922948583',
'922952756',
'922958997',
'922959028',
'922970553',
'925024526',
'925025397',
'925027297',
'925027331',
'925027333',
'925027352',
'925027360',
'925027362',
'925027388',
'925027398',
'925027419',
'925027424',
'925027428',
'925027430',
'925027436',
'925027440',
'925027443',
'925027451',
'925027455',
'925027461',
'925027468',
'925027469',
'925027473',
'925027495',
'925027507',
'925027515',
'925027518',
'925027534',
'925027542',
'925027545',
'925027549',
'925027552',
'925027554',
'925027572',
'925027580',
'925027588',
'925027594',
'925027600',
'925027609',
'925027614',
'925027617',
'925027636',
'925027638',
'925027653',
'925027659',
'925027666',
'925027671',
'925027675',
'925027677',
'925027685',
'925027708',
'925027717',
'925027719',
'925027721',
'925027727',
'925027729',
'925027732',
'925027758',
'925027765',
'925027778',
'925027781',
'925027782',
'925027784',
'925027786',
'925027790',
'925027792',
'925027794',
'925027796',
'925027800',
'925027804',
'925027807',
'925027808',
'925027828',
'925027831',
'925027832',
'925027834',
'925027849',
'925027851',
'925027853',
'925027857',
'925027861',
'925027864',
'925027873',
'925027877',
'925027881',
'925027883',
'925027885',
'925027887',
'925027895',
'925027906',
'925027907',
'925027909',
'925027911',
'925027925',
'925027927',
'925027931',
'925027933',
'925027935',
'925027949',
'925027952',
'925027955',
'925027963',
'925027968',
'925048829',
'925049432',
'925049470',
'925049473',
'925049485',
'925049488',
'925049497',
'925049501',
'925049507',
'925049517',
'925049642',
'925049657',
'925050430',
'925051085',
'925051133',
'925071152',
'925072068',
'925908631',
'925908634',
'925908637',
'925908639',
'925911163',
'925911167',
'925911174',
'925911177',
'925911179',
'925911182',
'925911185',
'925911187',
'925911190',
'925911195',
'925911197',
'925911239',
'925911247',
'925911250',
'925911252',
'925911262',
'925911268',
'925911274',
'925911290',
'925911294',
'925911304',
'925911308',
'925911315',
'925911316',
'925911326',
'925911328',
'925911351',
'925911361',
'925911364',
'925911365',
'925911367',
'925911372',
'925911373',
'925911375',
'925911380',
'925911383',
'925911387',
'925911393',
'925911395',
'925911398',
'925911406',
'925911414',
'925911422',
'925911423',
'925911425',
'925911427',
'925911430',
'925911433',
'925911448',
'925911450',
'925911452',
'925911455',
'925911463',
'925911466',
'925911472',
'925911474',
'925911482',
'925911505',
'925911506',
'925911508',
'925911510',
'925911513',
'925911514',
'925911516',
'925911518',
'925911526',
'925911528',
'925911540',
'925911542',
'925911547',
'925911565',
'925911570',
'925911571',
'925911575',
'925911581',
'925911583',
'925911588',
'925911589',
'925911591',
'925911593',
'925911596',
'925911599',
'925911601',
'925911604',
'925911605',
'925911609',
'925911612',
'925911613',
'925911617',
'925911619',
'925911621',
'925911623',
'925911631',
'925911633',
'925911635',
'925911639',
'925911641',
'925911646',
'925911647',
'925911650',
'925911653',
'925911654',
'925911662',
'925911664',
'925911670',
'925911680',
'925911684',
'925911687',
'925911688',
'925911692',
'925911698',
'925911700',
'925911706',
'925911711',
'925911712',
'925911714',
'925911716',
'925911724',
'925911729',
'925911730',
'925911734',
'925911737',
'925911738',
'925911742',
'925911745',
'925911748',
'925911752',
'925911759',
'926055547',
'926055844',
'926925275',
'926961795',
'929013933',
'931045850',
'931051067',
'932014551',
'932934743',
'932993794',
'934036390',
'934040913',
'934050706',
'934059499',
'934061493',
'934956668',
'934956694',
'934976524',
'934980571',
'934980601',
'934980603',
'934980613',
'936009054',
'936016792',
'936926071',
'936926091',
'936999922',
'937013640',
'937013654',
'937037601',
'937049265',
'937055742',
'937055748',
'937951580',
'937962752',
'937963891',
'937971235',
'938018630',
'938021665',
'938027067',
'938032102',
'938064509',
'938067540',
'938069714',
'938076289',
'938083628',
'938917611',
'938933793',
'938933794'";
    }

    /**
     * Takes the passed in account number and verifies its existence in case dashes were not entered (Fidelity for example doesn't use dashes, but PC does)
     * @param $account_number
     */
    public function ConfirmAccountNumber($account_number){
        global $adb;

        $query = "SELECT portfolio_account_number FROM vtiger_portfolios p
				  WHERE replace(portfolio_account_number, '-', '') = replace(?, '-', '')";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'portfolio_account_number');
        }
        return 0;
    }

    public function GetBestPortfolioIDFromDupes($pids){
        global $adb;

        $questions = generateQuestionMarks($pids);
        $query = "SELECT COUNT(*) AS total_count FROM vtiger_pc_transactions WHERE portfolio_id IN ({$questions})";
        $result = $adb->pquery($query, array($pids));

        if($adb->num_rows($result) > 0){

        }
    }

    public function RemoveIntervalDashes(){
        global $adb;
        $query = "UPDATE intervals SET AccountNumber = REPLACE(AccountNumber, '-', '')";
        $adb->pquery($query, array());
    }

    public function GetPCIntervals(){
        global $adb;
        set_time_limit (0);
        ini_set('memory_limit', '4096M');

        $query = "SELECT MAX(IntervalID) AS MaxID FROM [PortfolioCenter].[dbo].[PortfolioIntervals] i 
                  JOIN [PortfolioCenter].[dbo].[Portfolios] p ON p.PortfolioID = i.PortfolioID
                  AND p.DataSetID IN (1) AND CodeID IS NULL";
        $result = $this->QueryStraightResult($query);

        $max_id = mssql_result ($result, 0, 'MaxID');
        $counter = 6767098;

        while($counter < $max_id) {
            $query = "SELECT p.AccountNumber, p.DataSetID, IntervalID, i.PortfolioID, CodeID, CONVERT(VARCHAR(50), IntervalBeginDate, 120), CONVERT(VARCHAR(50), IntervalEndDate, 120), IntervalBeginValue, IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, CONVERT(VARCHAR(50), EntryDate, 120), CONVERT(VARCHAR(50), PriceBeginDate, 120), CONVERT(VARCHAR(50), PriceEndDate, 120), IntervalSourceID, IntervalTypeID, FirstDayFlows, FirstDayGrossFlows, CONVERT(VARCHAR(50), i.LastModifiedDate, 120), i.LastModifiedUserID, CONVERT(VARCHAR(255), PortfolioIntervalUID)
                      FROM [PortfolioCenter].[dbo].[PortfolioIntervals] i
                      JOIN [PortfolioCenter].[dbo].[Portfolios] p ON p.PortfolioID = i.PortfolioID
                      AND p.DataSetID IN (1) AND CodeID IS NULL AND i.IntervalID >= " . $counter . " AND i.IntervalID <= " . ($counter + 100000);
            $result = $this->QueryStraightResult($query);

            while (list($AccountNumber, $DataSetID, $IntervalID, $PortfolioID, $CodeID, $IntervalBeginDate, $IntervalEndDate, $IntervalBeginValue, $IntervalEndValue, $NetFlowAmount, $NetReturnAmount, $GrossReturnAmount, $EntryDate, $PriceBeginDate, $PriceEndDate, $IntervalSourceID, $IntervalTypeID, $FirstDayFlows, $FirstDayGrossFlows, $LastModifiedDate, $LastModifiedUserID, $PortfolioIntervalUID) = mssql_fetch_array($result)) {
                $sql_rows[] = "('$AccountNumber', '$DataSetID', '$IntervalID', '$PortfolioID', '$CodeID', '$IntervalBeginDate', '$IntervalEndDate', '$IntervalBeginValue', '$IntervalEndValue', '$NetFlowAmount', '$NetReturnAmount', '$GrossReturnAmount', '$EntryDate', '$PriceBeginDate', '$PriceEndDate', '$IntervalSourceID', '$IntervalTypeID', '$FirstDayFlows', '$FirstDayGrossFlows', '$LastModifiedDate', '$LastModifiedUserID', '$PortfolioIntervalUID')";
            }

            $query = "INSERT IGNORE INTO intervals (AccountNumber, DataSetID, IntervalID, PortfolioID, CodeID, IntervalBeginDate, IntervalEndDate, IntervalBeginValue, IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, EntryDate, PriceBeginDate, PriceEndDate, IntervalSourceID, IntervalTypeID, FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, LastModifiedUserID, PortfolioIntervalUID) VALUES ";
            while (list($AccountNumber, $DataSetID, $IntervalID, $PortfolioID, $CodeID, $IntervalBeginDate, $IntervalEndDate, $IntervalBeginValue, $IntervalEndValue, $NetFlowAmount, $NetReturnAmount, $GrossReturnAmount, $EntryDate, $PriceBeginDate, $PriceEndDate, $IntervalSourceID, $IntervalTypeID, $FirstDayFlows, $FirstDayGrossFlows, $LastModifiedDate, $LastModifiedUserID, $PortfolioIntervalUID) = mssql_fetch_array($result)) {
                $sql_rows[] = "('$AccountNumber', '$DataSetID', '$IntervalID', '$PortfolioID', '$CodeID', '$IntervalBeginDate', '$IntervalEndDate', '$IntervalBeginValue', '$IntervalEndValue', '$NetFlowAmount', '$NetReturnAmount', '$GrossReturnAmount', '$EntryDate', '$PriceBeginDate', '$PriceEndDate', '$IntervalSourceID', '$IntervalTypeID', '$FirstDayFlows', '$FirstDayGrossFlows', '$LastModifiedDate', '$LastModifiedUserID', '$PortfolioIntervalUID')";
            }
            $adb->pquery($query . implode(',', $sql_rows) . ';', array());
            $counter+=100000;
        }
    }

    public function ResetAccountTransactions($account_number){
        global $adb;

        $account_number = $this->ConfirmAccountNumber($account_number);
        if(!$account_number){
            $msg = "Account Not Found";
            return $msg;
        }
        $query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number = ? AND account_closed = 0 AND isvalid = 1";
        $pid_result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($pid_result) > 1){
            foreach($pid_result AS $k => $v){
                $ids[] = $v['portfolio_id'];
            }
            include_once("include/utils/cron/cTransactionsAccess.php");
            $pid = cTransactionsAccess::DetermineValidPortfolioFromDupes($ids);
/*            $msg = "There is more than one portfolio with this ID when there should not be... Not resetting.  The ID's are: ";
            foreach($ids AS $k => $v){
                $msg .= "{$v}, ";
            }
            $msg .= " Delete using the portfolio ID delete method instead.";
            return $msg;*/
        }else
        if($adb->num_rows($pid_result) == 0){
            return "Account number does not exist";
        }else
            $pid = $adb->query_result($pid_result, 0, "portfolio_id");

        $query = "SELECT * FROM PortfolioCenter.dbo.Transactions WHERE PortfolioID = {$pid}";
        $result = $this->CustomQuery($query);

        if($result !== "Error Connecting to PC" && $result !== 0){
            $query = "DELETE FROM vtiger_pc_transactions WHERE portfolio_id = ?";
            $adb->pquery($query, array($pid));
            $transactions = new cTransactionsAccess();
            return $transactions->WriteTransactionsDirectly($result);
        }else
            return "No Transactions";
    }
    
    public function ResetPortfolioTransactions($portfolio_id){
        global $adb;
        $query = "DELETE FROM vtiger_pc_transactions WHERE portfolio_id = ?";
        $adb->pquery($query, array($portfolio_id));
        
        $query = "SELECT * FROM Transactions WHERE PortfolioID = {$portfolio_id}";
        $result = $this->CustomQuery($query);
        $transactions = new cTransactionsAccess();
        return $transactions->WriteTransactionsDirectly($result);
    }
    
    public function UpdatePositions($date){
        require_once("include/utils/cron/cSecuritiesAccess.php");
        $securities = new cSecuritiesAccess();
        $securities->UpdateCRMSecurities($date);        
    }
    
    public function TotalAnnihilation($account_number){
        global $adb;
        $query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number = ?";
        $pid_result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($pid_result) > 1){
            foreach($pid_result AS $k => $v){
                $ids[] = $v['portfolio_id'];
            }
            $msg = "There is more than one portfolio with this ID when there should not be... No Annihilation for you.  The ID's are: ";
            foreach($ids AS $k => $v){
                $msg .= "{$v}, ";
            }
            return $msg;
        } else
        if($adb->num_rows($pid_result) == 0){
            $this->DeleteSummaryInformation($account_number);
            $this->DeletePositionInformation($account_number);
            $this->DeletePortfolioInformation($account_number);
            $this->DeleteHistory($account_number);
            return "Account number does not exist";
        } else{
            $pid = $adb->query_result($pid_result, 0, "portfolio_id");
            $this->DeletePortfolioTransactions($pid);
            $this->DeleteSummaryInformation($account_number);
            $this->DeletePositionInformation($account_number);
            $this->DeletePortfolioInformation($account_number);
            $this->DeleteHistory($account_number);
            $msg = "<that trumpet song they play when somebody dies>{$account_number} is gone forever";
            return $msg;
        }
    }

    public function CopyIndividualSecurity($symbol){
        
    }

    public function DeletePositionInformation($account_number){
        global $adb;
        $query = "DELETE FROM vtiger_crmentity WHERE crmid IN (SELECT positioninformationid FROM vtiger_positioninformation WHERE account_number=?)";
        $adb->pquery($query, array($account_number));
        $query = "DELETE FROM vtiger_positioninformation WHERE account_number = ?";
        $adb->pquery($query, array($account_number));
    }
    
    public function DeletePortfolioTransactions($pid){
        global $adb;
        $query = "DELETE FROM vtiger_pc_transactions WHERE portfolio_id = ?";
        $adb->pquery($query, array($pid));
    }
    
    public function DeleteSummaryInformation($account_number){
        global $adb;
        $query = "DELETE FROM vtiger_position_summary WHERE account_number=?";
        $adb->pquery($query, array($account_number));
        $query = "DELETE FROM vtiger_portfolio_summary WHERE account_number=?";
        $adb->pquery($query, array($account_number));
    }
    
    public function DeletePortfolioInformation($account_number){
        global $adb; 
        $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            $crmid = $adb->query_result($result, 0, 'portfolioinformationid');
            $query = "DELETE FROM vtiger_crmentity WHERE crmid = ?";
            $adb->pquery($query, array($crmid));
            $query = "DELETE FROM vtiger_portfolioinformation WHERE portfolioinformationid = ?";
            $adb->pquery($query, array($crmid));
        }
        
        $query = "DELETE FROM vtiger_portfolios WHERE portfolio_account_number = ?";
        $adb->pquery($query, array($account_number));
    }
    
    public function DeleteHistory($account_number){
        global $adb;
        $query = "DELETE FROM vtiger_portfolioinformation_historical WHERE account_number = ?";
        $adb->pquery($query, array($account_number));
        $query = "DELETE FROM vtiger_portfolioinformation_current WHERE account_number = ?";
        $adb->pquery($query, array($account_number));
        $query = "DELETE FROM vtiger_portfolioinformation_fees WHERE account_number = ?";
        $adb->pquery($query, array($account_number));
    }
    
    public function UpdateAccountInceptionDate($account_number){
        return PortfolioInformation_Module_Model::UpdateInceptionDate($account_number);
    }
    
    public function GetAllPortfolioInformationAccountsClosedAndOpen(){
        global $adb;
        $numers = array();
        $query = "SELECT account_number FROM vtiger_portfolioinformation WHERE account_number != ''";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $numbers[] = $v['account_number'];
            }
            return $numbers;
        }
        return 0;
    }
    
    public function UpdateSMAAccountDescription(){
        global $adb;
        $query = "SELECT SMAAccountDescription, AccountNumber FROM Portfolios";
        $result = $this->CustomQuery($query);
        if($result != "Error Connecting to PC"){
            $query = "UPDATE vtiger_portfolioinformation SET smaaccountdescription = ? WHERE account_number = ?";
            foreach($result AS $k => $v){
                if(strlen($v['SMAAccountDescription']) > 0 && strlen($v['AccountNumber']) > 0){
                    $adb->pquery($query, array($v['SMAAccountDescription'], $v['AccountNumber']));
                }
            }
        }        
    }
}