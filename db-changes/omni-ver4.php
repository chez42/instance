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
if($module){
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
}

$instance_module_obj = Vtiger_Module::getInstance("Instances");
if($instance_module_obj){
	$blockInstance = Vtiger_Block::getInstance('LBL_INSTANCES_INFORMATION',$instance_module_obj);
	$fieldInstance = Vtiger_Field::getInstance('master_password', $instance_module_obj);
	if(!$fieldInstance){
		$field = new Vtiger_Field();
		$field->name = 'master_password';
		$field->label = 'Master Password';
		$field->uitype = 2;
		$field->typeofdata = 'V~O';
		$field->columntype = 'VARCHAR(255)';
		$blockInstance->addField($field);
	}

	$functionPath = "modules/Instances/InstancesHandler.php";
	$functiponName = "SyncMasterPasswordInInstance";
	$checkMeth = $adb->pquery("SELECT * FROM com_vtiger_workflowtasks_entitymethod
	WHERE function_path = ? AND function_name = ?",array($functionPath, $functiponName));
	if(!$adb->num_rows($checkMeth)){
		require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
		$emm = new VTEntityMethodManager($adb);
		$emm->addEntityMethod("Instances", "Sync Master Password In Instance", $functionPath, $functiponName);
	}
}

if(!$instance_module_obj){
	$operation = array('name'=>'sync_master_password',
		'path'=>'include/InstancesWebservices/SyncMasterPassword.php',
		'method'=>'vtws_sync_master_password',
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
}

$instance_module_obj = Vtiger_Module::getInstance("Instances");

if($instance_module_obj){
	
    $blockInstance = Vtiger_Block::getInstance('LBL_INSTANCES_INFORMATION',$instance_module_obj);
    
	$fieldInstance = Vtiger_Field::getInstance('instance_logo', $instance_module_obj);
    
	if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'instance_logo';
        $field->label = 'Login Page Logo';
        $field->uitype = 69;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('instance_background', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'instance_background';
        $field->label = 'Login Page Background';
        $field->uitype = 69;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('copyright_text', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'copyright_text';
        $field->label = 'Copyright Text';
        $field->uitype = 2;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $blockInstance = Vtiger_Block::getInstance('Social Media Links',$instance_module_obj);
    if (!$blockInstance) {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'Social Media Links';
        $instance_module_obj->addBlock($blockInstance);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('facebook_link', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'facebook_link';
        $field->label = 'Facebook Link';
        $field->uitype = 17;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('twitter_link', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'twitter_link';
        $field->label = 'Twitter Link';
        $field->uitype = 17;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('linkedin_link', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'linkedin_link';
        $field->label = 'LinkedIn link';
        $field->uitype = 17;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('youtube_link', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'youtube_link';
        $field->label = 'Youtube Link';
        $field->uitype = 17;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('instagram_link', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'instagram_link';
        $field->label = 'Instagram Link';
        $field->uitype = 17;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(255)';
        $blockInstance->addField($field);
    }
}

if($instance_module_obj){
    $operation = array('name'=>'get_instance_details',
        'path'=>'include/Webservices/GetInstanceDetails.php',
        'method'=>'vtws_get_instance_details',
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
}

if($instance_module_obj){
    $blockInstance = Vtiger_Block::getInstance('LBL_INSTANCES_INFORMATION',$instance_module_obj);
    $fieldInstance = Vtiger_Field::getInstance('portalfavicon', $instance_module_obj);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'portalfavicon';
        $field->label = 'Portal Favicon';
        $field->uitype = 69;
        $field->typeofdata = 'V~O';
        $field->columntype = 'VARCHAR(200)';
        $blockInstance->addField($field);
    }
}

$operation = array('name'=>'get_related_positions',
    'path'=>'include/PortalWebservices/GetPositions.php',
    'method'=>'vtws_get_positions',
    'type'=>'POST',
    'params'=>array(array('name'=>'element','type'=>'encoded'))
);
$rs = $adb->pquery('SELECT 1 FROM vtiger_ws_operation WHERE name=?', array($operation['name']));
if (!$adb->num_rows($rs)) {
    $operationId = vtws_addWebserviceOperation($operation['name'], $operation['path'], $operation['method'], $operation['type']);
    $sequence = 1;
    foreach ($operation['params'] as $param) {
        vtws_addWebserviceOperationParam($operationId, $param['name'], $param['type'], $sequence++);
    }
}

$adb->pquery("UPDATE vtiger_field SET displaytype=1 WHERE tabid = 29 AND fieldname LIKE '%appointment_url%'");
$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_email_queue ( 
    queueid INT(19) NOT NULL AUTO_INCREMENT , 
    emailid INT(19) NULL , 
    from_serveremailid INT(19) NULL , 
    selected_fields TEXT NULL , 
    cvid INT(19) NULL , 
    serch_params TEXT NULL , 
    selected_ids TEXT NULL , 
    excluded_ids TEXT NULL , 
    other_data TEXT NULL , 
    source_module VARCHAR(255) NULL,
    PRIMARY KEY (queueid)
);");

Vtiger_Cron::register('ProcessEmailQueue', 'cron/ProcessEmailQueue.service', 0);

$adb->pquery("UPDATE vtiger_field SET masseditable = 2 WHERE fieldname = ? AND tabid = ?",
    array('unit_price', getTabid('Products')));

$adb->pquery("UPDATE `vtiger_entityname` SET `fieldname` = 'name' WHERE `vtiger_entityname`.`tabid` = ?",array(getTabid('BillingSpecifications')));

$adb->pquery("ALTER TABLE vtiger_organizationdetails ADD google_login INT(3) NULL, ADD office_login INT(3) NULL;");
$instance = Vtiger_Module_Model::getInstance('Instances');

if(!empty($instance)){
    $quickCreateMenu = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND linkto = ?",
        array('Login Page Settings', 'index.php?parent=Settings&module=Vtiger&view=LoginPageSettings'));
    if(!$adb->num_rows($quickCreateMenu)){
        
        $blockid = $adb->query_result(
            $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_CONFIGURATION'",array()),0, 'blockid');
        
        $sequence = (int)$adb->query_result($adb->pquery("SELECT max(sequence)
    			as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
            0, 'sequence') + 1;
            
            $fieldid = $adb->getUniqueId('vtiger_settings_field');
            $adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
        VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,'Login Page Settings','','', 'index.php?parent=Settings&module=Vtiger&view=LoginPageSettings'));
            
    }
    
    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_login_page_settings (
    login_logo TEXT NULL ,
    login_background TEXT NULL ,
    copyright_text VARCHAR(255) NULL ,
    facebook_link VARCHAR(255) NULL ,
    twitter_link VARCHAR(255) NULL ,
    linkedin_link VARCHAR(255) NULL ,
    youtube_link VARCHAR(255) NULL ,
    instagram_link VARCHAR(255) NULL );");
}

$adb->pquery("ALTER TABLE vtiger_wsapp_recordmapping CHANGE serverid serverid VARCHAR(100)");

$adb->pquery("ALTER TABLE vtiger_google_sync ADD nextsynctoken TEXT NULL;");
$adb->pquery("ALTER TABLE vtiger_pandadocdocument_reference ADD crmid INT(19) NULL");
$query = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid =?",
    array( getTabid('Contacts'), getTabid('PandaDoc')));
if(!$adb->num_rows($query)){
    $moduleInstance = Vtiger_Module::getInstance("Contacts");
    $moduleInstance->setRelatedList(Vtiger_Module::getInstance('PandaDoc'), 'PandaDoc',Array(), 'get_pandadoc_documents');
}


$adb->pquery("UPDATE vtiger_field SET tablename=? WHERE tabid=?
AND columnname IN ('description', 'smownerid', 'createdtime', 'modifiedtime', 'source', 'starred')",
array('vtiger_notifications', getTabid('Notifications')));

$adb->pquery("ALTER TABLE vtiger_notifications
ADD starred VARCHAR(100) NULL, ADD modifiedtime DATETIME NOT NULL,
ADD createdtime DATETIME NOT NULL, ADD smownerid INT(19) NOT NULL,
ADD deleted INT(1) NOT NULL DEFAULT '0', ADD description TEXT NULL,
ADD source VARCHAR(100) NULL");


$entity_type_result = $adb->pquery("select * from vtiger_tab where name = 'Billing' AND isentitytype = 1");

if(!$adb->num_rows($entity_type_result)){

	$adb->pquery("UPDATE vtiger_tab SET isentitytype = '1' WHERE vtiger_tab.name = 'Billing';");

	$moduleInstance = Vtiger_Module::getInstance('Billing');

	if ($moduleInstance){
		
		$moduleInstance->initTables();
		
		$blockInstance = Vtiger_Block::getInstance('LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION', $moduleInstance);
		
		if (!$blockInstance) {
			$blockInstance = new Vtiger_Block();
			$blockInstance->label = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
			$moduleInstance->addBlock($blockInstance);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('billingno', $moduleInstance);
		if (!$fieldInstance) {
			$field1  = new Vtiger_Field();
			$field1->name = 'billingno';
			$field1->label= 'Billing No';
			$field1->uitype= 4;
			$field1->column = $field1->name;
			$field1->columntype = 'VARCHAR(255)';
			$field1->typeofdata = 'V~O';
			$blockInstance->addField($field1);
			$moduleInstance->setEntityIdentifier($field1);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('start_date', $moduleInstance);
		if (!$fieldInstance) {
			$field2  = new Vtiger_Field();
			$field2->name = 'start_date';
			$field2->label= 'Start Date';
			$field2->uitype= 5;
			$field2->column = $field2->name;
			$field2->columntype = 'DATE';
			$field2->typeofdata = 'D~O';
			$blockInstance->addField($field2);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('end_date', $moduleInstance);
		if (!$fieldInstance) {
			$field3  = new Vtiger_Field();
			$field3->name = 'end_date';
			$field3->label= 'End Date';
			$field3->uitype= 5;
			$field3->column = $field3->name;
			$field3->columntype = 'DATE';
			$field3->typeofdata = 'D~O';
			$blockInstance->addField($field3);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('portfolio_amount', $moduleInstance);
		if (!$fieldInstance) {
			$field4  = new Vtiger_Field();
			$field4->name = 'portfolio_amount';
			$field4->label= 'Portfolio Amount';
			$field4->uitype= 71;
			$field4->column = $field4->name;
			$field4->columntype = 'DECIMAL(25,8)';
			$field4->typeofdata = 'N~O';
			$blockInstance->addField($field4);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('portfolioid', $moduleInstance);
		if (!$fieldInstance) {
			$field5  = new Vtiger_Field();
			$field5->name = 'portfolioid';
			$field5->label= 'Portfolio';
			$field5->uitype= 10;
			$field5->column = $field5->name;
			$field5->columntype = 'INT(19)';
			$field5->typeofdata = 'I~O';
			$blockInstance->addField($field5);
			$field5->setrelatedmodules(array('PortfolioInformation'));
		}
		
		$fieldInstance = Vtiger_Field::getInstance('billingspecificationid', $moduleInstance);
		if (!$fieldInstance) {
			$field6  = new Vtiger_Field();
			$field6->name = 'billingspecificationid';
			$field6->label= 'Billing Specification';
			$field6->uitype= 10;
			$field6->column = $field6->name;
			$field6->columntype = 'INT(19)';
			$field6->typeofdata = 'I~O';
			$blockInstance->addField($field6);
			$field6->setrelatedmodules(array('BillingSpecifications'));
		}
		
		$fieldInstance = Vtiger_Field::getInstance('feeamount', $moduleInstance);
		if (!$fieldInstance) {
			$field7  = new Vtiger_Field();
			$field7->name = 'feeamount';
			$field7->label= 'Fee Amount';
			$field7->uitype= 71;
			$field7->column = $field7->name;
			$field7->columntype = 'DECIMAL(25,8)';
			$field7->typeofdata = 'N~O';
			$blockInstance->addField($field7);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
		if (!$fieldInstance) {
			$mfield1 = new Vtiger_Field();
			$mfield1->name = 'assigned_user_id';
			$mfield1->label = 'Assigned To';
			$mfield1->table = 'vtiger_crmentity';
			$mfield1->column = 'smownerid';
			$mfield1->uitype = 53;
			$mfield1->typeofdata = 'V~M';
			$blockInstance->addField($mfield1);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('createdtime', $moduleInstance);
		if (!$fieldInstance) {
			$mfield2 = new Vtiger_Field();
			$mfield2->name = 'createdtime';
			$mfield2->label= 'Created Time';
			$mfield2->table = 'vtiger_crmentity';
			$mfield2->column = 'createdtime';
			$mfield2->uitype = 70;
			$mfield2->typeofdata = 'DT~O';
			$mfield2->displaytype= 2;
			$blockInstance->addField($mfield2);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
		if (!$fieldInstance) {
			$mfield3 = new Vtiger_Field();
			$mfield3->name = 'modifiedtime';
			$mfield3->label= 'Modified Time';
			$mfield3->table = 'vtiger_crmentity';
			$mfield3->column = 'modifiedtime';
			$mfield3->uitype = 70;
			$mfield3->typeofdata = 'DT~O';
			$mfield3->displaytype= 2;
			$blockInstance->addField($mfield3);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('source', $moduleInstance);
		if (!$fieldInstance) {
			$mfield4 = new Vtiger_Field();
			$mfield4->name = 'source';
			$mfield4->label = 'Source';
			$mfield4->table = 'vtiger_crmentity';
			$mfield4->displaytype = 2; // to disable field in Edit View
			$mfield4->quickcreate = 3;
			$mfield4->masseditable = 0;
			$blockInstance->addField($mfield4);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('starred', $moduleInstance);
		if (!$fieldInstance) {
			$mfield5 = new Vtiger_Field();
			$mfield5->name = 'starred';
			$mfield5->label = 'starred';
			$mfield5->table = 'vtiger_crmentity_user_field';
			$mfield5->displaytype = 6;
			$mfield5->uitype = 56;
			$mfield5->typeofdata = 'C~O';
			$mfield5->quickcreate = 3;
			$mfield5->masseditable = 0;
			$blockInstance->addField($mfield5);
		}
		
		$fieldInstance = Vtiger_Field::getInstance('tags', $moduleInstance);
		if (!$fieldInstance) {
			$mfield6 = new Vtiger_Field();
			$mfield6->name = 'tags';
			$mfield6->label = 'tags';
			$mfield6->displaytype = 6;
			$mfield6->columntype = 'VARCHAR(1)';
			$mfield6->quickcreate = 3;
			$mfield6->masseditable = 0;
			$blockInstance->addField($mfield6);
		}
		
		$filter1 = new Vtiger_Filter();
		$filter1->name = 'All';
		$filter1->isdefault = true;
		$moduleInstance->addFilter($filter1);
		$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($mfield1, 3);
		
		// Sharing Access Setup
		$moduleInstance->setDefaultSharing();
		
		// Webservice Setup
		$moduleInstance->initWebservice();
		
		echo "OK\n";
		
	}

}

$module = Vtiger_Module::getInstance("BillingSpecifications");
if(!empty($module)){
    
    $adb->pquery("ALTER TABLE vtiger_billingspecifications CHANGE value amount_value VARCHAR(250)");
    
    $adb->pquery("UPDATE vtiger_field SET fieldname='amount_value', columnname='amount_value' WHERE tabid=? AND fieldname='value' AND columnname='value'",
        array(getTabid('BillingSpecifications')));
    
    $blockInstance = Vtiger_Block::getInstance('LBL_BILLINGSPECIFICATIONS_INFORMATION',$module);
    $fieldInstance = Vtiger_Field::getInstance('beginning_date', $module);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'beginning_date';
        $field->label = 'Beginning Date';
        $field->uitype = 5;
        $field->typeofdata = 'D~O';
        $field->columntype = 'DATE';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('ending_date', $module);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'ending_date';
        $field->label = 'Ending Date';
        $field->uitype = 5;
        $field->typeofdata = 'D~O';
        $field->columntype = 'DATE';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('beginning_price_date', $module);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'beginning_price_date';
        $field->label = 'Beginning Price Date';
        $field->uitype = 5;
        $field->typeofdata = 'D~O';
        $field->columntype = 'DATE';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('ending_price_date', $module);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'ending_price_date';
        $field->label = 'Ending Price Date';
        $field->uitype = 5;
        $field->typeofdata = 'D~O';
        $field->columntype = 'DATE';
        $blockInstance->addField($field);
    }
    
}

$module = Vtiger_Module::getInstance("Billing");
if(!empty($module)){
    
    $blockInstance = Vtiger_Block::getInstance('LBL_BILLING_INFORMATION',$module);
    $fieldInstance = Vtiger_Field::getInstance('beginning_price_date', $module);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'beginning_price_date';
        $field->label = 'Beginning Price Date';
        $field->uitype = 5;
        $field->typeofdata = 'D~O';
        $field->columntype = 'DATE';
        $blockInstance->addField($field);
    }
    
    $fieldInstance = Vtiger_Field::getInstance('ending_price_date', $module);
    if(!$fieldInstance){
        $field = new Vtiger_Field();
        $field->name = 'ending_price_date';
        $field->label = 'Ending Price Date';
        $field->uitype = 5;
        $field->typeofdata = 'D~O';
        $field->columntype = 'DATE';
        $blockInstance->addField($field);
    }
    
}

$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_billing_capitalflows (
            capitalflowsid INT(19) NOT NULL AUTO_INCREMENT ,
            billingid INT(19) NULL ,
            trade_date VARCHAR(255) NULL ,
            diff_days VARCHAR(255) NULL ,
            totalamount VARCHAR(255) NULL ,
            totaldays VARCHAR(255) NULL ,
            transactionamount VARCHAR(255) NULL ,
            transactiontype VARCHAR(255) NULL ,
            trans_fee VARCHAR(255) NULL ,
            totaladjustment VARCHAR(255) NULL ,
            PRIMARY KEY (capitalflowsid)
        );");

$tab_id = getTabid('PortfolioInformation');
$linkurl = 'javascript:Billing_Js.triggerBillingReportPdf("index.php?module=Billing&view=BillingReportPdf&mode=GenrateLink");';
Vtiger_Link::deleteLink($tab_id, 'LISTVIEWMASSACTION', 'Get Statement', $linkurl);