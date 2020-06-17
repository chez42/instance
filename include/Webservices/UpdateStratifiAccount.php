<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_update_stratifi_account($element,$user){

    global $log,$adb,$app_strings;

//    $data = json_decode($element);
    $data = json_decode($element);
    $stratid = $data->stratid;
    $score = $data->score;
    $entity_id = PortfolioInformation_Module_Model::GetEntityIDFromStratifiID($stratid);
    if($entity_id === 0)
        return json_encode(array("result" => "Fail", "message" => "Invalid Entity.  Probably from invalid Stratifi ID"));
    $model = Users_Privileges_Model::getInstanceById($user->id);
    if($model->isPermitted("PortfolioInformation", "EditView", $entity_id)) {
        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  SET stratifi_score = ? WHERE portfolioinformationid = ?";
        $adb->pquery($query, array($score, $entity_id));
        return json_encode(array("result" => "Success"));
    } else {
        return json_encode(array("result" => "Fail", "message" => "Permission Denied for user to alter this record"));
    }
}