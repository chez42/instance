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

$adb->pquery("ALTER TABLE vtiger_mail_accounts ADD from_name VARCHAR(250) NULL");

$adb->pquery("ALTER TABLE vtiger_mail_accounts ADD from_email VARCHAR(250) NULL");

Vtiger_Cron::register("Auto MSExchange Sync Task", "cron/MSExchangeTaskSync.service", 900, "MSExchange", 1, 0, "Recommended frequency for MSExchange Task Sync is 15 mins");

$adb->pquery("ALTER TABLE vtiger_mailscanner_ids ADD user_name VARCHAR(250) NULL");

$module = Vtiger_Module::getInstance("Transactions");
$fieldmodel = Vtiger_Field_Model::getInstance('description', $module);
if($fieldmodel){
   $adb->pquery("update vtiger_field set uitype = ?
   where fieldid = ?", array(19, $fieldmodel->getId()));
}


$moduleInstance = Vtiger_Module::getInstance('Users');
$blockInstance = Vtiger_Block::getInstance('User Brochure', $moduleInstance);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'User Brochure';
    $moduleInstance->addBlock($blockInstance);
}
$fieldInstance = Vtiger_Field::getInstance('brochure_file', $moduleInstance);
if (!$fieldInstance) {
    $field  = new Vtiger_Field();
    $field->name = 'brochure_file';
    $field->label= 'Brochure File';
    $field->uitype= 28;
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $blockInstance->addField($field);
}

$fieldInstance = Vtiger_Field::getInstance('brochure_shorturl', $moduleInstance);
if (!$fieldInstance) {
    $field  = new Vtiger_Field();
    $field->name = 'brochure_shorturl';
    $field->label= 'Brochure Short Url';
    $field->uitype= 17;
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;
    $blockInstance->addField($field);
    
}

$moduleInstance = Vtiger_Module::getInstance('Contacts');
$blockInstance = Vtiger_Block::getInstance('Contact Info', $moduleInstance);
if ($blockInstance) {
    $fieldInstance = Vtiger_Field::getInstance('twr_30', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'twr_30';
        $field->label = '30 Day TWR';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }

    $fieldInstance = Vtiger_Field::getInstance('twr_90', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'twr_90';
        $field->label = '90 Day TWR';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }

    $fieldInstance = Vtiger_Field::getInstance('twr_365', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'twr_365';
        $field->label = '365 Day TWR';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }
}

$moduleInstance = Vtiger_Module::getInstance('Accounts');
$blockInstance = Vtiger_Block::getInstance('Investing and Financial Information', $moduleInstance);
if ($blockInstance) {
    $fieldInstance = Vtiger_Field::getInstance('twr_30', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'twr_30';
        $field->label = '30 Day TWR';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }

    $fieldInstance = Vtiger_Field::getInstance('twr_90', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'twr_90';
        $field->label = '90 Day TWR';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }

    $fieldInstance = Vtiger_Field::getInstance('twr_365', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'twr_365';
        $field->label = '365 Day TWR';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }
}

$moduleInstance = Vtiger_Module::getInstance('Transactions');
$blockInstance = Vtiger_Block::getInstance('Transaction Information', $moduleInstance);
if ($blockInstance) {
    $fieldInstance = Vtiger_Field::getInstance('net', $moduleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name = 'net';
        $field->label = 'Net';
        $field->uitype = 28;
        $field->column = $field->name;
        $field->columntype = 'Decimal(5,2)';
        $field->typeofdata = 'N~O~2~2';
        $blockInstance->addField($field);
    }
}

$adb->pquery("ALTER TABLE `vtiger_emailslookup`  ADD `opt_out` TINYINT(3) NULL DEFAULT NULL  AFTER `fieldid`");

$adb->pquery("ALTER TABLE vtiger_mailmanager_mailattachments CHANGE muid muid VARCHAR(500) NULL DEFAULT NULL;");
$adb->pquery("ALTER TABLE vtiger_mailmanager_mailrecord CHANGE muid muid VARCHAR(500) NULL DEFAULT NULL;");
$adb->pquery("ALTER TABLE vtiger_mail_accounts ADD access_token TEXT NULL, ADD refresh_token TEXT NULL;");

$EventManager = new VTEventsManager($adb);

$EventManager->registerHandler('vtiger.entity.aftersave', 'modules/Vtiger/handlers/CustomHandler.php', 'CustomHandler');

$adb->pquery("
    CREATE TABLE `vtiger_oauth_configuration` (
 `id` int(19) NOT NULL AUTO_INCREMENT,
 `client_id` text,
 `client_secret` text,
 `redirect_url` varchar(500) DEFAULT NULL,
 `type` varchar(250) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
");

$checkField = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name ='Oauth Configuration'");

if(!$adb->num_rows($checkField)){
    $blockid = $adb->query_result(
        $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
    $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
    	 as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),0, 'sequence') + 1;
    $fieldid = $adb->getUniqueId('vtiger_settings_field');
    $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
    	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,'Oauth Configuration','','', 'index.php?parent=Settings&module=Vtiger&view=OauthConfiguration'));
}

$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_scheduled_portfolio_reports (
    id INT(11) NOT NULL AUTO_INCREMENT ,
    user_id INT(11) NULL ,
    user_email VARCHAR(255) NULL ,
    params TEXT NULL , PRIMARY KEY (id));");

Vtiger_Cron::register('SendPortfolioReportsPdf', 'cron/modules/PortfolioInformation/SendPortfolioReportsPdf.service', 0);

$office365_oauth_config = $adb->pquery("select * from vtiger_oauth_configuration where `type` = 'Office365'");

if(!$adb->num_rows($office365_oauth_config)){

	$adb->pquery("INSERT INTO `vtiger_oauth_configuration` (`client_id`, `client_secret`, `redirect_url`, `type`) VALUES
	('32679be5-4aeb-4cda-9193-fcfe74dbfdce', 'Ls51Tkjeo~-R.6Fkr_dyyD8pD6.Vvg9Bz1', 'https://oauth.omnisrv.com', 'Office365')");

}

$google_oauth_config = $adb->pquery("select * from vtiger_oauth_configuration where `type` = 'Google'");

if(!$adb->num_rows($office365_oauth_config)){
	$adb->pquery("INSERT INTO `vtiger_oauth_configuration` (`client_id`, `client_secret`, `redirect_url`, `type`) VALUES
	('351655144405-57ht69f7s00p1llkmio1g0hmpj90s93v.apps.googleusercontent.com', 'O3zkjOncVkypopLQiFoz31f7', 'https://oauth.omnisrv.com', 'Google')");
}



$adb->pquery("ALTER TABLE `vtiger_inventorytaxinfo` ADD `method` VARCHAR(10) CHARACTER SET utf8 
COLLATE utf8_general_ci NULL AFTER `deleted`, ADD `type` INT(10) NULL AFTER `method`, ADD `compoundon` 
VARCHAR(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `type`, ADD `region` TEXT CHARACTER SET utf8 
COLLATE utf8_general_ci NULL AFTER `compoundon`");


$adb->pquery("CREATE TABLE `vtiger_inventorycharges` ( `chargeid` INT(5) NOT NULL AUTO_INCREMENT , 
`name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `format` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `type` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `value` DECIMAL(12,5) NULL DEFAULT NULL , `regions` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `istaxable` INT(1) NOT NULL DEFAULT '1' , `taxes` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `deleted` INT(1) NOT NULL DEFAULT '0' , PRIMARY KEY (`chargeid`)) ENGINE = InnoDB");

$adb->pquery("CREATE TABLE `vtiger_inventorychargesrel` ( `recordid` INT(19) NOT NULL , `charges` TEXT CHARACTER SET utf8 
COLLATE utf8_general_ci NULL DEFAULT NULL ) ENGINE = InnoDB");

$adb->pquery("CREATE TABLE `vtiger_taxregions` ( `regionid` INT(10) NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) NOT NULL , PRIMARY KEY (`regionid`)) ENGINE = InnoDB");



$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_organization_attachmentsrel (
    id INT(19) NOT NULL , 
    attachmentsid INT(19) NOT NULL,
    short_url VARCHAR(255) NULL )");



$module = Vtiger_Module::getInstance("Notifications");
$blockInstance = Vtiger_Block::getInstance('LBL_NOTIFICATIONS_INFORMATION',$module);
$fieldInstance = Vtiger_Field::getInstance('title', $module);
if (!$fieldInstance) {
    $field  = new Vtiger_Field();
    $field->name = 'title';
    $field->label= 'Title';
    $field->uitype= 2;
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $blockInstance->addField($field);
}

$fieldInstance = Vtiger_Field::getInstance('notification_type', $module);
if (!$fieldInstance) {
    $field  = new Vtiger_Field();
    $field->name = 'notification_type';
    $field->label= 'Notification Type';
    $field->uitype= 15;
    $field->column = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $blockInstance->addField($field);
    if( !Vtiger_Utils::CheckTable('vtiger_'.$field->name) ) {
        $picklist_values = array("Event Invitation","New Comment Added", "Follow Record", "Upload New Document", "Message Recieved");
        $field->setPicklistValues($picklist_values);
    }
}

$adb->pquery("ALTER TABLE vtiger_document_designer_auth_settings ADD redirect_url VARCHAR(500) NULL ");

$ckEditor = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
array('Ck Editor Images', 'index.php?parent=Settings&module=Vtiger&view=CkEditorImages'));
if(!$adb->num_rows($ckEditor)){
    
    $blockid = $adb->query_result(
        $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),0, 'blockid');
		
    $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
    			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
        0, 'sequence') + 1;
		
    $fieldid = $adb->getUniqueId('vtiger_settings_field');
    $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,'Ck Editor Images','','', 'index.php?parent=Settings&module=Vtiger&view=CkEditorImages'));
        
}
Vtiger_Cron::register('Auto Office365 Sync Calendar', 'cron/modules/Office365/Office365CalendarSync.service', 
900, "Office365", 1, 0, "Recommended frequency for Office365 Events Sync is 15 mins");

$adb->pquery("ALTER TABLE vtiger_globalsearch ADD fieldname_show TEXT NULL ");

$adb->pquery("ALTER TABLE `vtiger_globalsearch` CHANGE `fieldnames` `fieldnames` TEXT ");

$module = Vtiger_Module::getInstance("Instances");
$blockInstance = Vtiger_Block::getInstance('LBL_INSTANCES_INFORMATION',$module);
$fieldInstance = Vtiger_Field::getInstance('portal_title', $module);
if(!$fieldInstance && $blockInstance){
    $field = new Vtiger_Field();
    $field->name = 'portal_title';
    $field->label = 'Portal Title';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->columntype = 'VARCHAR(500)';
    $blockInstance->addField($field);
}
$fieldInstance = Vtiger_Field::getInstance('portal_subtitle', $module);
if(!$fieldInstance && $blockInstance){
    $field = new Vtiger_Field();
    $field->name = 'portal_subtitle';
    $field->label = 'Portal Sub Title';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->columntype = 'VARCHAR(500)';
    $blockInstance->addField($field);
}

$quickCreateMenu = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
    array('Quick Create Menu', 'index.php?parent=Settings&module=Vtiger&view=QuickCreateMenu'));
if(!$adb->num_rows($quickCreateMenu)){
    
    $blockid = $adb->query_result(
        $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_CONFIGURATION'",array()),0, 'blockid');
    
    $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
    			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
        0, 'sequence') + 1;
        
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,'Quick Create Menu','','', 'index.php?parent=Settings&module=Vtiger&view=QuickCreateMenu'));
        
}

$adb->pquery("ALTER TABLE vtiger_tab ADD quick_create_seq INT(11) NULL");

$adb->pquery("ALTER TABLE vtiger_default_portal_permissions
 ADD tickets_visible INT(3) NULL DEFAULT '0',
 ADD tickets_record_across_org INT(3) NULL DEFAULT '0',
 ADD tickets_edit_records INT(3) NULL DEFAULT '0';");

$adb->pquery("ALTER TABLE vtiger_troubletickets ADD financial_advisor int(11) NULL ");

$adb->pquery("UPDATE  vtiger_field SET tablename = 'vtiger_troubletickets' WHERE
tabid = ? AND columnname = 'financial_advisor'",array(getTabid('HelpDesk')));

$quickCreateMenu = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
    array('Global Portal Permissions', 'index.php?parent=Settings&module=Vtiger&view=GlobalPortalPermission'));
if(!$adb->num_rows($quickCreateMenu)){
    
    $blockid = $adb->query_result(
    $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_CONFIGURATION'",array()),0, 'blockid');
    
    $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
	as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
    0, 'sequence') + 1;
        
    $fieldid = $adb->getUniqueId('vtiger_settings_field');
    $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,'Global Portal Permissions','','',
    'index.php?parent=Settings&module=Vtiger&view=GlobalPortalPermission'));
        
}

$votingPick = $adb->pquery("SELECT * FROM vtiger_proxy_voting_code WHERE proxy_voting_code = ?",array("Client"));
if(!$adb->num_rows($votingPick)){
    $module = Vtiger_Module::getInstance('PortfolioInformation');
    $fieldInstance = Vtiger_Field::getInstance('proxy_voting_code', $module);
    $picklist_values = array("Client");
    $fieldInstance->setPicklistValues($picklist_values);
}

$adb->pquery("UPDATE vtiger_field SET defaultvalue = 'Client' WHERE tabid = ? AND columnname = ?",
    array(getTabid('PortfolioInformation'), 'proxy_voting_code'));

$adb->pquery("UPDATE vtiger_field SET defaultvalue = '1' WHERE tabid = ? AND columnname = ?",
    array(getTabid('PortfolioInformation'), 'advisor_discretion'));

$adb->pquery("ALTER TABLE vtiger_contact_portal_permissions ADD tickets_edit_records INT(3) NULL DEFAULT '0'");

$adb->pquery("UPDATE vtiger_field SET displaytype = '3' WHERE tabid =? and fieldname=?",
    array(getTabid('Contacts'), 'salutationtype'));
	
$result = $adb->pquery("select * from vtiger_emailtemplates where templatename = ?",
array('Outgoing Email Configuration Confirmation Template'));
if(!$adb->num_rows($result)){
	$adb->pquery("INSERT INTO vtiger_emailtemplates 
	(templatename, subject, description, body, deleted, creatorid, systemtemplate, module, templateid) 
	VALUES (    
	'Outgoing Email Configuration Confirmation Template', 
	'Test mail about the mail server configuration.', 
	'Test mail about the mail server configuration.', 
	'<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n<html>\r\n<head>\r\n <title></title>\r\n</head>\r\n<body class=\"scayt-enabled\"><br />\r\n<br />\r\n<b>This is a test mail sent to confirm if a mail is actually being sent through the smtp server that you have configured. </b><br />\r\nFeel free to delete this mail.<br />\r\n<br />\r\nThanks and Regards,<br />\r\nTeam Omniscient<br />\r\n&nbsp;</body>\r\n</html>\r\n', 
	'0',
	'1', 
	'1', 
	'Contacts',
	".$adb->getUniqueID('vtiger_emailtemplates').")");
}

$adb->pquery("UPDATE vtiger_entityname SET fieldname = 'firstname,lastname' WHERE tabid IN(".getTabid('Contacts').",".getTabid('Leads').")");


$module = Vtiger_Module::getInstance('Home');
$linkURL = 'index.php?module=Contacts&view=ShowWidget&name=ClientDistribution';
$module->addLink('DASHBOARDWIDGET', 'Client Distribution', $linkURL, '', '', '');

$blockLabel = 'LBL_PORTAL_CONFIGURATION';

$adb->pquery("delete from vtiger_settings_field WHERE name = ? AND linkto = ?",
array('Editable Profile Fields', 'index.php?parent=Settings&module=Vtiger&view=ConfigurePortalEditableProfileFields'));

$block_result = $adb->pquery("SELECT * FROM vtiger_settings_blocks 
WHERE label = ?",array($blockLabel));

if(!$adb->num_rows($block_result)){
    
    $blockId = $adb->getUniqueId('vtiger_settings_blocks');
    
    $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
	as sequence FROM vtiger_settings_blocks ",array()),
        0, 'sequence') + 1;
        
    $adb->pquery("INSERT INTO vtiger_settings_blocks(blockid, label, sequence) 
    VALUES (?, ?, ?)", array($blockId, $blockLabel, $sequence));
        
} else {
    $blockId = $adb->query_result($block_result, 0, 'blockid');
}

if($blockId){
    
    $quickCreateMenu = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
    array('Editable Profile Fields', 'index.php?parent=Settings&module=Vtiger&view=PortalConfiguration'));
    
    if(!$adb->num_rows($quickCreateMenu)){
        
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
    	as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockId)),
        0, 'sequence') + 1;
            
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
        
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
    	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockId,$sequence, 'Editable Profile Fields', '','',
	    'index.php?parent=Settings&module=Vtiger&view=PortalConfiguration'));
            
    }
    
    $adb->pquery("update vtiger_settings_field set blockid = ? WHERE name = ? AND linkto = ?",
    array($blockId, 'Global Portal Permissions', 'index.php?parent=Settings&module=Vtiger&view=GlobalPortalPermission'));
    
}

$quickCreateMenu = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
    array('Configure Chat Widget', 'index.php?parent=Settings&module=Vtiger&view=PortalConfiguration&mode=chatWidget'));

if(!$adb->num_rows($quickCreateMenu)){
    
    $blockid = $adb->query_result(
        $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_PORTAL_CONFIGURATION'",array()),0, 'blockid');
    
    $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
	as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
    0, 'sequence') + 1;
        
    $fieldid = $adb->getUniqueId('vtiger_settings_field');
    $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,'Configure Chat Widget','','',
    'index.php?parent=Settings&module=Vtiger&view=PortalConfiguration&mode=chatWidget'));
        
}

$adb->pquery("ALTER TABLE vtiger_portal_configuration ADD portal_chat_widget_code TEXT NULL");

$actions = array('Download');
foreach($actions as $action){
    $actionQuery= $adb->pquery("SELECT actionid FROM vtiger_actionmapping ORDER BY actionid DESC LIMIT 1");
    $actionId = $adb->query_result($actionQuery, 0, "actionid");
    $checkAction = $adb->pquery("SELECT * FROM vtiger_actionmapping WHERE actionname = ?",array($action));
    if(!$adb->num_rows($checkAction)){
        $adb->pquery("INSERT INTO vtiger_actionmapping(actionid, actionname, securitycheck) VALUES (?,?,?)",
            array($actionId+1,$action,0));
    }
}

$tabid = getTabid("Documents");
$results = $adb->pquery("SELECT actionid FROM vtiger_actionmapping WHERE actionname in ('Download')", array());
if ($adb->num_rows($results)) {
    while ($res_row = $adb->fetch_array($results)) {
        $actionid = $res_row["actionid"];
        $permission = "1";
        $profileids = Vtiger_Profile::getAllIds();
        foreach ($profileids as $useprofileid) {
            $result = $adb->pquery("SELECT permission FROM vtiger_profile2utility WHERE profileid=? AND tabid=? AND activityid=?", array($useprofileid, $tabid, $actionid));
            if (0 < $adb->num_rows($result)) {
                $curpermission = $adb->query_result($result, 0, "permission");
                $adb->pquery("UPDATE vtiger_profile2utility set permission=? WHERE profileid=? AND tabid=? AND activityid=?", array($curpermission, $useprofileid, $tabid, $actionid));
            } else {
                $adb->pquery("INSERT INTO vtiger_profile2utility (profileid, tabid, activityid, permission) VALUES(?,?,?,?)", array($useprofileid, $tabid, $actionid, $permission));
            }
        }
    }
}


$blockId = '';
$blockLabel = 'LBL_OTHER_SETTINGS';
$block_result = $adb->pquery("SELECT * FROM vtiger_settings_blocks
WHERE label = ?",array($blockLabel));
if($adb->num_rows($block_result)){
    $blockId = $adb->query_result($block_result, 0, 'blockid');
}

if($blockId){
    
    $quickCreateMenu = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
    array('Stratifi Configuration', 'index.php?parent=Settings&module=Vtiger&view=StratifiConfiguration'));
    
    if(!$adb->num_rows($quickCreateMenu)){
        
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
    	as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockId)),
            0, 'sequence') + 1;
            
        $fieldid = $adb->getUniqueId('vtiger_settings_field');
            
        $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
    	VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockId,$sequence, 'Stratifi Configuration', '','',
	    'index.php?parent=Settings&module=Vtiger&view=StratifiConfiguration'));
        
    
    }
    
    
}

$adb->pquery("CREATE TABLE `vtiger_stratifi_configuration` (
 `rep_codes` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

$adb->pquery("UPDATE `vtiger_role` SET `rolename` = 'Organization' WHERE `vtiger_role`.`roleid` = 'H1'");
$adb->pquery("ALTER TABLE vtiger_contact_portal_permissions ADD tickets_visible INT(3) NULL DEFAULT '0'");

$adb->pquery("ALTER TABLE vtiger_contact_portal_permissions ADD tickets_record_across_org INT(3) NULL DEFAULT '0'");

$adb->pquery("UPDATE vtiger_ws_operation SET handler_method = 'vtws_get_tickets' WHERE name = 'get_related_tickets'");
$adb->pquery("ALTER TABLE vtiger_field ADD quickpreview INT(1) NULL DEFAULT '0', ADD quick_preview_field_seq INT(19) NULL;");

$operation = array('name'=>'sync_ticket_and_comments_with_instance',
    'path'=>'include/Webservices/SyncTicketsAndCommentsWithInstance.php',
    'method'=>'vtws_sync_tickets_and_comments_with_instance',
    'type'=>'POST',
    'params'=>array(array('name'=>'element','type'=>'encoded'))
);

$rs = $adb->pquery('SELECT 1 FROM vtiger_ws_operation WHERE name=?', array($operation['name']));
if (!$adb->num_rows($rs)) {
    $operationId = vtws_addWebserviceOperation($operation['name'], $operation['path'], $operation['method'], $operation['type'], 1);
    $sequence = 1;
    foreach ($operation['params'] as $param) {
        vtws_addWebserviceOperationParam($operationId, $param['name'], $param['type'], $sequence++);
    }
}

$lead_gender_field_result = $adb->pquery("SELECT * FROM vtiger_field WHERE fieldname='cf_1602' AND uitype=15");
if(!$adb->num_rows($lead_gender_field_result)){
    $adb->pquery("UPDATE vtiger_field  SET uitype=15 WHERE fieldname = 'cf_1602'");
    $lead_module_model = Vtiger_Module::getInstance('Leads');
    $fieldInstance = Vtiger_Field::getInstance('cf_1602', $lead_module_model);
    $fieldInstance->setPicklistValues(array("Male", "Female"));
}

$own_cloud_result = $adb->pquery("SELECT * FROM vtiger_tab WHERE name = 'OwnCloud'");
if (!$adb->num_rows($own_cloud_result)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'OwnCloud';
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}


$mod_tracker_result = $adb->pquery("SELECT * FROM vtiger_tab WHERE name = 'ModTracker'");
if (!$adb->num_rows($mod_tracker_result)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ModTracker';
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}

$kanban_view_result = $adb->pquery("SELECT * FROM vtiger_tab WHERE name = 'KanbanView'");
if (!$adb->num_rows($kanban_view_result)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'KanbanView';
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}

$office365_result = $adb->pquery("SELECT * FROM vtiger_tab WHERE name = 'Office365'");
if (!$adb->num_rows($office365_result)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Office365';
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}

$modcomment_module_model = Vtiger_Module::getInstance("ModComments");
$fieldInstance = Vtiger_Field_Model::getInstance('userid', $modcomment_module_model);
$useridField = $adb->pquery("SELECT * FROM vtiger_def_org_field WHERE vtiger_def_org_field.fieldid = ?",
    array($fieldInstance->getId()));
if(!$adb->num_rows($useridField)){
    $adb->pquery("INSERT INTO vtiger_def_org_field(tabid, fieldid, visible, readonly) VALUES (?,?,?,?)",
        array($modcomment_module_model->getId(), $fieldInstance->getId(), 0, 0));
}

$adb->pquery("UPDATE vtiger_settings_field SET active = '1' WHERE name = 'LBL_CUSTOMER_PORTAL' AND linkto = 'index.php?module=CustomerPortal&parent=Settings&view=Index'");




$module = Vtiger_Module::getInstance("PortfolioInformation");
$blockInstance = Vtiger_Block::getInstance('Portfolio Information',$module);
$fieldInstance = Vtiger_Field::getInstance('billingspecificationid', $module);
if(!$fieldInstance){
    $field = new Vtiger_Field();
    $field->name = 'billingspecificationid';
    $field->label = 'Billing Specification';
    $field->uitype = 10;
    $field->typeofdata = 'I~O';
    $field->columntype = 'INT(19)';
    $blockInstance->addField($field);
    $field->setrelatedmodules(array('BillingSpecifications'));
}

$module = Vtiger_Module::getInstance("BillingSpecifications");
$blockInstance = Vtiger_Block::getInstance('Pro Rate Flows',$module);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'Pro Rate Flows';
    $module->addBlock($blockInstance);
}

$fieldInstance = Vtiger_Field::getInstance('proratefromdate', $module);
if(!$fieldInstance){
    $field = new Vtiger_Field();
    $field->name = 'proratefromdate';
    $field->label = 'From';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->columntype = 'DATE';
    $blockInstance->addField($field);
}

$fieldInstance = Vtiger_Field::getInstance('proratetodate', $module);
if(!$fieldInstance){
    $field = new Vtiger_Field();
    $field->name = 'proratetodate';
    $field->label = 'To';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->columntype = 'DATE';
    $blockInstance->addField($field);
}

$fieldInstance = Vtiger_Field::getInstance('prorateamount', $module);
if(!$fieldInstance){
    $field = new Vtiger_Field();
    $field->name = 'prorateamount';
    $field->label = 'Exclude Flows Under';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $field->columntype = 'DECIMAL(25, 8)';
    $blockInstance->addField($field);
}
