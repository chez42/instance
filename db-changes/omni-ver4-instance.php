<?php
$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

$adb = PearDatabase::getInstance();

$notification_module_instance = Vtiger_Module_Model::getInstance('Notifications');
$field_instance = Vtiger_Field_Model::getInstance('related_to', $notification_module_instance);
$field_instance->setrelatedmodules(array('Calendar'));

// Create all Day Event Field
$module = Vtiger_Module::getInstance("Events");
$blockInstance = Vtiger_Block::getInstance('LBL_EVENT_INFORMATION',$module);
$fieldInstance = Vtiger_Field::getInstance('all_day_event', $module);
if(!$fieldInstance){
	$field = new Vtiger_Field();
	$field->name = 'all_day_event';
	$field->label = 'All Day Event';
	$field->uitype = 56;
	$field->typeofdata = 'C~O';
	$field->table = 'vtiger_activity';

	$field->columntype = 'VARCHAR(50)';
	$field->quickcreate = 2;
	$blockInstance->addField($field);
}

// PBX Manager Handler for convert Lead
$EventManager = new VTEventsManager($adb);
$convertLeadEvent = 'vtiger.lead.convertlead';
$handler_path = 'modules/PBXManager/PBXManagerHandler.php';
$className = 'PBXManagerHandler';
$EventManager->registerHandler($convertLeadEvent, $handler_path, $className);

$adb->pquery("ALTER TABLE vtiger_mailscanner ADD userid INT(11) NULL AFTER scannerid");

$adb->pquery("ALTER TABLE `vtiger_field` ADD `related_tab_field_seq` INT(19) NULL AFTER `isunique`");

$adb->pquery('CREATE TABLE IF NOT EXISTS vtiger_roundrobin_roles ( roles TEXT NULL )');



$user_module_instance = Vtiger_Module::getInstance("Users");
$blockInstance = Vtiger_Block::getInstance('LBL_CALENDAR_SETTINGS', $user_module_instance);
$fieldInstance = Vtiger_Field::getInstance('15min',$user_module_instance);

if(!$fieldInstance){
    $field  = new Vtiger_Field();
    $field->name = '15min';
    $field->label= '15 min text';
    $field->uitype= 2;
	$field->displaytype = '3';
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(100)';
    $field->typeofdata = 'V~O';
    $blockInstance->addField($field);
}

$fieldInstance = Vtiger_Field::getInstance('30min',$user_module_instance);
if(!$fieldInstance){
    $field  = new Vtiger_Field();
    $field->name = '30min';
    $field->label= '30 min text';
    $field->uitype= 2;
	$field->displaytype = '3';
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(100)';
    $field->typeofdata = 'V~O';
    $blockInstance->addField($field);
}

$fieldInstance = Vtiger_Field::getInstance('1hr',$user_module_instance);

if(!$fieldInstance){
    $field  = new Vtiger_Field();
    $field->name = '1hr';
    $field->label= '1 hr text';
    $field->uitype= 2;
	$field->displaytype = '3';
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(100)';
    $field->typeofdata = 'V~O';
    $blockInstance->addField($field);
}


$moduleInstance = Vtiger_Module::getInstance('Events');
$blockInstance = Vtiger_Block::getInstance('Recurrence Details', $moduleInstance);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'Recurrence Details';
    $moduleInstance->addBlock($blockInstance);
}
$fieldIns = Vtiger_Field_Model::getInstance('recurringtype', $moduleInstance);
$adb->pquery('UPDATE vtiger_field SET block=? WHERE fieldid=?', Array($blockInstance->id, $fieldIns->getId()));


$module = Vtiger_Module::getInstance("Events");
$blockInstance = Vtiger_Block::getInstance('LBL_EVENT_INFORMATION',$module);
$fieldInstance = Vtiger_Field::getInstance('activity_reminder_time', $module);
if (!$fieldInstance) {
    $field  = new Vtiger_Field();
    $field->name = 'activity_reminder_time';
    $field->label= 'Reminder Time';
    $field->uitype= 6;
    $field->table = 'vtiger_activity';
    $field->column = $field->name;
    $field->columntype = 'DATETIME';
    $field->typeofdata = 'DT~O';
    $field->displaytype = 3;
    $blockInstance->addField($field);
}

$moduleName = 'Task';
$widgetType = 'EXTENSIONLINK';
$module = Vtiger_Module::getInstance($moduleName);
if ($module) {
    $linkURL = 'index.php?module='.$moduleName.'&view=Extension&extensionModule=MSExchange&extensionView=Index';
    $module->addLink($widgetType, 'MSExchange', $linkURL, '', '', '');
}

$field_result = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid = ? AND 
(fieldname = 'related_to' OR columnname = 'related_to')", array(getTabid('ModComments')));
if($adb->num_rows($field_result)){
    $fieldId = $adb->query_result($field_result, 0, 'fieldid');
    $adb->pquery("DELETE FROM vtiger_fieldmodulerel WHERE module ='ModComments' AND relmodule = 'Task'");
    $adb->pquery("INSERT INTO vtiger_fieldmodulerel (fieldid, module, relmodule) VALUES (?, 'ModComments', 'Task')",array($fieldId));
}


$adb->pquery("update vtiger_field set summaryfield = '1' where fieldname = 'faq_no' 
and tabid = ?", array( getTabid('Faq') ) );

$adb->pquery("ALTER TABLE vtiger_mail_accounts ADD smtp_servername VARCHAR(250) NULL");

Vtiger_Cron::register("Auto MSExchange Sync Task", "cron/MSExchangeTaskSync.service", 900, "MSExchange", 1, 0, "Recommended frequency for MSExchange Task Sync is 15 mins");

$adb->pquery("ALTER TABLE vtiger_mailscanner_ids ADD user_name VARCHAR(250) NULL");

$adb->pquery("ALTER TABLE vtiger_mail_accounts ADD from_name VARCHAR(250) NULL");

$adb->pquery("ALTER TABLE vtiger_mail_accounts ADD from_email VARCHAR(250) NULL");

$adb->pquery("ALTER TABLE vtiger_tab ADD ishide INT(3) NULL DEFAULT '0' ");

$operation = array(
	'name'=>'managemodules',
    'path'=>'include/InstancesWebservices/ManageModules.php',
    'method'=>'vtws_managemodules',
    'type'=>'POST',
    'params'=>array(array('name'=>'element','type'=>'encoded'))
);


$rs = $adb->pquery('SELECT 1 FROM vtiger_ws_operation WHERE name = ?', array($operation['name']));
if (!$adb->num_rows($rs)) {
    $operationId = vtws_addWebserviceOperation($operation['name'], $operation['path'], $operation['method'], $operation['type'], 1);
    $sequence = 1;
    foreach ($operation['params'] as $param) {
        vtws_addWebserviceOperationParam($operationId, $param['name'], $param['type'], $sequence++);
    }
}

$module = Vtiger_Module::getInstance("Transactions");
$fieldmodel = Vtiger_Field_Model::getInstance('description', $module);
if($fieldmodel){
   $adb->pquery("update vtiger_field set uitype = ?
   where fieldid = ?", array(19, $fieldmodel->getId()));
}