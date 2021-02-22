<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_get_potentials($element, $user){
    
    global $log, $adb;
    
    $id = $element['id'];
    
    $permission_result = $adb->pquery("SELECT * FROM `vtiger_contact_portal_permissions` inner join
	vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contact_portal_permissions.crmid
	where crmid = ?", array($id));
    
    $potential_across_org = 0;
    
    //28743010
    $contact_ids = array();
    
    $contact_ids[] = $id;
    
    if($adb->num_rows($permission_result)){
        $potential_across_org = $adb->query_result($permission_result, 0, "potentials_record_across_org");
        $account_id = $adb->query_result($permission_result, 0, "accountid");
        if($account_id && $potential_across_org){
            $contact_ids[] = $account_id;
            $contact_result = $adb->pquery("SELECT * FROM `vtiger_contactdetails`
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			where accountid = ? and deleted = 0", array($account_id));
            for($i = 0; $i < $adb->num_rows($contact_result); $i++){
                $contact_ids[] = $adb->query_result($contact_result, $i, "contactid");
            }
        }
    }
    
    $module = $element['module'];
    
    $pageLimit = $element['pageLimit'];
    
    $startIndex = $element['startIndex'];
    
    $opportunity = $element['opportunity'];
    
    $sales_stage = $element['sales_stage'];
    
    $primary_email = $element['primary_email'];
    
    $mobile_phone = $element['mobile_phone'];
    
    $modifiedtime = $element['modifiedtime'];
    
    if($startIndex == ''){
        $startIndex = 0;
    }
    
    
    $potentials = array();
    
    $params = array();
    
    $count = 0;
    
    $sql = "SELECT * FROM vtiger_potential
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
    INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
    LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
    LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
    WHERE vtiger_crmentity.deleted = 0 
    AND vtiger_potential.contact_id IN ('" . implode("','", $contact_ids) . "') ";
    //AND vtiger_potential.related_to = ?
    
    
    if($opportunity){
        $sql .= " AND vtiger_potential.potentialname LIKE ?";
        $params[] = '%'.$opportunity.'%';
    }
    if($sales_stage){
        $sql .= " AND vtiger_potential.sales_stage = ?";
        $params[] = $sales_stage;
    }
    if($mobile_phone){
        $sql .= " AND vtiger_potentialscf.cf_2899 LIKE ?";
        $params[] = '%'.$mobile_phone.'%';
    }
    if($primary_email){
        $sql .= " AND vtiger_potentialscf.cf_869 LIKE ?";
        $params[] = '%'.$primary_email.'%';
    }
    
    if($modifiedtime){
        $sql .= " AND vtiger_crmentity.modifiedtime LIKE ?";
        $params[] = '%'.$modifiedtime.'%';
    }
   
    $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC ";
    
    $result = $adb->pquery($sql, $params);
    
    $count = $adb->num_rows($result);
    
    $potentialIds = array();
    
    if($count){
        
        for($ti=0;$ti<$adb->num_rows($result);$ti++){
            $potentialIds[] = $adb->query_result($result, $ti, 'potentialid');
        }
        
        $sql = "SELECT DISTINCT vtiger_potential.*, vtiger_crmentity.*, vtiger_potentialscf.* FROM vtiger_potential
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
        INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
        LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
        WHERE vtiger_crmentity.deleted = 0 
        AND vtiger_potential.contact_id IN ('" . implode("','", $contact_ids) . "')";
        
        $params = array();
        
        if($opportunity){
            $sql .= " AND vtiger_potential.potentialname LIKE ?";
            $params[] = '%'.$opportunity.'%';
        }
        if($sales_stage){
            $sql .= " AND vtiger_potential.sales_stage = ?";
            $params[] = $sales_stage;
        }
        if($mobile_phone){
            $sql .= " AND vtiger_potentialscf.cf_2899 LIKE ?";
            $params[] = '%'.$mobile_phone.'%';
        }
        if($primary_email){
            $sql .= " AND vtiger_potentialscf.cf_869 LIKE ?";
            $params[] = '%'.$primary_email.'%';
        }
        
        if($modifiedtime){
            $sql .= " AND vtiger_crmentity.modifiedtime LIKE ?";
            $params[] = '%'.$modifiedtime.'%';
        }
        
        $sql .=" ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT {$startIndex},{$pageLimit}";
        
        $result = $adb->pquery($sql, $params);
        
        if($adb->num_rows($result)){
            
            while($row = $adb->fetchByAssoc($result)){
                $potentials[] = $row;
            }
            
        }
    }
    
    return array("data" => $potentials, "count" => $count, 'potential_ids'=>$potentialIds);
    
}
?>