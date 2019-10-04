<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class SignedRecord_Module_Model
 */
class SignedRecord_Module_Model extends Vtiger_Module_Model
{
    /**
     * @return array
     */
    public function getSettingLinks()
    {
        $settingsLinks = parent::getSettingLinks();
        $settingsLinks[] = array("linktype" => "MODULESETTING", "linklabel" => "Uninstall", "linkurl" => "index.php?module=" . $this->name . "&parent=Settings&view=Uninstall", "linkicon" => "");
        return $settingsLinks;
    }
}

?>