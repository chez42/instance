<?php
$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

$adb = PearDatabase::getInstance();

$db = PearDatabase::getInstance();

$fieldNamesList = array();
$updateQuery = 'UPDATE vtiger_field SET fieldlabel = CASE fieldname';
$result = $db->pquery('SELECT taxname, taxlabel FROM vtiger_inventorytaxinfo', array());
while($row = $db->fetch_array($result)) {
    $fieldName = $row['taxname'];
    $fieldLabel = $row['taxlabel'];
    
    $updateQuery .= " WHEN '$fieldName' THEN '$fieldLabel' ";
    $fieldNamesList[] = $fieldName;
}
$updateQuery .= 'END WHERE fieldname in ('. generateQuestionMarks($fieldNamesList) .')';

$db->pquery($updateQuery, $fieldNamesList);
$db->pquery('UPDATE vtiger_field SET fieldlabel=? WHERE displaytype=? AND fieldname=?', array('Item Discount Amount', 5, 'discount_amount'));

$inventoryModules = getInventoryModules();
foreach ($inventoryModules as $moduleName) {
    $tabId = getTabid($moduleName);
    $blockId = getBlockId($tabId, 'LBL_ITEM_DETAILS');
    $db->pquery('UPDATE vtiger_field SET displaytype=?, block=? WHERE tabid=? AND fieldname IN (?, ?)', array(5, $blockId, $tabId, 'hdnDiscountAmount', 'hdnDiscountPercent'));
}

$itemFieldsName = array('image','purchase_cost','margin');
$itemFieldsLabel = array('Image','Purchase Cost','Margin');
$itemFieldsTypeOfData = array('V~O','N~O','N~O');
$itemFieldsDisplayType = array('56', '71', '71');
$itemFieldsDataType = array('VARCHAR(2)', 'decimal(27,8)', 'decimal(27,8)');

$fieldIdsList = array();
foreach ($inventoryModules as $moduleName) {
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    $blockInstance = Vtiger_Block::getInstance('LBL_ITEM_DETAILS', $moduleInstance);
    
    for($i=0; $i<count($itemFieldsName); $i++) {
        $fieldName = $itemFieldsName[$i];
        
        if ($moduleName === 'PurchaseOrder' && $fieldName !== 'image') {
            continue;
        }
        
        $fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if (!$fieldInstance) {
            $fieldInstance = new Vtiger_Field();
            
            $fieldInstance->name		= $fieldName;
            $fieldInstance->column		= $fieldName;
            $fieldInstance->label		= $itemFieldsLabel[$i];
            $fieldInstance->columntype	= $itemFieldsDataType[$i];
            $fieldInstance->typeofdata	= $itemFieldsTypeOfData[$i];
            $fieldInstance->uitype		= $itemFieldsDisplayType[$i];
            $fieldInstance->table		= 'vtiger_inventoryproductrel';
            $fieldInstance->presence	= '1';
            $fieldInstance->readonly	= '0';
            $fieldInstance->displaytype = '5';
            $fieldInstance->masseditable = '0';
            
            $blockInstance->addField($fieldInstance);
            $fieldIdsList[] = $fieldInstance->id;
        }
    }
}

$columns = $db->getColumnNames('vtiger_products');
if (!in_array('is_subproducts_viewable', $columns)) {
    $db->pquery('ALTER TABLE vtiger_products ADD COLUMN is_subproducts_viewable INT(1) DEFAULT 1', array());
}
$columns = $db->getColumnNames('vtiger_seproductsrel');
if (!in_array('quantity', $columns)) {
    $db->pquery('ALTER TABLE vtiger_seproductsrel ADD COLUMN quantity INT(19) DEFAULT 1', array());
}
$columns = $db->getColumnNames('vtiger_inventorysubproductrel');
if (!in_array('quantity', $columns)) {
    $db->pquery('ALTER TABLE vtiger_inventorysubproductrel ADD COLUMN quantity INT(19) DEFAULT 1', array());
}

$model = Settings_Vtiger_TermsAndConditions_Model::getInstance('Inventory');
if($model->getType()){
    $tAndC = $model->getText();
    $db->pquery('DELETE FROM vtiger_inventory_tandc', array());
    
    $inventoryModules = getInventoryModules();
    foreach ($inventoryModules as $moduleName) {
        $model = Settings_Vtiger_TermsAndConditions_Model::getInstance($moduleName);
        $model->setText($tAndC);
        $model->setType($moduleName);
        $model->save();
    }
}

$db->pquery('CREATE TABLE IF NOT EXISTS vtiger_taxregions(regionid INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL)', array());

$sql = 'CREATE TABLE IF NOT EXISTS vtiger_inventorycharges(
				chargeid INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(100) NOT NULL,
				format VARCHAR(10),
				type VARCHAR(10),
				value DECIMAL(12,5),
				regions TEXT,
				istaxable INT(1) NOT NULL DEFAULT 1,
				taxes VARCHAR(1024),
				deleted INT(1) NOT NULL DEFAULT 0
			)';
$db->pquery($sql, array());

$taxIdsList = array();
$result = $db->pquery('SELECT taxid FROM vtiger_shippingtaxinfo', array());
while ($rowData = $db->fetch_array($result)) {
    $taxIdsList[] = $rowData['taxid'];
}

$invChargesCheck = $db->pquery("SELECT * FROM vtiger_inventorycharges WHERE name = 'Shipping & Handling'");
if(!$db->num_rows($invChargesCheck))
    $db->pquery('INSERT INTO vtiger_inventorycharges VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array(1, 'Shipping & Handling', 'Flat', 'Fixed', '', '[]', 1, ZEND_JSON::encode($taxIdsList), 0));


$db->pquery('CREATE TABLE IF NOT EXISTS vtiger_inventorychargesrel(recordid INT(19) NOT NULL, charges TEXT)', array());

$shippingTaxNamesList = array();
$result = $db->pquery('SELECT taxid, taxname FROM vtiger_shippingtaxinfo', array());
while ($rowData = $db->fetch_array($result)) {
    $shippingTaxNamesList[$rowData['taxid']] = $rowData['taxname'];
}

$tablesList = array('quoteid' => 'vtiger_quotes', 'purchaseorderid' => 'vtiger_purchaseorder', 'salesorderid' => 'vtiger_salesorder', 'invoiceid' => 'vtiger_invoice');
$isResultExists = false;

$query = 'INSERT INTO vtiger_inventorychargesrel VALUES';
foreach ($tablesList as $index => $tableName) {
    $sql = "SELECT vtiger_inventoryshippingrel.*, s_h_amount FROM vtiger_inventoryshippingrel
		INNER JOIN $tableName ON $tableName.$index = vtiger_inventoryshippingrel.id";
    
    $result = $db->pquery($sql, array());
    while ($rowData = $db->fetch_array($result)) {
        $isResultExists = false;
        $recordId = $rowData['id'];
        
        $taxesList = array();
        foreach ($shippingTaxNamesList as $taxId => $taxName) {
            $taxesList[$taxId] = $rowData[$taxName];
        }
        $check = $adb->pquery("SELECT * FROM vtiger_inventorychargesrel WHERE recordid = ?",array($recordId));
        
        if(!$adb->num_rows($check)){
            $query .= "($recordId, '".Zend_Json::encode(array(1 => array('value' => $rowData['s_h_amount'], 'taxes' => $taxesList)))."'), ";
            $isResultExists = true;
        }
    }
}

if ($isResultExists) {
    $db->pquery(rtrim($query, ', '), array());
}

//Updating existing tax tables
$taxTablesList = array('vtiger_inventorytaxinfo', 'vtiger_shippingtaxinfo');
foreach ($taxTablesList as $taxTable) {
    $columns = $db->getColumnNames($taxTable);
    if (!in_array('method', $columns)) {
        $db->pquery("ALTER TABLE $taxTable ADD COLUMN method VARCHAR(10)", array());
    }
    if (!in_array('type', $columns)) {
        $db->pquery("ALTER TABLE $taxTable ADD COLUMN type VARCHAR(10)", array());
    }else if(in_array('type',$columns)){
        $db->pquery("ALTER TABLE vtiger_inventorytaxinfo CHANGE type type VARCHAR(10) NULL");
    }
    if (!in_array('compoundon', $columns)) {
        $db->pquery("ALTER TABLE $taxTable ADD COLUMN compoundon VARCHAR(400)", array());
    }
    if (!in_array('regions', $columns)) {
        $db->pquery("ALTER TABLE $taxTable ADD COLUMN regions TEXT", array());
    }
    
    $db->pquery("UPDATE $taxTable SET method =?, type=?, compoundon=?, regions=? WHERE method = '' AND type = '' AND compoundon = '' AND regions = '' ", array('Simple', 'Fixed', '[]', '[]'));
}

//Updating existing tax tables
$columns = $db->getColumnNames('vtiger_producttaxrel');
if (!in_array('regions', $columns)) {
    $db->pquery('ALTER TABLE vtiger_producttaxrel ADD COLUMN regions TEXT', array());
}
$db->pquery('UPDATE vtiger_producttaxrel SET regions=? WHERE regions = ""', array('[]'));

$modulesList = array('Quotes' => 'vtiger_quotes', 'PurchaseOrder' => 'vtiger_purchaseorder', 'SalesOrder' => 'vtiger_salesorder', 'Invoice' => 'vtiger_invoice');
$fieldName = 'region_id';

foreach ($modulesList as $moduleName => $tableName) {
    //Updating existing inventory tax tables
    $columns = $db->getColumnNames($tableName);
    if (!in_array('compound_taxes_info', $columns)) {
        $db->pquery("ALTER TABLE $tableName ADD COLUMN compound_taxes_info TEXT", array());
    }
    $db->pquery('UPDATE '.$tableName.' SET compound_taxes_info=? WHERE compound_taxes_info = ""', array('[]'));
    
    //creating new field in entity tables
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    $blockInstance = Vtiger_Block::getInstance('LBL_ITEM_DETAILS', $moduleInstance);
    
    $fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
    if (!$fieldInstance) {
        $fieldInstance = new Vtiger_Field();
        
        $fieldInstance->name = $fieldName;
        $fieldInstance->column		= $fieldName;
        $fieldInstance->table		= $tableName;
        $fieldInstance->label		= 'Tax Region';
        $fieldInstance->columntype	= 'int(19)';
        $fieldInstance->typeofdata	= 'N~O';
        $fieldInstance->uitype		= '16';
        $fieldInstance->readonly	= '0';
        $fieldInstance->displaytype	= '5';
        $fieldInstance->masseditable= '0';
        
        $blockInstance->addField($fieldInstance);
    }
}

$inventoryResult = $db->pquery('SELECT blockid FROM vtiger_settings_blocks WHERE label=?', array('LBL_INVENTORY'));
if ($db->num_rows($inventoryResult)) {
    $inventoryBlockId = $db->query_result($inventoryResult, 0, 'blockid');
    $db->pquery('UPDATE vtiger_settings_blocks SET sequence=? WHERE blockid=?', array(6, $inventoryBlockId));
} else {
    $inventoryBlockId = $db->getUniqueID('vtiger_settings_blocks');
    $db->pquery('INSERT INTO vtiger_settings_blocks(blockid, label, sequence) VALUES(?, ?, ?)', array($inventoryBlockId, 'LBL_INVENTORY', 6));
}

$inventoryFields = array(	'LBL_TAX_SETTINGS'				=> 'index.php?module=Vtiger&parent=Settings&view=TaxIndex',
    'INVENTORYTERMSANDCONDITIONS'	=> 'index.php?parent=Settings&module=Vtiger&view=TermsAndConditionsEdit');

$inventorySequence = 1;
foreach ($inventoryFields as $fieldName => $linkTo) {
    $db->pquery('UPDATE vtiger_settings_field SET sequence=?, linkto=?, blockid=? WHERE name=?', array($inventorySequence++, $linkTo, $inventoryBlockId, $fieldName));
}
