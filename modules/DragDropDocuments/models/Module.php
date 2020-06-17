<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class DragDropDocuments_Module_Model extends Vtiger_Module_Model
{
    public function getSettingLinks()
    {
        
		$settingsLinks[] = array("linktype" => "MODULESETTING", "linklabel" => "Settings", "linkurl" => "index.php?module=DragDropDocuments&parent=Settings&view=Settings", "linkicon" => "");
        return $settingsLinks;
    }
}

?>