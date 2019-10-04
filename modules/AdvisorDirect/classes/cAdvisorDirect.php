<?php
include_once('modules/Custodians/Custodians.php');

class cAdvisorDirect{
    public function __construct() {
        ;
    }
    
    /**
     * Returns the k=>v pairing in the form of custodian => email
     * @global type $adb
     * @return type
     */
    public function GetCustodianList(){
        global $adb;
        
        $custodiansModule = new Custodians();

        $query = $custodiansModule->getListQuery("Custodians");

        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0)
            foreach($result AS $k => $v){
                $list[$v['custodian_name']] = array("fax_number" => $v['fax_number'],
                                                    "from_fax_number" => $v['from_fax_number'],
                                                    "to_fax_number" => $v['to_fax_number']);
            }
        return $list;
    }
    
    /**
     * Returns the custodian information based on the passed in fax number
     * @param type $fax_number
     */
    public function GetCustodianInfoFromFax($fax_number){
        global $adb;
        
        $query = "SELECT * FROM vtiger_custodians c
                  LEFT JOIN vtiger_custodianscf cf ON c.custodiansid = cf.custodiansid
                  WHERE c.fax_number = ?";
        $result = $adb->pquery($query, array($fax_number));
        if($adb->num_rows($result) > 0){
            return $result;
        }
        else
            return 0;
    }
}
?>
