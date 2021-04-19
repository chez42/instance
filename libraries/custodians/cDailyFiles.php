<?php
include_once "libraries/custodians/cCatalogue.php";

#class cDaysFileData{
#    public $date, $file_data;
#}

class cDailyFiles extends cParsing{
    protected $unarchived, $archived;
    protected $data;
    protected $files;

    public function __construct($rep_code){
        parent::__construct();
        $loc = new cFileHandling();
        $this->data = $loc->GetFileLocations()[$rep_code];//This sets the data variable of the parent class
    }

    public function GetLocationData(){
        return $this->data;
    }




    /*    public function SetFileHistoryFromRepCode($rep_code, $num_days){
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
        }*/
}