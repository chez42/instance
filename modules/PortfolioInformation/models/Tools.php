<?php
include_once("libraries/Reporting/ReportCommonFunctions.php");
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

    static public function GetFileInfoFromTypeAndDates($type, $sdate, $edate){
        global $adb;

        $query = "DROP TABLE IF EXISTS extensions";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS file_results";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS file_results_tmp";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE extensions
                  SELECT extension FROM custodian_omniscient.extension_types t
                  WHERE t.type = ?";
        $adb->pquery($query, array($type));

        $query = "CREATE TEMPORARY TABLE file_results_tmp
                  SELECT DATE(p.filemtime) AS file_date, p.*, TRIM(BOTH '/' FROM p.directory) AS link_directory, SUBSTRING_INDEX(TRIM(BOTH '/' FROM p.directory), '/', -1) AS folder
                  FROM custodian_omniscient.parsing_directory_structure p
                  JOIN extensions e ON e.extension = p.extension
                  WHERE filemtime BETWEEN ? AND ?
                  GROUP BY p.directory, p.extension, DATE(p.filemtime), p.size";
        $adb->pquery($query, array($sdate, $edate), true);

        $query = "CREATE TEMPORARY TABLE file_results
                  SELECT p.*, fl.id AS directoryid, fl.rep_code
                  FROM file_results_tmp p
                  JOIN custodian_omniscient.file_locations fl ON SUBSTRING_INDEX(TRIM(BOTH '/' FROM fl.file_location), '/', -1) = p.folder";
        $adb->pquery($query, array());

/*
        $query = "CREATE TEMPORARY TABLE file_results
                  SELECT fl.id AS directoryid, fl.rep_code, DATE(p.filemtime) AS file_date, p.*, TRIM(BOTH '/' FROM p.directory) AS link_directory
                  FROM custodian_omniscient.parsing_directory_structure p
                  JOIN extensions e ON e.extension = p.extension
                  JOIN custodian_omniscient.file_locations fl ON TRIM(BOTH '/' FROM fl.file_location) = TRIM(BOTH '/' FROM p.directory)
                  WHERE filemtime BETWEEN ? AND ?
                  GROUP BY p.directory, p.extension, DATE(p.filemtime), p.size
                  ORDER BY directoryid ASC";
        $adb->pquery($query, array($sdate, $edate), true);
*/
        $query = "SELECT * FROM file_results";
        $result = $adb->pquery($query, array(), true);
        $file_info = array();

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $file_info[$v['file_date']][] = $v;
            }
            return $file_info;
        }
        return 0;
    }

    /**
     * Returns a list of directories that exists on date 1 but not in date 2
     * @param $extensions
     * @param $sdate
     * @param $edate
     * @return array
     */
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
                  GROUP BY directory";//Get all files of extension type for passed in day
        $adb->pquery($query, array($extensions, $sdate), true);

        $query = "UPDATE d1 SET directory = TRIM(BOTH '/' FROM directory)";//Remove the file and get the directory
        $adb->pquery($query, array(), true);

        $query = "CREATE TEMPORARY TABLE d2
                  SELECT directory 
                  FROM custodian_omniscient.parsing_directory_structure 
                  WHERE extension IN ({$questions})
                        AND DATE(filemtime) = ?
                  GROUP BY directory";//Get all files of extension type for passed in day
        $adb->pquery($query, array($extensions, $edate), true);

        $query = "UPDATE d2 SET directory = TRIM(BOTH '/' FROM directory)";//Remove the file and get the directory
        $adb->pquery($query, array(), true);

        $query = "SELECT d1.directory FROM d1 
                  LEFT JOIN d2 ON d1.directory = d2.directory
                  WHERE d2.directory IS NULL
                  GROUP BY d1.directory";//Get a list of directories that exists on date 1 that don't on date 2
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $values[] = $v;
            }
            return $values;
        }
    }

    static public function GetMissingFilesBetween($extensions, $sdate, $edate){
        global $adb;
        $questions = generateQuestionMarks($extensions);
        $values = array();

        $query = "DROP TABLE IF EXISTS d1";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE d1
                  SELECT directory 
                  FROM custodian_omniscient.parsing_directory_structure 
                  WHERE extension IN ({$questions})
                        AND DATE(filemtime) = ?
                  GROUP BY directory";//Get all files of extension type for passed in day
        $adb->pquery($query, array($extensions, $sdate), true);

        $query = "UPDATE d1 SET directory = TRIM(BOTH '/' FROM directory)";//Remove the file and get the directory
        $adb->pquery($query, array(), true);

        $tmp_date = $sdate;
        $end_result = array();

        while(strtotime($tmp_date) <= strtotime($edate)){
            $query = "DROP TABLE IF EXISTS d2";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE d2
                      SELECT directory 
                      FROM custodian_omniscient.parsing_directory_structure 
                      WHERE extension IN ({$questions})
                      AND DATE(filemtime) = ?
                      GROUP BY directory";//Get all files of extension type for passed in day
            $adb->pquery($query, array($extensions, $edate), true);

            $query = "UPDATE d2 SET directory = TRIM(BOTH '/' FROM directory)";//Remove the file and get the directory
            $adb->pquery($query, array(), true);

            $query = "SELECT d1.directory FROM d1 
                      LEFT JOIN d2 ON d1.directory = d2.directory
                      WHERE d2.directory IS NULL
                      GROUP BY d1.directory";//Get a list of directories that exists on date 1 that don't on date 2
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                while($v = $adb->fetchByAssoc($result)){
                    $values[] = $v;
                }
                $end_result[$tmp_date] = $values;
            }
            $tmp_date = GetDatePlusOneDay($tmp_date);
        }
        return $end_result;
    }

    static public function GetCalendarRepCodes(){
        global $adb;
        $query = "SELECT rep_code FROM file_results GROUP BY rep_code";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rep_codes[] = $v;
            }
            return $rep_codes;
        }
        return 0;
    }

    static public function GetCalendarRepCodesFromDates($sdate, $edate){
        global $adb;
        $query = "SELECT fl.id, fl.rep_code
                  FROM custodian_omniscient.parsing_directory_structure p
                  JOIN custodian_omniscient.file_locations fl ON TRIM(BOTH '/' FROM fl.file_location) = TRIM(BOTH '/' FROM p.directory)
                  WHERE filemtime BETWEEN ? AND ?
                  GROUP BY fl.rep_code";
        $result = $adb->pquery($query, array($sdate, $edate));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rep_codes[$v['id']] = $v['rep_code'];
            }
            return $rep_codes;
        }
        return 0;
    }

    static public function GetRepCodesWithFilesFromDate($date){
        global $adb;
        $query = "SELECT fl.id, fl.rep_code
                  FROM custodian_omniscient.parsing_directory_structure p
                  JOIN custodian_omniscient.file_locations fl ON TRIM(BOTH '/' FROM fl.file_location) = TRIM(BOTH '/' FROM p.directory)
                  WHERE filemtime = ?
                  GROUP BY fl.id";
        $result = $adb->pquery($query, array($date));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rep_codes[$v['id']] = $v['rep_code'];
            }
            return $rep_codes;
        }
        return 0;
    }

    /**
     * Get rep codes from parsing_directory_structure table that were parsed within passed in dates
     * @param $sdate
     * @param $edate
     * @return int
     */
    static public function GetRepCodesWithFilesFromDates($sdate, $edate){
        global $adb;
        $query = "SELECT fl.id, fl.rep_code
                  FROM custodian_omniscient.parsing_directory_structure p
                  JOIN custodian_omniscient.file_locations fl ON TRIM(BOTH '/' FROM fl.file_location) = TRIM(BOTH '/' FROM p.directory)
                  WHERE filemtime >= ? AND filemtime <= ?
                  GROUP BY fl.id";
        $result = $adb->pquery($query, array($sdate, $edate));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rep_codes[$v['id']] = $v['rep_code'];
            }
            return $rep_codes;
        }
        return 0;
    }

    /**
     * Get all rep codes marked as "Active"
     */
    static public function GetActiveRepCodes(){
        global $adb;
        $query = "SELECT id, rep_code
                  FROM custodian_omniscient.file_locations 
                  WHERE currently_active = 1";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rep_codes[$v['id']] = $v['rep_code'];
            }
            return $rep_codes;
        }
        return 0;
    }

    static public function GetRepCodeFileInfo($rep_code, $sdate, $edate){
        global $adb;
        $query = "SELECT p.*
                  FROM custodian_omniscient.parsing_directory_structure p
                  JOIN custodian_omniscient.file_locations fl ON TRIM(BOTH '/' FROM fl.file_location) = TRIM(BOTH '/' FROM p.directory)
                  WHERE filemtime >= ? AND filemtime <= ?
                  AND fl.rep_code = ?";
        $result = $adb->pquery($query, array($sdate, $edate, $rep_code));
        $file_info = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $file_info = $v;
            }
            return $file_info;
        }
        return 0;
    }
}
