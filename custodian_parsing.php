<?php

$parse_query['schwab']['balances'][0] = "LOAD DATA LOCAL INFILE ? INTO TABLE tmp
                                         FIELDS TERMINATED BY '|'
                                         LINES STARTING BY 'D2' TERMINATED BY '\\r\\n'";
$parse_query['schwab']['balances'][1] = "UPDATE tmp SET record_type='D2', filename = ?, insert_date = NOW(),
                                         account_number = TRIM(LEADING '0' FROM account_number), account_value = net_mv_plus_cash + margin_balance";


$parse_query['schwab']['positions'][0] = "LOAD DATA LOCAL INFILE ? INTO TABLE tmp
                             FIELDS TERMINATED BY '|' OPTIONALLY ENCLOSED BY '\"'
                             LINES TERMINATED BY '\\r\\n' IGNORE 3 LINES;";
$parse_query['schwab']['positions'][1] = "";


$parse_query['rj']['all'][0] = "LOAD DATA LOCAL INFILE ? 
                                INTO TABLE tmp
                                FIELDS TERMINATED BY '|'
                                LINES TERMINATED BY '\\n'";



$Vtiger_Utils_Log = true;
define('VTIGER6_REL_DIR', '');
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


include_once 'includes/main/WebUI.php';

$adb = PearDatabase::getInstance();

$query = "CALL custodian_omniscient.SETUP_FILE_PARSING_TABLE()";
$adb->pquery($query, array());

$query = "SET TRANSACTION ISOLATION LEVEL READ COMMITTED";
$adb->pquery($query, array());

$query = "SELECT id, filename, ftp.skeleton_table, copy_to_table, on_update_fields, ftp.custodian, ftp.table_type
          FROM custodian_omniscient.files_to_parse ftp
          JOIN custodian_omniscient.file_parsing_rules fpr ON ftp.skeleton_table = fpr.skeleton_table
          WHERE finished = 0 AND ftp.skeleton_table IS NOT NULL ORDER BY id ASC";
$to_parse_result = $adb->pquery($query, array());

if($adb->num_rows($to_parse_result) > 0){
    while($v = $adb->fetch_array($to_parse_result)){
        $fields = array();
        $flist = "";
        $list_with_addon = "";
        $custodian = $v['custodian'];
        $table_type = $v['table_type'];
        $skeleton_table = $v['skeleton_table'];
        $mapping = array();

        $query = "DROP TABLE IF EXISTS tmp";
        $result = $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE tmp LIKE custodian_omniscient.{$skeleton_table}";
        $result = $adb->pquery($query, array());

        $query = "SELECT addon_field, addon_variable FROM custodian_omniscient.file_parsing_mapping WHERE skeleton_table = ?";
        $result = $adb->pquery($query, array($v['skeleton_table']));
        if($adb->num_rows($result) > 0){
            while($a = $adb->fetch_array($result)){
                $mapping[$a['addon_field']] = $a['addon_variable'];
            }
        }
#        Username: OmniOauth
#Password: Hj#Qzx$c?2GHJ8~?
        foreach($parse_query[$custodian][$table_type] AS $a => $b){//Do the actual parsing into a temp table, and perform any extra setup
            $params = array();
            $field_params = GetParsingParams($skeleton_table, $a);
            foreach($field_params AS $c => $d){
                $params[] = $v[$d];
            }
            $adb->pquery($b, $params);
/*
            if (strpos($v, '?') !== false) {
                $adb->pquery($v, array($v['filename']));//The first query in a series will always have a filename, the rest will have nothing
            }else{
                $adb->pquery($v, array());
            }
*/
        }

        $query = "SHOW COLUMNS FROM tmp";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($a = $adb->fetch_array($result)){
                $fields[] = $a['field'];
            }
            print_r($fields);exit;
            $flist = implode(', ', $fields);//First list is the skeleton list with no addon fields
            foreach($mapping AS $c => $d){
                $fields[] = $c;
            }
            $list_with_addons = implode(', ', $fields);//List with addon now includes our new fields

            $query = "INSERT INTO custodian_omniscient.{$v['copy_to_table']} ({$list_with_addons})
                  SELECT {$flist} ";
            foreach($mapping AS $c => $d){
                if($d == "NOW()"){
                    $pst = new DateTimeZone('America/Los_Angeles');
                    $time = new DateTime('', $pst); // first argument uses strtotime parsing
                    $query .= ", '" . $time->format('Y-m-d H:i:s') . "' ";
                }else {
                    $query .= ", '{$v{$d}}' ";
                }
            }
            $query .= " FROM tmp 
                        ON DUPLICATE KEY UPDATE {$v['on_update_fields']}";

            $result = $adb->pquery($query, array());
            if($result) {
                $query = "SELECT * FROM custodian_omniscient.file_parsing_steps WHERE skeleton_table = 'rj_account_data_skeleton'";
                $steps_result = $adb->pquery($query, array());

                if($adb->num_rows($steps_result) > 0){
                    while ($s = $adb->fetch_array($steps_result)) {
                        switch ($s['todo']) {
                            case "run_query":
                                $variables = explode(',', $s['variables']);
                                $r = $adb->pquery("{$s['command']}", $variables);
                                if(!$r)
                                    echo "No result";
                                break;
                        }
                    }
                }
                $query = "UPDATE custodian_omniscient.files_to_parse SET finished = 1 WHERE id = ?";
                $adb->pquery($query, array($v['id']));
            }else{
                echo $v['filename'] . ' failed to parse<br />';
            }
        }
    }
}

function GetParsingParams($skeleton_table, $step){
    global $adb;

    $query = "SELECT * FROM custodian_omniscient.file_parsing_params WHERE skeleton_table = ? AND step = ?";
    $params_result = $adb->pquery($query, array($skeleton_table, $step));
    while($a = $adb->fetch_array($params_result)){
        $params[] = $a['field_name'];
    }
    return $params;
}

echo 'done';exit;
