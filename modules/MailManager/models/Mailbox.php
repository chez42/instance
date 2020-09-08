<?php
chdir("/var/www/html/");
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';

ini_set('memory_limit', '-1');

set_time_limit(-1);

$adb = PearDatabase::getInstance();

getLiteratureData();
echo "rere";
exit;

function saveAccount($fieldVal, $user){
    
    $url = str_replace(array(" ", "https://", "http://", "www."), '', $fieldVal['Organization Name URL']);
    
    $result = $adb->pquery("SELECT * FROM `vtiger_account` inner join vtiger_crmentity on crmid = accountid
	WHERE (`domain` = ? or accountname  = ? )and deleted = 0", array($url, $fieldVal['Organization Name']));
    
    if($adb->num_rows($result)){
        echo $fieldVal['Organization Name'];
        echo "<br/>";
        continue;
    }
    
    $entity_data = array(
        'domain' => $url,
        'accountname' => $fieldVal['Organization Name'],
        'assigned_user_id' => vtws_getWebserviceEntityId('Users', 1),
        'industries' => explode(", ", $fieldVal['Industries']),
        'headquarters_location' => $fieldVal['Headquarters Location'],
        'description' => $fieldVal['Description'],
        'cb_rank' => str_replace(",", "", $fieldVal['CB Rank (Company)']),
        'website' => str_replace(array(" ", "https://", "http://", "www."), '', $fieldVal['Website']),
        'twitter' => str_replace(array(" ", "https://", "http://", "www."), '', $fieldVal['Twitter']),
        'facebook' => str_replace(array(" ", "https://", "http://", "www."), '', $fieldVal['Facebook']),
        'linkedin' => str_replace(array(" ", "https://", "http://", "www."), '', $fieldVal['LinkedIn']),
        'full_description' => $fieldVal['Full Description'],
        'founders' => $fieldVal['Founders'],
        'total_funding_rounds' => $fieldVal['Number of Funding Rounds'],
        'funding_status' => $fieldVal['Funding Status'],
        'last_funding_date' => $fieldVal['Last Funding Date'],
        'last_funding_amount' => $fieldVal['Last Funding Amount Currency (in USD)'],
        
        'last_funding_type' => $fieldVal['Last Funding Type'],
        'total_funding_amount' => $fieldVal['Total Funding Amount Currency (in USD)'],
        'similar_average_visits' => $fieldVal['SimilarWeb - Average Visits (6 months)'],
        
        'apptopia_apps' => $fieldVal['Apptopia - Number of Apps'],
        'aberdeen_it_spend' => $fieldVal['Aberdeen - IT Spend Currency (in USD)'],
        
        'apptopia_downloads' => $fieldVal['Apptopia - Downloads Last 30 Days'],
        'similar_global_rank' => str_replace(",", "", $fieldVal['SimilarWeb - Global Traffic Rank']),
        
        
        
        
    );
    
    
    vtws_create('Accounts', $entity_data, $user);
    
}

echo "$noOfRecord imported";


function getLiteratureData(){
    
    $user = CRMEntity::getInstance('Users');
    $user->id = $user->getActiveAdminId();
    $user->retrieve_entity_info($user->id, 'Users');
    
    $file = fopen('/var/www/html/custom/Organization-combinedFiles-12.csv', "r");
    
    $counter = 0;
    
    $data = array();
    $headers = array();
    
    $start_parse = false;
    
    while (!feof($file)) {
        
        if($counter == 0) {
            $temp_headers = fgetcsv($file,100000,',') ;
            foreach($temp_headers as $value){
                $headers[] = trim($value);
            }
        } else {
            
            $temp_data = fgetcsv($file,100000,',') ;
            
            if(!empty($temp_data)){
                
                $data = array_combine($headers, $temp_data);
                
                print_r($data);
                exit;
                
                if($data['Organization Name'] == 'Digital Axle'){
                    $start_parse = true;
                }
                
                if(!$start_parse) continue;
                
                saveAccount($data, $user);
                
                
                
            }
            
        }
        
        $counter++;
    }
    fclose($file);
    return $data;
}
?>