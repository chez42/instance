<?php

class PositionInformation_PageSummary_Model extends Vtiger_Module_Model{

    public function GetSummaryFromSymbols($symbols){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        global $current_user;
        
        if($currentUserModel->isAdminUser()){
            $symbol_questions = generateQuestionMarks($symbols);
            $query = "SELECT SUM(total_value) AS total_value, SUM(market_value) AS market_value, SUM(cash_value) AS cash_value, SUM(annual_management_fee) AS annual_management_fee 
                      FROM vtiger_positioninformation vpi
                      LEFT JOIN vtiger_crmentity e ON e.crmid = vpi.positioninformationid 
                      WHERE security_symbol IN ({$symbol_questions})";
            $result = $db->pquery($query, array($related_ids, $symbols));
        }
        else
        {
            require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

            foreach($PositionInformation_share_read_permission['GROUP'] AS $groups => $users){
                foreach($users AS $k => $v)
                    $related_ids[] = $v;
                $related_ids[] = $groups;
            }
            $questions = generateQuestionMarks($related_ids);
            $symbol_questions = generateQuestionMarks($symbols);
            $query = "SELECT SUM(total_value) AS total_value, SUM(market_value) AS market_value, SUM(cash_value) AS cash_value, SUM(annual_management_fee) AS annual_management_fee 
                      FROM vtiger_positioninformation vpi
                      LEFT JOIN vtiger_crmentity e ON e.crmid = vpi.positioninformationid 
                      WHERE e.smownerid IN ({$questions}) AND security_symbol IN ({$symbol_questions})";

            $result = $db->pquery($query, array($related_ids, $symbols));
        }
        
        if (is_object($result))
            foreach($result AS $k => $v){
                $values['total_value'] = $v['total_value'];
                $values['market_value'] = $v['market_value'];
                $values['cash_value'] = $v['cash_value'];
                $values['annual_management_fee'] = $v['annual_management_fee'];
            }
        
        return $values;        
        
    }
    
    static public function GetTotalsFromListViewID($id){
        global $adb;
        $listViewModel = Vtiger_ListView_Model::getInstance("PortfolioInformation", $id);
        $generator = $listViewModel->get('query_generator');
        $query = $generator->getQuery();
        $query = strstr($query, 'FROM');
        $query = " SELECT SUM(total_value) as total_value, SUM(market_value) AS market_value, SUM(annual_management_fee) as annual_management_fee, SUM(equities) as equities, 
                          SUM(fixed_income) as fixed_income, SUM(cash_value) as cash_value " . $query;
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $values['total_value'] = money_format('%.0n',$v['total_value']);
                $values['market_value'] = money_format('%.0n',$v['market_value']);
                $values['cash_value'] = money_format('%.0n',$v['cash_value']);
                $values['annual_management_fee'] = money_format('%.0n',abs($v['annual_management_fee']));
            }
            return $values;
        }
        return 0;
    }
        /**
         * Get non admin summary values
         * @global type $current_user
         * @param Vtiger_Request $request
         * @return type
         */
        public function getNonAdminSummaryValues(Vtiger_Request $request, PositionInformation_List_View $viewer){
            $moduleName = $request->getModule();
            $moduleModel = PositionInformation_Module_Model::getInstance($moduleName);                        
//            $this->            
            $moduleFocus = CRMEntity::getInstance($moduleName);
            $m = PositionInformation_ListView_Model::getInstance($moduleName);
            $listViewContoller = $m->get('listview_controller');
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', 1);
            $pagingModel->set('limit', 20);
            
            $listViewEntries = $moduleModel->getListViewName();
////            $rec = PositionInformation_Record_Model::getInstanceById(160644);
//            $rec = new PositionInformation_Record_Model();
            
//            echo "R: {$r}<br />";
//            print_r($r);exit;
//            exit;
//            $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);
            
/*

            global $current_user;
            $db = PearDatabase::getInstance();
            
            $moduleName = $request->getModule();
            $m = PositionInformation_ListView_Model::getInstance($moduleName);
            $query = $m->get('query_generator');
            $page = $request->get('page');
            $start = $page * 20;
            $end = $start + 20;
            $limit = " LIMIT {$start}, {$end} ";
            $result = $db->pquery($query->getQuery() . $limit, array());
            if(is_object($result))
                foreach($result AS $k => $v){
                    print_r($v);
                }
//            $listViewContoller = $m->get('listview_controller');
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', 1);
            $pagingModel->set('limit', 10);

            foreach($e as $recordId => $record) {
                    $rawData = $db->query_result_rowdata($result, $index++);
                    $record['id'] = $recordId;
                    $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
            }            
            $count = $m->getListViewCount();
//            $m->getListView

//            $d = $m->
//            $e = $m->getListViewEntries($pagingModel);
//            $count = $m->getListViewEntries($pagingModel);
            
            
            require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
            
            foreach($PortfolioInformation_share_read_permission['GROUP'] AS $groups => $users){
                foreach($users AS $k => $v)
                    $related_ids[] = $v;
                $related_ids[] = $groups;
            }
            $questions = generateQuestionMarks($related_ids);
            $query = "SELECT SUM(total_value) AS total_value, SUM(market_value) AS market_value, SUM(cash_value) AS cash_value, SUM(annual_management_fee) AS annual_management_fee 
                      FROM vtiger_portfolioinformation vpi
                      LEFT JOIN vtiger_crmentity e ON e.crmid = vpi.portfolioinformationid 
                      WHERE e.smownerid IN ({$questions})";
            echo $query;
            $result = $db->pquery($query, array($related_ids));
            if (is_object($result))
                foreach($result AS $k => $v){
                    $values['total_value'] = $v['total_value'];
                    $values['market_value'] = $v['market_value'];
                    $values['cash_value'] = $v['cash_value'];
                    $values['annual_management_fee'] = $v['annual_management_fee'];
                }*/
                
            return $values;
        }
}

?>
