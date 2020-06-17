<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OmniCal_ExchangeEws_View extends Vtiger_BasicAjax_View{
    public function __construct() {
    }
    
    public function process(\Vtiger_Request $request) {
//        echo 'getting for First Monday of 2015-02';
//        $date_to_use = date('Y-m-d', strtotime('First Monday of 2015-04'));
//        echo $date_to_use;
        
        $action = new OmniCal_ExchangeEws_Action();
        $action->process($request);
/*        $dates = array();
        $count = 2;
                $begin = new DateTime( '2015-01-20' );
                $begin->add(new DateInterval("P1D"));//Avoid dupes on the first day
                $end = new DateTime( '2015-01-31' );
                $end->add(new DateInterval("P1D"));
                $days_of_week = explode(' ', $v['recurringinfo']);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
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

                    $result = $tmp;
                    echo $count . "<br />";
        }
        */
/*        $start_date = '2014-07-07T00:00:00';
        OmniCal_ExchangeEws_Action::HandleEventsFromExchangeByDate('rsandnes', $start_date);*/
    }
}

?>