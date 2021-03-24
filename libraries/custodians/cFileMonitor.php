<?php
include_once "libraries/custodians/cCatalogue.php";

class cDaysFileData{
    public $date, $file_data;
}

class cFileMonitor extends cCatalogue{
    protected $unarchived, $archived;

    public function __construct(){
        parent::__construct();
    }

    public function SetFileHistoryFromRepCode($rep_code, $num_days){
        if(is_array($rep_code))//Individual rep codes only
            return;

        $locations = new cFileHandling();
        $data = $locations->GetLocationDataFromRepCode(array($rep_code));

        foreach($data AS $k => $v){
            $unarchived = $this->GetAllFiles($v->file_location, $num_days);
            $archived = $this->GetAllFiles($v->archive, $num_days);
        }

        foreach($unarchived AS $v){
            $this->unarchived[$v->modifiedDate][] = $v;
        }

        foreach($archived AS $v){
            $this->$archived[$v->modifiedDate][] = $v;
        }
    }

    public function GetUnarchivedHistory(){
        return $this->unarchived;
    }

    public function GetArchivedHistory(){
        return $this->archived;
    }
}