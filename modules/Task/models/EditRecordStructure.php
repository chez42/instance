<?php

class Task_EditRecordStructure_Model extends Vtiger_EditRecordStructure_Model {

	/**
	 * Function to get the values in stuctured format
	 * @return <array> - values in structure array('block'=>array(fieldinfo));
	 */
	public function getStructure() {
		if(!empty($this->structuredValues)) {
			return $this->structuredValues;
		}
        
		$values = array();
		$recordModel = $this->getRecord();
		$recordExists = !empty($recordModel);
		$moduleModel = $this->getModule();
		$blockModelList = $moduleModel->getBlocks();
                
		foreach($blockModelList as $blockLabel=>$blockModel) {
			$fieldModelList = $blockModel->getFields();
			if (!empty ($fieldModelList)) {
				$values[$blockLabel] = array();
				foreach($fieldModelList as $fieldName=>$fieldModel) {
					if($fieldModel->isEditable()) {
						if($recordExists) {
							$fieldValue = $recordModel->get($fieldName);
							if($fieldName == 'date_start') {
								$fieldValue = $fieldValue.' '.$recordModel->get('time_start');
							}  else {
								$defaultValue = $fieldModel->getDefaultFieldValue();
								if(!empty($defaultValue) && !$recordId)
									$fieldValue = $defaultValue;
							}
							$fieldModel->set('fieldvalue', $fieldValue);
						}
						$values[$blockLabel][$fieldName] = $fieldModel;
					}
				}
			}
		}
		$this->structuredValues = $values;
		return $values;
	}
}