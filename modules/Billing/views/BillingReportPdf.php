<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Billing_BillingReportPdf_View extends Vtiger_MassActionAjax_View {
    
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
        $selectedIds = $this->getRecordsListFromRequest($request);
        $excludedIds = $request->get('excluded_ids');
        $cvId = $request->get('viewname');
        
        $result = array();
        
        $result['link'] = 'index.php?module='.$moduleName.'&view=BillingReportPdf&mode=DownloadStatement&record='.implode(',',$selectedIds);
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    function DownloadStatement(Vtiger_Request $request){
        
        $recordId = explode(',',$request->get('record'));
        
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
                
                $totalValue = '299625.64';
                
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
                    array($portData['billingspecifications']));
                
                $billingData = array();
                $feeRate = '';
                
                if($adb->num_rows($billingSpec)){
                    
                    $billingData = $adb->query_result_rowdata($billingSpec);
                    $frequency = $billingData['billing_frequency'];
                    
                    $year = date('Y');
                    $type = $billingData['billing_type'];
                    $amountValue = $billingData['value'];
                    $feeamount = $billingData['value'];
                    $totalDays = '';
                    $start_date = ''; 
                    $end_date = '';
                    
                    if($frequency == 'Quaterly'){
                        
                        if(date('m') <= '3'){
                            
                            $start_date = date('d-m-Y',strtotime("01-01-".$year));
                            $end_date = date('d-m-Y',strtotime("31-03-".$year));
                            $totalDays = date('t', strtotime('01-01-'.$year)) + date('t', strtotime('01-02-'.$year)) + date('t', strtotime('01-03-'.$year));
                            
                        }else if(date('m') > '3' && date('m') <= '6'){
                            
                            $start_date = date('d-m-Y',strtotime("01-04-".$year));
                            $end_date = date('d-m-Y',strtotime("30-06-".$year));
                            $totalDays = date('t', strtotime('01-04-'.$year)) + date('t', strtotime('01-05-'.$year)) + date('t', strtotime('01-06-'.$year));
                            
                        }else if(date('m') > '6' && date('m') <= '9'){
                            
                            $start_date = date('d-m-Y',strtotime("01-07-".$year));
                            $end_date = date('d-m-Y',strtotime("30-09-".$year));
                            $totalDays = date('t', strtotime('01-07-'.$year)) + date('t', strtotime('01-08-'.$year)) + date('t', strtotime('01-09-'.$year));
                            
                        }else if(date('m') > '9'){
                            
                            $start_date = date('d-m-Y',strtotime("01-10-".$year));
                            $end_date = date('d-m-Y',strtotime("31-12-".$year));
                            $totalDays = date('t', strtotime('01-10-'.$year)) + date('t', strtotime('01-11-'.$year)) + date('t', strtotime('01-12-'.$year));
                            
                        }
                        
                        $feeRate = '1/4';
                        
                        if($type == 'Fixed Rate'){
                            $amountValue = ($totalValue * $amountValue)/100;
                        }
                        
                    }else if ($frequency == 'Monthly'){
                        
                        $start_date = date('01-m-Y');
                        $end_date = date('t-m-Y');
                        $feeRate = '1/12';
                        $totalDays = date('t', strtotime($start_date));
                        
                        if($type == 'Fixed Rate'){
                            $amountValue = ($totalValue * $amountValue)/100;
                        }
                    }
                    
                    $transactionData = array();
                    
                    if($type == 'Fixed Rate'){
                        
                        if($start_date)
                            $start_date = DateTimeField::convertToDBFormat($start_date);
                        
                        $transaction = $adb->pquery("SELECT * FROM vtiger_transactions 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_transactions.transactionsid
                        INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid
                        WHERE vtiger_crmentity.deleted = 0 AND vtiger_transactions.account_number = ?
                        AND vtiger_transactionscf.transaction_activity IN ('Deposit of funds', 'Withdrawal of funds')
                        AND vtiger_transactions.trade_date BETWEEN ? AND ?",
                        array($portData['account_number'],$start_date, date('Y-m-d')));
                       
                        if($adb->num_rows($transaction)){
                            
                            for($t=0;$t<$adb->num_rows($transaction);$t++){
                                
                                $transaction_data = $adb->query_result_rowdata($transaction,$t);
                                
                                $date1=date_create($transaction_data['trade_date']);
                                $date2=date_create(date('Y-m-d'));
                                $diff=date_diff($date1,$date2);
                                $diffDays = $diff->days;
                                
                                $transactionAmount = ($diffDays/$totalDays*$feeamount)/100;
                                
                                $totalAmount = number_format($transaction_data['net_amount'],2);
                                if($transaction_data['transaction_activity'] == 'Withdrawal of funds'){
                                    $totalAmount = '-'.number_format($transaction_data['net_amount'],2);
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
                                    $".$transdata['totalAmount']." = 
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
                            </div
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
    
    
}
