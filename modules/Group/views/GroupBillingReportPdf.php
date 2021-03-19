<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once "libraries/custodians/cCustodian.php";
class Group_GroupBillingReportPdf_View extends Vtiger_MassActionAjax_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('GenrateLink');
        $this->exposeMethod('DownloadStatement');
    }
    
    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
    function GenrateLink(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        
        $data = $request->getAll();
        unset($data['module']);
        unset($data['view']);
        unset($data['mode']);
        
        $result = array();
        
        $result['link'] = 'index.php?module='.$moduleName.'&view=GroupBillingReportPdf&mode=DownloadStatement&'.http_build_query($data);
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    function DownloadStatement(Vtiger_Request $request){
        
        
        global $adb, $site_URL;
        
        $records = $this->getRecordsListFromRequest($request);
        
        $html = "<html>
            	<head>
            		<link type='text/css' rel='stylesheet' href='".rtrim($site_URL,'/')."/layouts/v7/lib/todc/css/bootstrap.min.css'>
            	</head>
            	<body style='font-size:16px !important;font-family:Times New Roman,serif;'>";
        
        
        
        foreach($records as $record){
            
            $portQuery = $adb->pquery("SELECT * FROM vtiger_group_items
			INNER JOIN vtiger_group ON vtiger_group.groupid = vtiger_group_items.groupid
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_group_items.groupid
            INNER JOIN vtiger_crmentity portcrm ON portcrm.crmid = vtiger_group_items.portfolioid AND portcrm.deleted = 0
            INNER JOIN vtiger_crmentity billcrm ON billcrm.crmid = vtiger_group_items.billingspecificationid AND billcrm.deleted = 0
            WHERE vtiger_crmentity.deleted = 0 AND vtiger_group_items.active = 1 AND vtiger_group_items.groupid = ?",array($record));
            $portfolios = array();
            if($adb->num_rows($portQuery)){
                $groupName = $adb->query_result($portQuery, 0, 'group_name');
                for($b=0;$b<$adb->num_rows($portQuery);$b++){
                    $portfolios[$adb->query_result($portQuery, $b, 'billingspecificationid')][] = $adb->query_result($portQuery, $b, 'portfolioid');
                }
            }
            
            foreach($portfolios as $billing => $portfolio){
                
                $billingSpec = $adb->pquery("SELECT * FROM vtiger_billingspecifications
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_billingspecifications.billingspecificationsid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_billingspecifications.billingspecificationsid = ?",
                    array($billing));
                $billingData = array();
                $transaction_data = array();
                $transactionData = array();
                
                if($adb->num_rows($billingSpec)){
                    
                    $billingData = $adb->query_result_rowdata($billingSpec);
                    $frequency = $billingData['billing_frequency'];
                    
                    $type = $billingData['billing_type'];
                    $amountValue = $billingData['amount_value'];
                    $feeamount = $billingData['amount_value'];
                    $start_date = $billingData['beginning_date'];
                    $end_date = $billingData['ending_date'];
                    
                    $proStartDate = $billingData['proratefromdate'];
                    $proEndDate = $billingData['proratetodate'];
                    $proAmount = $billingData['prorateamount'];
                    
                    $beginningPriceDate=$billingData['beginning_price_date'];
                    $endingPriceDate=$billingData['ending_price_date'];
                    $priceDatediff=date_diff(date_create($proStartDate), date_create($proEndDate));
                    $totalDays = $priceDatediff->days;
                    
                    $totalValue =0;
                    
                    $portfolioAccountNumbers = array();
                    foreach($portfolio as $portId){
                        
                        $account_number = PortfolioInformation_Module_Model::GetAccountNumbersFromCrmid($portId);
                        
                        $portfolioAccountNumbers = array_merge($portfolioAccountNumbers, $account_number);
                        
                        $account = new CustodianAccess($account_number);
                        
                        $balance = $account->GetBalance($beginningPriceDate);
                        
                        $totalPortfolioAmount = $balance->value ? $balance->value : 0;
                        
                        $totalValue += $totalPortfolioAmount;
                        
                    }
                    
                    $rangeArray = array();
                    
                    $arrayAmount = array();
                    
                    $resultantTaxAmount = 0;
                    
                    $finalSchedule = array();
                    
                    if($type == 'Schedule'){
                        
                        $range = $adb->pquery("SELECT * FROM vtiger_billing_range WHERE billingid = ?",
                            array($billing));
                        
                        if($adb->num_rows($range)){
                            for($b=0;$b<$adb->num_rows($range);$b++){
                                $rangeData = $adb->query_result_rowdata($range, $b);
                                $rangeArray[] = array(
                                    'amount_from' => $rangeData['from'],
                                    'amount_to' => $rangeData['to'] ? $rangeData['to'] : $totalValue,
                                    'value' => $rangeData['value']/100
                                );
                            }
                        }
                        
                        $remainingAmount = $totalValue;
                        
                        foreach ($rangeArray as $key => $value) {
                            $resultArray = array();
                            if ($totalValue > $value['amount_to']) {
                                $sum                       = $value['amount_to'] - $value['amount_from'];
                                $resultArray['amount']     = $sum;
                                $resultArray['toAmount']   = $value['amount_to'];
                                $resultArray['percentage'] = $value['value'];
                                array_push($arrayAmount, $resultArray);
                                $remainingAmount = $remainingAmount - $sum;
                            } else {
                                $resultArray['amount']     = $remainingAmount;
                                $resultArray['toAmount']   = $totalValue;
                                $resultArray['percentage'] = $value['value'];
                                array_push($arrayAmount, $resultArray);
                                break;
                            }
                        }
                        
                        foreach ($arrayAmount as $key => $value) {
                            $cal                = (($value['amount'] * $value['percentage']));
                            $finalSchedule[] = array(
                                'account' => '&nbsp;',
                                'amount' => $value['toAmount'],
                                'feerate' => $value['percentage'],
                                'feeamount' => $cal
                            );
                            $feeamount = $value['percentage'];
                        }
                        
                    }else{
                        
                        if($frequency == 'Quaterly'){
                            
                            $feeRate = '1/4';
                            $feeamount = ($feeamount/4)/100;
                            if($type == 'Fixed Rate'){
                                $amountValue = ($totalValue * $feeamount);
                            }
                            
                        }else if ($frequency == 'Monthly'){
                            
                            $feeRate = '1/12';
                            $feeamount = ($feeamount/12)/100;
                            if($type == 'Fixed Rate'){
                                $amountValue = ($totalValue * $feeamount);
                            }
                        }
                    }
                    
                    $transaction = $adb->pquery("SELECT *, SUM(vtiger_transactionscf.net_amount) as totalamount,
					CASE
						WHEN (
						vtiger_transactionscf.transaction_activity = 'Deposit of funds' OR vtiger_transactionscf.transaction_activity = 'Receipt of securities' ) then 'add'
						WHEN (
						vtiger_transactionscf.transaction_activity = 'Transfer of funds' OR vtiger_transactionscf.transaction_activity = 'Withdrawal of funds' OR vtiger_transactionscf.transaction_activity = 'Transfer of securities') then 'minus'
					END  AS transaction_status
					FROM vtiger_transactions
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_transactions.transactionsid
					INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid
					WHERE vtiger_crmentity.deleted = 0 AND vtiger_transactions.account_number IN (".generateQuestionMarks($portfolioAccountNumbers).")
					AND vtiger_transactionscf.transaction_activity IN ('Transfer of funds', 'Deposit of funds', 'Withdrawal of funds', 'Receipt of securities', 'Transfer of securities')
					AND (vtiger_transactions.trade_date > ? AND vtiger_transactions.trade_date <= ?)
					AND vtiger_transactionscf.net_amount > ?
					GROUP BY vtiger_transactions.trade_date, transaction_status ORDER BY vtiger_transactions.trade_date DESC",
                        array($portfolioAccountNumbers,$proStartDate, $proEndDate, $proAmount));
                    
                    
                    if($adb->num_rows($transaction)){
                        
                        for($t=0;$t<$adb->num_rows($transaction);$t++){
                            
                            $transaction_data = $adb->query_result_rowdata($transaction,$t);
                            
                            $date1=date_create($transaction_data['trade_date']);
                            $date2=date_create($proEndDate);
                            $diff=date_diff($date2, $date1);
                            $diffDays = $diff->days + 1;
                            
                            $transactionAmount = ($diffDays/$totalDays*$feeamount);
                            
                            $totalAmount = $transaction_data['totalamount'];
                            if(
                                $transaction_data['transaction_activity'] == 'Transfer of funds' ||
                                $transaction_data['transaction_activity'] == 'Withdrawal of funds' ||
                                $transaction_data['transaction_activity'] == 'Transfer of securities'
                                ){
                                    $totalAmount = '-'.$transaction_data['totalamount'];
                            }
                            
                            $transactionData[] = array(
                                'trade_date' => $transaction_data['trade_date'],
                                'diff_days' => $diffDays,
                                'totalAmount' => $totalAmount,
                                'totalDays' => $totalDays,
                                'transactionamount' => $transactionAmount,
                                'transactiontype' => $transaction_data['transaction_activity'],
                                'trans_fee' => $feeamount
                            );
                            
                        }
                    }
                    
                    $billingQuery = $adb->pquery("SELECT * FROM vtiger_billing
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_billing.billingid
                    WHERE vtiger_crmentity.deleted = 0 AND vtiger_billing.group_billingid = ? 
                    AND vtiger_billing.billingspecificationid = ? AND vtiger_billing.beginning_price_date = ?",
                    array($record,$billing,$beginningPriceDate));
                    
                    if($adb->num_rows($billingQuery)){
                        $billingId = $adb->query_result($billingQuery, 0, 'billingid');
                        $billingObj = Vtiger_Record_Model::getInstanceById($billingId);
                        $billingObj->set('mode', 'edit');
                    }else{
                        $billingObj = Vtiger_Record_Model::getCleanInstance('Billing');
                    }
                    
                    $billingObj->set('start_date', $start_date);
                    $billingObj->set('end_date', $end_date);
                    $billingObj->set('portfolio_amount', $totalValue);
                    $billingObj->set('group_billingid', $record);
                    $billingObj->set('billingspecificationid', $billing);
                    $billingObj->set('feeamount', $amountValue);
                    $billingObj->set('beginning_price_date', $beginningPriceDate);
                    $billingObj->set('ending_price_date', $endingPriceDate);
                    $billingObj->set('billing_type', 'Group');
                    $billingObj->save();
                    
                    if($billingObj->getId() && !empty($transactionData)){
                        
                        $adb->pquery("DELETE FROM vtiger_billing_capitalflows WHERE billingid=?",array($billingObj->getId()));
                        
                        foreach($transactionData as $key => $transdata){
                            
                            $adjustment = $transdata['transactionamount']*$transdata['totalAmount'];
                            
                            $adb->pquery("INSERT INTO vtiger_billing_capitalflows(billingid, trade_date,
                            diff_days, totalamount, totaldays, transactionamount, transactiontype, trans_fee, totaladjustment)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($billingObj->getId(), $transdata['trade_date'],
                            $transdata['diff_days'], $transdata['totalAmount'], $transdata['totalDays'], $transdata['transactionamount'],
                            $transdata['transactiontype'], $transdata['trans_fee'], $adjustment));
                            
                        }
                        
                    }
                    
                    if($billingObj->getId() && !empty($portfolio)){
                        
                        $adb->pquery("DELETE FROM vtiger_billing_portfolio_accounts WHERE billingid=?",array($billingObj->getId()));
                    
                        foreach($portfolio as $portId){
                            
                            $portfolioModel = Vtiger_Record_Model::getInstanceById($portId);
                           
                            $account = new CustodianAccess($portfolioModel->get('account_number'));
                            
                            $balance = $account->GetBalance($beginningPriceDate);
                            
                            $totalPortfolioAmount = $balance->value ? $balance->value : 0;
                            
                            if($billingObj->getId() ){
                                
                                $adb->pquery("INSERT INTO vtiger_billing_portfolio_accounts( billingid, portfolioid, portfolio_amount, bill_amount)
                                VALUES (?, ?, ?, ?)", array($billingObj->getId(), $portId, $totalPortfolioAmount, ($totalPortfolioAmount*$feeamount)));
                                
                            }
                        }
                    
                    }
                    
                }
                
                $html.="<br>
                <br>
                <br>
        		<div class='row'>
                    
            		<div class='col-xs-12'>
                    
                        <br>
                		<div class='row'>
                			<div class='col-xs-12' style = 'font-weight:600;font-family:Times New Roman,serif;'>".strtoupper($groupName)."</div>
                		</div>
                			    
                        <br>
                        <br>
                        <br>
                        <div class='row'>
                            <div class='col-xs-12'>
                                Statement for asset management services ".date('F d, Y',strtotime($start_date))." to ".date('F d, Y',strtotime($end_date)).".
                            </div>
                        </div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-1'>
                            </div>
                            <div class='col-xs-3'>
                                ".date('F d, Y',strtotime($start_date))."
                            </div>
                            <div class='col-xs-8'>
                                Portfolio Value: ".number_format($totalValue,2)."
                            </div>
                        </div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-12'>
                                The following includes proration breakpoints and respective fees:
                            </div>
                        </div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-2'>
                                Account
                            </div>
                            <div class='col-xs-4' style = 'text-align:right;'>
                                Portion of Assets Calculated
                            </div>
                            <div class='col-xs-4' style = 'text-align:left;'>
                                Fee Rate (".$feeRate." Annual Rate)
                            </div>
                                    
                            <div class='col-xs-2' style = 'text-align:left;'>
                                Fee Amount
                            </div>
                        </div>";
                
                if($type == 'Schedule'){
                    $amountValue = 0;
                    foreach($finalSchedule as $key => $schedule){
                        $html .= "<div class='row'>
                                <div class='col-xs-2'>
                                   ".$schedule['account']." :
                                </div>
                                <div class='col-xs-4 text-center'>
                                    ".number_format($schedule['amount'], 2)."
                                </div>
                                <div class='col-xs-4'>
                                    x ".$schedule['feerate']."
                                </div>
                                        
                                <div class='col-xs-2'>
                                   = $".number_format($schedule['feeamount'],2)."
                                </div>
                            </div>
                           ";
                        $amountValue += $schedule['feeamount'];
                    }
                    $html .= " <div class='row'>
                            <div class='col-xs-9'>
                            </div>
                            <div class='col-xs-3 text-center'>
                                <strong>---------</strong>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-xs-9 text-right'>
                                gross fee
                            </div>
                            <div class='col-xs-3 text-center'>
                                $".number_format($amountValue,2)."
                            </div
                        </div>";
                    
                }else{
                    $html .= "<div class='row'>
                            <div class='col-xs-2'>
                               ".$portData['account_number']." :
                            </div>
                            <div class='col-xs-4' style = 'text-align:right;'>
                                ".number_format($totalValue, 2)."
                            </div>
                            <div class='col-xs-4' style = 'text-align:left;'>
                                &nbsp;&nbsp;x&nbsp;&nbsp;".$feeamount."
                            </div>
                                    
                            <div class='col-xs-2' style = 'text-align:left;'>
                                 =&nbsp;&nbsp;&nbsp;&nbsp;$".number_format($amountValue,2)."
                            </div>
                        </div>
                        <br>";
                }
                
                if(!empty($transactionData)){
                    
                    $html .= "<div class='row'>
                            <div class='col-xs-12'>
                                Adjustment for capital flows:
                            </div>
                        </div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-3' style = 'width:24%;'>
                                Date of Flow
                            </div>
                            <div class='col-xs-2' style = 'width:23%;'>
                                Time Period
                            </div>
                            <div class='col-xs-1' style = 'width:5%;'>
                                Rate
                            </div>
                            <div class='col-xs-3' style = 'width:24%;'>
                                Amount of Flow
                            </div>
                            <div class='col-xs-3' style = 'width:24%;text-align:right;'>
                                Total Adjustment
                            </div>
                        </div>";
                    $addFeeTrans = 0;
                    foreach($transactionData as $transdata){
                        $addFeeTrans += $transdata['transactionamount']*$transdata['totalAmount'];
                        $html .= "<div class='row'>
                                <div class='col-xs-3' style = 'width:24%;'>
                                    ".date('F d, Y',strtotime($transdata['trade_date']))."
                                </div>
                                <div class='col-xs-2' style = 'width:23%'>
                                    ".$transdata['diff_days']." / ".$transdata['totalDays']."  x  ".$transdata['trans_fee']."
                                </div>
                                <div class='col-xs-1' style = 'width:5%;'>
                                    x
                                </div>
                                <div class='col-xs-3' style = 'width:24%;'>
                                    $".number_format($transdata['totalAmount'],2)." =
                                </div>
                                <div class='col-xs-3 text-center' style = 'width:24%;text-align:right;'>
                                    $".number_format($transdata['transactionamount']*$transdata['totalAmount'], 2)."
                                </div>
                            </div>";
                    }
                    $html .= "<div class='row'>
							<div class='col-xs-9'>
							</div>
							<div class='col-xs-3' style = 'text-align:right;'>
								<strong>---------</strong>
							</div>
						</div>
						<div class='row'>
							<div class='col-xs-9 text-right'>
								add
							</div>
							<div class='col-xs-3' style = 'text-align:right;'>
								$".number_format($addFeeTrans,2)."
							</div>
						</div>
						<div class='row'>
							<div class='col-xs-9'>
							</div>
							<div class='col-xs-3' style = 'text-align:right;'>
								<strong>---------</strong>
							</div>
						</div>
						<div class='row'>
							<div class='col-xs-9 text-right'>
								equals
							</div>
							<div class='col-xs-3' style = 'text-align:right;'>
								$".number_format($addFeeTrans+$amountValue,2)."
							</div>
						</div>";
                    $amountValue = $addFeeTrans+$amountValue;
                }
                
                $html .="<br>
					<div class='row' style='margin-left:5px;margin-right:5px;'>
						<div class='col-xs-3 text-center'>Name</div>
						<div class='col-xs-3 text-center'>Acct. Num</div>
						<div class='col-xs-3 text-center'>Portfolio Value</div>
						<div class='col-xs-3 text-center'>Bill Amount</div>
					</div>";
                
                foreach($portfolio as $portId){
                    
                    $portfolioModel = Vtiger_Record_Model::getInstanceById($portId);
                    $contactName = '';
                    if($portfolioModel->get('contact_link')){
                        $conModel = Vtiger_Record_Model::getInstanceById($portfolioModel->get('contact_link'));
                        $contactName = (($conModel->get('salutationtype') != '--None--')?$conModel->get('salutationtype'):'').$conModel->get('firstname').' '.$conModel->get('lastname');
                    }
                    $account = new CustodianAccess($portfolioModel->get('account_number'));
                    
                    $balance = $account->GetBalance($beginningPriceDate);
                    
                    $totalPortfolioAmount = $balance->value ? $balance->value : 0;
                    
                    $html .="
					<div class='row' style='margin-left:5px;margin-right:5px;'>
						<div class='col-xs-3'>".$contactName."</div>
						<div class='col-xs-3'>".$portfolioModel->get('account_number')."</div>
						<div class='col-xs-3 text-right'>$".number_format($totalPortfolioAmount,2)."</div>
						<div class='col-xs-3 text-right'>$".number_format($totalPortfolioAmount*$feeamount, 2)."</div>
					</div>";
                    
                }
                $html.="<div class='row' style='margin-left:5px;margin-right:5px;' >
							<div class='col-xs-9'>
							</div>
							<div class='col-xs-3' style = 'text-align:right;'>
								<strong>----------------</strong>
							</div>
						</div>
						<div class='row' style='background-color: #dedede !important;margin-left:5px;margin-right:5px;'>
							<div class='col-xs-9 text-right' style = 'text-align:right;'>
								Total Fees Debited
							</div>
							<div class='col-xs-3 text-right' style = 'text-align:right;'>
								<strong>$".number_format($amountValue, 2)."</strong>
							</div>
						</div>
								    
								    
            		</div>
								    
        		</div>
				</div>
                <div class='page-break' style='page-break-before: always !important;'></div>";
            }
            
        }
        
        $html.="</body>
        </html>";
        
        $fileDir = 'cache/';
        $bodyFileName = $fileDir.'groupBillingStatement.html';
        $fb = fopen($bodyFileName, 'w');
        fwrite($fb, $html);
        fclose($fb);
        
        $output = shell_exec("wkhtmltopdf -T 5.0 -B 5.0 -L 20.0 -R 20.0  ".$bodyFileName." ".$fileDir."groupBillingStatement.pdf 2>&1");
        
        header('Content-Type: application/pdf');
        header('Content-disposition: attachment; filename=groupBillingStatement.pdf');
        readfile($fileDir."groupBillingStatement.pdf");
        
        unlink($bodyFileName);
        unlink($fileDir."groupBillingStatement.pdf");
    }
    
    function getRecordsListFromRequest(Vtiger_Request $request) {
        $cvId = $request->get('viewname');
        $module = $request->get('module');
        if(!empty($cvId) && $cvId=="undefined"){
            $sourceModule = $request->get('sourceModule');
            $cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
        }
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        if(!empty($selectedIds) && $selectedIds != 'all') {
            if(!empty($selectedIds) && count($selectedIds) > 0) {
                return $selectedIds;
            }
        }
        
        $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }
            
            /**
             *  Mass action on Documents if we select particular folder is applying on all records irrespective of
             *  seleted folder
             */
            if ($module == 'Documents') {
                $customViewModel->set('folder_id', $request->get('folder_id'));
                $customViewModel->set('folder_value', $request->get('folder_value'));
            }
            
            $customViewModel->set('search_params',$request->get('search_params'));
            return $customViewModel->getRecordIds($excludedIds,$module);
        }
    }
}
