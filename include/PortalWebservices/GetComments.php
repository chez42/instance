<?php

function vtws_getcomments($element,$user){
    
    global $adb,$site_URL;
    
    $html .= '';
    
    $commentData = array();
    
    if(isset($element['ID']) && $element['ID'] != ''){
        
        
        $commentQuery = $adb->pquery("SELECT DISTINCT(vtiger_modcomments.modcommentsid), vtiger_modcomments.*,
            vtiger_crmentity.createdtime, vtiger_crmentity.smcreatorid FROM vtiger_modcomments
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
            WHERE vtiger_crmentity.deleted=0 AND vtiger_modcomments.modcommentsid > 0
            AND related_to = ? ORDER BY vtiger_modcomments.modcommentsid DESC",array($element['ID']));
        
        if($adb->num_rows($commentQuery)){
            
            
            for($c=$adb->num_rows($commentQuery)-1; $c>=0; $c--){
                
                $attName = '';
                
                $attachmentId = $adb->query_result($commentQuery, $c, 'filename');
                
                $commentId = $adb->query_result($commentQuery, $c, 'modcommentsid');
                
                $query = "SELECT vtiger_attachments.name FROM vtiger_attachments
    				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
    				WHERE crmid = ? ";
                
                $params = array();
                
                $params[] = $commentId;
                
                if($attachmentId) {
                    $query .= 'AND vtiger_attachments.attachmentsid = ?';
                    $params[] = $attachmentId;
                }
                
                $attresult = $adb->pquery($query, $params);
                
                if($adb->num_rows($attresult)){
                    $attName = $adb->query_result($attresult, 0, 'name');
                }
                
                $customer = $adb->query_result($commentQuery, $c, 'customer');
                $commentContent = html_entity_decode($adb->query_result($commentQuery, $c, 'commentcontent'));
                $createdTime = date('M,d Y', strtotime($adb->query_result($commentQuery, $c, 'createdtime')));
                $smOwner = $adb->query_result($commentQuery, $c, 'smcreatorid');
                
                if($customer != $element['ID']){
                    
                    $userModel = Users_Record_Model::getInstanceById($smOwner,'Users');
                    $imageDetail = $userModel->getImageDetails();
                    
                    foreach($imageDetail['imagename'] as $imagedetails){
                        $profileImage = $site_URL."/".$imagedetails['path']."_".$imagedetails['orgname'];;
                    }
                    
                    $commentData[] = array(
                        'profileImage' => $profileImage,
                        'userName' => getOwnerName($smOwner),
                        'createdTime' => $createdTime,
                        'commentContent' => $commentContent,
                        'attachmentId' => is_numeric($attachmentId)?$attachmentId:'',
                        'siteUrl' => $site_URL,
                        'commentId' => $commentId,
                        'attName' => ($attName && $attName != 'null')?$attName:'',
                        'users' => true
                    );
                    
                } else {
                    
                    $cusModel = Vtiger_Record_Model::getInstanceById($element['ID']);
                    
                    $cusimageDetail = $cusModel->getImageDetails();
                    
                    foreach($cusimageDetail as $imagedetails){
                        $profileImage = $site_URL."/".$imagedetails['path']."_".$imagedetails['orgname'];
                    }
                    
                    $commentData[] = array(
                        'profileImage' => $profileImage,
                        'userName' => getOwnerName($smOwner),
                        'createdTime' => $createdTime,
                        'commentContent' => $commentContent,
                        'attachmentId' => is_numeric($attachmentId)?$attachmentId:'',
                        'siteUrl' => $site_URL,
                        'commentId' => $commentId,
                        'attName' => ($attName && $attName != 'null')?$attName:'',
                        'client' => true
                    );
                }
                
            }
            
        }
        
    }
    
    return $commentData;
    
}