<?php

class Omniscient_WorkflowParsing_Model extends Vtiger_Module_Model{
    public function ParseFunction($data, $function_name){
        switch($function_name){
            case 'advisor_action_start':
                return $this->AdvisorActionStart($data);
                break;
            case 'advisor_action_stop':
                return $this->AdvisorActionStop($data);
                break;
            case 'show_changes':
                return $this->ShowChanges($data);
                break;
            default:
                return 'continue';
        }
    }
    
    public function AdvisorActionStart($data){
        if($data['cf_711'] == 1){
            return '<div style="background-color:red;">';
        }
        return '';
    }
    
    public function AdvisorActionStop($data){
        if($data['cf_711'] == 1){
            return '</div>';
        }
        return '';
    }
    
    public function ShowChanges($data){
        global $adb;
        $tmp = explode('x', $data['id']);
        $entity_id = $tmp[1];
        $changes = self::GetChanges($entity_id);        
        foreach($changes AS $k => $v){
            $ch[] = $k;//set the temporary variable to hold the field_name
        }
        $questions = generateQuestionMarks($ch);
        $query = "SELECT fieldlabel 
                  FROM vtiger_field 
                  WHERE fieldname IN ({$questions})
                  AND tabid=13
                  AND fieldname NOT IN ('modifiedtime', 'cf_788')";
        $result = $adb->pquery($query, array($ch));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $fields[] = $v['fieldlabel'];
            }
        }
        
        $text = '';
        if(sizeof($fields) > 0){
            $text = 'The following fields have been modified: ';
            $i = 0;
            $len = count($fields);
            foreach($fields AS $k => $v){
                if($i == $len - 1){
                    $text .= $v;
                }
                else{
                    $text .= $v . ', ';
                }
                $i++;
            }
        }
        return $text;
    }
    
    static public function CompareData($entityData){
        global $adb;

        $entity_id = explode('x', $entityData->getId());
        $new_data = $entityData->getData();
        $old_data = self::GetLastSaveData($entity_id[1]);

        if($old_data != 0){//There actually is old data
            $query = "DELETE changes FROM vtiger_omniscient_saved_helpdesk WHERE entity_id = ?";
            $adb->pquery($query, array($entity_id[1]));
            self::SaveLastSaveData($entityData);//Save the new data
            $changed = self::arrayRecursiveDiff($new_data, $old_data);
            $data = base64_encode(serialize($changed));            
            $query = "INSERT INTO vtiger_omniscient_saved_helpdesk (entity_id, changes) 
                      VALUES (?, ?)
                      ON DUPLICATE KEY UPDATE entity_id=VALUES(entity_id), changes=VALUES(changes)";
            $adb->pquery($query, array($entity_id[1], $data));
        }
        else{
            self::SaveLastSaveData($entityData);
            return 0;
        }
    }
    
    static public function GetChanges($entity_id){
        global $adb;
        $query = "SELECT changes FROM vtiger_omniscient_saved_helpdesk WHERE entity_id = ?";
        $result = $adb->pquery($query, array($entity_id));
        if($adb->num_rows($result) > 0){
            $str = $adb->query_result($result, 0, 'changes');
            $data = unserialize(base64_decode($str));
            return $data;
        }
        return 0;
    }
    
    static public function GetLastSaveData($entity_id){
        global $adb;        
        $query = "SELECT data FROM vtiger_omniscient_saved_helpdesk WHERE entity_id = ?";
        $result = $adb->pquery($query, array($entity_id));
        if($adb->num_rows($result) > 0){
            $str = $adb->query_result($result, 0, 'data');
            $data = unserialize(base64_decode($str));
            return $data;
        }
        return 0;
    }
    
    static public function SaveLastSaveData($entityData){
        global $adb;
        $entity_id = explode('x', $entityData->getId());
        $data = $entityData->getData();
        $data = base64_encode(serialize($data));
        
        $query = "INSERT INTO vtiger_omniscient_saved_helpdesk (entity_id, data) 
                  VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE entity_id=VALUES(entity_id), data=VALUES(data)";
        $adb->pquery($query, array($entity_id[1], $data));
    }
    
    public function arrayRecursiveDiff($aArray1, $aArray2) { 
        $aReturn = array(); 

        foreach ($aArray1 as $mKey => $mValue) { 
            if (array_key_exists($mKey, $aArray2)) { 
                if (is_array($mValue)) { 
                    $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]); 
                    if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; } 
                } else { 
                    if ($mValue != $aArray2[$mKey]) { 
                        $aReturn[$mKey] = $mValue; 
                    } 
                } 
            } else { 
                $aReturn[$mKey] = $mValue; 
            } 
        } 

        return $aReturn; 
    } 
}

?>