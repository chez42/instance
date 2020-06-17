<?php

class BillingParent{
    protected $portfolio_settings, $title, $client_name;
    protected $module_type;//Portfolio, Household, Contact, etc
    protected $start_period, $end_period;//The start and end dates for the report
    protected $start_amount, $end_amount;//The overall start and end values

    public function __construct($report_title = null, $client_name = null, $module_type = null, $start_period = null, $end_period = null){
        $this->title = $report_title;
        $this->client_name = $client_name;
        $this->module_type = $module_type;
        $this->SetEndPeriod($end_period);
        $this->SetStartPeriod($start_period);
    }

    public function GetStartPeriod(){
        return $this->start_period;
    }

    public function SetStartPeriod($start_period){
        $dateTime = new DateTime($start_period);
        $this->start_period = $dateTime->format("F d, Y");
    }

    public function GetEndPeriod(){
        return $this->end_period;
    }

    public function SetEndPeriod($end_period){
        $dateTime = new DateTime($end_period);
        $this->end_period = $dateTime->format("F d, Y");
    }

    public function GetModuleType(){
        return $this->module_type;
    }

    public function GetTitle(){
        return $this->title;
    }

    public function GetClientName(){
        return $this->client_name;
    }

    /*Get settings for the individual portfolio so we know how to calculate its billing*/
    private function GetSettings(){

    }

    public function CalculateStartDateValue($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $query = "CALL GET_INTERVAL_END_DATE_VALUES(\"{$questions}\", '1900-01-01', ?)";
        $adb->pquery($query, array($account_numbers, $start_date));
        $result = $adb->pquery("SELECT SUM(intervalbeginvalue) AS intervalbeginvalue FROM tmp_intervals_daily AS intervalbeginvalue");
        if($adb->num_rows($result) > 0) {
            return $adb->query_result($result, 0, 'intervalbeginvalue');
        }
    }

    public function CalculateEndDateValue($account_numbers, $start_date, $end_date){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);
        $query = "CALL GET_INTERVAL_END_DATE_VALUES(\"{$questions}\", '1900-01-01', ?)";
        $adb->pquery($query, array($account_numbers, $end_date));
        $result = $adb->pquery("SELECT SUM(intervalendvalue) AS intervalendvalue FROM tmp_intervals_daily AS intervalendvalue");
        if($adb->num_rows($result) > 0) {
            return $adb->query_result($result, 0, 'intervalendvalue');
        }
    }

    public function SetStartDateValue($account_numbers, $start_date, $end_date){
        $this->start_amount = $this->CalculateStartDateValue($account_numbers, $start_date, $end_date);
    }

    public function SetEndDateValue($account_numbers, $start_date, $end_date){
        $this->end_amount = $this->CalculateEndDateValue($account_numbers, $start_date, $end_date);
    }

    public function GetStartDateValue(){
        return $this->start_amount;
    }

    public function GetEndDateValue(){
        return $this->end_amount;
    }

}