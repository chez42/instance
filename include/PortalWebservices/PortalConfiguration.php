<?php

function vtws_portalconfiguration($element,$user){
    
    global $adb,$site_URL;
    
    $returnData = array();
    
    if($element['portalurl']){
        
        $portalData = $adb->pquery("SELECT * FROM vtiger_instances 
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_instances.instancesid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_instances.portal_url like '%".$element['portalurl']."%'",array());
      
        if($adb->num_rows($portalData)){
            
            $returnData['user'] = $adb->query_result($portalData, 0, 'portal_user');
            $returnData['name'] = $adb->query_result($portalData, 0, 'company_name');
            $returnData['url'] = $adb->query_result($portalData, 0, 'domain');
            $returnData['accesskey'] = $adb->query_result($portalData, 0, 'portal_access_key');
            
            $returnData['portal_main_title'] = $adb->query_result($portalData, 0, 'portal_title');
            $returnData['portal_subtitle'] = $adb->query_result($portalData, 0, 'portal_subtitle');
            
            $recordData = Vtiger_Record_Model::getInstanceById($adb->query_result($portalData, 0, 'instancesid'));
            
            $imageDetails = $recordData->getImageDetails();
            $imageName ='';
            foreach ($imageDetails['imagename'] as $imageDetail){
                $imageName = $site_URL.'/'.$imageDetail['path'].'_'.$imageDetail['orgname'];
            }
            $returnData['image'] = $imageName;
           
            $iconName ='';
            foreach ($imageDetails['portalfavicon'] as $imageDetail){
                $iconName = $site_URL.'/'.$imageDetail['path'].'_'.$imageDetail['orgname'];
            }
            $returnData['icon'] = $iconName;
        }
        
    }
    
    return $returnData;
    
}