<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Calendar Field Model Class
 */
class ModSecurities_Field_Model extends Vtiger_Field_Model {

    /**
     * Function returns special validator for fields
     * @return <Array>
     */
/*	function getValidator() {
            $validator = array();
            $fieldName = $this->getName();

            switch($fieldName) {
                    case 'due_date':	$funcName = array('name' => 'greaterThanDependentField',
                                                                                            'params' => array('date_start'));
                                                            array_push($validator, $funcName);
                                                            break;
                    case 'eventstatus':	$funcName = array('name' => 'futureEventCannotBeHeld',
                                                                                            'params' => array('date_start'));
                                                            array_push($validator, $funcName);
                                                            break;
                    default : $validator = parent::getValidator();
                                            break;
            }
            return $validator;
    }
*/
    /**
     * Function to get the Webservice Field data type
     * @return <String> Data type of the field
    */
    public function getFieldDataType() {
        if($this->getName() == "asset_class"){
            return "AssetClass";
        }
        if($this->getName() == "sector"){
            return "Sector";
        }
        if($this->getName() == "pay_frequency"){
            return "PayFrequency";
        }
        if($this->getName() == "security_type"){
            return "SecurityType";
        }
        $webserviceField = $this->getWebserviceFieldObject();
        return $webserviceField->getFieldDataType();
    }

    public function getFieldInfo() {
        parent::getFieldInfo();
        if($fieldDataType == 'AssetClass' || $fieldDataType == 'Sector' || $fieldDataType == 'PayFrequency' || $fieldDataType == "SecurityType") {
            $pickListValues = $this->getPicklistValues();
            if(!empty($pickListValues)) {
                $this->fieldInfo['picklistvalues'] = $pickListValues;
            }
        }
        return $this->fieldInfo;
    }

    public function getPicklistValues() {
    $fieldDataType = $this->getFieldDataType();
            if($this->getName() == 'hdnTaxType') return null;
/*    if($fieldDataType == 'AssetClass')
        $data_type = 20;
    if($fieldDataType == 'Sector')
        $data_type = 10;
    */
    if($fieldDataType == 'PayFrequency') {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            if($this->isRoleBased() && !$currentUser->isAdminUser()) {
                $userModel = Users_Record_Model::getCurrentUserModel();
                $picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($this->getName(), $userModel->get('roleid'));
            }else{
                if($fieldDataType == 'PayFrequency')
                    $picklistValues = self::GetPaymentFrequencyPicklistValues($this->getName());
                else
                    $picklistValues = parent::getPicklistValues();
/*                if($fieldDataType == 'SecurityType')
                    $picklistValues = self::GetSecurityType($this->getName());
                else
                    $picklistValues = self::GetAssetClassPicklistValues($this->getName(), $data_type);
*/
            }
            foreach($picklistValues as $value) {
                    $fieldPickListValues[$value] = vtranslate($value,$this->getModuleName());
            }
            return $fieldPickListValues;
        }else
            return parent::getPicklistValues();
    }

    /**
     * Overriding in here rather than the vtiger_util_helper function
     * @param type $fieldName
     * @return type
     */
    public static function GetPaymentFrequencyPicklistValues($fieldName) {
        $cache = Vtiger_Cache::getInstance();
        if($cache->getPicklistValues($fieldName)) {
            return $cache->getPicklistValues($fieldName);
        }
        $db = PearDatabase::getInstance();

        $query = "SELECT frequency_type_name FROM vtiger_pc_frequency_types";
        $values = array();
        $result = $db->pquery($query, array());
        $num_rows = $db->num_rows($result);
        for($i=0; $i<$num_rows; $i++) {
			//Need to decode the picklist values twice which are saved from old ui
            $values[] = decode_html(decode_html($db->query_result($result,$i,'frequency_type_name')));
        }
        $cache->setPicklistValues('frequency_type_name', $values);
        return $values;
    }
    
    /**
     * Overriding in here rather than the vtiger_util_helper function
     * @param type $fieldName
     * @return type
     */
    public static function GetSecurityType($fieldName){
        $cache = Vtiger_Cache::getInstance();
        if($cache->getPicklistValues($fieldName)) {
            return $cache->getPicklistValues($fieldName);
        }
        $db = PearDatabase::getInstance();

        $query = "SELECT security_type_name FROM vtiger_security_types";
        $values = array();
        $result = $db->pquery($query, array());
        $num_rows = $db->num_rows($result);
        for($i=0; $i<$num_rows; $i++) {
			//Need to decode the picklist values twice which are saved from old ui
            $values[] = decode_html(decode_html($db->query_result($result,$i,'security_type_name')));
        }
        $cache->setPicklistValues('security_type_name', $values);
        return $values;
    }
    
    /**
     * Overriding in here rather than the vtiger_util_helper function
     * @param type $fieldName
     * @return type
     */
    public static function GetAssetClassPicklistValues($fieldName, $data_type) {
        $cache = Vtiger_Cache::getInstance();
        if($cache->getPicklistValues($fieldName)) {
            return $cache->getPicklistValues($fieldName);
        }
        $db = PearDatabase::getInstance();

//        $query = 'SELECT '.$fieldName.' FROM vtiger_'.$fieldName.' order by sortorderid';
        $query = "SELECT code_description FROM vtiger_pc_codes WHERE data_set_id IN (1,28) AND code_type_id = ?;";
        $values = array();
        $result = $db->pquery($query, array($data_type));
        $num_rows = $db->num_rows($result);
        for($i=0; $i<$num_rows; $i++) {
			//Need to decode the picklist values twice which are saved from old ui
            $values[] = decode_html(decode_html($db->query_result($result,$i,'code_description')));
        }
        $cache->setPicklistValues('code_description', $values);
        return $values;
    }
    
    /**
     * Customize the display value for detail view.
     */
    public function getDisplayValue($value, $record = false, $recordInstance = false) {
        if ($recordInstance) {
                if ($this->getName() == 'date_start') {
                        $dateTimeValue = $value . ' '. $recordInstance->get('time_start');
                        $value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
                        list($startDate, $startTime) = explode(' ', $value);

                        $currentUser = Users_Record_Model::getCurrentUserModel();
                        if($currentUser->get('hour_format') == '12')
                                $startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);

                        return $startDate . ' ' . $startTime;
                } else if ($this->getName() == 'due_date') {
                        $dateTimeValue = $value . ' '. $recordInstance->get('time_end');
                        $value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
                        list($startDate, $startTime) = explode(' ', $value);

                        $currentUser = Users_Record_Model::getCurrentUserModel();
                        if($currentUser->get('hour_format') == '12')
                                $startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);

                        return $startDate . ' ' . $startTime;
                }
        }
        return parent::getDisplayValue($value, $record, $recordInstance);
    }

    /**
     * Function to get Edit view display value
     * @param <String> Data base value
     * @return <String> value
     */
    public function getEditViewDisplayValue($value) {
            $fieldName = $this->getName();

            if ($fieldName == 'time_start' || $fieldName == 'time_end') {
                    return $this->getUITypeModel()->getDisplayTimeDifferenceValue($fieldName, $value);
            }

            //Set the start date and end date
            if(empty($value)) {
                    if ($fieldName === 'date_start') {
                            return DateTimeField::convertToUserFormat(date('Y-m-d'));
                    } elseif ($fieldName === 'due_date') {
                            $currentUser = Users_Record_Model::getCurrentUserModel();
                            $minutes = $currentUser->get('callduration');
                            return DateTimeField::convertToUserFormat(date('Y-m-d', strtotime("+$minutes minutes")));
                    }
            }
            return parent::getEditViewDisplayValue($value);
    }
}
