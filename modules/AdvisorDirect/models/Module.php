<?php

class AdvisorDirect_Module_Model extends Vtiger_Module_Model {
    
    /**
     * Returns the k=>v pairing in the form of custodian => email
     * @global type $adb
     * @return type
     */
    public function getCustodianList(){
        return Custodians_Record_Model::getAllCustodians();
    }
    
    /**
     * Returns the custodian information based on the passed in fax number
     * @param type $fax_number
     */
    public function getCustodianInfoFromFax($fax_number){
        return Custodians_Record_Model::getCustodianInfoFromFax($fax_number);
    } 
}
?>
