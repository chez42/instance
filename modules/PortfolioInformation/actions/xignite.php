<?php

require_once("libraries/xignite/companies.php");

class PortfolioInformation_xignite_Action extends Vtiger_BasicAjax_Action{
	public function process(Vtiger_Request $request) {
		switch($request->get('todo')) {
			case "GetFundamentals":
				$xignite = new XigniteCompanies();
				$symbol = $request->get('symbol');
				$result = $xignite->GetFundamentals("Symbol", $symbol);
				print_r($result);
				break;
			case "GetSectors":
				PortfolioInformation_xignite_Model::GetAndWriteSectorToMappingTable();
				break;
			case "MapSectors":
				PortfolioInformation_xignite_Model::MapSectorInformationIntoCRM();
				echo "Mapping Finished";
				break;
			case "PopulateUnclassified":
				PortfolioInformation_xignite_Model::PopulateUnclassified(1000);
				echo "Done populating ModSecurities";
				break;
		}
	}
}

?>