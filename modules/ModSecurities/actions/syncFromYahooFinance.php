<?php

class ModSecurities_syncFromYahooFinance_Action extends Vtiger_BasicAjax_Action{
    
	public $db_col_mapping = array(
		"Symbol"		=> "symbol",
		"Cash"			=> "cash",
		"Stocks"		=> "stock",
		"Bonds" 		=> "bonds",
		"Others"		=> "others",
		"Preferred"		=> "preferred",
		"Convertable"	=> "convertable",
		"YTD Return"	=> "ytd_return",
		"Category"		=> "category",
		"Beta (3y)"		=> "beta_3y",
		"Yield"			=> "yield",
		"Fund Summary"	=> "fund_summary",
		"5y Average Return" => "5y_avg_return"
	);
			
	public $index_col_mapping = array(
		"Symbol"		=> "security_symbol",
		"Cash"			=> "cash_net",
		"Stocks"		=> "us_stock",
		"Bonds" 		=> "us_bond",
		"Others"		=> "other_net",
		"Preferred"		=> "preferred_net",
		"Convertable"	=> "convertible_net",
		"YTD Return"	=> "cf_2515",
		"Category"		=> "cf_2519",
		"Beta (3y)"		=> "cf_2521",
		"Yield"			=> "dividend_yield",
		"Fund Summary"	=> "summary",
		"5y Average Return" => "cf_2517"
	);
					
	public function process(Vtiger_Request $request) {
		
		$adb = PearDatabase::getInstance();
		
		$record = $request->get('record');
		
		$recordModel = Vtiger_Record_Model::getInstanceById($record, $request->get('module'));
		
		$result = $adb->pquery("select * from vtiger_yahoo_finanace_modsecurities_symbol where symbol = ?",array($recordModel->get('security_symbol')));
			
		$date_var = date("Y-m-d H:i:s");
		
		if(!$adb->num_rows($result)){
			
			$adb->pquery("insert into vtiger_yahoo_finanace_modsecurities_symbol(symbol,modifiedtime) values (?,?)",
			array($recordModel->get('security_symbol'), $adb->formatDate($date_var, true)));
		}
		
		$symbol_data = $recordModel->getYahooFinanceSymbolDetail($recordModel->get('security_symbol'));
		
		if(!empty($symbol_data)){
			
			$db_col_mapping = $this->db_col_mapping;
			
			$index_col_mapping = $this->index_col_mapping;
			
			$params = $mod_params = array();
			
			$update_query = "UPDATE vtiger_yahoo_finanace_modsecurities_symbol SET ";
			
			$mod_update_query = "UPDATE vtiger_modsecurities
			INNER JOIN vtiger_modsecuritiescf ON vtiger_modsecurities.modsecuritiesid = vtiger_modsecuritiescf.modsecuritiesid
			INNER JOIN vtiger_yahoo_finanace_modsecurities_symbol ON vtiger_yahoo_finanace_modsecurities_symbol.symbol = vtiger_modsecurities.security_symbol
			SET ";
			
			foreach($symbol_data as $FieldLabel => $fvalue){
				
				$fvalue = rtrim($fvalue, "%");
				
				if($fvalue != 'N/A' && $fvalue != ''){
					
					if($FieldLabel == 'Fund Summary')
						$fvalue = decode_html($fvalue);
					
					$update_query .= $db_col_mapping[$FieldLabel]. " = ?, ";
					$params[] = $fvalue;
					
					$mod_update_query .= $index_col_mapping[$FieldLabel]. " = ?, ";
					$mod_params[] = $fvalue;
				}
			}
			
			if(!empty($params)){
				
				$update_query .= " modifiedtime = ?, yahoo_finance_modifiedtime = ? WHERE symbol = ?";
				
				$params[] = $adb->formatDate($date_var, true);
				$params[] = $adb->formatDate($date_var, true);
				$params[] = $recordModel->get('security_symbol');
				
				$adb->pquery($update_query,$params);
		
				$mod_update_query .= " yahoo_finance_last_update = ? WHERE vtiger_modsecurities.security_symbol = ?";
				$mod_params[] = $adb->formatDate($date_var, true);
				$mod_params[] = $recordModel->get('security_symbol');
				
				$adb->pquery($mod_update_query,$mod_params);
				
				$adb->pquery("update vtiger_crmentity set modifiedtime =? where crmid = ?",array($adb->formatDate($date_var, true), $record));
				
				$result = array("success" => true, "message" => "Symbol update successfully.");
			
			} else 
				$result = array("success" => false, 'message' => "Sorry, No data found.");
		} else 
			$result = array("success" => false, 'message' => "Sorry, No data found.");
		
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}