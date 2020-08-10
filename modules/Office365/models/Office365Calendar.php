<?php
use Microsoft\Graph\Model;
class Office365_Office365Calendar_Model extends Office365_Office365_Model{
    
    function getEvents($lastUpdatedTime, $offset = 0){
        
        $graph = $this->ews;
        
        $events = array();
        
        $date = new DateTime($lastUpdatedTime);
        
        $endDateTime = $date->modify('+10 minutes')->format('Y-m-d\TH:i:s.000\Z');
        
        $deltaToken = Office365_Utils_Helper::getSyncState('Calendar');
        
        if($deltaToken){
            $url = $deltaToken;
        }else{
            $url = '/me/calendarView/delta?startdatetime='.$lastUpdatedTime.'&enddatetime='.$endDateTime;
        }
        
		try{
		   
		    $all_calendar = $graph->createCollectionRequest("GET", $url)->addHeaders(array("Prefer" => "odata.track-changes"))
		    ->setReturnType(Model\Event::class);
		    
		    while (!$all_calendar->isEnd()){
		        foreach($all_calendar->getPage() as $calEvent){
		            $events[] = $calEvent;
		        }
		    }
		    
		    $token = strstr($all_calendar->getDeltaLink(),'/me');
		    
		    Office365_Utils_Helper::updateSyncState($token, 'Calendar');
		    
		    return $events;
		    
		} catch (Exception $e){
		    
			$error = $e->getMessage();
			return false;
			
		}
    }
    
    
    public function createCalendarItems($items, $options = array()){
        
       // $items = $this->ensureIsArray($items, true);
        $graph = $this->ews;
        
        $events = new Model\Event();
        
        $events->setSubject($items['Subject']);
        //$events->setLocation($items['Location']);
        
        $body = new Model\ItemBody();
        $body->setContent($items['Body']);
        $body->setContentType('HTML');
        
        $events->setBody($body);
        $events->setSensitivity($items['Sensitivity']);
        
        $start = new  Model\DateTimeTimeZone();
        $start->setDateTime($items['Start']);
        $start->setTimeZone('UTC');
        $events->setStart($start);
        
        $end = new  Model\DateTimeTimeZone();
        $end->setDateTime($items['End']);
        $end->setTimeZone('UTC');
        $events->setEnd($end);
        
        if(!empty($items['Attendee']))
            $events->setAttendees($items['Attendee']);
        
        if($changes['IsAllDayEvent'])
            $events->setIsAllDay(true);
       
		try{
		
		    $item = $graph->createRequest("POST", "/me/Events")
		    ->attachBody($events)
		    ->execute();
		    
		} catch (Exception $e){
			$error = $e->getMessage();
		}
		
		
        return $item;
    }
    
    public function updateCalendarItems($updateEvents, $options = array()){
        
        $graph = $this->ews;
        
        foreach($updateEvents as $itemId => $changes){
            
            $events = new Model\Event();
            
            $events->setId($itemId);
            $events->setSubject($changes['Subject']);
            //$events->setLocation($items['Location']);
            
            $body = new Model\ItemBody();
            $body->setContent($changes['Body']);
            $body->setContentType('HTML');
            
            $events->setBody($body);
            $events->setSensitivity($changes['Sensitivity']);
            
            $start = new  Model\DateTimeTimeZone();
            $start->setDateTime($changes['Start']);
            $start->setTimeZone('UTC');
            $events->setStart($start);
            
            $end = new  Model\DateTimeTimeZone();
            $end->setDateTime($changes['End']);
            $end->setTimeZone('UTC');
            $events->setEnd($end);
            
            if($changes['IsAllDayEvent'])
                $events->setIsAllDay(true);
            
            if(!empty($changes['Attendee']))
                $events->setAttendees($changes['Attendee']);
           
            try{
                $items[] = $graph->createRequest("PATCH", "/me/Events/".$itemId)
                ->attachBody($events)
                ->execute();
            } catch (Exception $e){
                $error = $e->getMessage();
            }
            
        }
        
        return $items;
    }
    
    
    public function getRecurringSeriesEvents($masterId){
        
        $graph = $this->ews;
        
        $seriesEvents = array();
        
        $startDateTime = date("Y-m-d\TH:i:s.000\Z", strtotime("-5 days", strtotime(date("Y-m-d"))));
        
        $date = new DateTime();
        
        $endDateTime = $date->modify('+100 years')->format('Y-m-d\TH:i:s.000\Z');
        
        $encodeFilter = urlencode("seriesMasterId eq '".$masterId."'");
        
        try{
            
            $all_calendar = $graph->createCollectionRequest("GET", '/me/Events/'.$masterId.'/instances?startDateTime='.$startDateTime.'&endDateTime='.$endDateTime)
            ->setReturnType(Model\Event::class)->setPageSize(50);
            
            while (!$all_calendar->isEnd()){
                foreach($all_calendar->getPage() as $calEvent){
                    $seriesEvents[] = $calEvent;
                }
            }
            
            return $seriesEvents;
            
        } catch (Exception $e){
            
            $seriesEvents = $e->getMessage(); 
        
        }
        
        
        return $seriesEvents;
        
    }
   
}