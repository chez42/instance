<?php

class Transactions_YesterdayTransactions_Dashboard extends Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		global $adb;
		
		$viewer = $this->getViewer($request);
		
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		
		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $page);
		$pagingModel->set('limit', 10);

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$fieldModelList = $moduleModel->getFields();
		
		$headerFields = array('account_number' => "Account", 'trade_date' => "Trade Date", "net_amount" => "Amount", "transaction_activity" => "Type");
		
		foreach($headerFields as $fieldName => $fieldLabel){
			
			$fieldModel = $fieldModelList[$fieldName];
		
			if(!$fieldModel->getPermissions())
				unset($headerFields[$fieldName]);
			else {
				$fieldModel->set("field_label", $fieldLabel);
				$headerFields[$fieldName] = $fieldModel;
			}
		}
	   $trade_date= '';
	   if($request->get('trade_date')){
		  $trade_date = $request->get('trade_date');
		  $trade_date['start'] = getValidDBInsertDateValue($trade_date['start']);
		  $trade_date['end'] = getValidDBInsertDateValue($trade_date['end']);
		  $_SESSION['transactionWidgetId'] =$trade_date; 
	   }else if($_SESSION['transactionWidgetId']){
		    $trade_date = $_SESSION['transactionWidgetId'];
	   }
		
		
		$transactionactivity = $request->get('transaction_activity');
		
		$tab = $request->get('tab');
		
		$condition = $adb->pquery("SELECT * FROM vtiger_dashboard_widget_conditions
        WHERE user_id = ? AND link_id = ? AND tab_id = ?",array($currentUser->getId(), $linkId, $tab));
		
		if(empty($transactionactivity) ){
		    if($adb->num_rows($condition)){
		        $con = $adb->query_result($condition,0,'conditions');
		        if($con){
		            
		            $cond = json_decode(html_entity_decode($con),true);
		            if(!empty($cond)){
		                $transaction_activity = $cond['transaction_activity'];
		                $request->set('transaction_activity',$transaction_activity);
		                
		            }
		        }
		    }
		   
		}else if($transactionactivity ){
		    $data = array(
		        "transaction_activity"=>$transactionactivity,
		    );
		    if(!empty($data)){
		        if((isset($transactionactivity)) && $adb->num_rows($condition)){
		            
		            $adb->pquery("UPDATE vtiger_dashboard_widget_conditions SET conditions = ? WHERE user_id = ?
                    AND link_id = ? AND tab_id = ?",array(json_encode($data), $currentUser->getId(), $linkId, $tab));
		            
		        }else if(isset($transactionactivity)){
		            
		            $adb->pquery("INSERT INTO vtiger_dashboard_widget_conditions(user_id, link_id, tab_id, conditions)
                    VALUES (?, ?, ?, ?)",array($currentUser->getId(), $linkId, $tab, json_encode($data)));
		            
		        }
		    }
		}
		
		$transaction_activity = $request->get('transaction_activity');
		if(!$transaction_activity)
            $transaction_activity = array("Deposit of funds");
		
		$tradeDates = array();
		
		if(!$trade_date){
		    
		    if(date('l') == 'Monday'){
		        $tradeDates['start_date'] = date("Y-m-d", strtotime("previous friday"));
		    }else{
                $tradeDates['start_date'] = date("Y-m-d", strtotime("-1 day"));
		    }
		   
		    $tradeDates['end_date'] = date("Y-m-d");
		    
		    $seachParams = implode(",",array_map('getValidDisplayDate', $tradeDates));
		    
		} else {
		    
		    $trade_date = $trade_date;
		    
		    $tradeDates['start_date'] = getValidDBInsertDateValue($trade_date['start']);
		    
		    $tradeDates['end_date'] = getValidDBInsertDateValue($trade_date['end']);
		    
		    $seachParams = implode(",",array_map('getValidDisplayDate', $tradeDates));
		}
			
		$data = $moduleModel->getWidgetTransactions(array_keys($headerFields), $pagingModel, $tradeDates, $transaction_activity);

		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
        
		$viewer->assign('HEADERS', $headerFields);
        $viewer->assign('DATA', $data);
        
		$viewer->assign("CURRENT_USER_MODEL", $currentUser);
		$viewer->assign('PAGING', $pagingModel);
		
		$viewer->assign('MORE_LINK_URL', $moduleModel->getWidgetLinkURL($seachParams, $transaction_activity));
		$viewer->assign('TRADE_DATE', $tradeDates);
		$viewer->assign('ACTVITY',$transaction_activity);
		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/TransactionContent.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/Transaction.tpl', $moduleName);
		}
	}
	
}
