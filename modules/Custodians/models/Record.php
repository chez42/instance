<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Custodians_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function to get the instance of Roles record model from query result
	 * @param <Object> $result
	 * @param <Number> $rowNo
	 * @return Custodians_Record_Model instance
	 */
	public static function getInstanceFromQResult($result, $rowNo) {
		$db = PearDatabase::getInstance();
		$row = $db->query_result_rowdata($result, $rowNo);
		$custodian = new self();
		return $custodian->setData($row);
	}
        
        /**
         * Return all custodians
         */
        public static function getAllCustodians(){
            $db = PearDatabase::getInstance();
            $params = array();

            $query = 'SELECT * FROM vtiger_custodians c
                    LEFT JOIN vtiger_custodianscf ccf ON ccf.custodiansid = c.custodiansid
                    LEFT JOIN vtiger_crmentity e ON e.crmid = c.custodiansid
                    WHERE e.deleted = 0';

            $result = $db->pquery($query, $params);
            $noOfCustodians = $db->num_rows($result);

            $custodians = array();
            for ($i=0; $i<$noOfCustodians; ++$i) {
                $custodian = self::getInstanceFromQResult($result, $i);
                $data = $custodian->getData();
                $custodians[$data['custodiansid']] = $data;
            }
            
            return $custodians;
        }
        
        public static function getCustodianInfoFromFax($fax_number){
            $db = PearDatabase::getInstance();
            $params = array($fax_number);

            $query = "SELECT * FROM vtiger_custodians c
                      LEFT JOIN vtiger_custodianscf cf ON c.custodiansid = cf.custodiansid
                      WHERE c.fax_number = ?";
            $result = $db->pquery($query, $params);
            $noOfCustodians = $db->num_rows($result);

            $custodians = array();
            for ($i=0; $i<$noOfCustodians; ++$i) {
                $custodian = self::getInstanceFromQResult($result, $i);
                $data = $custodian->getData();
                $custodians[$data['custodiansid']] = $data;
            }
            
            return $custodians;
        }
        
	/**
	 * Function returns the details of Accounts Hierarchy
	 * @return <Array>
	 */
	function getAccountHierarchy() {
		$focus = CRMEntity::getInstance($this->getModuleName());
		$hierarchy = $focus->getAccountHierarchy($this->getId());
		$i=0;
		foreach($hierarchy['entries'] as $accountId => $accountInfo) {
			preg_match('/<a href="+/', $accountInfo[0], $matches);
			if($matches != null) {
				preg_match('/[.\s]+/', $accountInfo[0], $dashes);
				preg_match("/<a(.*)>(.*)<\/a>/i",$accountInfo[0], $name);

				$recordModel = Vtiger_Record_Model::getCleanInstance('Custodians');
				$recordModel->setId($accountId);
				$hierarchy['entries'][$accountId][0] = $dashes[0]."<a href=".$recordModel->getDetailViewUrl().">".$name[2]."</a>";
			}
		}
		return $hierarchy;
	}

	/**
	 * Function returns the url for create event
	 * @return <String>
	 */
	function getCreateEventUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
	}

	/**
	 * Function returns the url for create todo
	 * @retun <String>
	 */
	function getCreateTaskUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
	}

	/**
	 * Function to check duplicate exists or not
	 * @return <boolean>
	 */
	public function checkDuplicate() {
		$db = PearDatabase::getInstance();

		$query = "SELECT 1 FROM vtiger_crmentity WHERE setype = ? AND label = ? AND deleted = 0";
		$params = array($this->getModule()->getName(), decode_html($this->getName()));

		$record = $this->getId();
		if ($record) {
			$query .= " AND crmid != ?";
			array_push($params, $record);
		}

		$result = $db->pquery($query, $params);
		if ($db->num_rows($result)) {
			return true;
		}
		return false;
	}

	/**
	 * Function to get List of Fields which are related from Accounts to Inventory Record.
	 * @return <array>
	 */
	public function getInventoryMappingFields() {
		return array(
				//Billing Address Fields
				array('parentField'=>'bill_city', 'inventoryField'=>'bill_city', 'defaultValue'=>''),
				array('parentField'=>'bill_street', 'inventoryField'=>'bill_street', 'defaultValue'=>''),
				array('parentField'=>'bill_state', 'inventoryField'=>'bill_state', 'defaultValue'=>''),
				array('parentField'=>'bill_code', 'inventoryField'=>'bill_code', 'defaultValue'=>''),
				array('parentField'=>'bill_country', 'inventoryField'=>'bill_country', 'defaultValue'=>''),
				array('parentField'=>'bill_pobox', 'inventoryField'=>'bill_pobox', 'defaultValue'=>''),

				//Shipping Address Fields
				array('parentField'=>'ship_city', 'inventoryField'=>'ship_city', 'defaultValue'=>''),
				array('parentField'=>'ship_street', 'inventoryField'=>'ship_street', 'defaultValue'=>''),
				array('parentField'=>'ship_state', 'inventoryField'=>'ship_state', 'defaultValue'=>''),
				array('parentField'=>'ship_code', 'inventoryField'=>'ship_code', 'defaultValue'=>''),
				array('parentField'=>'ship_country', 'inventoryField'=>'ship_country', 'defaultValue'=>''),
				array('parentField'=>'ship_pobox', 'inventoryField'=>'ship_pobox', 'defaultValue'=>'')
		);
	}
}
