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

    private function FillFileLocationData(){
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

    public function GetLocationDataFromID($id){
        global $adb;

        $query = "SELECT id, custodian, tenant, file_location, rep_code, master_rep_code, omni_code, prefix, suffix, last_filename, 
                         last_filedate, currently_active
                  FROM custodian_omniscient.file_locations
                  WHERE id = ?";
        $result = $adb->pquery($query, array($id));

        $tmp = new cFileLocations();
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
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
            }
        }

        return $tmp;
    }

    public function GetLocationDataFromRepCode(array $rep_code){
        global $adb;
        $questions = generateQuestionMarks($rep_code);

        $query = "SELECT id, custodian, tenant, file_location, rep_code, master_rep_code, omni_code, prefix, suffix, last_filename, 
                         last_filedate, currently_active
                  FROM custodian_omniscient.file_locations
                  WHERE rep_code IN ({$questions})";
        $result = $adb->pquery($query, array($rep_code));

        $data = array();
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
                $data[] = $tmp;
            }
        }

        return $data;
    }

    /**
     * Create new file location row
     * @param $data
     */
    public function CreateFieldRow($data){
        global $adb;
        $data = self::AutoFillData($data);
        $params = array();
        $params[] = $data['custodian'];
        $params[] = $data['tenant'];
        $params[] = $data['file_location'];
        $params[] = $data['rep_code'];
        $params[] = $data['omni_code'];
        $params[] = $data['prefix'];
        $params[] = $data['currently_active'];

        if(strlen($data['rep_code']) < 2)
            return;
        $query = "INSERT INTO custodian_omniscient.file_locations SET custodian = ?, tenant = ?, file_location = ?, rep_code = ?,
                                                                      omni_code = ?, prefix = ?, currently_active = ?";
        $adb->pquery($query, $params);
        return $adb->getLastInsertID();

    }

    /**
     * Update the file_locations table
     * @param $data
     */
    public function UpdateFieldRow($data){
        global $adb;
        $data = self::AutoFillData($data);
        $params = array();
        $params[] = $data['custodian'];
        $params[] = $data['rep_code'];
        $params[] = $data['omni_code'];
        $params[] = $data['currently_active'];
        $params[] = $data['id'];

        $query = "UPDATE custodian_omniscient.file_locations SET custodian = ?, rep_code = ?, omni_code = ?, currently_active = ? WHERE id = ?";
        $adb->pquery($query, $params, true);
    }

    /**
     * Determine what the file location should be when filling in the table
     * @param $data
     * @return string|null
     * @throws Exception
     */
    public function GetAutoFileLocation($data){
        global $adb;
        $query = "SELECT location FROM custodian_omniscient.file_location_mapping WHERE custodian = ?";
        $result = $adb->pquery($query, array($data['custodian']));
        if($adb->num_rows($result) > 0){
            $location = $adb->query_result($result, 0, 'location');
            $loc = $location . $data['rep_code'];
            return $loc;
#            $data['file_location'] = $location . $data['rep_code'];
        }
        return null;
    }

    /**
     * Logically fill data with proper file location, prefix, and tenant
     * @param $data
     * @return mixed
     */
    private function AutoFillData($data){
        $data['tenant'] = 'Omniscient';
        if(strlen($data['file_location']) < 5){
            $data['file_location'] = self::GetAutoFileLocation($data);
        }
        $data['prefix'] = strtolower($data['rep_code']) . '_';

        return $data;
    }

    public function AutoInsertOrUpdateData($data){
        $data = self::AutoFillData($data);
        if(strlen(TRIM($data['id'])) > 0){
            self::UpdateFieldRow($data);
        }else{
            return self::CreateFieldRow($data);
        }
    }

    public function ResetGoodRepCodeList(){
        global $adb;
        $query = "DELETE FROM live_omniscient.vtiger_good_rep_codes WHERE rep_code NOT IN (SELECT rep_code FROM custodian_omniscient.file_locations WHERE currently_active = 1)";
        $adb->pquery($query, array());
        $query = "INSERT IGNORE INTO live_omniscient.vtiger_good_rep_codes SELECT rep_code FROM custodian_omniscient.file_locations WHERE currently_active = 1";
        $adb->pquery($query, array());
    }
}