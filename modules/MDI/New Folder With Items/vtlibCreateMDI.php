<?php

$moduleTitle="TSolucio::vtiger CRM Doc Mass Import Module";

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head><title>vtlib $moduleTitle</title>";
echo '<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>';
echo '</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">';
echo '<table width=100% border=0><tr><td align=left>';
echo '<a href="index.php"><img src="themes/softed/images/vtiger-crm.gif" alt="vtiger CRM" title="vtiger CRM" border=0></a>';
echo '</td><td align=center style="background-image: url(\'vtlogowmg.png\'); background-repeat: no-repeat; background-position: center;">';
echo "<b><H1>$moduleTitle</H1></b>";
echo '</td><td align=right>';
echo '<a href="www.vtiger-spain.com"><img src="vtspain.gif" alt="vtiger-spain" title="vtiger-spain" border=0 height=100></a>';
echo '</td></tr></table>';
echo '<hr style="height: 1px">';

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

// Create module instance and save it first
$module = new Vtiger_Module();
$module->name = 'MDI';
$module->save();

// Initialize all the tables required
$module->initTables();
/**
 * Creates the following table:
 * vtiger_modulename  (modulenameid INTEGER)
 * vtiger_modulenamecf(modulenameid INTEGER PRIMARY KEY)
 * vtiger_modulenamegrouprel((modulenameid INTEGER PRIMARY KEY, groupname VARCHAR(100))
 */

// Add the module to the Menu (entry point from UI)
$menu = Vtiger_Menu::getInstance('Tools');
$menu->addModule($module);

// Add the basic module block
$block1 = new Vtiger_Block();
$block1->label = 'LBL_MDI_INFORMATION';
$module->addBlock($block1);

// Add custom block (required to support Custom Fields)
$block2 = new Vtiger_Block();
$block2->label = 'LBL_CUSTOM_INFORMATION';
$module->addBlock($block2);

// Add description block
$block3 = new Vtiger_Block();
$block3->label = 'LBL_DESCRIPTION_INFORMATION';
$module->addBlock($block3);

/** Create required fields and add to the block */
$field8 = new Vtiger_Field();
$field8->name = 'title';
$field8->label = 'Title';
$field8->table = 'vtiger_mdi';
$field8->column = 'title';
$field8->columntype = "varchar(50)";
$field8->uitype = 1;
$field8->presence = 0;
$field8->typeofdata = 'V~M';
$field8->quickcreate = 0;
$block1->addField($field8);
// Set at-least one field to identifier of module record
$module->setEntityIdentifier($field8);

$field1 = new Vtiger_Field();
$field1->name = 'folderid';
$field1->label = 'Folder Name';
$field1->table = 'vtiger_mdi';
$field1->column = 'folderid';
$field1->columntype = "VARCHAR(100)";
$field1->uitype = 26;
$field1->typeofdata = 'I~O';
$field1->presence = 0;
$field1->quickcreate = 0;
$block1->addField($field1); /** Creates the field and adds to block */

/** Common fields that should be in every module, linked to vtiger CRM core table */

$field9 = new Vtiger_Field();
$field9->name = 'assigned_user_id';
$field9->label = 'Assigned To';
$field9->table = 'vtiger_crmentity';
$field9->column = 'smownerid';
$field9->uitype = 53;
$field9->presence = 0;
$field9->displaytype= 1;
$field9->typeofdata = 'V~M';
$block1->addField($field9);

$field5 = new Vtiger_Field();
$field5->name = 'createdtime';
$field5->label= 'Created Time';
$field5->table = 'vtiger_crmentity';
$field5->column = 'createdtime';
$field5->uitype = 70;
$field5->typeofdata = 'T~O';
$field5->presence = 0;
$field5->displaytype= 2;
$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->name = 'modifiedtime';
$field6->label= 'Modified Time';
$field6->table = 'vtiger_crmentity';
$field6->column = 'modifiedtime';
$field6->uitype = 70;
$field6->presence = 0;
$field6->typeofdata = 'T~O';
$field6->displaytype= 2;
$block1->addField($field6);

/** Campo Descripcion */
$field7 = new Vtiger_Field();
$field7->name = 'description';
$field7->label= 'Description';
$field7->table = 'vtiger_crmentity';
$field7->column = 'description';
$field7->columntype = 'VARCHAR(256)';
$field7->uitype = 19;
$field7->presence = 0;
$field7->typeofdata = 'V~O';
$block3->addField($field7);

/** END */

// Create default custom filter (mandatory)
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);

// Add fields to the filter created
$filter1->addField($field8)->addField($field1, 1)->addField($field9, 2);

/** Set sharing access of this module */
$module->setDefaultSharing('Public');

/** Enable and Disable available tools */
$module->disableTools(Array('Import', 'Export'));
$module->disableTools('Merge');

$module->initWebservice();

echo '</body></html>';

?>
