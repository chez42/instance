<?php

class cFileLocations{
    public $id, $custodian, $tenant, $file_location, $rep_code, $master_rep_code, $omni_code, $prefix, $suffix, $last_filename,
           $last_filedate, $currently_active;
}

class cFileHandling{
    private $data;

    public function __construct(){
        self::FillFileLocationData();
    }

    private function FillFileLocationData($fields = null){
        global $adb;

        $query = "SELECT id, custodian, tenant, file_location, rep_code, master_rep_code, omni_code, prefix, suffix, last_filename, 
                         last_filedate, currently_active
                  FROM custodian_omniscient.file_locations";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $tmp = new cFileLocations();
                $tmp->id = $r['id'];
                $tmp->custodian = $r['custodian'];
                $tmp->tenant = $r['tenant'];
                $tmp->file_location = $r['file_location'];
                $tmp->rep_code = $r['rep_code'];
                $tmp->master_rep_code = $r['master_rep_code'];
                $tmp->omni_code = $r['omni_code'];
                $tmp->prefix = $r['prefix'];
                $tmp->suffix = $r['suffix'];
                $tmp->last_filename = $r['last_filename'];
                $tmp->last_filedate = $r['last_filedate'];
                $tmp->currently_active = $r['currently_active'];
                $this->data[] = $tmp;
            }
        }
    }

    public function GetFileLocations(){
        return $this->data;
    }
}