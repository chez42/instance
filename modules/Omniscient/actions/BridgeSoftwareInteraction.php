<?php

class Omniscient_BridgeSoftwareInteraction_Action extends Vtiger_BasicAjax_Action{
	static public function RunRequest($action, $data) {
		switch($action){
			case "AuditPositions":
				echo json_encode(Omniscient_BridgingFunctions_Model::CompareAccountData($data)) . "\r\n";
//				return array("message"=>"Audit Positions Yo");
				break;
			default:
				return array("message"=>"No valid action passed in");
				break;
		}
	}
}

?>