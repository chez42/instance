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
class Billing_BillingReportPdf_View extends Vtiger_MassActionAjax_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('GenrateLink');
        $this->exposeMethod('DownloadStatement');
        $this->exposeMethod('getPortfoilioLists');
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
        $cvId = $request->get('viewid');
        
        $result = array();
        
        $result['link'] = 'index.php?module='.$moduleName.'&view=BillingReportPdf&mode=DownloadStatement&viewid='.$cvId;
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    function DownloadStatement(Vtiger_Request $request){
        
        $cvId = $request->get('viewid');
        
        $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        
        $recordId = $customViewModel->getRecordIds(array(),'PortfolioInformation');
        
        global $site_URL, $adb;
        $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
        $companyLogo = rtrim($site_URL,'/').'/'.$companyModel->getLogoPath();
        
        $html = "<html>
            	<head>
            		<link type='text/css' rel='stylesheet' href='".rtrim($site_URL,'/')."/layouts/v7/lib/todc/css/bootstrap.min.css'>
            	</head>
            	<body style='font-size:1.8rem !important;'>";
        
        $portQuery = $adb->pquery("SELECT * FROM vtiger_portfolioinformation
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid
        WHERE vtiger_crmentity.deleted = 0 AND
        vtiger_portfolioinformation.portfolioinformationid IN (".generateQuestionMarks($recordId).")",$recordId);
        
        if($adb->num_rows($portQuery)){
            
            for($i=0;$i<$adb->num_rows($portQuery);$i++){
                
                $portData = $adb->query_result_rowdata($portQuery,$i);
                
                $userModel = Users_Record_Model::getInstanceById($portData['smownerid'],'Users');
                $logo = $userModel->getImageDetails();
                
                if($logo['user_logo'] && !empty($logo['user_logo'])){
                    if($logo['user_logo'][0] && !empty($logo['user_logo'][0])){
                        $logo = $logo['user_logo'][0];
                        $logo = rtrim($site_URL,'/').'/'.$logo['path']."_".$logo['name'];
                    }
                }
                if(empty($logo))
                    $logo = $companyLogo;
                    
                $account_number = $portData['account_number'];
                
                $conQuery = $adb->pquery("SELECT * FROM vtiger_contactdetails
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
                INNER JOIN vtiger_contactaddress ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_contactdetails.contactid = ?",
                    array($portData['contact_link']));
                
                $conData = array();
                $fullName = '';
                if($adb->num_rows($conQuery)){
                    $conData = $adb->query_result_rowdata($conQuery);
                    $fullName = $conData['firstname'].' '.$conData['lastname'];
                }
                
                $billingSpec = $adb->pquery("SELECT * FROM vtiger_billingspecifications
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_billingspecifications.billingspecificationsid
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_billingspecifications.billingspecificationsid = ?",
                    array($portData['billingspecificationid']));
                
                $billingData = array();
                $feeRate = '';
                
                $year = date('Y');
                $type = '';
                $amountValue = '';
                $feeamount = '';
                $totalDays = '';
                $start_date = '';
                $end_date = '';
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
                    
                    $account = new CustodianAccess($account_number);
                    
                    $balance = $account->GetBalance($beginningPriceDate);
                    
                    $totalValue = $balance->value ? $balance->value : 0;
                    
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
                    
                    if($type == 'Fixed Rate'){
                        
                        $transaction = $adb->pquery("SELECT *, SUM(vtiger_transactionscf.net_amount) as totalamount,
    					CASE
    						WHEN (vtiger_transactionscf.transaction_activity = 'Deposit of funds' OR vtiger_transactionscf.transaction_activity = 'Receipt of securities' ) then 'add'
    						WHEN (vtiger_transactionscf.transaction_activity = 'Withdrawal of funds') then 'minus'
                        END  AS transaction_status
    					FROM vtiger_transactions
    					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_transactions.transactionsid
    					INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid
    					WHERE vtiger_crmentity.deleted = 0 AND vtiger_transactions.account_number = ?
    					AND vtiger_transactionscf.transaction_activity IN ('Deposit of funds', 'Withdrawal of funds', 'Receipt of securities')
    					AND (vtiger_transactions.trade_date > ? AND vtiger_transactions.trade_date <= ?)
    					AND vtiger_transactionscf.net_amount > ?
    					GROUP BY vtiger_transactions.trade_date, transaction_status ORDER BY vtiger_transactions.trade_date DESC",
                            array($portData['account_number'],$proStartDate, $proEndDate, $proAmount));
                        
                        
                        if($adb->num_rows($transaction)){
                            
                            for($t=0;$t<$adb->num_rows($transaction);$t++){
                                
                                $transaction_data = $adb->query_result_rowdata($transaction,$t);
                                
                                $date1=date_create($transaction_data['trade_date']);
                                $date2=date_create($proEndDate);
                                $diff=date_diff($date2, $date1);
                                $diffDays = $diff->days + 1;
                                
                                $transactionAmount = ($diffDays/$totalDays*$feeamount);
                                
                                $totalAmount = $transaction_data['totalamount'];
                                if($transaction_data['transaction_activity'] == 'Withdrawal of funds'){
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
                    }
                    
                    $billingQuery = $adb->pquery("SELECT * FROM vtiger_billing
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_billing.billingid
                    WHERE vtiger_crmentity.deleted = 0 AND vtiger_billing.portfolioid = ?
                    AND vtiger_billing.beginning_price_date = ?",
                        array($portData['portfolioinformationid'],$beginningPriceDate));
                    
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
                    $billingObj->set('portfolioid', $portData['portfolioinformationid']);
                    $billingObj->set('billingspecificationid',$portData['billingspecificationid']);
                    $billingObj->set('feeamount', $amountValue);
                    $billingObj->set('beginning_price_date', $beginningPriceDate);
                    $billingObj->set('ending_price_date', $endingPriceDate);
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
                    
                }
                
                $html.="<br>
                <br>
                <br>
        		<div class='row'>
        			<div class='col-xs-1'></div>
            		<div class='col-xs-10'>
                		<div class='row'>
                			<div class='col-xs-12'><strong>".date('F d, Y')."</strong></div>
                		</div>
                        <br>
                		<div class='row'>
                			<div class='col-xs-3'>
                				<img src='". $logo ."' width='100%'>
                			</div>
                			<div class='col-xs-9'>
                			</div>
                		</div>
                        <br>
                		<div class='row'>
                			<div class='col-xs-12'><strong>".strtoupper($fullName)."</strong></div>
                		</div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-1'>
                                <label>To : </label>
                            </div>
                            <div class='col-xs-11'>
                                ".$conData['mailingstreet']."<br>
                                ".$conData['mailingcity']."<br>
                                ".$conData['mailingstate'].", ".$conData['mailingzip']."
                            </div>
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
                            <div class='col-xs-3'>
                                Portion of Assets Calculated
                            </div>
                            <div class='col-xs-3'>
                                Fee Rate (".$feeRate." Annual Rate)
                            </div>
                            <div class='col-xs-1'>
                            </div>
                            <div class='col-xs-3'>
                                Fee Amount
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-xs-2'>
                               ".$portData['account_number']." :
                            </div>
                            <div class='col-xs-3 text-center'>
                                ".number_format($totalValue, 2)."
                            </div>
                            <div class='col-xs-3'>
                                x ".$feeamount."
                            </div>
                            <div class='col-xs-1'>
                                =
                            </div>
                            <div class='col-xs-3'>
                                $".number_format($amountValue,2)."
                            </div>
                        </div>
                        <br>";
                    
                    if(!empty($transactionData)){
                        
                        $html .= "<div class='row'>
                            <div class='col-xs-12'>
                                Adjustment for capital flows:
                            </div>
                        </div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-3'>
                                Date of Flow
                            </div>
                            <div class='col-xs-2'>
                                Time Period
                            </div>
                            <div class='col-xs-1'>
                                Rate
                            </div>
                            <div class='col-xs-3'>
                                Amount of Flow
                            </div>
                            <div class='col-xs-3'>
                                Total Adjustment
                            </div>
                        </div>";
                        $addFeeTrans = 0;
                        foreach($transactionData as $transdata){
                            $addFeeTrans += $transdata['transactionamount']*$transdata['totalAmount'];
                            $html .= "<div class='row'>
                                <div class='col-xs-3'>
                                    ".date('F d, Y',strtotime($transdata['trade_date']))."
                                </div>
                                <div class='col-xs-2'>
                                    ".$transdata['diff_days']." / ".$transdata['totalDays']."  x  ".$transdata['trans_fee']."
                                </div>
                                <div class='col-xs-1'>
                                    x
                                </div>
                                <div class='col-xs-3'>
                                    $".number_format($transdata['totalAmount'],2)." =
                                </div>
                                <div class='col-xs-3 text-center'>
                                    $".number_format($transdata['transactionamount']*$transdata['totalAmount'], 2)."
                                </div>
                            </div>";
                        }
                        $html .= "<div class='row'>
                            <div class='col-xs-9'>
                            </div>
                            <div class='col-xs-3 text-center'>
                                <strong>---------</strong>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-xs-9 text-right'>
                                add
                            </div>
                            <div class='col-xs-3 text-center'>
                                $".number_format($addFeeTrans,2)."
                            </div
                        </div>
                        <div class='row'>
                            <div class='col-xs-9'>
                            </div>
                            <div class='col-xs-3 text-center'>
                                <strong>---------</strong>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-xs-9 text-right'>
                                equals
                            </div>
                            <div class='col-xs-3 text-center'>
                                $".number_format($addFeeTrans+$amountValue,2)."
                            </div>
                        </div>";
                        $amountValue = $addFeeTrans+$amountValue;
                    }
                    
                    $html .="<div class='row'>
                            <div class='col-xs-9'>
                            </div>
                            <div class='col-xs-3 text-center'>
                                <strong>----------------------</strong>
                            </div>
                        </div>
                        <div class='row' style='background-color: #dedede !important;'>
                            <div class='col-xs-9 text-right'>
                                Total Fees Debited
                            </div>
                            <div class='col-xs-3 text-center'>
                                <strong>$".number_format($amountValue, 2)."</strong>
                            </div>
                        </div>
                        <br>
                        <div class='row'>
                            <div class='col-xs-12'>
                                Disclaimer:<br>
                                This presentation is solely for informational purpose. Past performance is no guarantee of future returns.
                                Investing invol principal capital. The information in this report is believed to be accurate and complete
                                but cannot be guaranteed. Clients this information with ".strtolower($frequency)." statements from their
                                custodian, TD Ameritrade. Please contact us at your earliest conveni regarding the content of this presentation
                                and how it may be the right strategy for you.
                            </div>
                        </div>
            		</div>
            		<div class='col-xs-1'></div>
        		</div>
				</div>
                <div class='page-break' style='page-break-before: always !important;'></div>";
                    
            }
            
        }
        
        $html.="</body>
        </html>";
        
        
        $fileDir = 'cache/';
        $bodyFileName = $fileDir.'billingStatement.html';
        $fb = fopen($bodyFileName, 'w');
        fwrite($fb, $html);
        fclose($fb);
        
        $output = shell_exec("wkhtmltopdf --javascript-delay 6000 -T 10.0 -B 5.0 -L 5.0 -R 5.0  ".$bodyFileName." ".$fileDir."billingStatement.pdf 2>&1");
        
        header('Content-Type: application/pdf');
        header('Content-disposition: attachment; filename=billingStatement.pdf');
        readfile($fileDir."billingStatement.pdf");
        
        unlink($bodyFileName);
        unlink($fileDir."billingStatement.pdf");
        
    }
    
    function getPortfoilioLists(Vtiger_Request $request){
        
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        $allCustomViews = CustomView_Record_Model::getAll('PortfolioInformation');
        if (!empty($allCustomViews)) {
            $viewer->assign('CUSTOM_VIEWS', $allCustomViews);
        }
        
        $viewer->assign('MODULE', $moduleName);
        
        $viewer->view('GetPortfoliosList.tpl',$moduleName);
        
    }
}
