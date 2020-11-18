<?php
function vtws_get_instance_details($element){
    
    global $adb, $current_user, $site_URL;
   
    $detQuery = $adb->pquery("SELECT * FROM vtiger_instances
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_instances.instancesid
    WHERE vtiger_crmentity.deleted=0 AND vtiger_instances.domain=?",array($element['domain']));
    
    $insDetail = array();
    if($adb->num_rows($detQuery)){
        
        $recordData = Vtiger_Record_Model::getInstanceById($adb->query_result($detQuery, 0, 'instancesid'));
        
        $insDetail['copyright'] = $recordData->get('copyright_text');
        $insDetail['facebook'] = $recordData->get('facebook_link');
        $insDetail['twitter'] = $recordData->get('twitter_link');
        $insDetail['linkedin'] = $recordData->get('linkedin_link');
        $insDetail['youtube'] = $recordData->get('youtube_link');
        $insDetail['instagram'] = $recordData->get('instagram_link');
        
        $imageDetails = $recordData->getImageDetails();
        
        $logoName ='';
        foreach ($imageDetails['instance_logo'] as $imageDetail){
            $logoName = $site_URL.'/'.$imageDetail['path'].'_'.$imageDetail['orgname'];
            $mime = vtlib_mime_content_type($imageDetail['path'].'_'.$imageDetail['orgname']);
            if(strstr($mime, "video/")){
                $type = 'video';
            }else if(strstr($mime, "image/")){
                $type = 'image';
            }
        }
        
        $insDetail['logo'] = $logoName;
        
        $backgroundName ='';
        $bgType = '';
        foreach ($imageDetails['instance_background'] as $imageDetail){
            $backgroundName = $site_URL.'/'.$imageDetail['path'].'_'.$imageDetail['orgname'];
            $mime = vtlib_mime_content_type($imageDetail['path'].'_'.$imageDetail['orgname']);
            if(strstr($mime, "video/")){
                $bgType = 'video';
            }else if(strstr($mime, "image/")){
                $bgType = 'image';
            }
        }
       
        $insDetail['background'] = $backgroundName;
        $insDetail['bgtype'] = $bgType;
        
    }
    
    return $insDetail;
    
}