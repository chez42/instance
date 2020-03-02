<?php

class PortfolioInformation_Tools_Model extends Vtiger_Module {

    static public function GetExtensionsFromType($type){
        global $adb;

        $query = "SELECT extension FROM custodian_omniscient.extension_types WHERE type = ?";
        $result = $adb->pquery($query, array($type), true);
        $extensions = array();

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $extensions[] = $v['extension'];
            }
            return $extensions;
        }
    }

    static public function GetMissingFiles($extensions, $sdate, $edate){
        global $adb;
        $questions = generateQuestionMarks($extensions);
        $values = array();

        $query = "DROP TABLE IF EXISTS d1";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS d2";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE d1
                  SELECT directory 
                  FROM custodian_omniscient.parsing_directory_structure 
                  WHERE extension IN ({$questions})
                        AND DATE(filemtime) = ?
                  GROUP BY directory";
        $adb->pquery($query, array($extensions, $sdate), true);

        $query = "UPDATE d1 SET directory = TRIM(BOTH '/' FROM directory)";
        $adb->pquery($query, array(), true);

        $query = "CREATE TEMPORARY TABLE d2
                  SELECT directory 
                  FROM custodian_omniscient.parsing_directory_structure 
                  WHERE extension IN ({$questions})
                        AND DATE(filemtime) = ?
                  GROUP BY directory";
        $adb->pquery($query, array($extensions, $edate), true);

        $query = "UPDATE d2 SET directory = TRIM(BOTH '/' FROM directory)";
        $adb->pquery($query, array(), true);

        $query = "SELECT d1.directory FROM d1 
                  LEFT JOIN d2 ON d1.directory = d2.directory
                  WHERE d2.directory IS NULL
                  GROUP BY d1.directory";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $values[] = $v;
            }
            return $values;
        }
    }
}
