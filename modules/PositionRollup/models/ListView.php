<?php

class PositionRollup_ListView_Model extends Vtiger_ListView_Model{

    function getListViewCount() {
		$db = PearDatabase::getInstance();

		$queryGenerator = $this->get('query_generator');

        $searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');
		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}

		$listQuery = $this->getQuery();


		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)) {
			$moduleModel = $this->getModule();
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}
		$position = stripos($listQuery, ' from ');
		if ($position) {
			$split = spliti(' from ', $listQuery);
			$splitCount = count($split);
			$listQuery = 'SELECT count(*) AS count ';
			for ($i=1; $i<$splitCount; $i++) {
				$listQuery = $listQuery. ' FROM ' .$split[$i];
			}
		}

		if($this->getModule()->get('name') == 'Calendar'){
			$listQuery .= ' AND activitytype <> "Emails"';
		}
                $listQuery = str_replace("count(*)", "count(distinct (security_symbol))", $listQuery);
		$listResult = $db->pquery($listQuery, array());
                $num_rows = $db->num_rows($listResult);
                return $num_rows;
		return $db->query_result($listResult, 0, 'count');
//        parent::getListViewCount();
    }
/*    function getListViewCount() {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        global $current_user;
        
        if($currentUserModel->isAdminUser()){
            $symbol_questions = generateQuestionMarks($symbols);

            $query = "SELECT COUNT(DISTINCT (security_symbol)) AS num_results
                      FROM vtiger_positioninformation 
                      INNER JOIN vtiger_crmentity ON vtiger_positioninformation.positioninformationid = vtiger_crmentity.crmid 
                      WHERE vtiger_crmentity.deleted=0 AND vtiger_positioninformation.positioninformationid > 0 ";
            
            $result = $db->pquery($query, array());
            if(is_object($result))
                $num = $db->query_result($result, 0, "num_results");
            
            return $num;
        }
        else{
            require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
            
            foreach($PositionInformation_share_read_permission['GROUP'] AS $groups => $users){
                foreach($users AS $k => $v)
                    $related_ids[] = $v;
                $related_ids[] = $groups;
            }
            $related_ids[] = $current_user->id;
            $questions = generateQuestionMarks($related_ids);
            $query = "SELECT COUNT(DISTINCT (security_symbol)) AS num_results
                      FROM vtiger_positioninformation 
                      INNER JOIN vtiger_crmentity ON vtiger_positioninformation.positioninformationid = vtiger_crmentity.crmid 
                      WHERE vtiger_crmentity.deleted=0 AND vtiger_positioninformation.positioninformationid > 0 AND vtiger_crmentity.smownerid IN ({$questions})";

            $result = $db->pquery($query, array($related_ids));
            if(is_object($result))
                $num = $db->query_result($result, 0, "num_results");
            
            return $num;
        }
        
        return 1;

        return parent::getListViewCount();
    }*/
    
    function getQuery() {
        $query = parent::getQuery();
        $query = preg_replace("/INNER JOIN vtiger_positionrollup ON vtiger_positioninformation.positioninformationid = vtiger_positionrollup./", "INNER JOIN vtiger_positioninformationcf ON vtiger_positioninformation.positioninformationid = vtiger_positioninformationcf.positioninformationid", $query);
        $query = preg_replace("/vtiger_positionrollup/", "vtiger_positioninformation", $query);
        $query = preg_replace("/vtiger_positioninformation.current_value/", "SUM(vtiger_positioninformation.current_value) AS current_value", $query);
//        $query = str_replace("vtiger_positioninformation.quantity", "SUM(vtiger_positioninformation.quantity) AS quantity", $query);
        $query = preg_replace("/vtiger_positioninformation.quantity/", "SUM(vtiger_positioninformation.quantity) AS quantity", $query, 1);
        $query = preg_replace("/vtiger_positioninformation.asset_class/", "asset_class", $query);
        $query = preg_replace("/vtiger_positioninformation.security_type/", "security_type", $query);
        $query .= " GROUP BY security_symbol";
        
        return $query;
    }
    
}

?>
