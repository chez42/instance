<?php

    function vtws_getcomments($element,$user){
        
        global $adb,$site_URL;
        
        $html .= '';
        
        if(isset($element['ID']) && $element['ID'] != ''){
                
            if(isset($element['index'])){
                $startIndex = $element['index'];
            } else {
                $startIndex = 0;
            }
                
            $commentQuery = $adb->pquery("SELECT DISTINCT(vtiger_modcomments.modcommentsid), vtiger_modcomments.*,
            vtiger_crmentity.createdtime, vtiger_crmentity.smcreatorid FROM vtiger_modcomments
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid
            WHERE vtiger_crmentity.deleted=0 AND vtiger_modcomments.modcommentsid > 0 
            AND related_to = ? ORDER BY vtiger_modcomments.modcommentsid DESC LIMIT ".$startIndex.",10",array($element['ID']));
            
            if($adb->num_rows($commentQuery)){
                
                if($adb->num_rows($commentQuery) >= 10){
                    $html .= '<a href="#" style="min-width:100%!important;margin:15px!important;" class="pull-left more_comments" data-index="'.($startIndex + 10).'">More...</a>';
                }
                
                for($c=$adb->num_rows($commentQuery)-1;$c>=0;$c--){
                    
                    $attachmentId = $adb->query_result($commentQuery, $c, 'filename');
                    $commentId = $adb->query_result($commentQuery, $c, 'modcommentsid');
                    $query = "SELECT vtiger_attachments.name FROM vtiger_attachments
    				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
    				WHERE crmid = ? ";
                    $params = array($commentId);
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
                        
                        $html.='<div '. $c .' class="kt-chat__message kt-chat__message--success" style="margin:1.5rem!important;min-width:50%!important;">
                            <div class="kt-chat__user">
                                <span class="kt-media kt-media--circle kt-media--sm">';
                        if($profileImage && file_exists($profileImage)){
                            $html.= ' <img src="'.$profileImage.'" alt="image">';
                        }else{
                            $html.= '<i class="flaticon-user"  style="font-size:30px!important;"></i>';
                        }
                        $html.= '</span>
                                <a href="#" class="kt-chat__username">'.getOwnerName($smOwner).'</a>
                                <span class="kt-chat__datetime">'.$createdTime.'</span>
                            </div>
                            <div class="kt-chat__text">
                                '.$commentContent;
                                if($attachmentId){
                                    $html .= '<br/><a style="font-size:11px!important;" target="_blank" href="'.$site_URL.'/index.php?module=Vtiger&action=ExternalDownloadLink&record='.$commentId.'" >'.$attName.'</a>';
                                }
                        $html.=  '</div>
                        </div>';
                       
                    }else{
                        $cusModel = Vtiger_Record_Model::getInstanceById($element['ID']);
                        $cusimageDetail = $cusModel->getImageDetails();
                       
                        foreach($cusimageDetail as $imagedetails){
                            $profileImage = $site_URL."/".$imagedetails['path']."_".$imagedetails['orgname'];
                        }
                       
                        $html.='<div '. $c .' class="kt-chat__message kt-chat__message--right kt-chat__message--brand" style="margin:1.5rem!important;min-width:50%!important;">
                            <div class="kt-chat__user">
                                <span class="kt-chat__datetime">'.$createdTime.'</span>
                                <a href="#" class="kt-chat__username">You</a>
                                <span class="kt-media kt-media--circle kt-media--sm">';
                        if($profileImage && file_exists($profileImage)){
                            $html.= ' <img src="'.$profileImage.'" alt="image">';
                        }else{
                            $html.= '<i class="flaticon-user" style="font-size:30px!important;"></i>';
                        }
                        $html.= '</span>
                            </div>
                            <div class="kt-chat__text">
                               '.$commentContent;
                                if($attachmentId){
                                    $html .= '<br/><a style="font-size:11px!important;" target="_blank" href="'.$site_URL.'/index.php?module=Vtiger&action=ExternalDownloadLink&record='.$commentId.'" >'.$attName.'</a>';
                                }
                        $html.=  '</div>
                        </div>';
                    }
                     
                }
                
            }
            
        }
        
        return $html;
        
    }