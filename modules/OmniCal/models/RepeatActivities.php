<?php

class OmniCal_RepeatActivities_Model extends Vtiger_Module_Model{   
    public function __construct() {
        parent::__construct();
    }
    
    public static function IsActivityRecurring($activity_id){
        global $adb;
        $query = "SELECT recurringid FROM vtiger_recurringevents WHERE activityid = ?";
        $result = $adb->pquery($query, array($activity_id));
        if($adb->num_rows($result) > 0)
            return true;
        return false;
    }

    /**
     * Get repeating events based on the start/end dates passed in and user requirements.  The returned date/times are user local times, not UTC
     * @global type $adb
     * @param type $start_date
     * @param type $end_date
     * @param type $user_filter
     * @return int
     */
    public static function GetRepeatingActivities($start_date, $end_date, $user_filter, $request=null){
        global $adb;
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $questions = generateQuestionMarks($user_filter);
        $query = "SELECT re.*, a.time_start, a.time_end, a.subject FROM vtiger_recurringevents re
                  JOIN vtiger_activity a ON a.activityid = re.activityid
                  JOIN vtiger_crmentity e ON e.crmid = a.activityid
                  WHERE e.smownerid IN ({$questions})
                  AND e.deleted = 0";
        $result = $adb->pquery($query, array($user_filter));
        $repeating_activities = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $dateTimeFieldInstance = new DateTimeField($v['recurringdate'] . ' ' . $v['time_start']);
                $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
                $dateTimeComponents = explode(' ',$userDateTimeString);
                $dateComponent = $dateTimeComponents[0];
                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
                $true_start = $v['recurringdate'];
                $true_end = $v['recurringenddate'];
//                $item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];
                if($start_date > $dataBaseDateFormatedString)
                    $v['recurringdate'] = $start_date;
                else
                    $v['recurringdate'] = $dataBaseDateFormatedString;
                $v['time_start'] = $dateTimeComponents[1];

//OLD WAY WAS CAUSING DAYLIGHT SAVING TIME ISSUES                $dateTimeFieldInstance = new DateTimeField($v['recurringenddate'] . ' ' . $v['time_end']);
                $dateTimeFieldInstance = new DateTimeField($v['recurringenddate'] . ' ' . $v['time_end']);
                $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
                $dateTimeComponents = explode(' ',$userDateTimeString);
                $dateComponent = $dateTimeComponents[0];
                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));

                if(strtotime($end_date) > strtotime($dataBaseDateFormatedString)){
                    $v['recurringenddate'] = $dataBaseDateFormatedString;
                    $v['time_end'] = $dateTimeComponents[1];
                }
                else{
                    /**
                     * Without this section here, daylight savings time kills us if the date is >= November 1st.  
                     */
                    $dateTimeFieldInstance = new DateTimeField($end_date . ' ' . $v['time_end']);
                    $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
                    $dateTimeComponents = explode(' ',$userDateTimeString);
                    $dateComponent = $dateTimeComponents[0];
                    $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
                    $v['recurringenddate'] = $dataBaseDateFormatedString;
                    $v['time_end'] = $dateTimeComponents[1];
/*global $bb;
if($v['activityid'] == '1800309'){*/
        $timezone = new DateTimeZone($currentUser->get('time_zone')); 
        $startDST = DateTime::createFromFormat("Y-m-d", $true_start, $timezone);
        $endDST   = DateTime::createFromFormat("Y-m-d", $end_date, $timezone);
        $end_time = DateTime::createFromFormat("H:i:s", $v['time_end']);
        $start_savings = $startDST->format("I");//0 if winter, 1 if summer
        $end_savings   = $endDST->format("I");//0 if winter, 1 if summer
        if(!$start_savings && $end_savings){//We started in winter, but now it is summer... subtract an hour
            $end_time->sub( new DateInterval('PT1H') );
            $v['time_end'] = $end_time->format("H:i:s");
        }else if(!$end_savings && $start_savings){//We started in summer, but now it is winter... Add an hour
        //THIS LOGIC CANNOT BE APPLIED UNTIL WE ARE ACTUALLY IN WINTER TO SEE IT IN ACTION
            $end_time->add( new DateInterval('PT1H'));
            $v['time_end'] = $end_time->format("H:i:s");
        }
    
//    strtotime($v['time_end'])
/*        
    $message = "TRUE START: {$true_start}, END: {$true_end}, viewing end date: {$end_date}, Start DST: {$start_savings}, End DST: {$end_savings}, Time End: {$v['time_end']}";
    $bb->SendInfo("message",$message);
}*/
                }
//                $item['end']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

                $tmp = array('activityid' => $v['activityid'],
                             'recurringdate' => $v['recurringdate'],
                             'recurringtype' => $v['recurringtype'],
                             'recurringinfo' => $v['recurringinfo'],
                             'recurringenddate' => $v['recurringenddate'],
                             'recurringfrequency' => $v['recurringfreq'],
                             'truestart' => $true_start,
                             'trueend' => $true_end,
                             'viewing_month' => $request->get('viewing_month'),
                             'viewing_year' => $request->get('viewing_year'),
                             'currently_viewing_start' => $start_date,
                             'currently_viewing_end' => $end_date,
                             'subject' => $v['subject'],
                             'time_start' => $v['time_start'],
                             'time_end' => $v['time_end']);
                $repeating_activities[] = $tmp;
            }
        } else
            return 0;

        return $repeating_activities;
    }
        
    public static function SetIgnoreDates($parent_activity, $start_date){
        global $adb;
        $query = "INSERT INTO vtiger_recurringignore (parent_id, start_date)
                  VALUES (?, ?)";
        $adb->pquery($query, array($parent_activity, $start_date));
    }
    
    public static function GetIgnoreDates($activity_id){
        global $adb;
        $query = "SELECT start_date FROM vtiger_recurringignore WHERE parent_id = ?";
        $result = $adb->pquery($query, array($activity_id));
        $dates = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $dates[] = $v['start_date'];
            }
            return $dates;
        }
        return array();
    }
    
    public static function SaveMonthlyRecurring($activity_id, $info){
        global $adb;
        if($info['monthly_type'] == 'AbsoluteMonthly'){
            $query = "INSERT INTO vtiger_recurringevents(activityid, recurringdate, recurringtype, recurringfreq, recurringinfo, recurringenddate)
                      VALUES (?, ?, ?, ?, ?, ?)";
            $converted_start = date('Y-m-d', strtotime($info['recurring_start']));
            $converted_end = date('Y-m-d', strtotime($info['recurring_end']));
            $adb->pquery($query, array($activity_id, $converted_start, 'AbsoluteMonthly', $info['absolute_frequency'], $info['day_number'], $converted_end));
        }
        if($info['monthly_type'] == 'RelativeMonthly'){
            $query = "INSERT INTO vtiger_recurringevents(activityid, recurringdate, recurringtype, recurringfreq, recurringinfo, recurringenddate)
                      VALUES (?, ?, ?, ?, ?, ?)";
            $converted_start = date('Y-m-d', strtotime($info['recurring_start']));
            $converted_end = date('Y-m-d', strtotime($info['recurring_end']));
            $recurringinfo = $info['on_the'] . " " . $info['relative_day'];
            $adb->pquery($query, array($activity_id, $converted_start, 'RelativeMonthly', $info['relative_frequency'], $recurringinfo, $converted_end));
        }
    }
    
    public static function SaveRecurringInfo($activity_id, $info){
        global $adb;
        $query = "DELETE FROM vtiger_recurringevents WHERE activityid = ?";//Delete the current recurring info
        $adb->pquery($query, array($activity_id));

        if($info['is_recurring'] == 'true'){//If its supposed to be recurring
            if($info['type'] == 'Daily'){
                $query = "INSERT INTO vtiger_recurringevents (activityid, recurringdate, recurringtype, recurringfreq, recurringinfo, recurringenddate)
                          VALUES (?, ?, ?, ?, ?, ?)";
                $converted_start = date('Y-m-d', strtotime($info['recurring_start']));
                $converted_end = date('Y-m-d', strtotime($info['recurring_end']));
                $adb->pquery($query, array($activity_id, $converted_start, 'Daily', 1, 'Daily', $converted_end));
            }
            if($info['type'] == 'Weekly'){
                $query = "INSERT INTO vtiger_recurringevents (activityid, recurringdate, recurringtype, recurringfreq, recurringinfo, recurringenddate)
                          VALUES (?, ?, ?, ?, ?, ?)";
                $converted_start = date('Y-m-d', strtotime($info['recurring_start']));
                $converted_end = date('Y-m-d', strtotime($info['recurring_end']));
                foreach($info['day_list'] AS $k => $v){
                    $days .= $v . " ";
                }
                $adb->pquery($query, array($activity_id, $converted_start, 'Weekly', $info['recurringInterval'], $days, $converted_end));
            }
            if($info['type'] == 'Monthly'){
                self::SaveMonthlyRecurring($activity_id, $info);
            }
        }
    }
    
    public static function GetRecurringInfo($activity_id){
        global $adb;
        $query = "SELECT * from vtiger_recurringevents WHERE activityid = ?";
        $result = $adb->pquery($query, array($activity_id));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info = array("activityid" => $v['activityid'],
                              "recurringdate" => $v['recurringdate'],
                              'recurringtype' => $v['recurringtype'],
                              'recurringinfo' => $v['recurringinfo'],
                              'recurringenddate' => $v['recurringenddate'],
                              'recurringfrequency' => $v['recurringfreq']);
                return $info;
            }
        }
    }
    
    public static function DailyLoop($recurring_info, $cssClass, $color, $textColor){
        $count = 1;
        $begin = new DateTime( $recurring_info['recurringdate'] );
//        $begin->add(new DateInterval("P1D"));//Avoid dupes on the first day
        $end = new DateTime( $recurring_info['recurringenddate'] );
        $end->add(new DateInterval("P1D"));
        $days_of_week = explode(' ', $recurring_info['recurringinfo']);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $dates = self::GetIgnoreDates($recurring_info['activityid']);
        $result = array();
        foreach ( $period as $dt ){
            if(!in_array($dt->format( "Y-m-d" ), $dates)){
/*                $st = $dt->format("Y-m-d");
                $en = $dt->format("Y-m-d");
                echo "APPLYING FOR: {$st} -- {$en}";
                print_r($recurring_info);
                echo "<br /><br />";*/
                $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
                $tmp['index'] = $count;
                $result[] = $tmp;
            }
            $count++;
        }
        return $result;
    }
    
    public static function WeekLoop($recurring_info, $cssClass, $color, $textcolor){
        $start = new DateTime( $recurring_info['recurringdate'] );
//        $start->add(new DateInterval("P1D"));//Avoid dupes on the first day
        $end = new DateTime( $recurring_info['recurringenddate'] );
//        $end->add(new DateInterval("P1D"));
        $days_of_week = explode(' ', $recurring_info['recurringinfo']);
        
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        $dates = self::GetIgnoreDates($recurring_info['activityid']);
        
        // initialize fake week
        $fakeWeek = 0;
        $count = 2;
        $currentWeek = $start->format('W');
//global $adb;
        foreach ($period as $date) {
            if ($date->format('W') !== $currentWeek) {
                $currentWeek = $date->format('W');
                $fakeWeek++;
//                print ' WEEK ' . $currentWeek . '<br/>';
            }
            
            if ($fakeWeek % $recurring_info['recurringfrequency'] !== 0) {
                continue;
            }

            $dayOfWeek = $date->format('l');
            if(in_array($dayOfWeek, $days_of_week)){
//            if ($dayOfWeek == 'Monday' || $dayOfWeek == 'Wednesday' || $dayOfWeek == 'Friday') {
//                print $date->format('Y-m-d H:i:s') . '   ' . $dayOfWeek . '<br/>';
                $tmp = self::ApplyDate($recurring_info, $date, $cssClass, $color, $textColor);
                $tmp['index'] = $count;
                $result[] = $tmp;
                $count++;
//$hey = "greater...START DATE: {$start_date}, ACTIVITYID: {$v['activityid']}, recurringdate: {$dataBaseDateFormatedString}";
/*$hey = "APPLYING: " . json_encode($recurring_info);
$query = "INSERT INTO debug (info) values (?)";
$adb->pquery($query, array($hey));*/

            }
        }
        
        return $result;
    }
    
    public static function CheckInRange($start_date, $end_date, $date_from_user)
    {
      // Convert to timestamp
      $start_ts = strtotime($start_date);
      $end_ts = strtotime($end_date);
      $user_ts = strtotime($date_from_user);

      // Check that user date is between start & end
      return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
    
    /**
     * Using the frequency of 2 dates, return which months to show
     * @param type $start_date
     * @param type $end_date
     * @param type $frequency
     * @return type
     */
    public static function DetermineMonthsWithFrequency($start_date, $end_date, $frequency){
        $start = new DateTime($start_date);
        $end   = new DateTime($end_date);
        $interval = DateInterval::createFromDateString("{$frequency} month");
        $period = new DatePeriod($start, $interval, $end);
        $months = array();
        foreach($period AS $dt){
            $months[] = $dt->format('m');
//            print_r($dt);
//            echo "<br />";
        }
        return $months;
    }
    
    public static function AbsoluteMonthlyLoop($recurring_info, $cssClass, $color, $textcolor){
///        echo $recurring_info['activityid'];
///        print_r($recurring_info);
        //Get a list of months for the activity up to the month being viewed in the calendar
        $months = self::DetermineMonthsWithFrequency($recurring_info['truestart'], $recurring_info['recurringenddate'], $recurring_info['recurringfrequency']);
        $viewed_month = $recurring_info['viewing_month'];
        
        $begin = new DateTime( $recurring_info['recurringdate'] );
        $begin->add(new DateInterval("P1D"));//Avoid dupes on the first day
        $end = new DateTime( $recurring_info['recurringenddate'] );
        $end->add(new DateInterval("P1D"));
        $days_of_week = explode(' ', $recurring_info['recurringinfo']);
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $dates = self::GetIgnoreDates($recurring_info['activityid']);
        $result = array();
        $recurring_day = new DateTime($recurring_info['truestart']);
        $d = $recurring_day->format('d');

        foreach($period AS $dt){
            if($d == $dt->format('d') && !in_array($dt->format('Y-m-d'), $dates)){//We are on the proper day, check if on the proper month
                if(in_array($dt->format('m'), $months)){
                    $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
                    $tmp['index'] = $count;
                    $result[] = $tmp;
                }
            }
        }
/*        if(self::CheckInRange($begin, $end, $recurring_info['truestart']) && !in_array($recurring_info['truestart'], $dates)){
            $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
            $tmp['index'] = $count;
            $result[] = $tmp;
            $count++;          
        }*/

/*        print_r($recurring_info); echo "<br /><br />";
        
        foreach ( $period as $dt ){
            if(!in_array($dt->format( "Y-m-d" ), $dates)){
                $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
                $tmp['index'] = $count;
                $result[] = $tmp;
                $count++;
            }
        }*/
        return $result;
    }
    
    public static function RelativeMonthlyLoop($recurring_info, $cssClass, $color, $textcolor){
//        echo $recurring_info['activityid'];
//        print_r($recurring_info);
        //Get a list of months for the activity up to the month being viewed in the calendar
        $months = self::DetermineMonthsWithFrequency($recurring_info['truestart'], $recurring_info['recurringenddate'], $recurring_info['recurringfrequency']);
        $viewed_month = $recurring_info['viewing_month'];
        $viewed_year = $recurring_info['viewing_year'];
        $view = new DateTime(date("1-{$viewed_month}-{$viewed_year}"));

        $begin = new DateTime( $recurring_info['recurringdate'] );
        $begin->add(new DateInterval("P1D"));//Avoid dupes on the first day
        $end = new DateTime( $recurring_info['recurringenddate'] );
        $end->add(new DateInterval("P1D"));
        $relative_data = explode(' ', $recurring_info['recurringinfo']);
        $on_the = $relative_data[0];
        $day = $relative_data[1];
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $dates = self::GetIgnoreDates($recurring_info['activityid']);
        $result = array();
        $recurring_day = new DateTime($recurring_info['truestart']);
//echo $view->format("m-01-Y");
//echo $on_the . " " . $day;
        $d = $view->modify($on_the . ' ' . $day)->format('d');
//echo "D: {$d} -- FOR: {$recurring_info['activityid']}<br />";
        foreach($period AS $dt){
            if($d == $dt->format('d') && !in_array($dt->format('Y-m-d'), $dates)){//We are on the proper day, check if on the proper month
                if(in_array($dt->format('m'), $months)){
                    $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
                    $tmp['index'] = $count;
                    $result[] = $tmp;
                }
            }
        }
/*        if(self::CheckInRange($begin, $end, $recurring_info['truestart']) && !in_array($recurring_info['truestart'], $dates)){
            $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
            $tmp['index'] = $count;
            $result[] = $tmp;
            $count++;          
        }*/

/*        print_r($recurring_info); echo "<br /><br />";
        
        foreach ( $period as $dt ){
            if(!in_array($dt->format( "Y-m-d" ), $dates)){
                $tmp = self::ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor);
                $tmp['index'] = $count;
                $result[] = $tmp;
                $count++;
            }
        }*/
//        exit;
        return $result;
    }
    
    public static function ApplyDate($recurring_info, $dt, $cssClass, $color, $textColor){
//        echo "HERE FOR: {$recurring_info['activityid']} -- {$dt->format('Y-m-d')}<br />";
        $tmp['id'] = $recurring_info['activityid'];
        $tmp['title'] = $recurring_info['subject'];
        $tmp['ischild'] = 1;
        $tmp['className'] = $cssClass;
        $tmp['allDay'] = false;
        $tmp['color'] = $color;
        $tmp['textColor'] = $textColor;
        $tmp['start_date'] = $dt->format("Y-m-d");
        $tmp['end_date'] = $dt->format("Y-m-d");
        $tmp['start'] = $dt->format( "Y-m-d" ) . ' ' . $recurring_info['time_start'];
        $tmp['end'] = $dt->format( "Y-m-d" ) . ' ' . $recurring_info['time_end'];
        $tmp['uid'] = uniqid('uid_');
        return $tmp;
    }
    
    /**
     * This function takes the repeat activities row and essentially creates ghost events to display on the calendar.  Using the 2 dates, it calculates what days to show
     * @param type $result
     * @param type $repeat_activities
     * @param type $cssClass
     * @param type $color
     * @param type $textColor
     */
    public static function ConvertRepeatActivities(&$result, $repeat_activities, $cssClass, $color, $textColor){
        if($repeat_activities){
            foreach($repeat_activities AS $k => $v){
                if($v['recurringtype'] == 'Daily'){
                    $data = self::DailyLoop($v, $cssClass, $color, $textColor);
                    if(is_array($result) && is_array($data))
                        $result = array_merge($result, $data);
                } else if($v['recurringtype'] == 'Weekly'){
                    $data = self::WeekLoop($v, $cssClass, $color, $textcolor);
                    if(is_array($result) && is_array($data))
                        $result = array_merge($result, $data);
                } else if($v['recurringtype'] == 'AbsoluteMonthly'){
                    $data = self::AbsoluteMonthlyLoop($v, $cssClass, $color, $textcolor);
                    if(is_array($result) && is_array($data))
                        $result = array_merge($result, $data);
                } else if($v['recurringtype'] == 'RelativeMonthly'){
                    $data = self::RelativeMonthlyLoop($v, $cssClass, $color, $textcolor);
                    if(is_array($result) && is_array($data))
                        $result = array_merge($result, $data);
                }
/*
                $count = 2;
                $begin = new DateTime( $v['recurringdate'] );
                $begin->add(new DateInterval("P1D"));//Avoid dupes on the first day
                $end = new DateTime( $v['recurringenddate'] );
                $end->add(new DateInterval("P1D"));
                $days_of_week = explode(' ', $v['recurringinfo']);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $dates = self::GetIgnoreDates($v['activityid']);
                foreach ( $period as $dt ){
                    $tmp['id'] = $v['activityid'];
                    $tmp['index'] = $count;
                    $count++;
                    $tmp['title'] = $v['subject'];
                    $tmp['ischild'] = 1;
                    $tmp['className'] = $cssClass;
                    $tmp['allDay'] = false;
                    $tmp['color'] = $color;
                    $tmp['textColor'] = $textColor;
                    $tmp['start_date'] = $dt->format("Y-m-d");
                    $tmp['end_date'] = $dt->format("Y-m-d");
                    $tmp['start'] = $dt->format( "Y-m-d" ) . ' ' . $v['time_start'];
                    $tmp['end'] = $dt->format( "Y-m-d" ) . ' ' . $v['time_end'];

                    if(!in_array($dt->format( "Y-m-d" ), $dates) && $v['recurringtype'] == 'Daily'){
                        $result[] = $tmp;
                    } else
                    if(!in_array($dt->format( "Y-m-d"), $dates) && $v['recurringtype'] == 'Weekly'){
                        $day = $dt->format("l");
                        if(in_array($day, $days_of_week))
                            $result[] = $tmp;
                    } else
                    if(!in_array($dt->format( "Y-m-d"), $dates) && $v['recurringtype'] == 'AbsoluteMonthly'){
                        $day = $dt->format("d");
                        if(in_array($day, $days_of_week))
                            $result[] = $tmp;
                    } else
                    if(!in_array($dt->format( "Y-m-d"), $dates) && $v['recurringtype'] == 'RelativeMonthly'){
                        $day = $dt->format("Y-m");
                        $days_of_week = $v['recurringinfo'];
                        $date_to_use = date('Y-m-d', strtotime($days_of_week . ' of ' . $day));
                        if($date_to_use == $dt->format("Y-m-d"))
                            $result[] = $tmp;
                    }
                }
*/
            }
        }
    }
}