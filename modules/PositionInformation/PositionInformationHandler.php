<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-10-06
 * Time: 1:23 PM
 */

function handlePositionSave($entity){

	$symbol = $entity->get('security_symbol');
#	$id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($symbol);
#	if($id){
		PositionInformation_Module_Model::UpdateIndividualPositionBasedOnModSecurities($symbol);
#	}
}

class PositionInformationHandler extends VTEventHandler{
	function handleEvent($eventName, $entityData) {
		global $adb;
		$recordId = $entityData->getId();
		$moduleName = $entityData->getModuleName();

		if($eventName == 'vtiger.entity.beforesave.modifiable'){
		    return;
#			echo $recordId;exit;
			$data = $entityData->getData();
			$symbol = $data['security_symbol'];
			$account = $data['account_number'];

			$record = PositionInformation_Module_Model::GetPositionEntityIDForAccountNumberAndSymbol($account, $symbol);

			if($record && $entityData->isNew()){//If the record exists, undelete it and set the record id
				PositionInformation_Module_Model::UndeletePositionEntity($record);
				$query = "UPDATE vtiger_positioninformation SET quantity = ? WHERE positioninformationid=?";
				$adb->pquery($query, array($data['quantity'], $record));
				header("Location: index.php?module=PositionInformation&view=Alert&record={$record}");
				exit;
#				die("A new position cannot be created for this account because it already exists.  The quantity has been updated with the amount just entered");
#				echo "THE RECORD EXISTS: {$record}";exit;
#				$entityData->set('id', $record);
#				$entityData->set('mode', 'edit');
			}
#			echo "RECORD DOESNT EXIST";exit;
		}
	}
}