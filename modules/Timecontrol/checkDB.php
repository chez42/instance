<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 12.06.15 16:17
 * You must not use this file without permission.
 */

\TimeControl\VtUtils::checkColumn('vtiger_timecontrol', 'timecontrolstatus', 'VARCHAR(100)');
// \TimeControl\VtUtils::checkColumn('vtiger_timecontrol', 'invoiced_on', 'VARCHAR(100)');

$adb = \PearDatabase::getInstance();

$sql = 'SELECT * FROM vtiger_ws_entity WHERE name = "Timecontrol"';
$result = $adb->pquery($sql);

if($adb->num_rows($result) == 0) {
    $sql = 'INSERT INTO vtiger_ws_entity SET name = "Timecontrol", handlerpath = "include/Webservices/VtigerModuleOperation.php", handler_class = "VtigerModuleOperation"';
    $adb->pquery($sql);
}

include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Block.php');
include_once('vtlib/Vtiger/Field.php');

/** Add to Products */
/*$module = Vtiger_Module::getInstance('Timecontrol');
$field = Vtiger_Field::getInstance('related_account_id', $module);

if($field === false) {
    $block = Vtiger_Block::getInstance("LBL_TIMECONTROL_INFORMATION", $module);

    $field = new Vtiger_Field();
    $field->name = 'related_account_id';
    $field->label= 'Account Name';
    $field->table = $module->basetable;
    $field->column = 'related_account_id';
    $field->columntype = "varchar(100)";
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $block->addField($field);
    $field->setRelatedModules(array('Accounts'));
}

$field = Vtiger_Field::getInstance('related_account_id', $module);
$field->unsetRelatedModules(array('Invoice'));
$field->setRelatedModules(array('Accounts'));

*/
if(!\TimeControl\VtUtils::existTable("vtiger_timecontrol_config")) {
    echo "Create table vtiger_timecontrol_config ... ok<br>";
    $adb->query("CREATE TABLE IF NOT EXISTS `vtiger_timecontrol_config` (
  `key` varchar(128) NOT NULL,
  `value` text NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB;");

    $dateformat = str_replace(array('dd','mm','yyyy'), array('d','m','Y'), vglobal('current_user')->date_format);

    $sql = 'INSERT INTO vtiger_timecontrol_config SET `key` = ?, value = ?';
    $adb->pquery($sql, array('product_template', '$description
%Period% $[DATEFORMAT,$date_start,"'.$dateformat.'"] $time_start  - $time_end' ));
}
