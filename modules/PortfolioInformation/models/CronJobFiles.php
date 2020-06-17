<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-07-23
 * Time: 4:29 PM
 */

class PortfolioInformation_CronJobFiles_Model extends Vtiger_Module {
    static public function GetFilesGreaterEqualThanDate($date){
        global $adb;
        $query = "SELECT last_filename, last_filedate FROM custodian_omniscient.file_locations 
                  WHERE last_filedate >= ?";
        $result = $adb->pquery($query, array($date));
        $file_info = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $file_info[] = $v;
            }
        }

        return $file_info;
    }

    static public function DetermineMatchColorsForDirectories($file_info, $date){
        if(sizeof($file_info) > 0)
            foreach($file_info AS $k => $v){
                $tmp = $v;
                if(date_format($v['last_filedate'], 'Y-m-d') >= date_format($date, 'Y-m-d')){
                    $tmp['match'] = true;
                }else{
                    $tmp['match'] = false;
                }
                $files[] = $tmp;
            }
        return $files;
    }
}