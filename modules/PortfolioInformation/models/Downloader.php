<?php
class PortfolioInformation_Downloader_Model extends Vtiger_Module {
    private $adb;

    public function __construct()
    {
        global $adb;
        $this->adb = $adb;
    }

    private function hasResult($result){
        global $adb;
        if($adb->num_rows($result) > 0)
            return true;
        return false;
    }
    public function GetAllRepCodes(){
        $query = "SELECT rep_code FROM custodian_omniscient.downloader_data GROUP BY rep_code";
        $result = $this->adb->pquery($query, array());
        $rep_codes = array();
        if($this->hasResult($result)){
            while($v = $this->adb->fetchByAssoc($result)) {
                $rep_codes[] = $v['rep_code'];
            }
        }

        return $rep_codes;
    }

    public function GetAllCustodians(){
        $query = "SELECT custodian FROM custodian_omniscient.downloader_data GROUP BY custodian";
        $result = $this->adb->pquery($query, array());
        $custodians = array();
        if($this->hasResult($result)){
            while($v = $this->adb->fetchByAssoc($result)) {
                $custodians[] = $v['custodian'];
            }
        }

        return $custodians;
    }

    public function GetRepCodeHistory($rep_code, $sdate, $edate){
        $params = array();
        $and = '';
        if($sdate == null)
            $sdate = GetDateMinusDays(7);
        if($edate == null)
            $edate = date("Y-m-d");

        $params[] = $sdate;
        $params[] = $edate;

        if($rep_code != 'all' && $rep_code != null){
            $and .= " AND rep_code = ?";
            $params[] = $rep_code;
        }

        $query = "SELECT rep_code, filename, date(copy_date) AS copy_date 
                  FROM custodian_omniscient.downloader_data 
                  WHERE CAST(copy_date AS DATE) BETWEEN ? AND ? 
                  {$and} ORDER BY copy_date DESC";
        $result = $this->adb->pquery($query, $params);
        $history = array();
        if($this->hasResult($result)){
            while($v = $this->adb->fetchByAssoc($result)) {
                $history[] = $v;
            }
        }

        return $history;
    }

    public function GetDatePeriods($sdate, $edate){
        $period = new DatePeriod(
            new DateTime($sdate),
            new DateInterval('P1D'),
            new DateTime($edate)
        );

        foreach ($period as $key => $value) {
            $dates[] = $value->format('Y-m-d');
        }
        $dates[] = $edate;//Doesn't include the end date, so put it in
        return $dates;
    }

}