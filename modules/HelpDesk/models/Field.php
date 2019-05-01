<?php
/*28-Aug-2018*/
class HelpDesk_Field_Model extends Vtiger_Field_Model {

	/**
	 * Function to retieve display value for a value
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
	public function getDisplayValue($value, $record=false, $recordInstance = false) {

		if ($recordInstance) {

			if ($this->getName() == 'view_permission') {

				if(strpos($value, " |##| ") !== false)
					$value = explode(" |##| ", $value);
					
				if($value && !is_array($value))
					$value = array($value);
					
		        if($value == NULL && !is_array($value)) return;
		         
		        $currentUser = Users_Record_Model::getCurrentUserModel();
               
		        foreach($value as $val){
		            if (Vtiger_Multiowner_UIType::getOwnerType($val) === 'User') {
		                $userModel = Users_Record_Model::getCleanInstance('Users');
		                $userModel->set('id', $val);
		                $detailViewUrl = $userModel->getDetailViewUrl();
		            } else {
		                $recordModel = new Settings_Groups_Record_Model();
		                $recordModel->set('groupid',$val);
		                $detailViewUrl = $recordModel->getDetailViewUrl();
		            }
		            if(!$currentUser->isAdminUser())
               			$displayvalue[] = getOwnerName($val)."&nbsp";
               		else
               			$displayvalue[] = "<a href=" .$detailViewUrl. ">" .getOwnerName($val). "</a>&nbsp";
               	}
		        $displayvalue = implode(',',$displayvalue);
		        return $displayvalue;
			}
		}
		return parent::getDisplayValue($value, $record, $recordInstance);
	}
	
	
	public function getFieldDataType() {
	    
	    $uiType = $this->get('uitype');
	   
	    if($uiType == '54' ) {
	        return 'multiowner';
	    } 
	    
	    return parent::getFieldDataType();
	   
	}
	
	public function isAjaxEditable() {
	    if($this->getFieldName() == 'view_permission')
	        return false;
	        return 	parent::isAjaxEditable();
	}
	
}