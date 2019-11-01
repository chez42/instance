<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-10-05
 * Time: 1:16 PM
 */

define("TRAILING_0", 0);
define("TRAILING_1", 1);
define("TRAILING_2", 2);
define("TRAILING_3", 3);
define("TRAILING_4", 4);
define("TRAILING_5", 5);
define("TRAILING_6", 6);
define("TRAILING_7", 7);
define("TRAILING_8", 8);
define("TRAILING_9", 9);
define("TRAILING_10", 10);
define("TRAILING_11", 11);
define("TRAILING_12", 12);

class Calendar{
    public $year, $month, $month_name;
}

function GetDateMinusMonthsSpecified($date, $num_months){
    return date("Y-m-d",(strtotime("{$date} -{$num_months} months")));
}

function GetDateMinusMonths($num_months){
    return date("Y-m-d", strtotime("TODAY -{$num_months} months"));
}

function GetDateMinusDays($num_days){
    return date("Y-m-d", strtotime("TODAY -{$num_days} day"));
}

function GetDateMinusOneDay($date){
    return date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
}

function GetDatePlusOneDay($date){
    return date('Y-m-d',(strtotime ( '+1 day' , strtotime ( $date) ) ));
}

function GetDateStartOfYear(){
    return date('Y') . '-01-01';
}

function GetFirstDayLastYear(){
    return date("Y-m-d",strtotime("last year January 1st"));
}

function GetLastDayLastYear(){
    return date("Y-m-d",strtotime("last year December 31st"));
}

function GetLastDayLastMonth(){
    return date("Y-m-d", strtotime("last day of last month"));
}

function GetFirstDayMinusNumberOfMonthsFromEndOfLastMonth($numMonths){
    return date("Y-m-d", strtotime("first day of last month -{$numMonths} month"));
}

function GetFirstDayLastMonthLastYear(){
    return date("Y-m-d", strtotime("first day of last month -1 year"));
}

function GetFirstDayLastMonth(){
    return date("Y-m-d", strtotime("first day of last month"));
}

function GetFirstDayThisMonthLastYear(){
    return date("Y-m-d", strtotime("first day of this month -1 year"));
}

function GetDateMinusOneWorkingDay($date){
    return date('Y-m-d',(strtotime ( '-1 weekdays' , strtotime ( $date) ) ));
}

function GetFirstOfMonthFromMonthYear($date){
    return date('Y-m-d',(strtotime ( 'first day of ' . $date )));
}

function GetFirstWeekdayDateFromMonthYear($date){
    return date('Y-m-d',(strtotime ( 'first weekday ' . $date )));
}


/**
 * Returns the beginning of the month, minus one year
 */
function GetDatePreviousYearBeginningOfMonth(){
    return date('Y-m-d', strtotime('first day of this month last year'));
    #return date('Y-m-d', strtotime('last day of previous month'));
}

function GetDateEndOfLastMonth(){
    return date('Y-m-d', strtotime('last day of previous month'));
}

function GetDateFirstOfThisMonth(){
    return date('Y-m-d', strtotime('first day of this month'));
}

function GetDateLastOfThisMonthPlusOneYear(){
    return date('Y-m-d', strtotime('last day of this month next year'));
}

function GetDateLastOfPreviousMonthPlusOneYear(){
    return date('Y-m-d', strtotime('last day of previous month next year'));
}

function CreateMonthlyCalendar($start_date, $end_date){
    $return = array();
    $begin = new DateTime( $start_date );
    $end = new DateTime( $end_date );

    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($begin, $interval, $end);

    foreach ( $period as $dt ){
        $tmp = new Calendar();
        $tmp->year = $dt->format( "Y" );
        $tmp->month = $dt->format( "m" );
        $tmp->month_name = $dt->format( "M" );
        $return[] = $tmp;
    }
    return $return;
}

function GetClientNameFromRecord($record_id){
    $calling_instance = Vtiger_Record_Model::getInstanceById($record_id);
    $module_name = $calling_instance->getModule()->getName();
    $data = $calling_instance->getData();

    switch(strtoupper($module_name)){
        case "PORTFOLIOINFORMATION":
            $client_name = str_replace(' ', '', $data['first_name']) . str_replace(' ', '', $data['last_name']);
            return $client_name;
            break;
        case "CONTACTS":
            $client_name = str_replace(' ', '', $data['firstname']) . str_replace(' ', '', $data['lastname']);
            return $client_name;
            break;
        case "ACCOUNTS":
            $client_name = str_replace(' ', '', $data['accountname']);
            return $client_name;
            break;
    }
}

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

function IsTimeBetween($start_time, $end_time, $check_time){
    $date1 = DateTime::createFromFormat('H:i:s', $check_time);
    $date2 = DateTime::createFromFormat('H:i:s', $start_time);
    $date3 = DateTime::createFromFormat('H:i:s', $end_time);
    if ($date1 > $date2 && $date1 < $date3)
        return true;

    return false;
}