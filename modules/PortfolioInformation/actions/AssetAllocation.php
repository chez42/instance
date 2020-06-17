<?php
include_once('libraries/reports/new/nCommon.php');
include_once('include/utils/omniscientCustom.php');

class PortfolioInformation_AssetAllocation_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        if($request->get("run_all") == 1){
//            $this->UpdateAllAccounts();
        }
/*        $viewer = new Vtiger_Viewer();
        $viewer->assign("TRANSACTIONS", $transactions);
        $output = $viewer->view('TransactionsList.tpl', "PortfolioInformation", true);
        echo $output;*/
    }

    public function IsInPC($account_number){
        global $adb;
        $query = "SELECT * FROM vtiger_portfolios WHERE portfolio_account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0)
            return 1;
        return 0;
    }
    
    /**
     * Takes an array of account numbers optionally to insert
     * @global type $adb
     * @param type $account_numbers
     */
    public function UpdatePortfolioInformation($account_numbers = null){
        global $adb;
        if($account_numbers){
            $questions = generateQuestionMarks($account_numbers);
            $and = " AND p.account_number IN ({$questions})";
        }
        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformation_current pc ON p.account_number = pc.account_number
                  SET p.total_value = pc.total_value, 
                  p.market_value = pc.market_value, 
                  p.cash_value = pc.cash_value
                  WHERE p.account_number = pc.account_number {$and}";
        if($account_numbers){
            $adb->pquery($query, array($account_numbers));
        }else
            $adb->pquery($query, array());
    }

    static public function InsertDailyIndividualTotals($account_number, $date, $cash, $fixed, $equities, $market, $total){
        global $adb;
        $query = "INSERT INTO vtiger_portfolio_daily_individual (account_number, date, cash, fixed, equities, market, total)
                  VALUES (?,?,?,?,?,?,?)
                  ON DUPLICATE KEY UPDATE cash=VALUES(cash), fixed=VALUES(fixed), equities=VALUES(equities), market=VALUES(market), total=VALUES(total)";
        $adb->pquery($query, array($account_number, $date, $cash, $fixed, $equities, $market, $total));
    }

    public function UpdatePositionInformationQuantity(){

	}

	public function CreatePositionsFromAssetArray($account_number, $assets){
        if(!is_array($assets))
            return;

        foreach($assets AS $k => $v){
            $id = PositionInformation_Record_Model::GetPositionFromSymbolAndAccount($account_number, $v['security_symbol']);
            if($id == 0) {
                $record = PositionInformation_Record_Model::getCleanInstance("PositionInformation");
                $record->set('mode', 'create');
            }
            else {
                $record = PositionInformation_Record_Model::getInstanceById($id, "PositionInformation");
                $record->set('mode', 'edit');
            }
            $data = $record->getData();
            $data['quantity'] = $v['quantity'];
            $data['security_symbol'] = $v['security_symbol'];
            $data['current_value'] = $v['total_value'];
            $data['account_number'] = $account_number;

            $record->setData($data);
            $record->save();
        }
    }

    public function UpdateIndividualAccount($crmid){
        $description = "";
        $record = Vtiger_Record_Model::getInstanceById($crmid, 'PortfolioInformation');
        if(!$this->IsInPC($record->get('account_number')))
            return;

        $assets = CalculateAssetAllocations($record->get('account_number'));
        $this->CreatePositionsFromAssetArray($record->get('account_number'), $assets);

        if($assets != 0){
            $cash_value = $assets[11]['total_value'];
            unset($assets[11]);
            $fixed_income = $assets[2]['total_value'];
            unset($assets[2]);
//            $fixed_income += $assets[3]['total_value'];
//            unset($assets[3]);

            $other = 0;
            foreach($assets AS $k => $v){
                if(strcasecmp($v['security_symbol'], "Cash") == 0 || $v['security_type_id'] == 11){
                    $cash_value += $v['total_value'];
                    unset($assets[$k]);
                }
                else
                    $other+=$v['total_value'];
                $description .= $k . ", ";
            }

            $market_value = $fixed_income + $other;
            $user_id = $record->get("assigned_user_id");
            
            $contactid = Contacts_Module_Model::GetContactIDByTaxID($record->get('tax_id'));
            if($contactid){
                $record->set('contact_link', $contactid);
            }
            $record->set('cash_value', $cash_value);
            $record->set('fixed_income', $fixed_income);
            $record->set('equities', $other);
            $record->set('market_value', $market_value);
            $record->set('total_value', $market_value + $cash_value);
            $record->set('assigned_user_id', $user_id);
            $nickname = GetAccountNickname($record->get('account_number'));
            $record->set('nickname', $nickname);
            $data = $record->getData();
            $record->set('mode','edit');
            $record->save();
            $this->InsertDailyIndividualTotals($record->get('account_number'), date("Y-m-d"), $cash_value, $fixed_income, $other, $market_value, $market_value + $cash_value);
            unset($record);
            unset($assets);
            $assets = null;
            $record = null;
            $message = "Finished";
        }
        else {
            $user_id = $record->get("assigned_user_id");

            $contactid = Contacts_Module_Model::GetContactIDByTaxID($record->get('tax_id'));
            if($contactid){
                $record->set('contact_link', $contactid);
            }

            $record->set('cash_value', 0);
            $record->set('fixed_income', 0);
            $record->set('equities', 0);
            $record->set('market_value', 0);
            $record->set('total_value', 0);
            $record->set('assigned_user_id', $user_id);
            $nickname = GetAccountNickname($record->get('account_number'));
            $record->set('nickname', $nickname);
            $data = $record->getData();
            $record->set('mode','edit');
            $record->save();
            $this->InsertDailyIndividualTotals($record->get('account_number'), date("Y-m-d"), 0, 0, 0, 0, 0);
            unset($record);
            unset($assets);
            $assets = null;
            $record = null;
            $message = "Nothing to calculate";
//            $this->InsertDailyIndividualTotals($record->get('account_number'), date("Y-m-d"), 0, 0, 0, 0, 0);
        }

        return array("market_value"=>$market_value,
                     "cash_value"=>$cash_value,
                     "fixed_income"=>$fixed_income,
                     "equities"=>$other,
                     "total_value"=>$market_value+$cash_value,
                     "descriptions"=>$description,
                     "message"=>$message);
    }

    public function UpdateTrailing12Revenue(){
        global $adb;
        $query = "INSERT INTO vtiger_portfolioinformation_fees (account_number, year, month, expense_amount)
                  (SELECT portfolio_account_number, Year(trade_date), Month(trade_date), SUM(cost_basis_adjustment) AS expense_amount 
                                  FROM vtiger_pc_transactions t
                                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                                  AND t.trade_date BETWEEN '1900-01-01' AND NOW()
                                  AND t.activity_id = 160
                                  AND t.report_as_type_id = 60
                                  AND t.status_type_id = 100
                  GROUP BY Year(trade_date), Month(trade_date), p.portfolio_account_number)
                  ON DUPLICATE KEY UPDATE expense_amount = VALUES(expense_amount);";
        $adb->pquery($query, array());
    }

    public function UpdateAllAccounts(){                        
/*        global $adb;
        $query = "UPDATE batch_process SET maximum_id = (SELECT MAX(crmid) FROM vtiger_crmentity WHERE setype='PortfolioInformation')";
        $result = $adb->pquery($query, array());
        $query = "SELECT last_update_id, maximum_id FROM batch_process WHERE name='PortfolioInformation'";
        $result = $adb->pquery($query, array());
        $last_updated_id = $adb->query_result($result, 0, 'last_update_id');
        $maximum_id = $adb->query_result($result, 0, 'maximum_id');
        
        $query = "SELECT e.crmid 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0
                  AND e.crmid >= ?";

        $result = $adb->pquery($query, array($last_updated_id));

        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $crmid = $v['crmid'];
//                echo "ABOUT TO UPDATE: {$v['crmid']}<br />";
                $result = $this->UpdateIndividualAccount($crmid);
//                echo $crmid . ' -- ' . $result['message'] . " -- Descriptions (Cash/Fixed Income excluded): " . $result['descriptions'] . "<br />";
                $adb->pquery("UPDATE batch_process SET last_update_id = ? WHERE name='PortfolioInformation'", array($crmid));
            }
        }
        $result = null;*/
    }
    
    public function UpdateAllHistoricalAccounts(){
        global $adb;
        $query = "UPDATE batch_process SET maximum_id = (SELECT MAX(crmid) FROM vtiger_crmentity WHERE setype='PortfolioInformation')";
        $result = $adb->pquery($query, array());
        $query = "SELECT last_update_id, maximum_id FROM batch_process WHERE name='HistoricalPrices'";
        $result = $adb->pquery($query, array());
        $last_updated_id = $adb->query_result($result, 0, 'last_update_id');
        $maximum_id = $adb->query_result($result, 0, 'maximum_id');
        $query = "SELECT e.crmid 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0
                  AND p.closingdate is null
                  AND e.crmid >= ?";
        $result = $adb->pquery($query, array($last_updated_id));
        echo "Executing All Historical Accounts Update";
        if($adb->num_rows($result) > 0){
            $count = 0;
            foreach($result AS $k => $v){
                $crmid = $v['crmid'];
//                echo "ABOUT TO UPDATE: {$v['crmid']}<br />";
                $month_ini = new DateTime("first day of last month");
                $this->HistoricalUpdateIndividualAccount($crmid, $month_ini->format('Y-m-d'));
                $adb->pquery("UPDATE batch_process SET last_update_id = ? WHERE name='HistoricalPrices'", array($crmid));
                $count++;
                if($count > 1000){
                    echo "Done...1000";exit;
                }
            }
        }
        $result = null;
    }
    
    public function WriteComparisonTable(){
        global $adb;
        $query = "SELECT e.crmid 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0";

        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $crmid = $v['crmid'];
                $this->WriteToCompareTable($crmid);
            }
        }
    }
    
    public function WriteComparisonTableWithAccountsArray($accounts){
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $query = "SELECT e.crmid, account_number
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid AND p.account_number IN ({$questions})
                  WHERE e.deleted = 0";
        $result = $adb->pquery($query, array($accounts));
        foreach($result AS $k => $v){
            $crmid = $v['crmid'];
            $account_number = $v['account_number'];
            $this->WriteToCompareTable($crmid);
            $this->CopyAccountFromCompareTableToPortfolioInformation($account_number);
        }
    }
    
    static public function CalculateAndWriteTrailing12Fees($account_number = null){
        global $adb;
        $query = "CREATE TEMPORARY TABLE Trailing_12
                  SELECT p.portfolio_account_number, t.portfolio_id, SUM(t.quantity) AS trailing_12_fees FROM vtiger_pc_transactions t
                  JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE activity_id = 160
                  AND report_as_type_id=60
                  AND p.data_set_id IN (1,28)
                  AND t.trade_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)
                  GROUP BY portfolio_id;";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_portfolioinformation por
                  JOIN Trailing_12 t12 ON t12.portfolio_account_number = por.account_number
                  SET por.annual_management_fee = t12.trailing_12_fees;";
        $adb->pquery($query, array());
    }
    
    public function CopyAccountFromCompareTableToPortfolioInformation($account_number){
        global $adb;
        
        $query = "UPDATE vtiger_portfolioinformation p
                    JOIN vtiger_portfolioinformation_current pc ON p.account_number = pc.account_number
                    JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                    SET p.total_value = pc.total_value,
                    p.market_value = pc.market_value,
                    p.cash_value = pc.cash_value,
                    cf.equities = pc.equities,
                    cf.fixed_income = pc.fixed_income
                    WHERE p.account_number = ?";
        $adb->pquery($query, array($account_number));
    }
    
    public function WriteToCompareTable($crmid){
        global $adb;
        if(!$crmid)
            return;
        $record = Vtiger_Record_Model::getInstanceById($crmid, 'PortfolioInformation');
        $account_number = $record->get('account_number');
        $from_date = GetInceptionDate($account_number);        
        $date = date('Y-m-d');
        $assets = CalculateAssetAllocations($record->get('account_number'), $date);
        if($assets != 0){
            $cash_value = $assets[11]['total_value'];
            unset($assets[11]);
            $fixed_income = $assets[2]['total_value'];
            unset($assets[2]);
//            $fixed_income = $assets[3]['total_value'];
//            unset($assets[3]);
            $other = 0;
            foreach($assets AS $k => $v){
                if(strcasecmp($v['security_symbol'], "Cash") == 0 || $v['security_type_id'] == 11){
                    $cash_value += $v['total_value'];
                    unset($assets[$k]);
                }
                else
                    $other+=$v['total_value'];
                $description .= $k . ", ";
            }

            $market_value = $fixed_income + $other;
            $message = "Finished";
        }
        else
            $message = "Nothing to calculate";

        if(!$fixed_income)
            $fixed_income = 0;
        if(!$market_value)
            $market_value = 0;
        if(!$cash_value)
            $cash_value = 0;
        if(!$other)
            $other = 0;
        $historical_values[] = array("date"=>$date,
                                   "market_value"=>$market_value,
                                   "cash_value"=>$cash_value,
                                   "fixed_income"=>$fixed_income,
                                   "equities"=>$other,
                                   "total_value"=>$market_value+$cash_value,
                                   "descriptions"=>$description,
                                   "message"=>$message);

        $query = "INSERT INTO vtiger_portfolioinformation_current
                  (date, account_number, market_value, cash_value, fixed_income, equities, total_value, last_updated)
                  VALUES ";
        $duplicate = " ON DUPLICATE KEY UPDATE market_value=VALUES(market_value), cash_value=VALUES(cash_value),
                       fixed_income=VALUES(fixed_income), equities=VALUES(equities), total_value=VALUES(total_value), last_updated=NOW()";
        $count = 0;
        foreach($historical_values AS $k => $v){
            $insert .= "('{$v['date']}', '{$account_number}', {$v['market_value']}, {$v['cash_value']},
                         {$v['fixed_income']}, {$v['equities']}, {$v['total_value']}, NOW())";
            $count++;
            if($count < sizeof($historical_values)){//If we need to reset, don't add a comma
                $insert .= ",";
            }            
        }
        $query .= $insert . $duplicate;
        
        $adb->pquery($query, array());
    }
    
    /**
     * Update the historical table for the given crmid
     * @global type $adb
     * @param type $crmid
     */
    public function HistoricalUpdateIndividualAccount($crmid, $from_date=null){
        global $adb;
        $record = Vtiger_Record_Model::getInstanceById($crmid, 'PortfolioInformation');
        $account_number = $record->get('account_number');
        if(strlen($from_date) == 0)
            $from_date = GetInceptionDate($account_number);        
        $months = GetMonthsBetween($from_date, date('Y-m-d'));
        foreach($months AS $unused => $date){
            $assets = CalculateAssetAllocations($record->get('account_number'), $date);
            if($assets != 0){
                $cash_value = $assets[11]['total_value'];
                unset($assets[11]);
                $fixed_income = $assets[2]['total_value'];
                unset($assets[2]);
//                $fixed_income += $assets[3]['total_value'];
//                unset($assets[3]);
                $other = 0;
                foreach($assets AS $k => $v){
                    if(strcasecmp($v['security_symbol'], "Cash") == 0 || $v['security_type_id'] == 11){
                        $cash_value += $v['total_value'];
                        unset($assets[$k]);
                    }
                    else
                        $other+=$v['total_value'];
                    $description .= $k . ", ";
                }

                $market_value = $fixed_income + $other;
                $message = "Finished";
            }
            else
                $message = "Nothing to calculate";

            if(!$fixed_income)
                $fixed_income = 0;
            if(!$market_value)
                $market_value = 0;
            if(!$cash_value)
                $cash_value = 0;
            if(!$other)
                $other = 0;
            $historical_values[] = array("date"=>$date,
                                       "market_value"=>$market_value,
                                       "cash_value"=>$cash_value,
                                       "fixed_income"=>$fixed_income,
                                       "equities"=>$other,
                                       "total_value"=>$market_value+$cash_value,
                                       "descriptions"=>$description,
                                       "message"=>$message);
        }

        $query = "INSERT INTO vtiger_portfolioinformation_historical 
                  (date, account_number, market_value, cash_value, fixed_income, equities, total_value, last_updated)
                  VALUES ";
        $duplicate = " ON DUPLICATE KEY UPDATE market_value=VALUES(market_value), cash_value=VALUES(cash_value),
                       fixed_income=VALUES(fixed_income), equities=VALUES(equities), total_value=VALUES(total_value), last_updated=NOW()";
        $count = 0;
        foreach($historical_values AS $k => $v){
            $insert .= "('{$v['date']}', '{$account_number}', {$v['market_value']}, {$v['cash_value']},
                         {$v['fixed_income']}, {$v['equities']}, {$v['total_value']}, NOW())";
            $count++;
            if($count < sizeof($historical_values)){//If we need to reset, don't add a comma
                $insert .= ",";
            }            
        }
        $query .= $insert . $duplicate;
        
        $adb->pquery($query, array());
    }
}