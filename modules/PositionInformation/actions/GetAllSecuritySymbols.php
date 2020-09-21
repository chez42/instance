<?php
class PositionInformation_GetAllSecuritySymbols_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){}
    
    
    public function process(Vtiger_Request $request){
        
        global $adb, $current_user;
        
        $symbolQuery = $adb->pquery("SELECT * FROM vtiger_modsecurities
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modsecurities.modsecuritiesid
        WHERE vtiger_crmentity.deleted = 0");
        $symbols = array();
        if($adb->num_rows($symbolQuery)){
            for($i=0;$i<$adb->num_rows($symbolQuery);$i++){
                $symbols[] = array(
                    'symbol' => $adb->query_result($symbolQuery, $i, 'modsecuritiesid'),  
                    'value' => $adb->query_result($symbolQuery, $i, 'security_symbol')
                );
            }
        }
        
        $response = new Vtiger_Response();
        
        $response->setResult(($symbols));
        
        $response->emit();
    }
    
}
?>
