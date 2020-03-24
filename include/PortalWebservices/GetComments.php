<?php

    function vtws_getcomments($element,$user){
        
        global $adb,$site_URL;
        
        $html .= '';
        $commentData = array();
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
            AND related_to = ? ORDER BY vtiger_modcomments.modcommentsid DESC",array($element['ID']));
            
            if($adb->num_rows($commentQuery)){
                
                /*if($adb->num_rows($commentQuery) >= 10){
                    $html .= '<a href="#" style="min-width:100%!important;margin:15px!important;" class="pull-left more_comments" data-index="'.($startIndex + 10).'">More...</a>';
                }*/
                
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
                        
                        /*$html.='<div '. $c .' class="kt-chat__message kt-chat__message--success" style="margin: 1.5rem;padding: 10px;min-width: 50%!important;">
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
                                    $html .= '<a href="javascript:void(0)" data-filelocationtype="I" data-filename="" data-fileid="'.$commentId.'">
            							<span class="chat_document_preview" title="Preview" style="font-size:1.5em!important;">
            								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
            									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            										<rect x="0" y="0" width="24" height="24"></rect>
            										<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
            										<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"></path>
            									</g>
            								</svg>
            							</span>
            						</a>';
                                }
                        $html.=  '</div>
                        </div>';
                        */
                        $commentData[] = array(
                            'profileImage' => $profileImage,
                            'userName' => getOwnerName($smOwner),
                            'createdTime' => $createdTime,
                            'commentContent' => $commentContent,
                            'attachmentId' => $attachmentId,
                            'siteUrl' => $site_URL,
                            'commentId' => $commentId,
                            'attName' => $attName,
                            'users' => true
                        );
                        
                    }else{
                        $cusModel = Vtiger_Record_Model::getInstanceById($element['ID']);
                        $cusimageDetail = $cusModel->getImageDetails();
                       
                        foreach($cusimageDetail as $imagedetails){
                            $profileImage = $site_URL."/".$imagedetails['path']."_".$imagedetails['orgname'];
                        }
                       
                        /*$html.='<div '. $c .' class="kt-chat__message kt-chat__message--right kt-chat__message--brand" style="margin:1.5rem!important;min-width:50%!important;">
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
                                    $html .= '<a href="javascript:void(0)" data-filelocationtype="I" data-filename="" data-fileid="'.$commentId.'">
            							<span class="document_preview" title="Preview" style="font-size:1.5em!important;">
            								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
            									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            										<rect x="0" y="0" width="24" height="24"></rect>
            										<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
            										<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"></path>
            									</g>
            								</svg>
            							</span>
            						</a>';
                                }
                        $html.=  '</div>
                        </div>';*/
                        
                        $commentData[] = array(
                            'profileImage' => $profileImage,
                            'userName' => getOwnerName($smOwner),
                            'createdTime' => $createdTime,
                            'commentContent' => $commentContent,
                            'attachmentId' => $attachmentId,
                            'siteUrl' => $site_URL,
                            'commentId' => $commentId,
                            'attName' => $attName,
                            'client' => true   
                        );
                    }
                     
                }
                
            }
            
        }
        
        return $commentData;
        
    }