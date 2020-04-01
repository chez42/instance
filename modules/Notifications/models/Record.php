<?php
class Notifications_Record_Model extends Vtiger_Record_Model
{
    const NOTIFICATION_STATUS_NO = "No";
    const NOTIFICATION_STATUS_YES = "OK";
    
    public function getDetailViewUrl()
    {
        return "index.php?module=Notifications&view=Detail&record=" . $this->getId();
    }
    
    public static function getNotificationsByUser($userId)
    {
        $db = PearDatabase::getInstance();
        $sortBy = "ASC";
        
        global $current_user;
        
        $instances = array();
        $query = "SELECT * FROM vtiger_notifications AS notifications
                  INNER JOIN vtiger_crmentity  ON (notifications.notificationsid = vtiger_crmentity.crmid AND vtiger_crmentity.deleted = 0)";
        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Notifications');
        $query .= "WHERE (notifications.notification_status <> ? || notifications.notification_status IS NULL) 
                    AND (vtiger_crmentity.smownerid = ? OR vtiger_crmentity.smownerid IN 
                  (SELECT groupid FROM vtiger_users2group AS users2group WHERE users2group.userid = ?))
                   AND vtiger_crmentity.source = 'PORTAL'
                   GROUP BY vtiger_crmentity.crmid ORDER BY  vtiger_crmentity.crmid " . $sortBy . " ;";
        $rs = $db->pquery($query, array(self::NOTIFICATION_STATUS_YES, $userId, $userId));
        
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }
    
    public static function countNotificationsByUser($userId)
    {
        $db = PearDatabase::getInstance();
        $alias_total = "total";
        $query = "SELECT COUNT(`notifications`.`notificationsid`) AS ? FROM vtiger_notifications AS notifications
                 INNER JOIN vtiger_crmentity ON (notifications.notificationsid = vtiger_crmentity.crmid AND vtiger_crmentity.deleted = 0)";
        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Notifications');
        $query .= "WHERE (notifications.notification_status <> ? || notifications.notification_status IS NULL) 
                    AND (vtiger_crmentity.smownerid = ? OR vtiger_crmentity.smownerid IN 
                 (SELECT groupid FROM vtiger_users2group AS users2group WHERE users2group.userid = ?))
                 AND vtiger_crmentity.source = 'PORTAL' 
                 GROUP BY vtiger_crmentity.crmid;";
        $rs = $db->pquery($query, array($alias_total, self::NOTIFICATION_STATUS_YES, $userId, $userId));
        $total = 0;
        if ($db->num_rows($rs) && ($data = $db->fetch_array($rs))) {
            $total = intval($data[$alias_total]);
            //break;
        }
        return $total;
    }
     
    public static function updateNotificationStatus($id, $status)
    {
        $db = PearDatabase::getInstance();
        $sql = "UPDATE vtiger_notifications SET notification_status = ? WHERE notificationsid = ?";
        $params = array($status, $id);
        $result = $db->pquery($sql, $params);
        return $result ? true : false;
    }
}

?>