<?php

class Group_ActionAjax_Action extends Vtiger_Action_Controller
{
    
    public function checkPermission(Vtiger_Request $request)
    {
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("getAccountPortfolios");
    }
    
    
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    
    public function getAccountPortfolios(Vtiger_Request $request){
        
        global $adb;
        
        $record = $request->get('record');
        
        $query = $adb->pquery("SELECT * FROM vtiger_portfolioinformation
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid
        INNER JOIN vtiger_billingspecifications ON vtiger_billingspecifications.billingspecificationsid = vtiger_portfolioinformation.billingspecificationid
        INNER JOIN vtiger_crmentity crm ON crm.crmid = vtiger_billingspecifications.billingspecificationsid AND crm.deleted = 0
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_portfolioinformation.household_account = ?",array($record));
        
        $portfolioArray = array();
        
        if($adb->num_rows($query)){
            for($i=0;$i<$adb->num_rows($query);$i++){
                $portfolioData = $adb->query_result_rowdata($query, $i);
                
                $portfolioArray[] = array(
                    'portfolioid' => $portfolioData['portfolioinformationid'],
                    'portfolioname' => $portfolioData['account_number'],
                    'billingspecificationid' => $portfolioData['billingspecificationid'],
                    'billingspecificationname' => $portfolioData['name']
                    
                );
                
            }
        }
        
        $response = new Vtiger_Response();
        $response->setResult($portfolioArray);
        $response->emit();
    }
    
  
    
}

?>