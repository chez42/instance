<?php

    function vtws_get_notifications($element,$user){
        
        global $adb,$site_URL;
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        if(isset($element['mode'])){
            
            $notificationId = $element['notify_id'];
            
            $update = $adb->pquery("UPDATE vtiger_notifications SET notification_status = ? WHERE notificationsid = ?",
                array('OK', $notificationId));
            
            return array('success'=>true);
            
        }else{
            
            $id = $element['ID'];
            
            $permission_result = $adb->pquery("SELECT * FROM `vtiger_contact_portal_permissions` inner join
        	vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contact_portal_permissions.crmid
        	where crmid = ?", array($id));
            
            $ticket_across_org = 0;
            
            $contact_ids = array();
            
            $contact_ids[] = $id;
            
            if($adb->num_rows($permission_result)){
                $ticket_across_org = $adb->query_result($permission_result, 0, "tickets_record_across_org");
                $account_id = $adb->query_result($permission_result, 0, "accountid");
                if($account_id){
                    $contact_result = $adb->pquery("SELECT * FROM `vtiger_contactdetails`
        			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
        			where accountid = ? and deleted = 0", array($account_id));
                    for($i = 0; $i < $adb->num_rows($contact_result); $i++){
                        $contact_ids[] = $adb->query_result($contact_result, $i, "contactid");
                    }
                }
            }
            $contact_ids[] = 71414083;
            $sql = "SELECT DISTINCT vtiger_troubletickets.*, vtiger_crmentity.*, vtiger_ticketcf.*,
            vtiger_troubletickets.status as ticket_status
            FROM vtiger_troubletickets
            inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
            inner JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
            left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
            left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
            where vtiger_crmentity.deleted=0 and
    		vtiger_troubletickets.parent_id in ('" . implode("','", $contact_ids) . "') ";
            
            $ticketResult = $adb->pquery($sql);
            $ticketIds = array();
            if($adb->num_rows($ticketResult)){
                for($i=0;$i<$adb->num_rows($ticketResult);$i++){
                    $ticketIds[] = $adb->query_result($ticketResult, $i, 'ticketid');   
                }
            }
            
            $notifySql = "SELECT DISTINCT * FROM vtiger_notifications
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notifications.notificationsid
            WHERE (notification_status <> 'OK' || notification_status IS NULL) AND vtiger_crmentity.deleted = 0 AND vtiger_notifications.related_to IN 
            ('" . implode("','", $ticketIds) . "','".$id."') ORDER BY vtiger_crmentity.createdtime DESC";
            
            $notifyResult = $adb->pquery($notifySql);
            $html = ''; 
            $notifyCount = $adb->num_rows($notifyResult) ? $adb->num_rows($notifyResult):0;
            if($adb->num_rows($notifyResult)){
                for($n=0;$n<$adb->num_rows($notifyResult);$n++){
                    $notifyData = $adb->query_result_rowdata($notifyResult, $n);
                    
                    $titleTime = Vtiger_Util_Helper::formatDateTimeIntoDayString(date('Y-m-d H:i:s', strtotime($notifyData["createdtime"])));
                    $showTime = Vtiger_Util_Helper::formatDateDiffInStrings(date('Y-m-d H:i:s', strtotime($notifyData["createdtime"])));
                    
                    $html .= '<a href="#" class="kt-notification__item notifyItem" data-notify-id="'.$notifyData['notificationsid'].'">
                            <div class="kt-notification__item-icon">';
                    if(getSalesEntityType($notifyData['related_record']) == 'ModComments'){
                        $html .= '<i title="comment" class="flaticon-comment kt-font-primary"></i>';
                    }else if(getSalesEntityType($notifyData['related_record']) == 'Documents'){
                        $html .= '<i title="document" class="flaticon-doc kt-font-primary"></i>';
                    }
                    $html .= '</div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title" title="'.getSalesEntityType($notifyData['related_to']).' : '.Vtiger_Functions::getCRMRecordLabel($notifyData['related_to']).'">
                                '.html_entity_decode($notifyData['description']).'
                            </div></div>
                            <div class="kt-notification__item-time" title="'.$titleTime.'">
                                '.$showTime.'
                            </div>
                        </div>
                        <i class="flaticon-close closeNotify" title="close" data-notify-id="'.$notifyData['notificationsid'].'"></i>
                    </a>';
                }
            }else{
                $html .= '<div class="kt-grid kt-grid--ver" style="min-height: 200px;">
                    <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                        <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                            All caught up!
                            <br>No new notifications.
                        </div>
                    </div>
                </div>';
            }
            
            return array('html' => $html, 'count' =>$notifyCount);
            
        }
    }