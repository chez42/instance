<?php
include_once "libraries/custodians/cParsing.php";

class cCatalogue extends cParsing{
    public function __construct(){

    }

    public function GetFiles($directory, $extension, $num_days=null): array{
        return parent::GetFiles($directory, $extension, $num_days);
    }

    public function GetAllFiles($directory, $num_days=null): array{
        return parent::GetAllFiles($directory, $num_days);
    }

    public function WriteFiles($files, $rep_code){
        global $adb;

        $query = "INSERT IGNORE INTO custodian_omniscient.historical_catalogue (filename, directory, size, extension, created_date, 
                                                                         modified_date, num_rows, created_time, rep_code, line_1)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        foreach($files AS $k => $v){
            $file = new \SplFileObject($v->fullFile, 'r');
            $line_1 = $file->fgets();
            $file->seek(PHP_INT_MAX);
            $num_rows = $file->key() + 1;

            $adb->pquery($query, array($v->filename, $v->directory, $v->size, $v->extension, $v->createdDate, $v->modifiedDate,
                                       $num_rows, $v->createdTime, $rep_code, $line_1), true);
        }
    }

    public function FindDupeValues(array $rep_code){
        global $adb;
        $params = array();

        if(!empty($rep_code)){
            $questions = generateQuestionMarks($rep_code);
            $where = " WHERE rep_code IN ({$questions})";
            $params[] = $rep_code;
        }

        $query = "SELECT * 
                  FROM custodian_omniscient.historical_catalogue 
                  {$where}
                  ORDER BY size DESC";

        $result = $adb->pquery($query, $params);
        $token['id'] = 0;
        $token['size'] = 0;
        $dupes = array();
        $count = 0;
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
#                echo "({$count}) " . $x['size'] . ' -- ' . $token['size'] . '<br />';
                if($x['size'] == $token['size'] && $x['line_1'] == $token['line_1']){
                    $dupes[$token['id']] = $token;
                    $dupes[$x['id']] = $x;
                }
                $token = $x;
                $count++;
            }
        }
        $count = 0;
        foreach($dupes AS $k => $v){
            echo $v['filename'] . ' -- ' . substr($v['line_1'], 0, 100) . '<br />';
            $count++;
        }
        return $dupes;
    }
}