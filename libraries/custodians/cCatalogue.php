<?php
include_once "libraries/custodians/cParsing.php";

class cCatalogue extends cParsing{
    public function __construct(){

    }

    public function GetFiles($directory, $extension, $num_days=null): array{
        return parent::GetFiles($directory, $extension, $num_days);
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
}