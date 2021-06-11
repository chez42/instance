<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Billing_ListAjax_View extends Vtiger_ListAjax_View {
	
	/**
	 * Function to get listView count
	 * @param Vtiger_Request $request
	 */
	function getListViewCount(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$cvId = $request->get('viewname');
		$billing_period = $request->get("billing_period_range");
		
		if(empty($cvId)) {
			$cvId = '0';
		}

		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		$tagParams = $request->get('tag_params');

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);

		if(empty($tagParams)){
			$tagParams = array();
		}

		$searchParams = $request->get('search_params');
		if(empty($searchParams) && !is_array($searchParams)){
			$searchParams = array();
		}
		
		if($billing_period){
            $searchParams[0][] = array("start_date", 'bw', $billing_period);
        }
	
		$searchAndTagParams = array_merge($searchParams, $tagParams);

		$listViewModel->set('search_params',$this->transferListSearchParamsToFilterCondition($searchAndTagParams, $listViewModel->getModule()));

		$listViewModel->set('search_key', $searchKey);
		$listViewModel->set('search_value', $searchValue);
		$listViewModel->set('operator', $request->get('operator'));

		// for Documents folders we should filter with folder id as well
		$folder_value = $request->get('folder_value');
		if(!empty($folder_value)){
			$listViewModel->set('folder_id',$request->get('folder_id'));
			$listViewModel->set('folder_value',$folder_value);
		}

		$count = $listViewModel->getListViewCount();

		return $count;
	}

	

	
}