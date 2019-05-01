<?php

class Transactions_Field_Model extends Vtiger_Field_Model{

	/**
	 * Function to check whether field is ajax editable'
	 * @return <Boolean>
	 */
	public function isAjaxEditable() {
		if($this->getFieldName() == 'account_number')
			return false;
		return 	parent::isAjaxEditable();
	}
	
	/**
	 * Function to retieve display value for a value
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
	public function getDisplayValue($value, $record=false, $recordInstance = false) {
		    
		if($this->getFieldName() == 'account_number'){
			$record = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($value);
			if($record == 0)
                return parent::getDisplayValue($value, $record=false, $recordInstance = false);

			$result = "<a href='index.php?module=PortfolioInformation&view=Detail&record={$record}'>{$value}</a>";
			return $result;
			//Next 2 lines left uncommented, but they really slow down the loading of the related list page!
            $recordModel = Vtiger_Record_Model::getInstanceById($record, 'PortfolioInformation');
            $detailViewUrl = "<a href='".$recordModel->getDetailViewUrl()."'>".$value."<a>";
			return $detailViewUrl;
		} else
			return parent::getDisplayValue($value, $record=false, $recordInstance = false);
	}

}