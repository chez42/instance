<?php

chdir('../');

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'BillingSpecifications';
$db = PearDatabase::getInstance();

$sel = $db->pquery("SELECT * FROM vtiger_tab WHERE name = 'BillingSpecifications'");

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance || $db->num_rows($sel)) {
    echo "Module already present - choose a different name.";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent= 'Tools';
    $moduleInstance->save();
    
    // Schema Setup
    $moduleInstance->initTables();
    
    // Field Setup
    $block = new Vtiger_Block();
    $block->label = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
    $moduleInstance->addBlock($block);
   
    $field1  = new Vtiger_Field();
    $field1->name = 'billing_no';
    $field1->label= 'Billing Specification N0';
    $field1->uitype= 4;
    $field1->column = $field1->name;
    $field1->columntype = 'VARCHAR(255)';
    $field1->typeofdata = 'V~O';
    $block->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
    
    $field2  = new Vtiger_Field();
    $field2->name = 'name';
    $field2->label= 'Name';
    $field2->uitype= 2;
    $field2->column = $field2->name;
    $field2->columntype = 'VARCHAR(255)';
    $field2->typeofdata = 'V~M';
    $block->addField($field2);
    //$field2->setrelatedmodules(array('Contacts','HelpDesk'));
    
    
    $field3 = new Vtiger_Field();
    $field3->name = 'billing_frequency';
    $field3->label= 'Billing Frequency';
    $field3->uitype = 15;
    $field3->column = $field3->name;
    $field3->typeofdata = 'V~O';
    $field3->columntype = 'VARCHAR(250)';
    $block->addField($field3);
    if( !Vtiger_Utils::CheckTable('vtiger_'.$field4->name) ) {
        $picklist_values = array("Monthly","Quaterly");
        $field3->setPicklistValues($picklist_values);
    }
   
    $field4 = new Vtiger_Field();
    $field4->name = 'billing_type';
    $field4->label= 'Billing Type';
    $field4->uitype = 15;
    $field4->column = $field4->name;
    $field4->typeofdata = 'V~O';
    $field4->columntype = 'VARCHAR(250)';
    $block->addField($field4);
    if( !Vtiger_Utils::CheckTable('vtiger_'.$field4->name) ) {
        $picklist_values = array("Fixed Amount", "Fixed Rate", "Schedule");
        $field4->setPicklistValues($picklist_values);
    }
    
    $field5 = new Vtiger_Field();
    $field5->name = 'value';
    $field5->label= 'Value';
    $field5->uitype = 2;
    $field5->column = $field5->name;
    $field5->typeofdata = 'V~O';
    $field5->columntype = 'VARCHAR(250)';
    $block->addField($field5);

    
    // Recommended common fields every Entity module should have (linked to core table)
    $mfield1 = new Vtiger_Field();
    $mfield1->name = 'assigned_user_id';
    $mfield1->label = 'Assigned To';
    $mfield1->table = 'vtiger_crmentity';
    $mfield1->column = 'smownerid';
    $mfield1->uitype = 53;
    $mfield1->typeofdata = 'V~M';
    $block->addField($mfield1);
    
    $mfield2 = new Vtiger_Field();
    $mfield2->name = 'createdtime';
    $mfield2->label= 'Created Time';
    $mfield2->table = 'vtiger_crmentity';
    $mfield2->column = 'createdtime';
    $mfield2->uitype = 70;
    $mfield2->typeofdata = 'DT~O';
    $mfield2->displaytype= 2;
    $block->addField($mfield2);
    
    $mfield3 = new Vtiger_Field();
    $mfield3->name = 'modifiedtime';
    $mfield3->label= 'Modified Time';
    $mfield3->table = 'vtiger_crmentity';
    $mfield3->column = 'modifiedtime';
    $mfield3->uitype = 70;
    $mfield3->typeofdata = 'DT~O';
    $mfield3->displaytype= 2;
    $block->addField($mfield3);
    
    /* NOTE: Vtiger 7.1.0 onwards */
    $mfield4 = new Vtiger_Field();
    $mfield4->name = 'source';
    $mfield4->label = 'Source';
    $mfield4->table = 'vtiger_crmentity';
    $mfield4->displaytype = 2; // to disable field in Edit View
    $mfield4->quickcreate = 3;
    $mfield4->masseditable = 0;
    $block->addField($mfield4);
    
    $mfield5 = new Vtiger_Field();
    $mfield5->name = 'starred';
    $mfield5->label = 'starred';
    $mfield5->table = 'vtiger_crmentity_user_field';
    $mfield5->displaytype = 6;
    $mfield5->uitype = 56;
    $mfield5->typeofdata = 'C~O';
    $mfield5->quickcreate = 3;
    $mfield5->masseditable = 0;
    $block->addField($mfield5);
    
    $mfield6 = new Vtiger_Field();
    $mfield6->name = 'tags';
    $mfield6->label = 'tags';
    $mfield6->displaytype = 6;
    $mfield6->columntype = 'VARCHAR(1)';
    $mfield6->quickcreate = 3;
    $mfield6->masseditable = 0;
    $block->addField($mfield6);
    /* End 7.1.0 */
    
    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($mfield1, 3);
    
    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    
    // Webservice Setup
    $moduleInstance->initWebservice();
    
    mkdir('modules/'.$MODULENAME);
    echo "OK\n";
}