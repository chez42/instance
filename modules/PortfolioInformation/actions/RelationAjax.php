<?php

class PortfolioInformation_RelationAjax_Action extends Vtiger_RelationAjax_Action {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addRelation');
		$this->exposeMethod('deleteRelation');
		$this->exposeMethod('getRelatedListPageCount');
	}

	public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel) {
        return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
    }

}