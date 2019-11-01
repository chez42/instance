<?php
$Vtiger_Utils_Log = true;

define('VTIGER6_REL_DIR', '');

set_time_limit(-1);

include_once 'includes/main/WebUI.php';

global $adb;

$user = CRMEntity::getInstance("Users");
$user->id = 1;
$user->retrieve_entity_info($user->id, "Users");
vglobal("current_user", $user);

$result = $adb->pquery("SELECT * FROM `vtiger_productcategory`", array());

for($i = 0; $i < $adb->num_rows($result); $i++){
    
    $product_category_id = $adb->query_result($result, $i, "productcategoryid");
    
    $primaryKey = Vtiger_Util_Helper::getPickListId('productcategory');
    
    $pickListValues = array();
    
    $valuesOfDeleteIds = "SELECT productcategory FROM vtiger_productcategory WHERE productcategoryid IN (?)";
    
    $pickListValuesResult = $adb->pquery($valuesOfDeleteIds,array($product_category_id));
    
    $num_rows = $adb->num_rows($pickListValuesResult);
    
    for($i=0;$i<$num_rows;$i++) {
        $pickListValues[] = decode_html($adb->query_result($pickListValuesResult,$i,'productcategory'));
    }
    
    $picklistValueIdToDelete = array();
    
    $query = 'SELECT DISTINCT picklist_valueid,roleid FROM vtiger_productcategory
			  AS picklisttable LEFT JOIN vtiger_role2picklist AS roletable ON roletable.picklistvalueid = picklisttable.picklist_valueid
			  WHERE '.$primaryKey.' IN (?)';
    
    $result = $adb->pquery($query,$product_category_id);
    
    $num_rows = $adb->num_rows($result);
    
    for($i=0;$i<$num_rows;$i++) {
        
        $picklistValueId = $adb->query_result($result,$i,'picklist_valueid');
        $roleId = $adb->query_result($result,$i,'roleid');
        
        // clear cache to update with lates values
        Vtiger_Cache::delete('PicklistRoleBasedValues', 'productcategory'.$roleId);
        $picklistValueIdToDelete[$picklistValueId] = $picklistValueId;
    }
    
    $query = 'DELETE FROM vtiger_role2picklist WHERE picklistvalueid IN ('.generateQuestionMarks($picklistValueIdToDelete).')';
    $adb->pquery($query,$picklistValueIdToDelete);
    
    Vtiger_Cache::flushPicklistCache('productcategory');
    
    $query = 'DELETE FROM vtiger_productcategory WHERE productcategoryid IN (?)';
    
    $adb->pquery($query,$product_category_id);
    
}

exit;

$category_result = $adb->pquery("select DISTINCT productcategory from vtiger_products inner join vtiger_crmentity on crmid = productid where productcategory != 'None'");

$roleRecordList = Settings_Roles_Record_Model::getAll();

foreach($roleRecordList as $roleRecord) {
    $rolesSelected[] = $roleRecord->getId();
}
$moduleModel = Settings_Picklist_Module_Model::getInstance('Products');

$fieldModel = Settings_Picklist_Field_Model::getInstance('productcategory', $moduleModel);

$categories = array();

for($i = 0; $i < $adb->num_rows($category_result); $i++){
    
    $categories[] = $adb->query_result($category_result, $i, "productcategory");
    
    $product_category = $adb->query_result($category_result, $i, "productcategory");
    
    $result = $adb->pquery("SELECT * FROM `vtiger_productcategory` where productcategory = ?", array($product_category));
    
    if(!$adb->num_rows($result)){
        $moduleModel->addPickListValues($fieldModel, trim($product_category), $rolesSelected, '');
    }
    
}

$moduleModel->hand3leLabels('Products', $categories, array(), 'add');

exit;


$file = 'products.csv';

$products = process_csv($file);

foreach($products as $data){
    
    if($data['VARIETY'] == '') continue;
    
    $obj = CRMEntity::getInstance("Products");
    
    /*$product_result = $adb->pquery("select * from vtiger_products inner join vtiger_crmentity on crmid = productid
     where productname  = ? and productcategory = ? and subcategory = ? and deleted = 0", array($data['VARIETY'], $data['FLOWER'], $data['CATAGORY']));
    
    
     if($adb->num_rows($product_result)){
     //continue;
     $obj->id = $adb->query_result($product_result, 0, "productid");
     $obj->mode = "edit";
     $obj->retrieve_entity_info($obj->id, "Products");
     }*/
    
    $obj->column_fields['productname'] = $data['VARIETY'];
    
    $obj->column_fields['productcategory'] = $data['FLOWER'];
    
    $obj->column_fields['subcategory'] = $data['CATAGORY'];
    
    $obj->column_fields['vendor_id'] = 160965;
    
    $obj->column_fields['purchase_cost'] = 5;
    
    $obj->column_fields['unit_price'] = 5;
    
    $obj->column_fields['discontinued'] = 1;
    
    $obj->save("Products");
    
}


function process_csv($file) {
    
    $file = fopen($file, "r");
    
    $counter = 0;
    
    $data = array();
    $headers = array();
    
    while (!feof($file)) {
        
        if($counter == 0) {
            $temp_headers = fgetcsv($file,100000,',') ;
            foreach($temp_headers as $value){
                $headers[] = trim($value);
            }
        } else {
            
            $temp_data = fgetcsv($file,100000,',') ;
            
            if(!empty($temp_data)){
                
                $final_data = array_combine($headers, $temp_data);
                
                $data[] = $final_data;
            }
            
        }
        
        $counter++;
    }
    fclose($file);
    return $data;
}