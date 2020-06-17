<?php

class Settings_GlobalSearch_Record_Model extends Settings_Vtiger_Record_Model{
    
	const tableName = 'vtiger_globalsearch';
    
    public function getId() {
        return $this->get('globalsearchid');
    }
    
    public function getName() {
        return $this->get('modulename');
    }
    
    public function save() {
        $db = PearDatabase::getInstance();
        $id = $this->getId();
        $tableName = Settings_GlobalSearch_Record_Model::tableName;
        if(!empty($id)) {
            $query = 'UPDATE '.$tableName.' SET modulename = ?, fieldnames = ?, allow_global_search = ? WHERE globalsearchid = ?' ;
            $params = array(
                $this->get('modulename'), $this->get('fieldnames'),
                $this->get('allow_global_search'), $id
            );
        }else {
            $id = $db->getUniqueID($tableName);
            $query = 'INSERT INTO '. $tableName .' SET globalsearchid = ?, modulename = ?, fieldnames = ?, allow_global_search = ? ';
            $params = array(
                $id , $this->get('modulename'), $this->get('fieldnames'), $this->get('allow_global_search')
            );
        }
        $db->pquery($query,$params);
        return $id;
    }

    public static function getInstance($id) {
    	
    	    
        $db = PearDatabase::getInstance();
        $tableName = Settings_GlobalSearch_Record_Model::tableName;
        
        if(Vtiger_Utils::isNumber($id)){
            $query = 'SELECT * FROM ' . $tableName . ' WHERE globalsearchid = ?';
        }else{
            $query = 'SELECT * FROM ' . $tableName . ' WHERE modulename = ?';
        }
        
        $result = $db->pquery($query, array($id));
        
        if($db->num_rows($result) > 0) {
            $row = $db->query_result_rowdata($result,0);
            $instance = new self();
            $instance->setData($row);
        }
        return $instance;
    }
    
    public function getSelectedFields($modulename = ''){
    	
    	$selectedFields = array();
    	
    	$db = PearDatabase::getInstance();
        
    	$tableName = Settings_GlobalSearch_Record_Model::tableName;
        
        if( $modulename != ''){        	
       
            $query = 'SELECT fieldnames FROM ' . $tableName . ' WHERE modulename = ?';
        	
        	$result = $db->pquery($query, array($modulename));
        	
        	if($db->num_rows($result) > 0) {
	            $row = $db->query_result_rowdata($result,0);
	            $selectedFields = explode(',', $row['fieldnames']);
	        }
        }
        return $selectedFields;
    }
    
}