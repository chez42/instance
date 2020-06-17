<?php

class Omniscient_Restrictions_Model extends Vtiger_Module_Model {
    public function __construct() {
        parent::__construct();
    }
    
    static public function GetUserRestrictions($moduleName){
        global $adb;
        $currentUser = vglobal('current_user');
        $db = PearDatabase::getInstance();
        $q = "SELECT restricted_user_id FROM vtiger_portfolioinformation_restrictions WHERE user_id = ? AND module = ?";
        $result = $db->pquery($q, array($currentUser->id, $moduleName));
        if($adb->num_rows($result) > 0){
            if($moduleName == "HelpDesk"){
                $sharing = new HelpDesk_Sharing_Model();
                $tickets = $sharing->GetSharedTickets();
                if(is_array($tickets) && sizeof($tickets) > 0){
                    $tickets = SeparateArrayWithCommas($tickets);
//                    $or = " OR vtiger_troubletickets.ticketid IN ({$tickets}) ";
                }
                $and = self::GetDeletedRestrictions($moduleName);
            }

            if($db->num_rows($result) > 0){
                foreach($result AS $k=>$v){
                    $cancelled .= $v['restricted_user_id'] . ', ';
                }
                $cancelled = rtrim($cancelled, ', ');
                return " {$and} AND vtiger_crmentity.smownerid NOT IN ({$cancelled}) {$or}";
            }
        }
        return '';
    }
    
    static public function GetRestrictUserIDs($moduleName){
        global $adb;
        $currentUser = vglobal('current_user');
        $db = PearDatabase::getInstance();
        $q = "SELECT restricted_user_id FROM vtiger_portfolioinformation_restrictions WHERE user_id = ? AND module = ?";
        $result = $db->pquery($q, array($currentUser->id, $moduleName));
        
        if($db->num_rows($result) > 0){
            foreach($result AS $k=>$v){
                $ids[] = $v['restricted_user_id'];
            }
            return $ids;
        }
        else
            return 0;
    }
    
    /**
     * GetDeletedRestrictions goes with the query generator getWhereClause and injects itself immediately after that.  
     * In a query, this is about "WHERE vtiger_crmentity.deleted = 0 AND "  <-- special instructions to go here
     * @param type $moduleName
     */
    static public function GetDeletedRestrictions($moduleName){
        switch($moduleName){
            case "HelpDesk":
                $restricted_users = self::GetRestrictUserIDs($moduleName);
                if($restricted_users){
                    $ignore_entities = self::GetFullIgnoreEntities($restricted_users);
                    if($ignore_entities){
                        $ids = SeparateArrayWithCommas($ignore_entities);
                        return " AND vtiger_troubletickets.parent_id NOT IN ({$ids}) ";
                    }
                }
            break;
        }
        return '';
    }
    
    /**
     * Get CRMID list for all owners in the owner_ids list passed in
     * @global type $adb
     * @param array $owner_ids
     * @return int
     */
    static public function GetFullIgnoreEntities(array $owner_ids){
        global $adb;
        $questions = generateQuestionMarks($owner_ids);
        $query = "SELECT crmid FROM vtiger_crmentity WHERE smownerid IN ({$questions})";
        $result = $adb->pquery($query, array($owner_ids));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $ids[] = $v['crmid'];
            }
            return $ids;
        }
        return 0;
    }
    
    public function IsEnabled($record){
        global $adb;
        $query = "SELECT exchange_enabled FROM vtiger_users WHERE id = ?";
        $result = $adb->pquery($query, array($record));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'exchange_enabled');
        }
        return 0;
    }
    
    
    //type, state, date, 
}

?>