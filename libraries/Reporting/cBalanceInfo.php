<?php
class cBalanceInfo{
    public $start_value, $end_value;
    public $date_requested;//The requested date to check data for
    /*End of day date represents the day's end value. Previous end of day date is the last known date value before the passed in date
      If January 1st is passed in (a holiday for example), the previous end of day date would be December 31st and the start value
      would be the end of day value for December 31st -- Meaning January 1st started with what December 31st ended with.
      Because it is a holiday, the the end of day date would have to be January 2nd and the end of day value of course would be for
      January 2nd
    */
    public $previous_end_of_day_date, $end_of_day_date;

    public function __construct($account_number, $date){
        $this->date_requested = $date;
        $map = new CustodianClassMapping($account_number);

        $data = $map->portfolios::GetBeginningBalanceAsOfDate(array($account_number), $date);
        $this->SetStartInfo($data[$account_number]['date'], $data[$account_number]['value']);

        $data = $map->portfolios::GetEndingBalanceAsOfDate(array($account_number), $date);
        $this->SetEndInfo($data[$account_number]['date'], $data[$account_number]['value']);
    }

    protected function SetStartInfo($date, $value){
        $this->previous_end_of_day_date = $date;
        $this->start_value = $value;
    }

    protected function SetEndInfo($date, $value){
        $this->end_of_day_date = $date;
        $this->end_value = $value;
    }
}