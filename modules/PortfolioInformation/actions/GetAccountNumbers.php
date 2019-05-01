<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-05-31
 * Time: 3:32 PM
 */

class PortfolioInformation_GetAccountNumbers_Action extends Vtiger_BasicAjax_Action{

    public function process(Vtiger_Request $request) {
        $account_number = $request->get('account_number') . '%';
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $adb = PearDatabase::getInstance();

        $query = "SELECT account_number FROM vtiger_portfolioinformation
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid ";

        if(!$currentUser->isAdminUser()){
            $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('PortfolioInformation');
        }

        $query .= " WHERE vtiger_crmentity.deleted=0 AND account_number LIKE (?) ";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            $account_numbers = array();
            while($x = $adb->fetchByAssoc($result)){
                $account_numbers[] = $x['account_number'];
            }
            echo json_encode($account_numbers);
            return;
        }
        echo 0;
    }
}