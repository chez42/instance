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
        $query = "SELECT * FROM vtiger_notifications AS notifications";
        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Notifications');
        $query .= "WHERE (notifications.smownerid = ? OR notifications.smownerid IN 
                  (SELECT groupid FROM vtiger_users2group AS users2group WHERE users2group.userid = ?))
                   AND (notifications.source != 'PORTAL' OR notifications.source IS NULL) 
                   GROUP BY notifications.notificationsid ORDER BY  notifications.notificationsid " . $sortBy . " LIMIT 20;";
        $rs = $db->pquery($query, array($userId, $userId));
        
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
        $query = "SELECT COUNT(`notifications`.`notificationsid`) AS ? FROM vtiger_notifications AS notifications";
        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Notifications');
        $query .= "WHERE (notifications.notification_status <> ? || notifications.notification_status IS NULL) 
                    AND (notifications.smownerid = ? OR notifications.smownerid IN 
                 (SELECT groupid FROM vtiger_users2group AS users2group WHERE users2group.userid = ?))
                 AND (notifications.source != 'PORTAL' OR notifications.source IS NULL);";
        $rs = $db->pquery($query, array($alias_total, self::NOTIFICATION_STATUS_YES, $userId, $userId));
        $total = 0;
        if ($db->num_rows($rs) && ($data = $db->fetch_array($rs))) {
            $total = intval($data[$alias_total]);
            //break;
        }
        return $total;
    }
     
    public static function updateNotificationStatus($status)
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        $sql = "UPDATE vtiger_notifications SET notification_status = ? WHERE smownerid = ? 
        AND (source != 'PORTAL' OR source IS NULL)";
        $params = array($status, $current_user->id);
        $result = $db->pquery($sql, $params);
        return $result ? true : false;
    }
    
    public function getTitle($fieldInstance) {
        $fieldName = $fieldInstance->get('listViewRawFieldName');
        
        $fieldValue = $this->get($fieldName);
        $rawData = $this->getRawData();
        $rawValue = $rawData[$fieldName];
        if ($fieldInstance) {
            $dataType = $fieldInstance->getFieldDataType();
            $uiType = $fieldInstance->get('uitype');
            $nonRawValueDataTypes = array('date', 'datetime', 'time', 'currency', 'boolean', 'owner');
            $nonRawValueUITypes = array(117);
            
            if (in_array($dataType, $nonRawValueDataTypes) || in_array($uiType, $nonRawValueUITypes)) {
                return $fieldValue;
            }
            if (in_array($dataType, array('reference', 'multireference'))) {
                $recordName = Vtiger_Util_Helper::getRecordName($rawValue);
                if ($recordName) {
                    return $recordName;
                } else {
                    return '';
                }
            }
            if($dataType == 'multipicklist') {
                $rawValue = $fieldInstance->getDisplayValue($rawValue);
            }
        }
        
        if($fieldName == 'description')
            return strip_tags(decode_html($rawValue));
        
        return $rawValue;
    }
    
    public function isEditable() {
        return false;
    }
    
    function get($key) {
        $value = parent::get($key);
        if ($key === 'description') {
            return decode_html($value);
        }
        return $value;
    }
    
    
}

?>