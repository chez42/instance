<?php

class OmniCal_ExchangeRecurring_Model extends Vtiger_Module_Model{

    static public function SetRecurringData($activity_recurrence){
        
		$data = array();
        
		if(is_object($activity_recurrence)){
            if(is_object($activity_recurrence->DailyRecurrence)){//It is a daily recurrence
                if(is_object($activity_recurrence->EndDateRecurrence)){
                    $start = explode('-',$activity_recurrence->EndDateRecurrence->StartDate);
                    $end = explode('-',$activity_recurrence->EndDateRecurrence->EndDate);
                    $data = array(
						"type" => "Daily",
						"has_end" => 1,
						"recurring_info" => "Daily",
						"start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
						"end_date" => $end[0] . '-' . $end[1] . '-' . $end[2],
						"interval" => $activity_recurrence->DailyRecurrence->Interval,
						"recurring_freq" => $activity_recurrence->DailyRecurrence->Interval
					);
                }else{
                    $start = explode('-',$activity_recurrence->NoEndRecurrence->StartDate);
					$end = date('Y-m-d', strtotime("+3 months", strtotime($activity_recurrence->NoEndRecurrence->StartDate)));
                    $data = array(
						"type" => "Daily",
                        "has_end" => 0,
						"recurring_info" => "Daily",
						"start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
						"end_date" => $end, // create upto 3 months
						"interval" => $activity_recurrence->DailyRecurrence->Interval,
						"recurring_freq" => $activity_recurrence->DailyRecurrence->Interval
					);
                }
                return $data;
            }
            if(is_object($activity_recurrence->WeeklyRecurrence)){
                if(is_object($activity_recurrence->EndDateRecurrence)){
                    $start = explode('-',$activity_recurrence->EndDateRecurrence->StartDate);
                    $end = explode('-',$activity_recurrence->EndDateRecurrence->EndDate);
                    $daysOfWeek = $activity_recurrence->WeeklyRecurrence->DaysOfWeek;
                    $data = array("type" => "Weekly",
                                  "has_end" => 1,
                                  "recurring_info" => $daysOfWeek,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end[0] . '-' . $end[1] . '-' . $end[2],
                                  "days_of_week" => $daysOfWeek,
								  "interval" => $activity_recurrence->WeeklyRecurrence->Interval,
								  "recurring_freq" => $activity_recurrence->WeeklyRecurrence->Interval
					);                    
                }else{
                    $start = explode('-',$activity_recurrence->NoEndRecurrence->StartDate);
                    $end = date('Y-m-d', strtotime("+3 months", strtotime($activity_recurrence->NoEndRecurrence->StartDate)));
                    $daysOfWeek = $activity_recurrence->WeeklyRecurrence->DaysOfWeek;
                    $data = array("type" => "Weekly",
                                  "has_end" => 0,
                                  "recurring_info" => $daysOfWeek,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end,// create upto 3 months
						          "days_of_week" => $daysOfWeek,
                				  "interval" => $activity_recurrence->WeeklyRecurrence->Interval,
								  "recurring_freq" => $activity_recurrence->WeeklyRecurrence->Interval
					);                    
                }
                return $data;
            }
            if(is_object($activity_recurrence->AbsoluteMonthlyRecurrence)){
                if(is_object($activity_recurrence->EndDateRecurrence)){
                    $start = explode('-',$activity_recurrence->EndDateRecurrence->StartDate);
                    $end = explode('-',$activity_recurrence->EndDateRecurrence->EndDate);
                    $day_of_month = $activity_recurrence->AbsoluteMonthlyRecurrence->DayOfMonth;
                    $data = array("type" => "AbsoluteMonthly",
                                  "has_end" => 1,
                                  "recurring_info" => $day_of_month,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end[0] . '-' . $end[1] . '-' . $end[2],
                                  "days_of_week" => $daysOfWeek,
								  "days_of_month" => $day_of_month,
								  "interval" => $activity_recurrence->AbsoluteMonthlyRecurrence->Interval,
								  "recurring_freq" => $activity_recurrence->AbsoluteMonthlyRecurrence->Interval);
                }else{
                    $start = explode('-',$activity_recurrence->NoEndRecurrence->StartDate);
					$end = date('Y-m-d', strtotime("+2 years", strtotime($activity_recurrence->NoEndRecurrence->StartDate)));
                    $day_of_month = $activity_recurrence->AbsoluteMonthlyRecurrence->DayOfMonth;
                    $data = array("type" => "AbsoluteMonthly",
                                  "has_end" => 0,
                                  "recurring_info" => $day_of_month,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end,
						          "days_of_month" => $day_of_month,
								  "interval" => $activity_recurrence->AbsoluteMonthlyRecurrence->Interval,
								  "recurring_freq" => $activity_recurrence->AbsoluteMonthlyRecurrence->Interval);
                }
                return $data;
            }
            if(is_object($activity_recurrence->RelativeMonthlyRecurrence)){
                if(is_object($activity_recurrence->EndDateRecurrence)){
                    $start = explode('-',$activity_recurrence->EndDateRecurrence->StartDate);
                    $end = explode('-',$activity_recurrence->EndDateRecurrence->EndDate);
                    $day_of_week_index = $activity_recurrence->RelativeMonthlyRecurrence->DayOfWeekIndex;
                    $days_of_week = $activity_recurrence->RelativeMonthlyRecurrence->DaysOfWeek;
                    $data = array("type" => "RelativeMonthly",
                                  "has_end" => 1,
                                  "recurring_info" => $day_of_week_index . ' ' . $days_of_week,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end[0] . '-' . $end[1] . '-' . $end[2],
                                  "days_of_month" => $days_of_week_index . ' ' . $days_of_week,
								  "interval" => $activity_recurrence->RelativeMonthlyRecurrence->Interval,
								  "recurring_freq" => $activity_recurrence->RelativeMonthlyRecurrence->Interval);
                }else{
                    $start = explode('-',$activity_recurrence->NoEndRecurrence->StartDate);
                    $days_of_week = $activity_recurrence->RelativeMonthlyRecurrence->DaysOfWeek;
                    $day_of_week_index = $activity_recurrence->RelativeMonthlyRecurrence->DayOfWeekIndex;
                    $data = array("type" => "RelativeMonthly",
                                  "has_end" => 0,
                                  "recurring_info" => $day_of_week_index . ' ' . $days_of_week,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => '2200-01-01',
                                  "days_of_month" => $days_of_week_index . ' ' . $days_of_week,
								  "interval" => $activity_recurrence->RelativeMonthlyRecurrence->Interval,
								  "recurring_freq" => $activity_recurrence->RelativeMonthlyRecurrence->Interval);
                }
                return $data;
            }
            if(is_object($activity_recurrence->YearlyRecurrence)){
//                echo "ITS YEARLY";
            }
			if(is_object($activity_recurrence->AbsoluteYearlyRecurrence)){
                if(is_object($activity_recurrence->EndDateRecurrence)){
                    $start = explode('-',$activity_recurrence->EndDateRecurrence->StartDate);
                    $end = explode('-',$activity_recurrence->EndDateRecurrence->EndDate);
                    $day_of_month = $activity_recurrence->AbsoluteYearlyRecurrence->DayOfMonth;
                    $month = $activity_recurrence->AbsoluteYearlyRecurrence->Month;
                    $data = array("type" => "AbsoluteYearly",
                                  "has_end" => 1,
                                  "recurring_info" => $day_of_month . ' ' . $month,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end[0] . '-' . $end[1] . '-' . $end[2],
                                  "days_of_month" => $day_of_month . ' ' . $month);
                }else{
                    $start = explode('-',$activity_recurrence->NoEndRecurrence->StartDate);
                    $day_of_month = $activity_recurrence->AbsoluteYearlyRecurrence->DayOfMonth;
                    $month = $activity_recurrence->AbsoluteYearlyRecurrence->Month;
                    $data = array("type" => "AbsoluteYearly",
                                  "has_end" => 0,
                                  "recurring_info" => $day_of_month . ' ' . $month,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => date("Y-m-d", strtotime(date("Y-m-d", strtotime($activity_recurrence->NoEndRecurrence->StartDate)) . " + 1 year")),
						          "days_of_month" => $day_of_month . ' ' . $month);
                }
                return $data;
            }
			
			if(is_object($activity_recurrence->RelativeYearlyRecurrence)){
                if(is_object($activity_recurrence->EndDateRecurrence)){
                    $start = explode('-',$activity_recurrence->EndDateRecurrence->StartDate);
                    $end = explode('-',$activity_recurrence->EndDateRecurrence->EndDate);
                    $day_of_month = $activity_recurrence->RelativeYearlyRecurrence->DayOfMonth;
                    $month = $activity_recurrence->RelativeYearlyRecurrence->Month;
                    $data = array("type" => "RelativeYearly",
                                  "has_end" => 1,
                                  "recurring_info" => $day_of_month . ' ' . $month,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => $end[0] . '-' . $end[1] . '-' . $end[2],
                                  "days_of_month" => $day_of_month . ' ' . $month);
                }else{
                    $start = explode('-',$activity_recurrence->NoEndRecurrence->StartDate);
                    $day_of_month = $activity_recurrence->RelativeYearlyRecurrence->DayOfMonth;
                    $month = $activity_recurrence->RelativeYearlyRecurrence->Month;
                    $data = array("type" => "RelativeYearly",
                                  "has_end" => 0,
                                  "recurring_info" => $day_of_month . ' ' . $month,
                                  "start_date" => $start[0] . '-' . $start[1] . '-' . $start[2],
                                  "end_date" => date("Y-m-d", strtotime(date("Y-m-d", strtotime($activity_recurrence->NoEndRecurrence->StartDate)) . " + 1 year")),
						          "days_of_month" => $day_of_month . ' ' . $month);
                }
                return $data;
            }
			
        }
        return 0;
    }
    
    /**
     * Inserts the recurring information into the recurringevents table
     * @global type $adb
     * @param type $record
     * @param type $recurring_info
     */
    static public function InsertRecurringInfoIntoCRM($record, $recurring_info){
        global $adb;
        $query = "DELETE FROM vtiger_recurringevents WHERE activityid = ?";
        $adb->pquery($query, array($record));
        
		if(!isset($recurring_info['recurring_freq']) || !$recurring_info['recurring_freq'])
			$recurring_info['recurring_freq'] = "1";
			
        $query = "INSERT INTO vtiger_recurringevents (activityid, recurringdate, recurringtype, recurringfreq, recurringinfo, recurringenddate)
                  VALUES (?, ?, ?, ?, ?, ?)";
                
        $adb->pquery($query, array($record,
                                   $recurring_info['start_date'],
                                   $recurring_info['type'],
								   $recurring_info['recurring_freq'],
                                   $recurring_info['recurring_info'],
                                   $recurring_info['end_date']));
    }
    
    static public function GetModifiedOccurrences($event){
        
		$modified = array();
        
		if(is_array($event->ModifiedOccurrences)){
            foreach($event->ModifiedOccurrences AS $a => $b){
                foreach($b AS $k => $v){
                    $modified[] = $v;
                }
            }
        } else {
            if(is_array($event->ModifiedOccurrences->Occurrence)){
                foreach($event->ModifiedOccurrences->Occurrence AS $a => $b){
                    $modified[] = $b;
                }                
            } else if(is_object($event->ModifiedOccurrences->Occurrence)){
				$modified[] = $event->ModifiedOccurrences->Occurrence;
            }
        }
        
        return $modified;
    }
    
    static public function InsertModifiedOccurrencesIntoCRM($record, $occurrences){
        global $adb;
        
    }
}

?>