<?php
class MSExchange_Utils_Helper {
    
    const settings_table_name = 'vtiger_msexchange_sync_settings';
    const syncdetail_table_name = "vtiger_msexchange_sync";
    const fieldmapping_table_name = "vtiger_msexchange_fieldmapping";
    
    public static $defaultMapping = array(
        "Contacts" => array(
            "firstname" => "First Name",
            "lastname" => "Last Name",
            "email" => "Email",
        ),
        "Calendar" => array(
            "subject" => "Event", 
            "date_start" => "Start", 
            "due_date" => "Until Date & Time", 
            "eventstatus" => "Planned",
            "activitytype" => "Meeting",
            "location" => "Where", 
            "visibility" => "Privacy",
            "description" => "Body"    
        )
    );
    
    public static $exchangeContactFields = array(
        "First Name", "Last Name", "Notes", "Birthday",
        "Email Address", "Business Phone", "Home Phone", "Mobile Phone", "Job Title",
        "Department", "Company", "Assistant", 
        "Address" => array(
            "Business" => array("Street", "City", "State/Province", "ZIP/Postal code", "Country/Region"),
            "Home" => array("Street", "City", "State/Province", "ZIP/Postal code", "Country/Region"),
            "Other" => array("Street", "City", "State/Province", "ZIP/Postal code", "Country/Region")
        )
    );
    
    static function getCredentialsForUser($user, $module) {
        
        $userId = $user->getId();
        
        $db = PearDatabase::getInstance();
        
        $sql = 'SELECT * FROM ' . self::settings_table_name . ' WHERE user = ? and module = ?';
        
        $result = $db->pquery($sql, array($userId, $module));
        
        if($db->num_rows($result)){
        	
            $row = $db->fetchByAssoc($result);
        	
        	return $row;
        }
        
        return array();
    }
    
	static function getSyncDirectionForUser($module , $user = false) {
        
	    if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        $db = PearDatabase::getInstance();
        
        $sql = 'SELECT direction FROM ' . self::settings_table_name . ' WHERE 
        user = ? and module = ?';
        
        $result = $db->pquery( $sql, array($user->getId(), $module) );
        
        if($db->num_rows($result)){
        	return $db->query_result($result, 0, 'direction');
        }
        
        return '11';
        
    }

    static function getLastSyncTime($module) {
        
        $db = PearDatabase::getInstance();
        
        $user = Users_Record_Model::getCurrentUserModel();
        
        $userId = $user->getId();
        
        $sql = "SELECT lastsynctime FROM " . self::syncdetail_table_name . " WHERE 
        user = ? and exchangemodule = ?";
        
        $result = $db->pquery($sql, array($userId, $module));
        
        if($db->num_rows($result)){
            return $db->query_result($result, 0, 'lastsynctime');
        }
        
        return '';
    }
    
    
    public static function getLastVtigerSyncTime($sourceModule) {
        
        $db = PearDatabase::getInstance();
        
        $user = Users_Record_Model::getCurrentUserModel();
        
        $result = $db->pquery("SELECT vtigersynctime FROM " . self::syncdetail_table_name . " 
        WHERE user = ? AND exchangemodule = ?", array($user->id, $sourceModule));
        
        if ($db->num_rows($result) > 0) {
            return $db->query_result($result, 0, 'vtigersynctime');
        } else {
            return '';
        }
    }
    
	public static function updateLastVtigerSyncTime($sourceModule, $modifiedTime = false) {
        $db = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();
		if (!$modifiedTime) {
            $modifiedTime = self::getLastVtigerSyncTime($sourceModule);
        }
        
        $result = $db->pquery("SELECT vtigersynctime FROM " . self::syncdetail_table_name . " WHERE user = ? AND exchangemodule = ?", array($user->id, $sourceModule));
        if ($db->num_rows($result) > 0) {
		    $db->pquery("UPDATE " . self::syncdetail_table_name . " SET vtigersynctime = ? WHERE user = ? AND exchangemodule = ?", array($modifiedTime, $user->id, $sourceModule));
        } else {
        	if ($modifiedTime) {
                $db->pquery("INSERT INTO " . self::syncdetail_table_name . " (exchangemodule,user,vtigersynctime) VALUES (?,?,?)", array($sourceModule, $user->id, $modifiedTime));
            }
        }
    }
    
    static function deleteSync($module, $userid){
    	$db = PearDatabase::getInstance();
    	$db->pquery("delete from ". self::syncdetail_table_name . " where userid = ? and module = ?",array($userid, $module));
    	$db->pquery("delete from ". self::settings_table_name . " where userid = ? and module = ?",array($userid, $module));
    	$db->pquery("delete from ".  self::auth_table_name . " where userid = ?",array($userid, $module));
    }
    
    
    public static function getImpersonationIdentifierForUser($user, $module){
        
        $db = PearDatabase::getInstance();
        
        $result = $db->pquery("select impersonation_identifier, username, password from vtiger_msexchange_sync_settings where user = ? and module = ?",array($user, $module));
      
        if($db->num_rows($result)){
            
            $userData = $db->fetchByAssoc($result);
            
            $uname = $userData['username'];
            
            $password = $userData['password'];
            
            if(!empty($uname) && !empty($password))
                return true;
            
            if(!empty($userData['impersonation_identifier']))
                return true;
        } 
            
        return false;
    }
    
    static function hasSettingsForUser($userId,$source_module) {
        
        $db = PearDatabase::getInstance();
        
        $sql = 'SELECT 1 FROM ' . self::settings_table_name . ' WHERE user = ? AND module = ?';
        
        $result = $db->pquery($sql, array($userId,$source_module));
        
        if($db->num_rows($result) > 0){
            return true;
        }
        
        return false;
    }
    
    public static function saveSyncSettings($request){
        
        $db = PearDatabase::getInstance();
        
        $user = Users_Record_Model::getCurrentUserModel();
        
        $userId = $user->getId();
        
        $source_module = $request->get('sourcemodule');
        
        $impersonation_identifier = $request->get("impersonation_identifier", '');
        
        $username = $request->get("username", '');
        
        $password = $request->get("password", '');
        
        $sync_direction = $request->get('sync_direction');
        
        if($request->get('enabled') == 'on' || $request->get('enabled') == 1) {
            $enabled = 1;
        } else {
            $enabled = 0;
        
        }
        
        if($source_module == 'Calendar' || $source_module == 'Task'){
            
            $enable_cron = $request->get("enable_cron");
            
            if($enable_cron == 'on')
                $enable_cron = 1;
            else
                $enable_cron = 0;
            
            $syncStartDate = getValidDBInsertDateValue($request->get("sync_start_from"));
        
        } else {
            
            $syncStartDate = 'NULL';
            
            $enable_cron = 0;
        }
        
        if(MSExchange_Utils_Helper::hasSettingsForUser($userId,$source_module)) {
            $sql = 'UPDATE ' . self::settings_table_name . ' SET impersonation_identifier = ?, username = ?, password = ?, direction = ?, sync_start_from = ?, enable_cron = ? WHERE user = ? AND module = ?';
            $params = array($impersonation_identifier, $username, $password, $sync_direction, $syncStartDate, $enable_cron, $userId, $source_module);
            $db->pquery($sql, $params);
        } else {
            $syncId = $db->getUniqueID(self::settings_table_name);
            $sql = 'INSERT INTO ' . self::settings_table_name . ' VALUES (?,?,?,?,?,?,?,?,?,?)';
            $params = array($syncId, $userId,$source_module,$sync_direction,$impersonation_identifier, $username, $password,$syncStartDate,$enable_cron, '');
            $db->pquery($sql, $params);
            
            $sync_result = $db->pquery("select * from vtiger_msexchange_sync where user = ? and exchangemodule = ?",
                array($userId, $source_module));
            
            if($db->num_rows($sync_result) > 0 && $source_module == 'Calendar'){
                $syncStartDate = $syncStartDate ." 00:00:00";
                $db->pquery("update vtiger_msexchange_sync set synctime = ?, vtigersynctime = ? where user = ? and exchangemodule = ?",
                    array($syncStartDate,$syncStartDate,$userId, $source_module));
            }
        }
    }
    
    public static function getCurrentUserImpersonation($module){
        
        $user = Users_Record_Model::getCurrentUserModel();
        
        $userId = $user->getId();
        
        $db = PearDatabase::getInstance();
        
        $result = $db->pquery("select impersonation_identifier, username, password from vtiger_msexchange_sync_settings where user = ? and module = ?",array($userId, $module));
        
        if($db->num_rows($result)){
            
            $userData = $db->fetchByAssoc($result);
            
            $uname = $userData['username'];
            
            $password = $userData['password'];
            
            if(!empty($uname) && !empty($password))
                return array("username" => $uname, "password" => $password);
            
            if(!empty($userData['impersonation_identifier']))
                return array("impersonation_identifier" => $userData['impersonation_identifier']);
        }
        return false;
    }
    
    
    function getCalendarSyncStartDate($user = false){
        
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        $userId = $user->getId();
        
        if(!MSExchange_Utils_Helper::hasSettingsForUser($userId,'Calendar')) {
            return false;
        } else {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT sync_start_from FROM ' . self::settings_table_name . ' WHERE user = ? AND module = ?';
            $result = $db->pquery($sql, array($userId,'Calendar'));
            $syncStartDate = $db->query_result($result, 0, 'sync_start_from');
            if($syncStartDate)
                return $syncStartDate;
            else
                return false;
        }
    }
    
    public static function checkCronEnabled($module){
        
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        $userId = $user->getId();
        
        if(!MSExchange_Utils_Helper::hasSettingsForUser($userId,$module)) {
            return true; // defaults to enabled
        } else {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT enable_cron FROM ' . self::settings_table_name . ' WHERE user = ? AND module = ?';
            $result = $db->pquery($sql, array($userId,$module));
            $enabled = $db->query_result($result, 0, 'enable_cron');
        }
        
        if($enabled == 1) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Updates the database with syncronization times
     * @param <sting> $sourceModule module to which sync time should be stored
     * @param <date> $modifiedTime Max modified time of record that are sync
     */
    public static function updateSyncTime($sourceModule, $modifiedTime = false, $user = false) {
        $db = PearDatabase::getInstance();
        
        if(!$user)
            $user = Users_Record_Model::getCurrentUserModel();
        
        if (!$modifiedTime) {
            $modifiedTime = self::getSyncTime($sourceModule, $user);
        }
        
        if(!$modifiedTime){
            $modifiedTime = date("Y-m-d H:i:s");
            $modifiedTime = $db->formatDate($modifiedTime, true);
        }
        
        if (!self::getSyncTime($sourceModule, $user)) {
            if ($modifiedTime) {
                $date_var = date("Y-m-d H:i:s");
                $db->pquery('INSERT INTO vtiger_msexchange_sync (exchangemodule,user,synctime,lastsynctime) VALUES (?,?,?,?)', array($sourceModule, $user->id, $modifiedTime, $db->formatDate($date_var, true)));
            }
        } else {
            $db->pquery('UPDATE vtiger_msexchange_sync SET synctime = ?,lastsynctime = ? WHERE user=? AND exchangemodule=?', array($modifiedTime, date('Y-m-d H:i:s'), $user->id, $sourceModule));
        }
    }
    
    /**
     *  Gets the max Modified time of last sync records
     *  @param <sting> $sourceModule modulename to which sync time should return
     *  @return <date> max Modified time of last sync records OR <boolean> false when date not present
     */
    public static function getSyncTime($sourceModule, $user = false) {
        $db = PearDatabase::getInstance();
        if(!$user)
            $user = Users_Record_Model::getCurrentUserModel();
        $result = $db->pquery('SELECT synctime FROM vtiger_msexchange_sync WHERE user=? AND exchangemodule=?', array($user->id, $sourceModule));
        if ($result && $db->num_rows($result) > 0) {
            $row = $db->fetch_array($result);
            return $row['synctime'];
        } else {
            return false;
        }
    }
    
    public static function errorLog() {
        $i = 0;
        $debug = debug_backtrace();
        array_shift($debug);
        foreach ($debug as $value) {
            $error.= "\t#".$i++.'  File : '.$value['file'].' || Line : '.$value['line'].' || Class : '.$value['class'].' || Function : '.$value['function']."\n";
        }
        $fp = fopen('logs/exchangeErrorLog.txt','a+');
        fwrite($fp, "Debug traced ON ".date('Y-m-d H:i:s')."\n\n");
        fwrite($fp, $error);
        fwrite($fp, "\n\n");
        fclose($fp);
    }
    
    static function getFieldMappingDetails($user, $module) {
        
        $msExchangeFieldMapping = array();
        
        $userId = $user->getId();
        
        $db = PearDatabase::getInstance();
        
        $sql = 'SELECT * FROM ' . self::fieldmapping_table_name . ' WHERE userid = ? and module = ?';
        
        $result = $db->pquery($sql, array($userId, $module));
            
        $moduleDefaultMapping = self::$defaultMapping[$module];
            
        $moduleModel = Vtiger_Module_Model::getInstance($module);
            
        $moduleFields = $moduleModel->getFields();
            
        foreach($moduleDefaultMapping as $fieldName => $msExchangeLabel){
                
            $fieldModel = $moduleFields[$fieldName];
                
            $msExchangeFieldMapping[$fieldModel->getId()] = array(
                "CRM" => $fieldModel->get("name"),
                "MSExchange" => $msExchangeLabel
            );
        }
        
        if($db->num_rows($result)){
            
            $row = $db->fetchByAssoc($result);
            
            $msExchangeFieldMapping = $msExchangeFieldMapping + Zend_Json::decode(decode_html($row['field_mapping']));
        }
        return $msExchangeFieldMapping;
    }
    
    /**
     * Function to mask input text.
     */
    static function toProtectedText($text) {
        if (empty($text)) return $text;
        
        require_once 'include/utils/encryption.php';
        $encryption = new Encryption();
        return '$ve$'.$encryption->encrypt($text);
    }
    
    /*
     * Function to determine if text is masked.
     */
    static function isProtectedText($text) {
        return !empty($text) && (strpos($text, '$ve$') === 0);
    }
    
    /*
     * Function to unmask the text.
     */
    static function fromProtectedText($text) {
        if (static::isProtectedText($text)) {
            require_once 'include/utils/encryption.php';
            $encryption = new Encryption();
            return $encryption->decrypt(substr($text, 4));
        }
        return $text;
    }
    
    /**
     * Function to get the sync count based on Extension or Extension and Module
     * @global type $adb
     * @param type $pagingModel
     * @param type $extension
     * @param type $module
     * @return $syncCounts
     */
    static function getSyncCounts($pagingModel, $extension, $module = false) {
        global $adb;
        $tabid = getTabid($extension);
        $user = Users_Record_Model::getCurrentUserModel();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        
        $query = 'SELECT * FROM vtiger_wsapp_logs_basic WHERE extensiontabid=?';
        $params = array($tabid);
        if($module) {
            $query .= ' AND module=?';
            $params[] = $module;
        }
        
        $query .= ' AND userid=?';
        $params[] = $user->getId();
        
        $query .= " ORDER BY sync_datetime DESC LIMIT $startIndex,".($pageLimit+1);
        
        $result = $adb->pquery($query, $params);
        $syncCounts = array();
        
        if($adb->num_rows($result)) {
            for($i=0;$i<$adb->num_rows($result);$i++) {
                $syncCounts[] = $adb->query_result_rowdata($result, $i);
            }
        }
        
        return $syncCounts;
    }
    
    /**
    * Function get the total number of syncs
    * @param <string> $extension
    * @param <string> $module
    * @return <int> $syncCount
    */
    
    static function getTotalSyncCount($extension, $module = false) {
        global $adb;
        $user = Users_Record_Model::getCurrentUserModel();
        $tabid = getTabid($extension);
        
        $query = 'SELECT count(*) as count FROM vtiger_wsapp_logs_basic WHERE extensiontabid=?';
        $params = array($tabid);
        if($module) {
            $query .= ' AND module=?';
            $params[] = $module;
        }
        
        $query .= ' AND userid=?';
        $params[] = $user->getId();
        
        $result = $adb->pquery($query, $params);
        
        $syncCount = 0;
        if($adb->num_rows($result)) {
            $syncCount = $adb->query_result($result, 0, 'count');
        }
        
        return $syncCount;
    }
    
    public static function getSyncState($sourceModule, $user = false) {
        
        $db = PearDatabase::getInstance();
        
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        $sql = 'SELECT delete_sync_state FROM ' . self::settings_table_name . ' WHERE user = ? and module = ?';
        
        $result = $db->pquery($sql, array($user->id, $sourceModule));
        
        if ($result && $db->num_rows($result) > 0) {
            return $db->query_result($result, 0, 'delete_sync_state');
        } else {
            return false;
        }
    }
    
    function updateSyncState($syncState, $sourceModule, $user = false){
        
        $db = PearDatabase::getInstance();
        
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        $sql = 'update ' . self::settings_table_name . ' SET delete_sync_state = ? WHERE user = ? and module = ?';
        
        $result = $db->pquery($sql, array($syncState, $user->id, $sourceModule));
        
    }
    
    function getTaskSyncStartDate($user = false){
        
        if(!$user) $user = Users_Record_Model::getCurrentUserModel();
        
        $userId = $user->getId();
        
        if(!MSExchange_Utils_Helper::hasSettingsForUser($userId,'Task')) {
            return false;
        } else {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT sync_start_from FROM ' . self::settings_table_name . ' WHERE user = ? AND module = ?';
            $result = $db->pquery($sql, array($userId,'Task'));
            $syncStartDate = $db->query_result($result, 0, 'sync_start_from');
            if($syncStartDate)
                return $syncStartDate;
            else
                return false;
        }
    }
}
